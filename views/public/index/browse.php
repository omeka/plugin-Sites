<?php
echo head(array('title' => __('Sites') , 'bodyclass' => 'sites browse')); 
?>
<div id="primary">

<h1><?php echo __('Sites'); ?> <?php echo __('(%s total)', total_records('sites')); ?></h1>

<?php foreach(loop('site') as $site) : ?>
    <div class='sites-site'>
        <?php $imageState = (sites_site_logo($site)) ? 'image' : 'no image'; ?>
        <div class="logo <?php echo $imageState; ?>">
            <?php echo sites_site_logo($site); ?>
        </div>
        <h2><?php echo link_to($site, 'show', $site->title); ?></h2>
        <p>(<?php echo sites_link_to_original_site($site, 'Original url'); ?>)</p>
        <?php if ($description = snippet($site->description, 0, 250, '...')): ?>
            <p><?php echo $description; ?></p>
        <?php endif; ?>
        <p class="items"><span class="number"><?php echo metadata($site, 'total items'); ?></span> items</p>
    </div>
<?php endforeach; ?>
</div>
<?php echo foot(); ?>