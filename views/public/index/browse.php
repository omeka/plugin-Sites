<?php
echo head(array('title' => __('Browse sites') , 'bodyclass' => 'sites browse')); 
?>
<div id="primary">

<?php foreach(loop('site') as $site) : ?>
    <div class='sites-site'>
    <h2><?php echo link_to($site, 'show', $site->title); ?></h2>
    <p>(<?php echo sites_link_to_original_site($site, 'Original url'); ?>)</p>
    <?php echo sites_site_logo($site); ?>
    <?php if ($description = snippet($site->description, 0, 250, '...')): ?>
        <p><?php echo $description; ?></p>
    <?php endif; ?>
    <p class="items"><span class="number"><?php echo metadata($site, 'total items'); ?></span> items</p>
    </div>
<?php endforeach; ?>
</div>
<?php echo foot(); ?>