<?php

/**
 * @author  <lucke@elan-ev.de>
 *
 * @property int     $user_id
 * @property bool    $teacher
 * @property json    $preferences
 * @property bool    $has_tutor
 * @property int     $mkdate
 * @property int     $chdate
 */

class OskaMentees extends SimpleORMap
{
    protected static function configure($config = array())
    {
        $config['db_table'] = 'oska_mentees';

        parent::configure($config);
    }

    public function __construct($id = null)
    {
        parent::__construct($id);
    }
    
    public function register($id = NULL, $data) {
        // TODO store mentee data
    }
}
