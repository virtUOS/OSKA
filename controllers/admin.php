<?php

/**
 * OSKA controller class for Stud.IP
 *
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @author    Viktoria Wiebe <vwiebe@uni-osnabrueck.de>
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 **/

class AdminController extends PluginController {

    function before_filter(&$action, &$args)
    {
        global $perm;
        parent::before_filter($action, $args);
        if(!$perm->have_studip_perm('tutor', Context::getId())) {
            throw new AccessDeniedException('Sie verfügen nicht über die notwendigen Rechte für diese Aktion');
        }
    }

    public function index_action()
    {
        PageLayout::addStylesheet($this->plugin->getPluginURL() . '/css/oska.css?v=42');
        Navigation::activateItem('/course/oska/admin');
        $this->title = _('Übersicht');
        $this->issues = OskaMatches::getIssues();
        $this->mentees_all = OskaMentees::countMentees();
        $this->mentees_without = sizeof(OskaMentees::findBySQL('has_tutor = 0', array()));

        if($this->mentees_all != 0) {
            $this->mentees_with_percentage = ($this->mentees_all - $this->mentees_without) / $this->mentees_all * 100;
            $this->mentees_without_percentage = $this->mentees_without / $this->mentees_all * 100;
        }

        $this->oska_all = OskaMentors::countMentors();
        $this->oska_counters = OskaMentors::getCounters();
        $this->pie_bg_colors = array('#28497c', '#7e92b0', '#d4dbe5', '#899ab9', '#b8c2d5', '#e7ebf1', '#536d96', '#a9b6cb', '#a1aec7', '#d0d7e3');
    }

    public function matches_action($page = 1, $fach_selection = null)
    {
        $fach_filter = $fach_selection;
        $fach_selection = ($fach_selection !== '0') ? $fach_selection : null;

        Navigation::activateItem('/course/oska/matches');
        $this->title            = _('Matches');
        $this->fächer           = $this->getSubjects();
        $this->fach_filter      = $fach_filter;
        $this->entries_per_page = Config::get()->ENTRIES_PER_PAGE;
        $this->matches_counter  = OskaMatches::countMatches($fach_selection);
        $this->page             = (int) $page;

        $matches = OskaMatches::findAllMatches(
            ($this->page - 1) * $this->entries_per_page, // lower bound
            $this->entries_per_page, // elements per page
            $fach_selection);

        foreach ($matches as $index => &$match) {

            $match['mentee'] = User::find($match['mentee_id']);
            $match['mentor'] = User::find($match['mentor_id']);

            $mentee = OskaMentees::find($match['mentee_id']);
            $mentor = OskaMentors::find($match['mentor_id']);

            $preferred_studycourse = $mentee->getMenteePrefStudycourse();

            if ($fach_selection && $preferred_studycourse != $fach_selection) {
                unset($matches[$index]);
                $this->matches_counter--;
                continue;
            }

            foreach ($match['mentee']->studycourses as $i => $val) {
                if ($val->studycourse->fach_id == $preferred_studycourse) {
                    $matches[$index]['matched_course'] = $val->studycourse->name;
                }
            }

            $match['mentee_studycourses'] = implode(', ', $mentee->getMenteeStudycourses());
            $match['mentor_studycourses'] = implode(', ', $mentor->getMentorStudycourses());
        }

        $this->matches = $matches;
    }

