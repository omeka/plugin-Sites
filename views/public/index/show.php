<?php

$bodyclass = 'sites display-case';
echo head(array('title' => $site->title , 'bodyclass' => $bodyclass)); 
?>
<?php echo flash(); ?>
<h1><?php echo $site->title; ?></h1>
<?php if (sites_site_logo($site)): ?>
<div id="sidebar">
    <div id='site-logo'>
    <?php echo sites_site_logo($site); ?>
    </div>
</div>
<?php endif; ?>
<div id="primary">
    <div id="sites-overview">
    <?php if(isset($aggregation)): ?>
    <p><?php echo metadata($site, 'title'); ?> is part of <?php echo link_to($aggregation, 'show', metadata($aggregation, 'name'));?></p>
    <?php endif; ?>
    
    <?php if ($siteDesc = $site->description): ?>
    <div id="site-description">
        <h3><?php echo __('Description'); ?></h3>
        <?php echo $siteDesc; ?>
    </div>
    <?php endif; ?>
    <?php if ($site->getSiteContextCollections()): ?>
    <h3>Collections</h3>
    <ul id='sites-context'> 
    <?php foreach(loop('site_context_collection') as $collection) : ?>
        <li>
        <?php echo sites_link_to_original_context(); ?>
        </li>
    <?php endforeach; ?>
    </ul>
    <?php endif; ?>

    </div>

    <?php if ($this->tags): ?>
        <?php echo tag_cloud($this->tags, '/commons/items'); ?>
    <?php endif; ?>
</div>

<div id="recent-items" class="items-list with-images">
    <h2><?php echo __('Recent items saved to ') . metadata($site, 'title'); ?> (<?php echo metadata($site, 'total_items') . __(' total'); ?>)</h2>

    <?php foreach (loop('items') as $item): ?>
    <div class="item">
        <?php $item_files = $item->getFiles(); ?>
        <?php foreach ($item_files as $item_file): ?>
            <?php $stop = 0; ?>
            <?php if ($item_file->has_derivative_image == 1): ?>
                <div class="image" style="background-image: url('<?php echo file_display_url($item_file); ?>')"></div>
                <?php $stop = 1; ?>
            <?php endif; ?>
            <?php if ($stop == 1) { break; } ?>
        <?php endforeach; ?>
        <?php if (count($item_files) < 1): ?>
            <div class="no image"></div>
        <?php endif; ?>

        <h2><?php echo link_to_item(); ?></h2>

        <?php if($desc = metadata('item', array('Dublin Core', 'Description'), array('snippet'=>150))): ?>

        <div class="item-description"><?php echo $desc; ?></div>

        <?php endif; ?>

    </div>
    <?php endforeach; ?>
    
    <p><?php echo link_to_items_browse(__('View All Items'), array('site_id' => $site->id), array('class' => 'view-all-items-link button')); ?></p>

</div>


<?php echo foot(); ?>