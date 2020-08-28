<?php

class WidgetController extends PluginController {
    
    function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
    }
    
    /**
    * Saves data about the mentee into the oska database and redirects to start page.
    */
    public function add_mentee_action() {
    
        CSRFProtection::verifyUnsafeRequest();

        $data = [
            'studycourse'   => Request::option('studycourse'),
            'teacher'       => Request::int('teacher'),
            'gender'        => Request::int('gender'),
            'migrant'       => Request::int('migrant'),
            'first_gen'     => Request::int('first_generation'),
            'children'      => Request::int('children'),
            'apprentice'    => Request::int('apprentice')
        ];
        
        OskaMentees::register($user_id, $data);
        
        StudipController::redirect('../../dispatch.php/start');
        
    }
    
}
