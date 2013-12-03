<?php 
$request = Zend_Controller_Front::getInstance()->getRequest();
?>

<div class="field">
    <?php echo $this->formLabel('site_title', __('Search By Sites')); ?>
    <div class="inputs">
    <?php echo $this->formText('site_title', @$_REQUEST['sites'], array('id' => 'sites-title-search')); ?>
    </div>
</div>