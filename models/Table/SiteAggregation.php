<?php

class Table_SiteAggregation extends Omeka_Db_Table
{
    public function getSelect()
    {
        //for site-admins on admin side, hide everything that they don't own
        $select = parent::getSelect();
        $user = current_user();
        $alias = $this->getTableAlias();
        if($user && ($user->role == 'site-admin') && is_admin_theme()) {
            $select->where($alias . '.owner_id = ?', $user->id);
        }
        return $select;
    }

    public function findItemsForSiteAggregation($siteAggregation, $params = array(), $limit = 6)
    {
        if(is_numeric($siteAggregation)) {
            $siteAggregationId = $siteAggregation;
        } else {
            $siteAggregationId = $siteAggregation->id;
        }
        $itemTable = $this->getDb()->getTable('Item');
        $select = $itemTable->getSelectForFindBy($params);
        $select->join(array('site_items'=>$this->_db->SiteItem), 'site_items.item_id = items.id', array());
        $select->join(array('sites' => $this->_db->Sites), 'site_items.site_id = sites.id', array());
        $select->where("sites.site_aggregation_id = ? ", $siteAggregationId);
        $select->limit($limit);
        return $itemTable->fetchObjects($select);
    }

}