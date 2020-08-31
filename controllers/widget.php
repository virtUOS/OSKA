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

        $preferences = [
            'studycourse'   => Request::option('studycourse'),
            'gender'        => Request::int('gender'),
            'migrant'       => Request::int('migrant'),
            'first_gen'     => Request::int('first_generation'),
            'children'      => Request::int('children'),
            'apprentice'    => Request::int('apprentice')
        ];

        $data = [
            'user_id'       => $GLOBALS['user']->id,
            'teacher'       => Request::int('teacher'),
            'has_tutor'     => 0,
            'preferences'   => json_encode($preferences)
        ];
        
        OskaMentees::register($data);
        
        $this->redirect('../../dispatch.php/start');
        
    }
    
}
