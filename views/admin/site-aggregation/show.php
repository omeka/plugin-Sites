<?php
echo head(array('title'=>$site_aggregation->name));
?>

<section class='five columns alpha'>
<div>
<?php echo metadata($site_aggregation, 'description'); ?>
</div>

<ul>
<?php foreach(loop('sites', $sites) as $site): ?>
<li><?php echo link_to($site, 'show', metadata($site, 'title')); ?></li>
<?php endforeach; ?>
</ul>
</section>
<section class='three columns omega'>
    <div id='edit' class='panel'>
    <a class='big green button' href="<?php echo record_url($site_aggregation, 'edit'); ?>">Edit</a>
    
    </div>
</section>
<?php echo foot(); ?>