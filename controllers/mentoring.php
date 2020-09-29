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

class MentoringController extends PluginController {

    function before_filter(&$action, &$args)
    {
        global $perm;
        parent::before_filter($action, $args);

        if(!$perm->have_studip_perm('autor', Context::getId())) {
            throw new AccessDeniedException('Sie verfügen nicht über die notwendigen Rechte für diese Aktion');
        }
    }

    public function index_action()
    {
        global $user;
        
        PageLayout::addStylesheet($this->plugin->getPluginURL() . '/css/oska.css');

        $this->title = _('Profil');
        Navigation::activateItem('/course/oska/mentor_profile');

        $this->gender = $user->geschlecht;

        $mentor = OskaMentors::find($user->user_id);
        $this->studycourses = new SimpleCollection(UserStudyCourse::findByUser($user->user_id));

        if($mentor == null) {
            $this->mentor->lehramt = 0;
            $this->mentor->firstgen = 2;
            $this->mentor->children = 2;
            $this->mentor->apprentice = 2;
            $this->mentor->migration = 2;
            $this->mentor->description = '';
            $this->default_data = true;
        } else {
            $this->mentor = $mentor->getProfile();
            $this->default_data = false;
        }
    }

    public function store_profile_action()
    {
        global $user;

        if (!Request::optionArray('studycourse')) {
            PageLayout::postError(_('Ihre Daten wurden nicht gespeichert: Bitte wählen Sie mindestens ein Studienfach aus.'));
            $this->redirect('mentoring/index');
        } else {

            $mentor = OskaMentors::find($user->user_id);

            if($mentor == null)  {
                $mentor = new OskaMentors();
                $mentor->user_id = $user->user_id;
                $mentor->abilities =  json_encode(array());
            }

            if(in_array(Request::option('lehramt'), ['0', '1'])) {
                $mentor->teacher = Request::option('lehramt');
                if (Request::option('lehramt') == '1') {
                    if(in_array(Request::option('lehramt_detail'), ['0', '1', '2'])) {
                        $teacher_detail = Request::option('lehramt_detail');
                    } else {
                        $teacher_detail = '2';
                    }
                } else {
                    $teacher_detail = '-1';
                }
            } else {
                $mentor->teacher = 0;
                $teacher_detail = '-1';
            }

            $abilities = [
                'migration'         => Request::int('migration'),
                'firstgen'          => Request::int('firstgen'),
                'children'          => Request::int('children'),
                'apprentice'        => Request::int('apprentice'),
                'lehramt_detail'    => $teacher_detail,
                'studycourse'       => Request::optionArray('studycourse')
            ];
            $mentor->abilities = json_encode($abilities);

            $mentor->description = \Studip\Markup::purifyHtml(Request::get('description'));

            $mentor->store();
            $this->redirect('mentoring/index');
        }
    }

    public function mentee_list_action()
    {
        global $user;

        $this->title = _('Mentee Liste');
        Navigation::activateItem('/course/oska/mentee_list');

        $this->mentor = $user;
        $this->subject = _('Nachricht von deinem OSKA');
        $this->mentees = OskaMatches::getMentees($user->id);

        $sidebar = Sidebar::Get();

        $actions = $sidebar->addWidget(new ActionsWidget());
        if (!$this->has_studygroup()) {
            $actions->addLink(
                _('Studiengruppe anlegen'),
                $this->url_for('mentoring/create_studygroup/'),
                Icon::create('studygroup', 'clickable')
            );
        } else {
            $actions->addLink(
                _('zur Studiengruppe'),
                URLHelper::getURL('dispatch.php/course/overview', ['cid' => $this->get_studygroup()]),
                Icon::create('studygroup', 'clickable')
            );
        }
    }

    public function create_studygroup_action()
    {
        global $user;

        $mentees = OskaMatches::getMentees($user->user_id);

        $studygroup = new Seminar();
        $studygroup->setId($studygroup->getNewId());
        $studygroup->status = studygroup_sem_types()[0];
        $studygroup->name = 'OSKA';
        $studygroup->read_level  = 1;
        $studygroup->write_level = 1;
        $studygroup->institut_id = Config::Get()->STUDYGROUP_DEFAULT_INST;
        $studygroup->visible = 1;
        $current_semester = SemesterData::getSemesterDataByDate(time());
        $studygroup->semester_start_time = $current_semester['beginn'];
        $studygroup->duration_time = -1;
        $studygroup->store();

        $course_mentor = new CourseMember();
        $course_mentor->user_id = $user->user_id;
        $course_mentor->seminar_id = $studygroup->seminar_id;
        $course_mentor->status = 'dozent';
        $course_mentor->store();

        foreach($mentees as $mentee){
            StudygroupModel::inviteMember($mentee['user_id'] ,$studygroup->seminar_id);
        }

        $studygroup_url = URLHelper::getURL('dispatch.php/course/overview', ['cid' => $studygroup->seminar_id]);
        $this->redirect($studygroup_url);
    }

    public function support_action()
    {
        if(Request::get('mentor_name') != '' && Request::get('mentee_name') != '') {
            $mentor = User::findByUsername(Request::option('mentor_name'));
            $mentee = User::findByUsername(Request::option('mentee_name'));
            $match = OskaMatches::find([$mentor->user_id, $mentee->user_id]);
            $match['issue'] = true;
            $match->store();
        }

        $this->redirect('mentoring/mentee_list');
    }

    private function has_studygroup()
    {
        if ($this->get_studygroup() != null){
            return true;
        }
        return false;
    }

    private function get_studygroup()
    {
        global $user;

        $sql = "
            SELECT 
                seminare.Seminar_id 
            FROM 
                seminare 
            JOIN 
                seminar_user 
            ON 
                seminare.Seminar_id = seminar_user.Seminar_id 
            WHERE 
                seminare.status = " . studygroup_sem_types()[0] ."
            AND 
                seminar_user.user_id = '". $user->user_id ."' 
            AND 
                seminar_user.status = 'dozent'
            AND 
                seminare.Name = 'OSKA'
            ";
        $statement = DBManager::get()->prepare($sql);
        $statement->execute();
        $result = $statement->fetch();

        return $result['Seminar_id'];
    }
}
