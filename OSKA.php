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
        global $perm;
        
        parent::__construct();

        $is_first_sem = true;
        $is_bachelor = true;
        
        // Show widget only to first semester Bachelor students
        // go through all subjects of the user and check if user is first semester and Bachelor student
        $studycourses = new SimpleCollection(UserStudyCourse::findByUser($GLOBALS['user']->id));
        
        foreach ($studycourses as $studycourse) {
            if ($studycourse->semester > 1) {
                $is_first_sem = false;
            }
            if ($studycourse->degree_name != "Bachelor") {
                $is_bachelor = false;
            }
        }
        
        // retrieve column of OSKA plugin on overview page to check for access permission later
        // and whether or not to display it
        $query = "SELECT col FROM widget_user where pluginid = ? AND range_id = ?";
            $statement = DBManager::get()->prepare($query);
            $statement->execute([$this->getPluginId(), $GLOBALS['user']->id]);
            $col = $statement->fetchColumn();
        
        // add OSKA plugin to widget_user in unavailable column so it is not displayed
        // if user is not first semester Bachelor student
        if (!$is_first_sem || !$is_bachelor || $perm->have_perm('tutor')) {

            // if OSKA plugin is already on unavailable column do nothing
            if ($col != 2) {
                
                // if OSKA plugin is already displayed, remove it from display
                if ($col !== FALSE) {
                    $query = "DELETE FROM widget_user WHERE pluginid = ? AND range_id = ?";
                    DBManager::get()->execute($query, [$this->getPluginId(), $GLOBALS['user']->id]);
                }
                // add plugin into widget_user to prevent it from being loaded and
                // from being added to available widgets
                $query = "INSERT INTO widget_user (`pluginid`, `position`, `range_id`, `col`) VALUES (?,?,?,?)";
                DBManager::get()->execute($query, [$this->getPluginId(), 0, $GLOBALS['user']->id, 2]);

                // refresh page to show change immediately
                header("Refresh:0");
                die();
            }
        } else {
            if ($col === FALSE) {
                // display widget on page under 
                $db = DBManager::get();

                // Push all entries in the column one position away
                $db->execute("UPDATE widget_user SET position = position + 1 WHERE range_id = ? AND col = ? AND position >= ?", 
                            [$GLOBALS['user']->id, 0, 2]);

                // Insert element
                $db->execute("INSERT INTO widget_user (`pluginid`, `position`, `range_id`, `col`) VALUES (?,?,?,?)", 
                            [$this->getPluginId(), 2, $GLOBALS['user']->id, 0]);

                // refresh page to show change immediately
                header("Refresh:0");
                die();
            }
        }
        
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
    
    public function isActivatableForContext(Range $context) {
        // hard code which course this plugin is activatable for via course ID
        if ($context->id == '3f28a0f6c986f45e434ea2433f53a936') {
            return true;
        }
        
        return false;
    }

}
