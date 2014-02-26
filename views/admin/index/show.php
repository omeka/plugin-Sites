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
        <?php
            set_theme_base_url('public');
            $url = record_url($site, 'show');
            revert_theme_base_url();
        ?>
        <?php if(is_allowed('Sites_Index', 'approve')): ?>
        <a href="<?php echo record_url($site, 'edit'); ?>" class="big green button"><?php echo __('Edit'); ?></a>
        <?php endif; ?>
        <a href="<?php echo $url; ?>" class="big blue button" target="_blank"><?php echo __('View Public Page'); ?></a>
    </div>
</section>
</form>


<?php echo foot(); ?>