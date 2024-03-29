<?php

/**
 * OSKA model class for Stud.IP
 *
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @author    Viktoria Wiebe <vwiebe@uni-osnabrueck.de>
 * 
 * @property int     $mentor_id
 * @property int     $mentee_id
 * @property bool    $issue
 * @property int     $mkdate
 * @property int     $chdate
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class OskaMatches extends SimpleORMap
{
    protected static function configure($config = array())
    {
        $config['db_table'] = 'oska_matches';

        parent::configure($config);
    }

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public function findAllMaches()
    {
        $matches = [];
        foreach (self::findBySQL('mentor_id != ""', array()) as $match) {
            array_push($matches, ['mentor' => User::find($match->mentor_id), 'mentee' => User::find($match->mentee_id)]);
        }

        return $matches;
    }

    public function getMentees($mentor_id)
    {
        $mentees = [];
        foreach (self::findBySQL('mentor_id = ?', array($mentor_id)) as $match) {
            $user = User::find($match->mentee_id);
            $mentee = [];
            $mentee['user_id'] = $user->user_id;
            $mentee['issue'] = $match->issue;
            $mentee['username'] = $user->username;
            $mentee['vorname'] = $user->vorname;
            $mentee['nachname'] = $user->nachname;
            $len = count($user->studycourses);
            foreach ($user->studycourses as $index => $val) {
                $mentee['studycourse'] .= $val->studycourse->name;
                if ($index != $len -1) {
                    $mentee['studycourse'] .= ', ';
                }
            }
            array_push($mentees, $mentee);
        }
        return $mentees;
    }

    public function getIssues()
    {
        $issues = [];
        foreach (self::findBySQL('issue = 1', array()) as $issue) {
            array_push($issues, ['mentor' => User::find($issue->mentor_id), 'mentee' => User::find($issue->mentee_id)]);
        }

        return $issues;
    }

    public function getMentor($mentee_id)
    {
        return self::findOneBySQL('mentee_id = ?', array($mentee_id));
    }

    public function countMatches($fach_selection = null)
    {
        return count(self::findAllMatches(1, null, $fach_selection));
    }

    public function findAllMatches($lower_bound = 1, $elements_per_page = null, $fach_id = null)
    {

        $matches = [];

        $sql = "
            SELECT
                *
            FROM
                oska_matches
            JOIN
                user_studiengang
            ON
                oska_matches.mentee_id = user_studiengang.user_id";

        if($fach_id != null) {
            $sql .= " WHERE fach_id = :fach_id";
        }

        $sql .= " GROUP BY oska_matches.mentee_id";

        if($elements_per_page != null) {
            $sql .= " LIMIT :lower_bound, :elements_per_page";
        }
        $statement = DBManager::get()->prepare($sql);
        $statement->execute(['fach_id' => $fach_id,
                             'lower_bound' => $lower_bound,
                             'elements_per_page' => $elements_per_page]);
        $matches = $statement->fetchAll();

        return $matches;
    }
}
