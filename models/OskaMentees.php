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
    * Saves a user as mentee and their given data in db.
    *
    * @param string $id the user ID
    * @param mixed[] $data array of mentee data: (
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
    * Returns whether a mentee already has a matched tutor.
    */
    public function hasTutor()
    {
        return $this->has_tutor;
    }

    public function setHasTutor($val)
    {
        $this->has_tutor = $val;
    }
    
    /**
    * Returns the int value of the given preference if it is a valid number.
    *
    * @param string $type preference type name
    */
    public function getMenteePreferences($type)
    {
        if ($type != 'gender' && $type != 'studycourse') {
            $preferences = json_decode($this->preferences);

            if (in_array($preferences->$type, [-1, 0, 1, 2])) {
                return $preferences->$type;
            } else {
                return 2;
            }
        } else {
            return NULL;
        }
    } 
    
    /**
    * Returns the studycourse id of the preferred studycourse.
    */
    public function getMenteePrefStudycourse()
    {
        $preferences = json_decode($this->preferences);
        
        return $preferences->studycourse;
    }
    
    /**
    * Returns the gender value preferred by the mentee if it is a valid number.
    */
    public function getMenteePrefGender()
    {
        $preferences = json_decode($this->preferences);
        if (in_array($preferences->gender, [0, 1, 2, 3])) {
            return $preferences->gender;
        } else {
            return 0;
        }
    }

    public function countMentees($fach_selection)
    {
        return count(self::findAllMentees(1, null, $fach_selection));
    }

    public function findAllMentees($lower_bound = 1, $elements_per_page = null, $fach_id = null)
    {
        $sql = "SELECT * FROM oska_mentees JOIN user_studiengang ON oska_mentees.user_id = user_studiengang.user_id";
        if($fach_id != null) {
            $sql .= " WHERE fach_id = '" . $fach_id . "'";
        }
        if($elements_per_page != null){
            $sql .= " LIMIT ". $lower_bound. ', '. $elements_per_page;
        }

        $statement = DBManager::get()->prepare($sql);
        $statement->execute($parameters);
        $mentees = $statement->fetchAll();

        return $mentees;
    }

}
