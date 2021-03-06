<?php

$title = strip_formatting(metadata('site', 'title'));
echo head(array('title'=>$title));
echo flash();
?>
<?php $columns = get_db()->getTable('Site')->getColumns();?>

<form method="post">
<section class="seven columns alpha">
<?php echo sites_site_logo($site); ?>
<?php foreach($columns as $column): ?>
<div class="element" id='<?php echo $column; ?>'>
        <div class="field two columns alpha">
            <label><?php echo Inflector::humanize($column); ?></label>
        </div>
        <div class="element-text five columns omega"><?php echo metadata('site', $column);?></div>
</div>
<?php endforeach; ?>
</section>

<section class="three columns omega">
    <div id="save" class="panel">
        <?php if(is_allowed('Sites_Index', 'approve')): ?>
        <?php echo $this->formSubmit('submit', __('Save Changes'), array('id'=>'save-changes', 'class'=>'submit big green button')); ?>
        <?php endif; ?>
        <?php
            set_theme_base_url('public');
            $url = record_url($site, 'show');
            revert_theme_base_url();
        ?>
        <a href="<?php echo $url; ?>" class="big blue button" target="_blank"><?php echo __('View Public Page'); ?></a>

        <?php if(is_allowed('Sites_Index', 'approve')): ?>
        <div id="public-featured">
            <div class="featured">
                <label for="featured"><?php echo __('Featured'); ?>:</label>
                <?php echo $this->formCheckbox('featured', $site->featured, array(), array('1', '0')); ?>
            </div>
        </div>
        <div class="approve">

            <label for="approve"><?php echo __('Approve'); ?>:</label>
            <?php echo $this->formCheckbox('approved', !is_null($site->date_approved), array(), array('1', '0')); ?>
            <?php if(!is_null($site->date_approved)): ?>
            <p>Approved: <?php echo metadata($site, 'date_approved');?></p>
            <?php endif;?>
        </div>
        <?php endif; ?>
    </div>
</section>
</form>


<?php echo foot(); ?>