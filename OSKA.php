<?php

/**
 * OSKA plugin class for Stud.IP
 *
 * @author   
 * @license  
 * @category Plugin
 **/


class OSKA extends StudIPPlugin implements StandardPlugin, PortalPlugin
{
    
    public function __construct()
    {
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
        // TODO only show to first semester Bachelor students + default visible on start page
        
        PageLayout::addStylesheet($this->getPluginURL() . '/css/oska.css');
        
        $template_path = $this->getPluginPath() . '/templates';
        $template_factory = new Flexi_TemplateFactory($template_path);
        
        $template = $template_factory->open('widget_index');

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

}
