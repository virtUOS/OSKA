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
                $template->mentor_name = $user->username;
                $template->mentor_desc = OskaMentors::getMentorDescription($mentor);

                // Fachbereiche / Institutes
                $study_institutes = [];
                if ($user->perms !== 'dozent') {
                    if (count($user->institute_memberships) > 0 
                            && Visibility::verify('studying', $mentor)) {
                            
                        $study_institutes = $user->institute_memberships->filter(function ($a) {
                            return $a->inst_perms === 'user';
                        });
                        $template->study_institutes = $study_institutes;
                    }
                }

            } else {
                $template = $template_factory->open('widget_searching');
            }
        } else {

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
        
        $template->title = _('Mein OSKA');

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
