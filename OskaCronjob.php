<?php

/**
 * OSKA cronjob for Stud.IP
 *
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @author    Viktoria Wiebe <vwiebe@uni-osnabrueck.de>
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

require_once('lib/classes/CronJob.class.php');

class OskaCronjob extends CronJob
{
    public static function getName()
    {
        return _('OSKA Matching');
    }

    public static function getDescription()
    {
        return _('Findet einen möglichst passenden OSKA für einen Mentee.');
    }

    public static function getParameters()
    {
        return [
            'verbose' => [
                'type'        => 'boolean',
                'default'     => false,
                'status'      => 'optional',
                'description' => _('Sollen Ausgaben erzeugt werden'),
            ],
        ];
    }

    public function setUp()
    {
        global $STUDIP_BASE_PATH;
        require_once $STUDIP_BASE_PATH . '/public/plugins_packages/virtUOS/OSKA/models/OskaMentors.php';
        require_once $STUDIP_BASE_PATH . '/public/plugins_packages/virtUOS/OSKA/models/OskaMentees.php';
        require_once $STUDIP_BASE_PATH . '/public/plugins_packages/virtUOS/OSKA/models/OskaMatches.php';
    }

    /**
    * Finds a matching mentor for each mentee in the database.
    *
    * Goes through all mentors and refines the selection with each step,
    * according to studycourse, teacher (lehramt) and preferences.
    * Selection stops when no more refined matches can be found and selects
    * mentors with the smallest number of mentees and assigns one of them randomly.
    */
    public function execute($last_result, $parameters = [])
    {
        $max_mentees = 8;

        $mentees = OskaMentees::findBySql('has_tutor = 0');
        $all_mentors = OskaMentors::findBySql("mentee_counter < ?", [$max_mentees]);

        foreach ($mentees as $mentee) {

            // match according to preferred studycourse of mentee
            // and only select mentors who don't have the maximum of mentees
            $mentee_studycourse = $mentee->getMenteePrefStudycourse();

            $mentors = [];
            foreach ($all_mentors as $mentor) {
                if($mentor->getMentorPrefStudycourse() == $mentee_studycourse) {
                    array_push($mentors, $mentor);
                }
            }

            // match according to whether mentee studies to become a teacher
            if (!empty($mentors)) {
                $mentors_tmp = array_filter(
                    $mentors,
                    function ($mentor) {
                        return $mentor->teacher == $mentee->teacher;
                    }
                );
                if ($mentors_tmp) {
                    $mentors = $mentors_tmp;
                }
            }

            // match according to preferences
            // create a sum over all picked preferences and pick all the mentors
            // with the maximum sum reached
            if (!empty($mentors)) {

                $mentor_prefsums = [];
                $preferences['lehramt_detail'] = $mentee->getMenteePreferences('lehramt_detail');
                $preferences['firstgen'] = $mentee->getMenteePreferences('firstgen');
                $preferences['children'] = $mentee->getMenteePreferences('children');
                $preferences['apprentice'] = $mentee->getMenteePreferences('apprentice');
                $preferences['migration'] = $mentee->getMenteePreferences('migration');
                $preferences['gender'] = $mentee->getMenteePrefGender();

                foreach ($mentors as $mentor) {
                    $abilities['lehramt_detail'] = $mentor->getMentorAbilities('lehramt_detail');
                    $abilities['firstgen'] = $mentor->getMentorAbilities('firstgen');
                    $abilities['children'] = $mentor->getMentorAbilities('children');
                    $abilities['apprentice'] = $mentor->getMentorAbilities('apprentice');
                    $abilities['migration'] = $mentor->getMentorAbilities('migration');
                    $abilities['gender'] = User::find($mentor->user_id)->geschlecht;

                    $mentor_prefsums[$mentor->user_id] = 0;

                    // calculate number of matching preferences/abilities
                    foreach ($abilities as $ability => $value) {
                        if ($value == $preferences->$ability && ($preferences->$ability > 0 ||
                                $ability == 'lehramt_detail' && $preferences->$ability >= 0)) {
                            $mentor_prefsums[$mentor->user_id]++;
                        }
                    }
                }

                $max_prefsum = max($mentor_prefsums);

                // pick only mentors that have the highest number of matching preferences/abilities
                $mentors_tmp = array_filter(
                    $mentors,
                    function ($mentor) {
                        return ($mentor_prefsums[$mentor->user_id] == $max_prefsum);
                    }
                );

                if ($mentors_tmp) {
                    $mentors = $mentors_tmp;
                }
            }

            if (!empty($mentors)) {
                // pick mentors with smallest number of mentees
                if (count($mentors) > 1) {
                    $mentors_tmp = [];
                    foreach($mentors as $mentor) {
                        $mentors_tmp[$mentor->user_id] = $mentor->mentee_counter;
                    }
                    $min_mentee_count = min($mentors_tmp);
                    $mentors = array_filter(
                        $mentors,
                        function ($mentor) {
                            return $mentor->mentee_counter == $min_mentee_count;
                        }
                    );
                }

                // randomly pick one mentor from list
                $matched_mentor = $mentors[mt_rand(0, count($mentors) - 1)];
                if (!empty($matched_mentor->user_id)) {
                    OskaMatches::create(['mentor_id' => $matched_mentor->user_id, 
                    'mentee_id' => $mentee->user_id]);

                    // update mentee
                    $mentee->has_tutor = 1;
                    $mentee->store();

                    // update mentor
                    $matched_mentor->mentee_counter++;
                    $matched_mentor->store();
                }
            }
        }
    }
}
