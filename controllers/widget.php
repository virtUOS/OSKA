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
 */

class WidgetController extends PluginController {

    function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
    }

    /**
    * Saves data about the mentee into the oska database and redirects to start page.
    */
    public function add_mentee_action()
    {
        CSRFProtection::verifyUnsafeRequest();

        $preferences = [
            'studycourse'   => Request::option('studycourse'),
            'gender'        => Request::int('gender'),
            'migration'       => Request::int('migration'),
            'firstgen'     => Request::int('firstgen'),
            'children'      => Request::int('children'),
            'apprentice'    => Request::int('apprentice')
        ];

        $data = [
            'user_id'       => $GLOBALS['user']->id,
            'teacher'       => Request::int('lehramt'),
            'has_tutor'     => 0,
            'preferences'   => json_encode($preferences)
        ];

        OskaMentees::register($data);

        $this->redirect('../../dispatch.php/start');
    }
}
