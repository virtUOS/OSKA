<?php

/**
 * OSKA plugin class for Stud.IP
 *
 * @author   
 * @license  
 * @category Plugin
 **/

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

        // only tutors and lecturers have access to the OSKA administration
        $perm->check('tutor');
        
        if ($perm->have_perm('dozent')) {
            $navigation = new Navigation($this->getPluginName(), PluginEngine::getURL('OSKA/admin/index'));
            $navigation->addSubNavigation('admin', new Navigation(_('Admin'), PluginEngine::getURL('OSKA/admin/index')));
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

        $template_path = $this->getPluginPath() . '/templates';
        $template_factory = new Flexi_TemplateFactory($template_path);

        // Show widget only to first semester Bachelor students
        $is_first_sem = true;
        $is_bachelor = true;
        $studycourses = new SimpleCollection(UserStudyCourse::findByUser($GLOBALS['user']->id));

        // go through all subjects of the user and check if user is first semester and Bachelor student
        foreach ($studycourses as $studycourse) {
            if ($studycourse->semester > 1) {
                $is_first_sem = false;
            }
            if ($studycourse->degree_name != "Bachelor") {
                $is_bachelor = false;
            }
        }

        // show info about OSKA if not first semester Bachelor student
        if (!$is_first_sem || !$is_bachelor || $perm->have_perm('tutor')) {
            $template = $template_factory->open('widget_info');
            $template->title = _('Mein OSKA');
        } else {
            // show OSKA widget otherwise
            $template = $template_factory->open('widget_index');
            $template->title = _('Mein OSKA');
        }

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

    public function isActivatableForContext(Range $context) {
        // hard code which course this plugin is activatable for via course ID
        if ($context->id == '3f28a0f6c986f45e434ea2433f53a936') {
            return true;
        }

        return false;
    }

}
