<?php

class MentoringController extends PluginController {
    
    function before_filter(&$action, &$args)
    {
        global $perm;
        
        parent::before_filter($action, $args);
        
        $perm->check('tutor');
        
        Navigation::activateItem('/course/oska');
        
    }
    
    public function index_action() {
        $this->title = _('Profil');
    }
    
    public function mentee_list_action() {
        $this->title = _('Mentee Liste');
    }
    
}
