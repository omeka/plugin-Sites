<?php 
$request = Zend_Controller_Front::getInstance()->getRequest();
?>

<div class="field">
    <?php echo $this->formLabel('site_title', __('Search By Sites')); ?>
    <div class="inputs">
    <p class="explanation"><?php echo __('Omeka will find all sites similar to this keyword, and show items from those sites.'); ?></p>
    <?php echo $this->formText('site_title', @$_REQUEST['sites'], array('id' => 'sites-title-search')); ?>
    </div>
</div>