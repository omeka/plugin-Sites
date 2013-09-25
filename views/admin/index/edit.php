<?php

$title = strip_formatting(metadata('site', 'title'));
echo head(array('title'=>$title));
echo flash();
?>
<?php $columns = get_db()->getTable('Site')->getColumns();?>

<?php 
$columns = array('title', 'content_summary', 'join_reason', 'description');
?>

<form method="post">
<section class="seven columns alpha">
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
        <?php echo $this->formSubmit('submit', __('Save Changes'), array('id'=>'save-changes', 'class'=>'submit big green button')); ?>
        <?php 
            set_theme_base_url('public');
            $url = url("/sites/display-case/show/id/" . $site->id);
            revert_theme_base_url();
        ?>
        <a href="<?php echo $url; ?>" class="big blue button" target="_blank"><?php echo __('View Public Page'); ?></a>
        <div id="public-featured">
            <div class="featured">
                <label for="featured"><?php echo __('Featured'); ?>:</label> 
                <?php echo $this->formCheckbox('featured', $site->featured, array(), array('1', '0')); ?>
            </div>
        </div>
        <div class="approve">
            <?php if(is_null($site->date_approved)): ?>
            <label for="approve"><?php echo __('Approve'); ?>:</label>
            <?php echo $this->formCheckbox('approved', 1, array(), array('1', '0')); ?>
            <?php else: ?>
            <p>Approved: <?php echo metadata($site, 'date_approved');?></p>
            <?php endif;?>
        </div>                        
    </div>
</section>
</form>


<?php echo foot(); ?>