    public function mentees_action($page = 1, $fach_selection= null, $has_oska = null)
    {
        $fach_filter = $fach_selection;
        $fach_selection = $fach_selection !== '0' ? $fach_selection : null;
        $has_oska = $has_oska == '' ? null : intval($has_oska);
        $search_term = Request::get('searchterm') ?: '';

        Navigation::activateItem('/course/oska/mentees');
        $this->title            = _('Mentees');
        $this->page             = (int) $page;
        $this->user             = $GLOBALS['user'];
        $this->entries_per_page = Config::get()->ENTRIES_PER_PAGE;
        $this->mentees          = [];
        $this->mentees_usernames = [];
        $this->mentees_counter  = OskaMentees::countMentees($search_term, $fach_selection, $has_oska);
        $this->fächer           = $this->getSubjects();
        $this->fach_filter      = $fach_filter;
        $this->has_oska_filter  = $has_oska;
        $this->search_term      = $search_term;

        $oska_mentees = OskaMentees::findAllMentees(
            ($this->page - 1) * $this->entries_per_page,
            $this->entries_per_page,
            $search_term,
            $fach_selection,
            $has_oska
        );

        foreach($oska_mentees as $mentee) {
            $user = User::find($mentee['user_id']);

            if($user) {
                $fach = '';
                $len = count($user->studycourses);
                foreach ($user->studycourses as $index => $val) {
                    $fach .= $val->studycourse->name;
                    if ($index != $len -1) {
                        $fach .= ', ';
                    }
                }
                array_push($this->mentees_usernames, $user->username);
                array_push($this->mentees, array(
                    'user' => $user,
                    'preferences' => json_decode($mentee['preferences']),
                    'has_tutor' => boolval($mentee['has_tutor']),
                    'fach' => $fach
                    )
                );
            }
        }
        $sidebar = Sidebar::Get();

        $actions = $sidebar->addWidget(new ActionsWidget());
        $actions->addLink(
            _('Mentee hinzufügen'),
            $this->url_for('admin/add_mentee/'),
            Icon::create('add', 'clickable'),
            ['data-dialog' => 'size=auto']
        );

        $actions->addLink(
            _('Mentee-Liste exportieren'),
            $this->url_for('admin/export_mentees/'),
            Icon::create('export', 'clickable')
        );
        $actions->addLink(
            _('Nachricht an Mentees schreiben'),
            URLHelper::getURL('dispatch.php/messages/write', [
                'filter'          => 'send_sms_to_all',
                'emailrequest'    => 1,
                'rec_uname'       => $this->mentees_usernames,
                'default_subject' => _(''),
            ]),

            Icon::create('mail', 'clickable'),
            ['data-dialog' => '']
            );
    }

    public function mentors_action($page = 1, $fach_selection = null ,$mentee_count = null)
    {
        PageLayout::addStylesheet($this->plugin->getPluginURL() . '/css/oska.css?v=42');
        PageLayout::addScript($this->plugin->getPluginURL() . '/js/oska.js');

        $fach_filter = $fach_selection;
        $fach_selection = $fach_selection !== '0' ? $fach_selection : null;
        $mentee_count = $mentee_count != null ? intval($mentee_count) : null;
        $search_term = Request::get('searchterm') ?: '';

        Navigation::activateItem('/course/oska/mentors');
        $this->title             = _('Mentoren');
        $this->page              = (int) $page;
        $this->user              = $GLOBALS['user'];
        $this->entries_per_page  = Config::get()->ENTRIES_PER_PAGE;
        $this->mentors           = [];
        $this->mentors_usernames = [];
        $this->mentors_counter   = OskaMentors::countMentorsWithFilter($search_term, $fach_selection, $mentee_count);
        $this->fächer            = $this->getSubjects('mentors');
        $this->fach_filter       = $fach_filter;
        $this->mentee_count      = $mentee_count;
        $this->search_term       = $search_term;

        $oska_mentors = OskaMentors::findAllMentors(
            ($this->page - 1) * $this->entries_per_page,
            $this->entries_per_page,
            $search_term,
            $fach_selection
        );

        if (isset($this->mentee_count)) {
            $oska_mentors = array_filter(
                $oska_mentors, function($mentor) {
                    return intval($mentor['mentee_counter']) == $this->mentee_count;
                }
            );
        }

        foreach($oska_mentors as $mentor) {
            $user = User::find($mentor['user_id']);

            if($user) {
                $abilities = json_decode($mentor['abilities']);
                $fach = '';
                $fach_selected = '';
                $len = count($user->studycourses);
                if ($len > 0) {
                    foreach ($user->studycourses as $index => $val) {
                        $fach .= $val->studycourse->name;
                        if ($index != $len -1) {
                            $fach .= ', ';
                        }
                    }
                }
                foreach($abilities->studycourse as $i => $studycourse) {
                    $fach_selected .= Fach::find($studycourse)->name;
                    if ($i != count($abilities->studycourse) -1) {
                        $fach_selected .= ', ';
                    }
                }
                array_push($this->mentors_usernames, $user->username);
                array_push($this->mentors, array(
                    'user' => $user,
                    'abilities' => $abilities,
                    'mentee_counter' => intval($mentor['mentee_counter']),
                    'fach' => $fach,
                    'fach_selected' => $fach_selected
                    )
                );
            }
        }
        $sidebar = Sidebar::Get();

        $actions = $sidebar->addWidget(new ActionsWidget());
        $actions->addLink(
            _('Mentor-Liste exportieren'),
            $this->url_for('admin/export_mentors/'.($fach_selection != null ? $fach_selection : 0).'/'.$mentee_count),
            Icon::create('export', 'clickable')
        );
        $actions->addLink(
            _('Nachricht an Mentoren schreiben'),
            URLHelper::getURL('dispatch.php/messages/write', [
                'filter'          => 'send_sms_to_all',
                'emailrequest'    => 1,
                'rec_uname'       => $this->mentors_usernames,
                'default_subject' => _(''),
            ]),

            Icon::create('mail', 'clickable'),
            ['data-dialog' => '']
            );  
    }

