<?php
echo head(array('title'=>$site_aggregation->name));
?>

<h1><?php echo metadata($site_aggregation, 'name'); ?></h1>
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

<?php echo foot(); ?>