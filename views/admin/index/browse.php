<?php
queue_js_file('sites');
queue_js_string("var webRoot = '" . WEB_ROOT . "';");
queue_css_file('sites');
$head = array('bodyclass' => 'sites primary',
              'title' => html_escape('Sites'));
echo head($head);

?>

<div id="primary">
    <div class="pagination"><?php echo pagination_links(); ?></div>

<form method="post" action="<?php echo url('sites/index/batch-approve'); ?>">

<?php if(is_allowed('Sites_Index', 'approve')): ?>
Search by:
<label for='title'>Title</label><input size='10' type='text' name='title'/>
<label for='affiliation'>Affiliation</label><input size='10' type='text' name='affiliation'/>
<label for='admin_name'>Admin Name</label><input size='10' type='text' name='admin_name'/>
<button>Submit</button>

<ul class="quick-filter-wrapper">
    <li><a href="#" tabindex="0">Filter Approved</a>
    <ul class="dropdown">
        <li><span class="quick-filter-heading">Filter Approved</span></li>
         <li><a href="<?php echo url('sites'); ?>">All</a></li>
         <li><a href="<?php echo url('sites?approved=true') ?>">Approved</a></li>
         <li><a href="<?php echo url('sites?approved=false') ?>">Needs approval</a></li>
        </ul>
    </li>
</ul>
<button id="batch-approve" class="small blue button">Approve</button>
<?php endif; ?>
    <table>
        <thead>
        <tr>
        <th class="batch-edit-heading"><input type='checkbox' id='check-all' /></th>
        <?php
        $browseHeadings[__('Site')] = 'title';
        $browseHeadings[__('Affiliation')] = 'affiliation';
        $browseHeadings[__('Admin Name')] = 'admin_name';
        $browseHeadings[__('Last Import')] = 'last_import';
        $browseHeadings[__('Approved')] = 'date_approved';
        echo browse_sort_links($browseHeadings, array('link_tag' => 'th scope="col"', 'list_tag' => ''));
        ?>

        </tr>

        </thead>
        <tbody>
        <?php foreach(loop('sites') as $site): ?>
        <?php if(is_allowed($site, 'edit')): ?>
        <tr>
        <td class="batch-edit-check" scope="row">
            <?php if(!$site->date_approved):?>
            <input type="checkbox" name="sites[]" class="site-checkbox" value="<?php echo $site->id; ?>" />
            <?php endif;?>
        </td>
        <?php if($site->featured): ?>
        <td class='featured'>
        <?php else: ?>
        <td>
        <?php endif; ?>
            <span class='title'><a href="<?php echo record_url($site, 'edit'); ?>"><?php echo metadata($site, 'title') ?></a></span>

        </td>
        <td><?php echo $site->affiliation; ?></td>
        <td>
            <?php echo $site->admin_name; ?>
            <ul class='action-links group'>
                <li class='details-link'><a href='mailto: <?php echo $site->admin_email; ?>'>Email admin</a></li>
            </ul>
        </td>
        <td><?php echo $site->last_import; ?></td>
        <td>
        <?php if(!$site->date_approved): ?>
            <span class='approve' id='approve-<?php echo $site->id; ?>'>Approve</span>
        <?php else: ?>
            <?php echo $site->date_approved; ?>
        <?php endif; ?>
        </td>
        </tr>
        <?php endif; ?>
        <?php endforeach; ?>

        </tbody>

    </table>

</form>
</div>
    <div class="pagination"><?php echo pagination_links(); ?></div>
<?php echo foot(); ?>
