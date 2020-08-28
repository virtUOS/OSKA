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

    public function getProfile()
    {
        $mentor->lehramt = $this->teacher;
        $mentor->lehramt_detail = $this->getMentorAbilities('lehramt_detail');
        $mentor->firstgen = $this->getMentorAbilities('firstgen');
        $mentor->children = $this->getMentorAbilities('children');
        $mentor->apprentice = $this->getMentorAbilities('apprentice');
        $mentor->migration = $this->getMentorAbilities('migration');

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

    public function setMentorAbilities($type, $value)
    {
        $abilities = json_decode($this->abilities);
        $abilities->$type = $value;

        $this->abilities = json_encode($abilities);
    }
}
