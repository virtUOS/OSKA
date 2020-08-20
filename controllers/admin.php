<?php

class AdminController extends PluginController {
    
    function before_filter(&$action, &$args)
    {
        global $perm;
        
        parent::before_filter($action, $args);
        
        $perm->check('dozent');

        Navigation::activateItem('/course/oska');
        
    }
    
    public function index_action() {
        $this->title = _('Administration');
    }
    
}
