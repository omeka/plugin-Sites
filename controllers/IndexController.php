<?php

class Sites_IndexController extends Omeka_Controller_AbstractActionController
{

    protected $_browseRecordsPerPage = 10;
    
    public function init()
    {
        $this->_helper->db->setDefaultModelName('Site');
    }
    
    public function batchApproveAction()
    {
        foreach($_POST['sites'] as $siteId) {
            $site = $this->_helper->db->getTable()->find($siteId);
            $this->approveSite($site, false);
        }
        $this->redirect('sites/index');    
    }
    
    public function editAction()
    {
        $site = $this->_helper->db->findById();
        $this->view->site = $site;
        
        if(!empty($_POST)) {
            $site->featured = $_POST['featured'];
            if(is_null($site->date_approved) && $_POST['approved'] == 1) {
                $this->approveSite($site, false);
            }
            if(!is_null($site->date_approved) && $_POST['approved'] == 0) {
                $site->date_approved = null;
            }
            $site->save();
            $this->_redirectAfterEdit($site);
        }
    }
    
    public function aggregationAction()
    {
        $siteId = $this->getParam('id');
        $site = $this->_helper->db->getTable()->findById($siteId);
        $aggregation = $site->getSiteAggregation();
        $sites = $aggregation->getSites();
        $this->view->site_aggregations = $aggregation;
        $this->sites = $sites;
    }
    
    public function approveAction()
    {
        $db = $this->_helper->db;
        //$db = get_db();
        $id = $this->getParam('id');
        if($id) {
            $site = $db->getTable('Site')->find($id);
            $this->approveSite($site);
        }
    }


    public function sendApprovalEmail($site)
    {
        $to = $site->admin_email;
        $from = get_option('administrator_email');
        $subject = "Omeka Commons participation approved!";
        $body = "Thank you for participating in the Omeka Commons. blah blah blah
You will need to enter your Omeka Commons API key into the configuration form
of the Commons plugin you installed on your Omeka site.

Copy and paste the API key into the API key input on the form and save the configuration.
You will then be able to send individual items and entire collections to be preserved in the Commons.
When you do so, some basic information about your items, collections, and exhibits will be
available in the commons to help others discover your material and incorporate it into their research
and interests.

API key: " . $site->api_key;

        $mail = new Zend_Mail();
        $mail->setBodyText($body);
        $mail->setFrom($from, "Omeka Commons");
        $mail->addTo($to, $site->title . " Administrator");
        $mail->setSubject($subject);
        $mail->addHeader('X-Mailer', 'PHP/' . phpversion());
        $mail->send();
    }

    
    public function showAction()
    {
        $db = $this->_helper->db;
        $id = $this->getRequest()->getParam('id');
        $site = $this->_helper->db->getTable('Site')->find($id);
        $items = $this->_helper->db->getTable('SiteItem')->findItemsBy(array('site_id' => $id), get_option('per_page_public'));
        $collections = $this->_helper->db->getTable('SiteContext_Collection')->findBy(array('site_id'=>$id));
        $aggregation = $this->_helper->db->getTable('SiteAggregation')->find($site->site_aggregation_id);
    
    
        $params = array(
                'subject_record_type'=>'Site',
                'subject_id' => $site->id,
                'object_record_type' => 'Tag'
        );
    
        $tags = $this->_helper->db->getTable('RecordRelationsRelation')->findObjectRecordsByParams($params);
        $this->view->site = $site;
        $this->view->items = $items;
        $this->view->site_context_collections = $collections;
        $this->view->tags = $tags;
        $this->view->aggregation = $aggregation;
    }    
    
    protected function _redirectAfterEdit($site)
    {
        $this->_helper->redirector('');
    }
    
    
    protected function approveSite($site, $ajax = true)
    {
        $site->date_approved = Zend_Date::now()->toString('yyyy-MM-dd HH:mm:ss');
        $site->save();
        if(!file_exists(SITES_PLUGIN_DIR . '/views/shared/images/' . $site->id)) {
            mkdir(SITES_PLUGIN_DIR . '/views/shared/images/' . $site->id, 0755);
        }
        $responseArray = array('id' => $site->id, 'date_approved'=>$site->date_approved);
        try {
            $this->sendApprovalEmail($site);
        } catch(Exception $e) {
            _log($e);
        }
        if($ajax) {
            $this->_helper->json(json_encode($responseArray));
        } else {
            $this->_redirectAfterEdit($site);
        }    
    }    
}