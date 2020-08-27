<?php

/**
 * @author  <lucke@elan-ev.de>
 *
 * @property int     $user_id
 * @property bool    $teacher
 * @property json    $abilities
 * @property int     $mentee_counter
 * @property string  $description
 * @property int     $mkdate
 * @property int     $chdate
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
}