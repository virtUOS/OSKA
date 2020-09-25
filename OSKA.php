<?php

/**
 * OSKA plugin class for Stud.IP
 *
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @author    Viktoria Wiebe <vwiebe@uni-osnabrueck.de>
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

require_once 'models/OskaMentors.php';
require_once 'models/OskaMentees.php';
require_once 'models/OskaMatches.php';

class OSKA extends StudIPPlugin implements StandardPlugin, PortalPlugin
{
    public function __construct()
    {
        global $perm;

        parent::__construct();

        // set up translation domain
        bindtextdomain('OSKA', dirname(__FILE__) . '/locale');

        StudipAutoloader::addClassLookups([
            'OskaMatches' => __DIR__ . '/models/OskaMatches.php',
            'OskaMentees' => __DIR__ . '/models/OskaMentees.php',
            'OskaMentors' => __DIR__ . '/models/OskaMentors.php',
        ]);
    }

    /**
    * Returns the plugin name
    */
    public function getPluginName()
    {
        return 'OSKA';
    }

    /**
    * Returns the tab navigation for the OSKA plugin in the given course.
    *
    * @param $course_id the given course ID
    */
    public function getTabNavigation($course_id) 
    {
        global $perm;

        if ($perm->have_studip_perm('tutor', $course_id)) {
            $navigation = new Navigation($this->getPluginName(), PluginEngine::getURL('OSKA/admin/index'));
            $navigation->addSubNavigation('admin', new Navigation(_('Übersicht'), PluginEngine::getURL('OSKA/admin/index')));
            $navigation->addSubNavigation('matches', new Navigation(_('Matches'), PluginEngine::getURL('OSKA/admin/matches')));
            $navigation->addSubNavigation('mentees', new Navigation(_('Mentees'), PluginEngine::getURL('OSKA/admin/mentees')));
            $navigation->addSubNavigation('mentors', new Navigation(_('Mentors'), PluginEngine::getURL('OSKA/admin/mentors')));
        } else {
            $navigation = new Navigation($this->getPluginName(), PluginEngine::getURL('OSKA/mentoring/index'));
            $navigation->addSubNavigation('mentor_profile', new Navigation(_('Profil'), PluginEngine::getURL('OSKA/mentoring/index')));
            $navigation->addSubNavigation('mentee_list', new Navigation(_('Mentee Liste'), PluginEngine::getURL('OSKA/mentoring/mentee_list')));
        }

        return ['oska' => $navigation];
    }

    /**
    * Dispatches all actions.
    */
    public function perform($unconsumed_path)
    {
        parent::perform($unconsumed_path);
    }

    /**
     * Returns the portal widget template.
     */
    function getPortalTemplate()
    {
        global $perm;

        PageLayout::addStylesheet($this->getPluginURL() . '/css/oska.css');
        PageLayout::addScript($this->getPluginURL() . '/js/oska.js');

        $template_path = $this->getPluginPath() . '/templates';
        $template_factory = new Flexi_TemplateFactory($template_path);
        
        // if the mentee is already registered, show the searching template
        // if the mentee is also matched, show their tutor
        $mentee = OskaMentees::find($GLOBALS['user']->id);

        if ($mentee) {
            if ($mentee->hasTutor()) {
            
                $template = $template_factory->open('widget_mentor');
                
                $mentor = OskaMatches::getMentor($GLOBALS['user']->id)['mentor_id'];
                $template->avatar = Avatar::getAvatar($mentor);

                // mentor data
                $user = User::find($mentor);
                $template->mentor_name = $user->vorname . ' ' .$user->nachname;
                $template->mentor_desc = OskaMentors::getMentorDescription($mentor);

                // Fachbereiche / Institutes
                $study_institutes = [];

                if (count($user->institute_memberships) > 0 
                        && Visibility::verify('studying', $mentor)) {
                        
                    $study_institutes = $user->institute_memberships->filter(function ($a) {
                        return $a->inst_perms === 'user';
                    });
                }
            
                $template->study_institutes = $study_institutes;

            } else {
                $template = $template_factory->open('widget_searching');
            }
        } else {

            // Show widget only to first semester Bachelor students
            $show_info = false;
            $studycourses = new SimpleCollection(UserStudyCourse::findByUser($GLOBALS['user']->id));

            // go through all subjects of the user and check if user is first semester and Bachelor student
            foreach ($studycourses as $studycourse) {
                if ($studycourse->semester > 1 || strpos($studycourse->degree_name, "Bachelor") !== false) {
                    $show_info = true;
                }
            }

            // show info if user has no subject
            if(sizeof($studycourses) == 0) {
                $show_info = true;
            }

            // show info about OSKA if not first semester Bachelor student
            if ($show_info) {
                $template = $template_factory->open('widget_info');
            } else {
                // show OSKA widget otherwise
                $show_form = Request::option('show_form');

                if ($show_form) {
                    // show form if button was clicked
                    $template = $template_factory->open('widget_form');
                    $template->studycourses = $studycourses;
                } else {
                    // show oska info for first semester students otherwise
                    $template = $template_factory->open('widget_index');
                }
            }
        }
        $template->title = _('OSKA – Mein*e Mentor*in am Studienanfang');
        $template->oska_image_url = $this->getPluginURL() . '/images/OSKA.jpg';
        $template->oska_footer_image_url = $this->getPluginURL() . '/images/OSKA_footer.png';

        return $template;
    }

    /**
    * Returns the course summary page template.
    *
    * @param $course_id the given course ID
    */
    public function getInfoTemplate($course_id)
    {
        return NULL;
    }

    /**
    * Returns the icon navigation object.
    *
    * @param $course_id the given course ID
    * @param $last_visit point in time of the user's last visit
    * @param $user_id the ID of the given user
    */
    public function getIconNavigation($course_id, $last_visit, $user_id)
    {
        return NULL;
    }

    public function isActivatableForContext(Range $context)
    {
        return $GLOBALS['perm']->have_perm('root');
    }

}
