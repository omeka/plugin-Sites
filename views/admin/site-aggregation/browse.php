<?php
queue_js_file('site-aggregation');
queue_css_string("p#add_site_key {cursor: pointer}");
echo head(array('title' => 'Your Site Groups'));

?>
<?php echo flash(); ?>
<a href="<?php echo  url('sites/site-aggregation/add'); ?>">Add a new aggregation</a>
<?php foreach($this->site_aggregations as $agg): ?>
<p><?php echo link_to($agg, 'edit', metadata($agg, 'name')); ?></p>

<?php endforeach;?>
<?php
echo foot();
?>