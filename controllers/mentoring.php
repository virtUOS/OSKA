<?php

class MentoringController extends PluginController {

    function before_filter(&$action, &$args)
    {
        global $perm;

        parent::before_filter($action, $args);

        $perm->check('tutor');
    }

    public function index_action()
    {
        global $user;

        $this->title = _('Profil');
        Navigation::activateItem('/course/oska/mentor_profile');

        $this->gender = $user->geschlecht;

        $mentor = OskaMentors::find($user->user_id);

        if($mentor == null) {
            $this->mentor->lehramt = 0;
            $this->mentor->firstgen = 2;
            $this->mentor->children = 2;
            $this->mentor->apprentice = 2;
            $this->mentor->migration = 2;
            $this->default_data = true;
        } else {
            $this->mentor = $mentor->getProfile();
            $this->default_data = false;
        }
    }

    public function store_profile_action()
    {
        global $perm, $user;
        $this->cid = Context::getId();

        if(!$perm->have_studip_perm('tutor', $this->cid)) {
            throw new AccessDeniedException('Sie verfügen nicht über die notwendigen Rechte für diese Aktion');
        }

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
                    $mentor->setMentorAbilities('lehramt_detail',  Request::option('lehramt_detail'));
                } else {
                    $mentor->setMentorAbilities('lehramt_detail', '2');
                }
            } else {
                $mentor->setMentorAbilities('lehramt_detail', '-1');
            }
        } else {
            $mentor->teacher = 0;
            $mentor->setMentorAbilities('lehramt_detail', '-1');
        }
        if(in_array(Request::option('firstgen'), ['0', '1', '2'])) {
            $mentor->setMentorAbilities('firstgen',  Request::option('firstgen'));
        } else {
            $mentor->setMentorAbilities('firstgen', '2');
        }
        if(in_array(Request::option('children'), ['0', '1', '2'])) {
            $mentor->setMentorAbilities('children',  Request::option('children'));
        } else {
            $mentor->setMentorAbilities('children', '2');
        }
        if(in_array(Request::option('apprentice'), ['0', '1', '2'])) {
            $mentor->setMentorAbilities('apprentice',  Request::option('apprentice'));
        } else {
            $mentor->setMentorAbilities('apprentice', '2');
        }
        if(in_array(Request::option('migration'), ['0', '1', '2'])) {
            $mentor->setMentorAbilities('migration',  Request::option('migration'));
        } else {
            $mentor->setMentorAbilities('migration', '2');
        }

        $mentor->store();
        $this->redirect('mentoring/index');
    }

    public function mentee_list_action()
    {
        global $user;

        $this->title = _('Mentee Liste');
        Navigation::activateItem('/course/oska/mentee_list');

        $this->mentor = $user;
        $this->subject = 'Nachricht von deinem OSKA';
        $this->mentees = OskaMatches::getMentees($user->id);
    }

    public function support_action()
    {
        global $perm;
        $this->cid = Context::getId();

        if(!$perm->have_studip_perm('tutor', $this->cid)) {
            throw new AccessDeniedException('Sie verfügen nicht über die notwendigen Rechte für diese Aktion');
        }

        if(Request::get('mentor_name') != '' && Request::get('mentee_name') != '') {
            $mentor = User::findByUsername(Request::option('mentor_name'));
            $mentee = User::findByUsername(Request::option('mentee_name'));
            $match = OskaMatches::find([$mentor->user_id, $mentee->user_id]);
            $match['issue'] = true;
            $match->store();
        }

        $this->redirect('mentoring/mentee_list');
    }
}