    public function mentors_mentees_action($mentor_id)
    {
        Navigation::activateItem('/course/oska/mentors');
        $mentor = User::find($mentor_id);
        $this->title = $mentor->nachname . ", " . $mentor->vorname;
        $this->mentees = OskaMatches::getMentees($mentor_id);
    }

    public function mentees_filter_action()
    {
        $search_term = Request::get('search_term') ?: null;
        $fach_id = Request::get('fach_filter') ?: 0;
        $has_oska = Request::get('has_oska_filter') == '' ? null : Request::int('has_oska_filter');
        $this->redirect($this->url_for('admin/mentees/1/'
                                        . $fach_id . '/'
                                        . $has_oska,
                                        ['searchterm' => ($search_term ?: '')]));
    }

    public function fach_filter_mentor_action()
    {
        $search_term = Request::get('search_term') ?: null;
        $fach_id = Request::get('fach_filter') ?: 0;
        $mentee_count_filter = Request::get('mentee_count_filter') !== '' ? Request::int('mentee_count_filter') : null;
        $this->redirect($this->url_for('admin/mentors/1/'
                                        . $fach_id . '/'
                                        . $mentee_count_filter,
                                        ['searchterm' => ($search_term ?: '')]));
    }

    public function matches_filter_action()
    {
        $fach_id = Request::get('fach_filter') ?: 0;
        $this->redirect('admin/matches/1/' . $fach_id);
    }

    public function set_match_action()
    {
        $user_id = Request::get('mentee_id');
        $this->mentee = OskaMentees::find($user_id);
        $this->mentee_user = User::find($user_id);
        $this->mentee_avatar = Avatar::getAvatar($this->mentee->user_id);
        $this->mentee_preferences = json_decode($this->mentee->preferences);
        $this->mentee_studycourse_preference = Fach::find($this->mentee_preferences->studycourse)->name;
        $this->mentee_studycourses = '';

        $len = count($this->mentee_user->studycourses);
        foreach ($this->mentee_user->studycourses as $index => $val) {
            $this->mentee_studycourses .= $val->studycourse->name;
            if ($index != $len -1) { $this->mentee_studycourses .= ', ';}
        }

        $oska_mentors = OskaMentors::findBySQL('mentee_counter < ? ORDER BY mentee_counter', array('8'));
        $this->mentors_all = [];
        $this->mentors_have_studycourse = [];
        $this->mentors = [];

        foreach($oska_mentors as $oska_mentor) {
            $mentor = new \stdClass();
            $mentor->user = User::find($oska_mentor->user_id);
            $mentor->abilities = json_decode($oska_mentor->abilities);
            $mentor->teacher = $oska_mentor->teacher;
            $mentor->mentee_counter = $oska_mentor->mentee_counter;
            $mentor->studycourses = '';
            $mentor->studycourses_pref = '';
            $len = count($mentor->user->studycourses);
            $matching_studycourse = false;
            $has_studycourse = false;
            foreach ($mentor->user->studycourses as $index => $val) {
                if($val->fach_id == $this->mentee_preferences->studycourse){
                    $has_studycourse = true;
                }
                if(in_array($this->mentee_preferences->studycourse, (array)$mentor->abilities->studycourse)){
                    $matching_studycourse = true;
                }
                $mentor->studycourses .= $val->studycourse->name;
                if(in_array($val->studycourse->id, (array)$mentor->abilities->studycourse)) {
                    if($mentor->studycourses_pref != '') {
                        $mentor->studycourses_pref .= ', ';
                    }
                    $mentor->studycourses_pref .= $val->studycourse->name;
                }
                if ($index != $len -1) { $mentor->studycourses .= ', ';}
            }
            array_push($this->mentors_all, $mentor);

            if($has_studycourse){
                array_push($this->mentors_have_studycourse, $mentor);
            }

            if($matching_studycourse){
                array_push($this->mentors, $mentor);
            }
        }

        if(count($this->mentors) == 0) {
            $this->message_no_mentors_found = true;
            if(count($this->mentors_have_studycourse) != 0) {
                $this->mentors = $this->mentors_have_studycourse;
                $this->message_show_studycourse_mentors = true;
            } else {
                $this->mentors = $this->mentors_all;
                $this->message_show_all_mentors = true;
            }
        }

        shuffle($this->mentors);
        $this->mentors = array_slice($this->mentors, 0, 20);

        $this->search = new SQLSearch(
            "SELECT auth_user_md5.user_id, CONCAT(Nachname, ', ', Vorname, ' (',username, ')') ".
            "FROM auth_user_md5 " .
            "JOIN oska_mentors " .
            "ON oska_mentors.user_id = auth_user_md5.user_id " .
            "WHERE (CONCAT(auth_user_md5.Vorname, \" \", auth_user_md5.Nachname) LIKE :input " .
            "OR CONCAT(auth_user_md5.Nachname, \" \", auth_user_md5.Vorname) LIKE :input " .
            "OR auth_user_md5.username LIKE :input) " .
            "ORDER BY Vorname, Nachname", _("OSKA suchen"), "username");
    }

