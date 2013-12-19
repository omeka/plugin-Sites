<?php

class Table_SiteAggregation extends Omeka_Db_Table
{
    public function getSelect()
    {
        $select = parent::getSelect();
        $user = current_user();
        if($user && ($user->role == 'site-admin') && is_admin_theme()) {
            $select->where('owner_id = ?', $user->id);
        }
        return $select;
    }

}