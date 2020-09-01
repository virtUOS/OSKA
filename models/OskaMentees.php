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
    
    /**
    * Saves a user as mentee and their given data in db
    *
    * @param id the user ID
    * @param data array of mentee data: (
    *               [studycourse] => string,
    *               [teacher]     => int,
    *               [gender]      => int,
    *               [migrant]     => int,
    *               [first_gen]   => int,
    *               [children]    => int,
    *               [apprentice]  => int)
    */
    public function register($data) 
    {
        return self::create($data);
    }
    
    /**
    * Returns whether a mentee already has a matched tutor
    */
    public function hasTutor()
    {
        return $this->has_tutor;
    }
}