    public function store_match_action()
    {
        $mentee = OskaMentees::find(Request::get('mentee_id'));
        $mentor_id = Request::get('mentor_id');
        if (Request::get('mentor_id_search') != '') {
            $mentor_id = Request::get('mentor_id_search');
        }
        if ($mentor_id == '') {
            PageLayout::postError(_('Es wurde kein OSKA ausgewählt'));
        } else {
            $mentor = OskaMentors::find($mentor_id);

            $match = new OskaMatches();
            $match->mentee_id = $mentee->user_id;
            $match->mentor_id = $mentor->user_id;
            $match->store();

            $mentee->has_tutor = true;
            $mentee->store();

            $mentor->raiseCounter();
            $mentor->store();
        }

        $this->redirect('admin/mentees');
    }

    public function add_mentee_action()
    {
        $this->studycourses = StudyCourse::findBySQL("LENGTH(fach_id) != 32 ORDER BY name");
    }

    public function store_mentee_action()
    {
        $user_id = Request::get('user_id');
        if ($user_id == '') {
            PageLayout::postError(_('Es wurde kein Nutzer ausgewählt!'));
        } else {
            $user = User::find($user_id);
            $mentee = OskaMentees::find($user_id);
            if($mentee != null) {
                PageLayout::postInfo($user->getFullname() . _(' ist bereits Mentee'));
            } else {
                $mentor = OskaMentors::find($user_id);
                if($mentor != null) {
                    PageLayout::postInfo($user->getFullname() . _(' ist bereits Mentor'));
                } else {
                    if(count($user->studycourses) == 0) {
                        PageLayout::postError($user->getFullname() . _(' hat kein Studienfach'));
                    } else {
                        $studycourse = Request::option('studycourse');
                        $preferences = [
                            'studycourse'   => $studycourse,
                            'gender'        => Request::int('gender'),
                            'migration'     => Request::int('migration'),
                            'firstgen'      => Request::int('firstgen'),
                            'children'      => Request::int('children'),
                            'apprentice'    => Request::int('apprentice')
                        ];

                        $data = [
                            'user_id'       => $user->id,
                            'teacher'       => Request::int('lehramt'),
                            'has_tutor'     => 0,
                            'preferences'   => json_encode($preferences)
                        ];
                        OskaMentees::register($data);
                        PageLayout::postSuccess($user->getFullname() . _(' wurde als Mentee eingetragen'));
                    }
                }
            }
        }

        $this->redirect('admin/mentees');
    }

