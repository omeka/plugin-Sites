<?php
echo head(array('title'=>$site_aggregation->name));
?>

<h1><?php echo metadata($site_aggregation, 'name'); ?></h1>

<div class='primary'>
    <div>
    <?php echo metadata($site_aggregation, 'description'); ?>
    </div>

    <ul>
    <?php foreach(loop('sites', $sites) as $site): ?>
    <li><?php echo link_to($site, 'show', metadata($site, 'title')); ?></li>
    <?php endforeach; ?>
    </ul>

    <div id="recent-items" class="items-list with-images">
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
    </div>
</div>
<?php echo foot(); ?>