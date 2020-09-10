<?php

class AdminController extends PluginController {
    
    function before_filter(&$action, &$args)
    {
        global $perm;

        parent::before_filter($action, $args);

        $perm->check('dozent');

        Navigation::activateItem('/course/oska');
    }

    public function index_action()
    {
        PageLayout::addStylesheet($this->plugin->getPluginURL() . '/css/oska.css');

        $this->title = _('Übersicht');
        Navigation::activateItem('/course/oska/admin');
        $this->issues = OskaMatches::getIssues();

        $this->mentees_all = OskaMentees::countMentees(null);
        $this->mentees_without = sizeof(OskaMentees::findBySQL('has_tutor = 0', array()));

        if($this->mentees_all != 0) {
            $this->mentees_with_percentage = ($this->mentees_all - $this->mentees_without) / $this->mentees_all * 100;
            $this->mentees_without_percentage = $this->mentees_without / $this->mentees_all * 100;
        }

        $this->oska_all = OskaMentors::countMentors();
        $this->oska_counters = OskaMentors::getCounters();

        $this->pie_bg_colors = array('#28497c', '#7e92b0', '#d4dbe5', '#899ab9', '#b8c2d5', '#e7ebf1', '#536d96', '#a9b6cb', '#a1aec7', '#d0d7e3');
    }

    public function matches_action()
    {
        $this->title = _('Matches');
        Navigation::activateItem('/course/oska/matches');

        $this->matches = OskaMatches::findAllMaches();
    }

    public function mentees_action($page = 1, $fach_selection = null)
    {
        Navigation::activateItem('/course/oska/mentees');
        $this->title            = _('Mentees');
        $this->page             = (int) $page;
        $this->user             = $GLOBALS['user'];
        $this->entries_per_page = Config::get()->ENTRIES_PER_PAGE;
        $this->mentees          = [];
        $this->mentees_usernames = [];
        $this->mentees_counter  = OskaMentees::countMentees($fach_selection);
        $this->fächer           = $this->getFächer();
        $this->fach_filter      = $fach_selection;

        $oska_mentees = OskaMentees::findAllMentees(
            ($this->page - 1) * $this->entries_per_page, // lower bound
            $this->entries_per_page, // elements per page
            $fach_selection //fach filter
        );

        foreach($oska_mentees as $mentee){
            $user = User::find($mentee['user_id']);
            $fach = '';
            $len = count($user->studycourses);
            foreach ($user->studycourses as $index => $val) {
                $fach .= $val->studycourse->name;
                if ($index != $len -1) {
                    $fach .= ', ';        if($elements_per_page != null){
                        $sql .= " LIMIT ". $lower_bound. ', '. $elements_per_page;
                    }
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
        $sidebar = Sidebar::Get();

        $actions = $sidebar->addWidget(new ActionsWidget());
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

    public function fach_filter_action()
    {
        $fach_id = Request::get('fach_filter');

        $this->redirect('admin/mentees/1/'.$fach_id);
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
        $this->mentors_alt = [];
        $this->mentors = [];

        foreach($oska_mentors as $oska_mentor) {
            $mentor = new \stdClass();
            $mentor->user = User::find($oska_mentor->user_id);
            $mentor->studycourses = '';
            $len = count($mentor->user->studycourses);
            $matching_studycourse = false;
            foreach ($mentor->user->studycourses as $index => $val) {
                if($val->fach_id == $this->mentee_preferences->studycourse){
                    $matching_studycourse = true;
                }
                $mentor->studycourses .= $val->studycourse->name;
                if ($index != $len -1) { $mentor->studycourses .= ', ';}
            }
            $mentor->abilities = json_decode($oska_mentor->abilities);
            $mentor->teacher = $oska_mentor->teacher;
            $mentor->mentee_counter = $oska_mentor->mentee_counter;

            array_push($this->mentors_alt, $mentor);

            if($matching_studycourse){
                array_push($this->mentors, $mentor);
            }
        }

        if(count($this->mentors) == 0) {
            $this->mentors = $this->mentors_alt;
        }

        shuffle($this->mentors);
        $this->mentors = array_slice($this->mentors, 0, 20);
    }

    public function store_match_action()
    {
        $mentee = OskaMentees::find(Request::get('mentee_id'));
        $mentor = OskaMentors::find(Request::get('mentor_id'));

        $match = new OskaMatches();
        $match->mentee_id = $mentee->user_id;
        $match->mentor_id = $mentor->user_id;
        $match->store();

        $mentee->has_tutor = true;
        $mentee->store();

        $mentor->raiseCounter();
        $mentor->store();

        $this->redirect('admin/mentees');
    }

    public function export_mentees_action()
    {
        $this->mentees = [];

        $f = fopen('php://output', 'w');
        $csv_header = array(_('Vorname'), _('Nachname'), _('Studiengang'), 'OSKA');
        fputcsv($f, $csv_header, ',');

        foreach(OskaMentees::findAllMentees() as $mentee){
            $user = User::find($mentee['user_id']);
            $fach = '';
            $len = count($user->studycourses);
            foreach ($user->studycourses as $index => $val) {
                $fach .= $val->studycourse->name;
                if ($index != $len -1) {
                    $fach .= ', ';        if($elements_per_page != null){
                        $sql .= " LIMIT ". $lower_bound. ', '. $elements_per_page;
                    }
                }
            }
            $line = array($user->vorname, $user->nachname, $fach, boolval($mentee['has_tutor']));
            fputcsv($f, $line, ',');
        }

        $filename = 'Mentees';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'.csv";');
        fpassthru($f);
        exit();
    }

    public function remove_issue_action()
    {
        global $perm;
        $this->cid = Context::getId();

        if(!$perm->have_studip_perm('dozent', $this->cid)) {
            throw new AccessDeniedException('Sie verfügen nicht über die notwendigen Rechte für diese Aktion');
        }

        if(Request::get('mentor_id') != '' && Request::get('mentee_id') != '') {
            $match = OskaMatches::find([Request::option('mentor_id'), Request::option('mentee_id')]);
            $match['issue'] = false;
            $match->store();
        }

        $this->redirect('admin');
    }

    public function delete_match_action()
    {
        global $perm;
        $this->cid = Context::getId();

        if(!$perm->have_studip_perm('dozent', $this->cid)) {
            throw new AccessDeniedException('Sie verfügen nicht über die notwendigen Rechte für diese Aktion');
        }

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

    private function getFächer()
    {
        $sql = "SELECT fach.fach_id, fach.name FROM oska_mentees JOIN user_studiengang on oska_mentees.user_id = user_studiengang.user_id JOIN fach on user_studiengang.fach_id = fach.fach_id join abschluss on user_studiengang.abschluss_id = abschluss.abschluss_id WHERE abschluss.name LIKE '%bachelor%'";

        $statement = DBManager::get()->prepare($sql);
        $statement->execute($parameters);
        $fächer = $statement->fetchAll();

        return $fächer;
    }
}