    public function export_mentees_action()
    {
        $data = [array(_('Vorname'), _('Nachname'), _('Benutzername'), _('E-Mail'), _('Studiengang'), _('präferiertes Fach'), _('hat OSKA'))];

        foreach(OskaMentees::findAllMentees() as $mentee){
            $user = User::find($mentee['user_id']);
            $preferences = json_decode($mentee['preferences']);
            $fach = '';
            $pref_fach = '';
            $len = count($user->studycourses);
            foreach ($user->studycourses as $index => $val) {
                $fach .= $val->studycourse->name;
                if ($index != $len -1) {
                    $fach .= '; ';
                }
                if( $val->studycourse->id == $preferences->studycourse) {
                    $pref_fach = $val->studycourse->name;
                    if ($index != $len -1) {
                        $pref_fach .= '; ';
                    }
                }
            }

            $mentee_data = array(
                $user->vorname,
                $user->nachname,
                $user->username,
                $user->email,
                $fach,
                $pref_fach,
                'hat einen OSKA' => $mentee['has_tutor']
            );
            array_push($data, $mentee_data);
        }

        $this->render_csv(
            $data,
            'Mentees.csv'
        );
    }

    public function export_mentors_action($fach_selection = null, $mentee_count = null)
    {
        $data = [array(_('Vorname'), _('Nachname'), _('Benutzername'), _('E-Mail'), _('Studiengang'), _('präferiertes Fach'), _('Anzahl Mentees'))];

        foreach(OskaMentors::findAllMentors() as $mentor){
            if ($mentee_count == '' || $mentor['mentee_counter'] == $mentee_count) {
                $user = User::find($mentor['user_id']);
                $preferences = json_decode($mentor['abilities']);
                $fach = '';
                $pref_fach = '';
                $len = count($user->studycourses);
                foreach ($user->studycourses as $index => $val) {
                    if (!$fach_selection || $val->studycourse->id == $fach_selection) {
                        $fach_chosen = true;
                    }
                    $fach .= $val->studycourse->name;
                    if ($index != $len -1) {
                        $fach .= '; ';
                    }
                }
                foreach($preferences->studycourse as $index => $studycourse) {
                    $pref_fach .= Fach::find($studycourse)->name;
                    if ($index != count($preferences->studycourse) -1) {
                        $pref_fach .= '; ';
                    }
                }
                if ($fach_chosen) {
                    $mentor_data = array(
                        $user->vorname, 
                        $user->nachname,
                        $user->username, 
                        $user->email, 
                        $fach, 
                        $pref_fach, 
                        intval($mentor['mentee_counter'])
                    );
                    array_push($data, $mentor_data);
                }
            }
        }

        $this->render_csv(
            $data,
            'Mentors.csv'
        );
    }

    public function remove_issue_action()
    {
        if(Request::get('mentor_id') != '' && Request::get('mentee_id') != '') {
            $match = OskaMatches::find([Request::option('mentor_id'), Request::option('mentee_id')]);
            $match['issue'] = false;
            $match->store();
        }

        $this->redirect('admin');
    }

    public function delete_match_action()
    {
        if(Request::get('mentor_id') != '' && Request::get('mentee_id') != '') {
            $mentor_id = Request::get('mentor_id');
            $mentee_id = Request::get('mentee_id');
            $match = OskaMatches::find([$mentor_id, $mentee_id]);
            $match->delete();

            $mentee = OskaMentees::find($mentee_id);
            $mentee->has_tutor = false;
            $mentee->store();

            $mentor = OskaMentors::find($mentor_id);
            $mentor->lowerCounter();
            $mentor->store();

        }

        $this->redirect('admin');
    }

    private function getSubjects($role = NULL)
    {
        if ($role == 'mentors') {
            $role_table = 'oska_mentors';
        } else {
            $role_table = 'oska_mentees';
        }

        $sql = "SELECT DISTINCT fach.fach_id, fach.name FROM $role_table JOIN user_studiengang " .
               "on $role_table.user_id = user_studiengang.user_id JOIN fach " .
               "on user_studiengang.fach_id = fach.fach_id join abschluss " .
               "on user_studiengang.abschluss_id = abschluss.abschluss_id " .
               "WHERE abschluss.abschluss_id IN ('08','12','14','61','62','65','91')" .
               "ORDER BY fach.name ASC";

        $statement = DBManager::get()->prepare($sql);
        $statement->execute($parameters);
        $subjects = $statement->fetchAll();

        return $subjects;
    }
}
