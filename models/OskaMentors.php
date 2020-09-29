<?php

/**
 * OSKA model class for Stud.IP
 *
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @author    Viktoria Wiebe <vwiebe@uni-osnabrueck.de>
 * 
 * @property int     $user_id
 * @property bool    $teacher
 * @property json    $abilities
 * @property int     $mentee_counter
 * @property string  $description
 * @property int     $mkdate
 * @property int     $chdate
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class OskaMentors extends SimpleORMap
{
    protected static function configure($config = array())
    {
        $config['db_table'] = 'oska_mentors';

        parent::configure($config);
    }

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public function getProfile()
    {
        $mentor = new \stdClass();
        $mentor->lehramt = $this->teacher;
        $mentor->studycourse = $this->getMentorPrefStudycourse();
        $mentor->lehramt_detail = $this->getMentorAbilities('lehramt_detail');
        $mentor->firstgen = $this->getMentorAbilities('firstgen');
        $mentor->children = $this->getMentorAbilities('children');
        $mentor->apprentice = $this->getMentorAbilities('apprentice');
        $mentor->migration = $this->getMentorAbilities('migration');
        $mentor->description = $this->description;

        return $mentor;
    }

    public function getMentorAbilities($type)
    {
        $abilities = json_decode($this->abilities);

        if (in_array($abilities->$type, [-1, 0, 1, 2])) {
            return $abilities->$type;
        } else {
            return 2;
        }
    }

    public static function getMentorDescription($id = NULL)
    {
        if (self::find($id)) {
            return self::find($id)->description;
        } else {
            return NULL;
        }
    }

    public function getMentorPrefStudycourse()
    {
        $abilities = json_decode($this->abilities);

        return is_array($abilities->studycourse) ? $abilities->studycourse : [$abilities->studycourse];
    }

    public function getMentorStudycourses()
    {
        $studycourses = new SimpleCollection(UserStudyCourse::findByUser($this->user_id));
        $studycourse_data = [];
        foreach ($studycourses as $studycourse) {
            $studycourse_data[$studycourse->fach_id] = $studycourse->studycourse_name;
        }

        return $studycourse_data;
    }

    public function raiseCounter()
    {
        $this->mentee_counter += 1;
    }

    public function lowerCounter()
    {
        $this->mentee_counter -= 1;
    }

    public function countMentors()
    {
        return count(self::findBySQL('user_id != ""'));
    }

    public function getCounters()
    {
        $sql = "
            SELECT 
                mentee_counter, COUNT(*) as count
            FROM 
                oska_mentors
            GROUP BY
                mentee_counter 
            ORDER BY 
                mentee_counter ASC";

        $statement = DBManager::get()->prepare($sql);
        $statement->execute($parameters);
        $counters = $statement->fetchAll();

        return $counters;
    }

    public function countMentorsWithFilter($fach_selection, $mentee_counter)
    {
        return count(self::findAllMentors(1, null, $fach_selection, $mentee_counter));
    }

    public function findAllMentors($lower_bound = 1, $elements_per_page = null, $fach_id = null, $mentee_counter = null)
    {
        $sql = "
            SELECT 
                *
            FROM
                oska_mentors
            JOIN
                user_studiengang
            ON
                oska_mentors.user_id = user_studiengang.user_id";
        if($fach_id != null) {
            $sql .= " WHERE fach_id = '" . $fach_id . "'";
        }

        if ($mentee_counter != null) {
            $sql .= $fach_id != null ? " AND" : " WHERE";
            $sql .= " mentee_counter = $mentee_counter";
        }

        $sql .= " GROUP BY oska_mentors.user_id";
        
        if($elements_per_page != null){
            $sql .= " LIMIT ". $lower_bound. ', '. $elements_per_page;
        }

        $statement = DBManager::get()->prepare($sql);
        $statement->execute($parameters);
        $mentors = $statement->fetchAll();

        return $mentors;
    }
}
