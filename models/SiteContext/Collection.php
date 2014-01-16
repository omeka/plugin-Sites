<?php

class SiteContext_Collection extends SiteContext
{

    protected function _initializeMixins()
    {
        $this->_mixins[] = new Mixin_Search($this);
    }

    protected function afterSave()
    {
        $this->setSearchTextTitle($this->title);
        $this->addSearchText($this->title);
        $this->addSearchText($this->description);
    }
}