<?php

class Site extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{
    public $id;
    public $site_aggregation_id;
    public $owner_id;
    public $url;
    public $super_email;
    public $admin_email;
    public $admin_name;
    public $content_summary;
    public $affiliation;
    public $join_reason;
    public $title;
    public $description;
    public $api_key;
    public $date_applied;
    public $date_approved;
    public $last_import;
    public $copyright_info;
    public $author_info;
    public $omeka_version;
    public $commons_settings;
    public $public;
    public $featured;


    protected $_related = array('SiteOwner'=>'getSiteOwner');

    protected function _initializeMixins()
    {
        $this->_mixins[] = new Mixin_Owner($this);
        $this->_mixins[] = new Mixin_Search($this);
        $this->_mixins[] = new Mixin_PublicFeatured($this);
    }

    protected function beforeSave()
    {
        if(!is_array($this->commons_settings)) {
            $this->commons_settings = array();
        }
        $this->commons_settings = serialize($this->commons_settings);
    }

    protected function afterSave($args)
    {
        $this->setSearchTextTitle($this->title);
        $this->addSearchText($this->title);
        $this->addSearchText($this->description);
        $this->addSearchText($this->content_summary);
        if(!is_null($this->date_approved) && !file_exists(SITES_PLUGIN_DIR . '/views/shared/images/' . $this->id)) {
            mkdir(SITES_PLUGIN_DIR . '/views/shared/images/' . $this->id, 0755);
        }
    }

    public function getSiteAggregation()
    {
        if($this->site_aggregation_id) {
            return  $this->getTable('SiteAggregation')->find($this->site_aggregation_id);
        }
        return false;
    }

    public function getSiteContextCollections($params = array())
    {
        if(!isset($params['site_id'])) {
            $params['site_id'] = $this->id;
        }

        return $this->getTable('SiteContext_Collection')->findBy($params);
    }

    public function getProperty($property)
    {
        switch ($property) {
            case 'total_items':
                return $this->totalItems();
            break;

        }
        return parent::getProperty($property);
    }

    public function totalItems()
    {
        return $this->_db->getTable('SiteItem')->count(array('site_id'=>$this->id));
    }

    public function getResourceId()
    {
        return 'Sites_Index';
    }
}