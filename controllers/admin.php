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
        Navigation::activateItem('/course/oska/admin');
        $this->issues = OskaMatches::getIssues();
    }

    public function remove_issue_action()
    {
        global $perm;
        $this->cid = Context::getId();

        if(!$perm->have_studip_perm('dozent', $this->cid)) {
            throw new AccessDeniedException('Sie verfügen nicht über die notwendigen Rechte für diese Aktion');
        }

        if(Request::get('mentor_id') != '' && Request::get('mentee_id') != '') {
            $match = OskaMatches::find([Request::option('mentor_id'), Request::option('mentee_id')]);
            $match['issue'] = false;
            $match->store();
        }

        $this->redirect('admin');
    }

    public function delete_match_action()
    {
        global $perm;
        $this->cid = Context::getId();

        if(!$perm->have_studip_perm('dozent', $this->cid)) {
            throw new AccessDeniedException('Sie verfügen nicht über die notwendigen Rechte für diese Aktion');
        }

        if(Request::get('mentor_id') != '' && Request::get('mentee_id') != '') {
            $match = OskaMatches::find([Request::option('mentor_id'), Request::option('mentee_id')]);
            $match->delete();
        }

        $this->redirect('admin');
    }
    
}
