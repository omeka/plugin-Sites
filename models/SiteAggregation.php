<?php

/**
 * SiteAggregation allow aggregation of Sites under one controlling authority, for example when one institution
 * is contributing data from many different Omeka sites.
 *
 * @author patrickmj
 *
 */

class SiteAggregation extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{
    public $id;
    public $name;
    public $description;
    public $owner_id;

    protected function _initializeMixins()
    {
        $this->_mixins[] = new Mixin_Owner($this);
        $this->_mixins[] = new Mixin_Search($this);
    }

    public function getSites()
    {
        if($this->exists()) {
            return $this->getTable('Site')->findBy(array('site_aggregation_id'=>$this->id));
        }
        return array();
    }

    public function getRecordUrl($action = 'show')
    {
        return url("sites/site-aggregation/$action/id/{$this->id}");
    }

    public function getResourceId()
    {
        return 'Sites_SiteAggregation';
    }
}