<?php

class MentoringController extends PluginController {
    
    function before_filter(&$action, &$args)
    {
        global $perm;

        parent::before_filter($action, $args);

        $perm->check('tutor');
    }
    
    public function index_action() {
        $this->title = _('Profil');
        Navigation::activateItem('/course/oska/mentor_profile');
    }
    
    public function mentee_list_action() {
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
