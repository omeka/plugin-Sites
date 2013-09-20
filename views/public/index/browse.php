<?php
echo head(array('title' => __('Browse sites') , 'bodyclass' => 'sites browse')); 
?>
<div id="primary">

<?php foreach(loop('site') as $site) : ?>
    <div class='sites-site'>
    <h2><?php echo  $site->title; ?></h2>
    <p>(<?php echo sites_link_to_original_site($site, 'Original url'); ?>)</p>
    <?php echo sites_site_logo($site); ?>
    <p><?php echo $site->description; ?></p>
    <p class="items"><span class="number"><?php echo metadata($site, 'total items'); ?></span> items</p>
    </div>
<?php endforeach; ?>
</div>
<?php echo foot(); ?>