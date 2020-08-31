<?php

/**
 * @author  <lucke@elan-ev.de>
 *
 * @property int     $mentor_id
 * @property int     $mentee_id
 * @property bool    $issue
 * @property int     $mkdate
 * @property int     $chdate
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

    public function getMentees($mentor_id)
    {
        $mentees = [];
        foreach (self::findBySQL('mentor_id = ?', array($mentor_id)) as $match) {
            $user = User::find($match->mentee_id);
            $mentee = [];
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
}
