<?php

define('SITES_PLUGIN_DIR', dirname(__FILE__));
require_once(SITES_PLUGIN_DIR . '/helpers/functions.php');
require_once(SITES_PLUGIN_DIR . '/helpers/ContextFunctions.php');

class SitesPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'install',
        'uninstall',
        'upgrade',
        'site_browse_sql',
        'items_browse_sql',
        'define_routes',
        'define_acl',
        'public_theme_header',
        'public_items_show',
        'public_items_search',
        'admin_items_show_sidebar',
        'after_save_user', // a little inappropriate since it isn't relevant to this plugin, but just a cheap shortcut since it shouldn't go in Groups as a general use plugin feature
        'embed_codes_browse_each'
    );

    protected $_filters = array(
        'admin_navigation_main',
        'search_record_types'
    );

    public function setUp()
    {
        parent::setUp();
        require_once(SITES_PLUGIN_DIR . '/models/SiteContext/Table/Collection.php');
        require_once(SITES_PLUGIN_DIR . '/models/SiteContext/Table/Exhibit.php');
        //require_once(SITES_PLUGIN_DIR . '/models/SiteContext/Table/ExhibitSection.php');
        //require_once(SITES_PLUGIN_DIR . '/models/SiteContext/Table/ExhibitSectionPage.php');
    }

    /*
     * For Commons, each new user gets a new gruop
     */

    public function hookAfterSaveUser($args)
    {
        $user = $args['record'];
        if($args['insert']) {
            $group = new Group();
            $group->visibility = 'closed';
            $group->title = $user->name . "'s Group";
            $group->owner_id = $user->id;
            $group->flagged = 0;
            $group->save();
            $group->addMember($user, 0, 'is_owner');
        }
    }

    public function hookPublicThemeHeader()
    {
        queue_css('sites');
    }

    /**
     * Sets up permissions so on admin side site-admins can only get to their own stuff
     * @param array $args
     */

    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];
        $acl->addResource('Sites_Index');
        $acl->addResource('Sites_SiteAggregation');
        $acl->addRole(new Zend_Acl_Role('site-admin'), null);

        $acl->allow(array('site-admin'), 'Users', null,
                    new Omeka_Acl_Assert_User);

        $acl->allow('site-admin',
                    array( 'Items'),
                    array( 'editSelf', 'showSelf', 'browse', 'index')
                );

        $acl->allow('site-admin',
                array('Sites_Index', 'Sites_SiteAggregation'),
                array('edit', 'show', 'showSelf'),
                new Omeka_Acl_Assert_Ownership
        );

        $acl->deny('site-admin', 'Items', array('delete-confirm', 'delete'));
        $acl->allow('site-admin',
                array('Sites_Index', 'Sites_SiteAggregation'),
                array('editSelf', 'showSelf', 'browse', 'index', 'add')
                );

        //make sure regular browsing can happen on the public side
        if(!is_admin_theme()) {
            $acl->allow(null,
                        array('Sites_Index', 'Sites_SiteAggregation'),
                        array('show')
                        );
        }

        $resources = array('Collections', 'ElementSets', 'Files', 'Plugins',
                'Settings', 'Security', 'Upgrade', 'Tags', 'Themes',
                'SystemInfo', 'ItemTypes', 'Appearance', 'Elements');
        $acl->deny('site-admin', $resources);
    }

    public function hookPublicItemsShow($args)
    {
        $html = '';
        $html .= $this->_siteContextsHtml($args);
        $html .= $this->_siteInfoHtml($args);
        echo $html;
    }

    public function hookPublicItemsSearch($args)
    {
        $view = $args['view'];
        echo $view->partial('advanced-search-partial.php', array('searchFormId'=>'advanced-search-form', 'searchButtonId'=>'submit_search_advanced'));
    }

    public function hookAdminItemsShowSidebar($args)
    {
        $contentHtml = $this->_siteContextsHtml($args, array('h-level'=>4));
        $contentHtml .= $this->_siteInfoHtml($args, array('h-level'=>4));
        if(!empty($contentHtml)) {
            $html = '<div class="panel">';
            $html .= '</div>';
            echo $html;
        }
    }

    public function hookEmbedCodesBrowseEach($args)
    {
        $item = $args['item'];
        $site = $this->_db->getTable('SiteItem')->findSiteForItem($item);
        if($site) {
            $html = "<li>";
            $html .= __("Site: ") . $site->title;
            $html .= "</li>";
            echo $html;
        }
    }

    public function filterAdminNavigationMain($navArray)
    {
        $user = current_user();
        if($user->role == 'site-admin') {
            $navArray = array();
        }
        $navArray['Items'] = array('label'=>'Items', 'uri'=>url('items') );
        $navArray['Sites'] = array('label'=>'Sites', 'uri'=>url('sites/index') );
        $navArray['SiteAggregations'] = array('label'=>'Site Aggregations', 'uri'=>url('sites/site-aggregation/'));
        return $navArray;
    }

    public function filterSearchRecordTypes($searchableRecordTypes) {
        $searchableRecordTypes['Site'] = __('Site');
        $searchableRecordTypes['SiteContext_Collection'] = __('Collection');
        return $searchableRecordTypes;
    }

    public function hookInstall()
    {
        $db = get_db();
        //Site table
        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->Site` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `site_aggregation_id` int(10) unsigned NULL,
          `owner_id` int(10) unsigned NOT NULL,
          `url` text NULL,
          `super_email` text NOT NULL,
          `admin_email` text NOT NULL,
          `admin_name` text NOT NULL,
          `content_summary` text NOT NULL,
          `affiliation` text NOT NULL,
          `join_reason` text NOT NULL,
          `title` text NULL,
          `description` text NULL,
          `api_key` text NULL,
          `last_import` timestamp NULL DEFAULT NULL,
          `date_approved` timestamp NULL DEFAULT NULL,
          `date_applied` timestamp NULL DEFAULT NULL,
          `copyright_info` text,
          `author_info` text NULL,
          `commons_settings` text NULL,
          `omeka_version` text NOT NULL,
          `public` tinyint(1) DEFAULT NULL,
          `featured` tinyint(1) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

        ";
        $db->query($sql);

        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->SiteAggregation` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `name` text NULL,
        `description` text NULL,
        `owner_id` int(10) unsigned NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ";

        $db->query($sql);

        //SiteCollection table
        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->SiteContextCollection` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `site_id` int(10) unsigned NOT NULL,
          `orig_id` int(10) unsigned NOT NULL,
          `url` text NULL,
          `title` text NULL,
          `description` text NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ";

        $db->query($sql);
        //SiteItem table
        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->SiteItem` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `item_id` int(10) unsigned NOT NULL,
          `site_id` int(10) unsigned NOT NULL,
          `orig_id` int(10) unsigned NOT NULL,
          `url` text NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ";
        $db->query($sql);

        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->SiteAggregation` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `name` text NULL,
          `description` text NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ";

        $db->query($sql);

        $prop = get_db()->getTable('RecordRelationsProperty')->findByVocabAndPropertyName(SIOC, 'has_container');
        if(empty($prop)) {
            $propData = array(
                'namespace_prefix' => 'sioc',
                'namespace_uri' => SIOC,
                'properties' => array(
                    array(
                        'local_part' => 'has_container',
                        'label' => 'Has container',
                        'description' => 'The Container to which this Item belongs.'
                    )
                )
            );
            record_relations_install_properties(array($propData));
        }

        //put in Europeana properties
        $europeanaProps = array(
              array(
                    'name' => 'Europeana',
                    'description' => 'Europeana relations',
                    'namespace_prefix' => 'europeana',
                    'namespace_uri' => EUROPEANA,
                    'properties' => array(
                        array(
                            'local_part' => 'isShownBy',
                            'label' => 'is shown by',
                            'description' => ''
                        ),
                        array(
                            'local_part' => 'isDisplayedBy',
                            'label' => 'is displayed by',
                            'description' => ''
                        ),
                        array(
                            'local_part' => 'provider',
                            'label' => 'provider',
                            'description' => ''
                        ),
                        array(
                            'local_part' => 'dataProvider',
                            'label' => 'data provider',
                            'description' => ''
                        ),
                    )
                )

          );
        record_relations_install_properties($europeanaProps);

        $commonsProps = array(
              array(
                    'name' => 'Commons',
                    'description' => 'Commons relations',
                    'namespace_prefix' => 'commons',
                    'namespace_uri' => 'http://ns.omeka-commons.org/',
                    'properties' => array(
                        array(
                            'local_part' => 'usesTag',
                            'label' => 'Uses Tag',
                            'description' => 'The subject Site uses the object Tag'
                        ),                    )
                )

          );

        record_relations_install_properties($commonsProps);

        //blocks_register_blocks(array('CommonsOriginalInfoBlock', 'CommonsSiteInfoBlock' ));
    }

    public function hookUninstall()
    {

        $db = get_db();

        $sql = "DROP TABLE IF EXISTS `$db->Site`,
                `$db->SiteContextExhibit`,
                `$db->SiteContextExhibitSection`,
                `$db->SiteContextExhibitPage`,
                `$db->SiteContextCollection`,
                `$db->SiteItem`,
                `$db->SiteFamily`,
                `$db->SiteToken` ;
        ";

        $db->query($sql);

        //blocks_unregister_blocks(array('CommonsOriginalInfoBlock', 'CommonsSiteInfoBlock' ));
    }

    public function hookUpgrade($args)
    {
        $old = $args['old_version'];
        $new = $args['new_version'];
        $db = get_db();

        if($new == '1.2') {
            $sql = "
            ALTER TABLE `$db->Site` ADD `public` BOOLEAN NULL,
            ADD `featured` BOOLEAN NULL
            ";
            $db->query($sql);
        }
    }

    public function hookDefineRoutes($args)
    {
        $router = $args['router'];
        $router->addRoute(
            'sites-site-route',
            new Zend_Controller_Router_Route(
                'sites/:action/:id',
                array(
                    'module'        => 'sites',
                    'controller'    => 'index',
                    'action'        => 'browse',
                    )
            )
        );

        $router->addRoute(
                'sites-site-aggregation',
                new Zend_Controller_Router_Route(
                        'sites/site-aggregation/add',
                        array(
                                'module'        => 'sites',
                                'controller'    => 'site-aggregation',
                                'action'        => 'add',
                        )
                )
        );

        $router->addRoute(
                'sites-site-approve-route',
                new Zend_Controller_Router_Route(
                        'sites/approve',
                        array(
                                'module'        => 'sites',
                                'controller'    => 'index',
                                'action'        => 'approve',
                        )
                )
        );
        $router->addRoute(
                'sites-batch-approve-route',
                new Zend_Controller_Router_Route(
                        'sites/index/batch-approve',
                        array(
                                'module'        => 'sites',
                                'controller'    => 'index',
                                'action'        => 'batch-approve',
                        )
                )
        );
    }

    public function hookSiteBrowseSql($args)
    {
        $select = $args['select'];
        $params = $args['params'];
        if(isset($_GET['unapproved']) && $_GET['unapproved'] == true) {
            $select->where('added IS NULL');
        }
        return $select;
    }

    public function hookItemsBrowseSql($args)
    {
        $select = $args['select'];
        $params = $args['params'];
        $db = $this->_db;

        if(is_admin_theme()) {
            $user = current_user();
            if($user->role != 'admin' && $user->role != 'super') {
                $select->where('items.owner_id = ?', $user->id);
            }
        }

        if(!empty($params['site_collection_id'])) {
            $select->join(array('site_items'=>$db->SiteItem), 'site_items.item_id = items.id', array());
            $select->join(array('record_relations_relation'=>$db->RecordRelationsRelation),
                'record_relations_relation.subject_id = site_items.id', array()
            );
            $select->where("record_relations_relation.object_id = ?", $params['site_collection_id']);
            $select->where("record_relations_relation.subject_record_type = 'SiteItem'");
            $select->where("record_relations_relation.object_record_type = 'SiteContext_Collection'");
        }

        if(!empty($params['site_id'])) {
            $select->join(array('site_items'=>$db->SiteItem), 'site_items.item_id = items.id', array());
            $select->where("site_id = ? ", $params['site_id']);
            if(!empty($params['site_title'])) {
                $select->join(array('sites' => $db->Site), 'sites.id = site_items.site_id', array());
                $select->where('sites.title LIKE ?', $params['site_title']);
            }
        }
    }

    private function _findSiteContexts($pred, $objectContextType, $siteItemId)
    {
        $db = get_db();

        $relParams = array(
                'subject_id' => $siteItemId,
                'subject_record_type' => 'SiteItem',
                'property_id' => $pred->id,
                'object_record_type' => $objectContextType,
                'public' => true
        );

        return $db->getTable('RecordRelationsRelation')->findObjectRecordsByParams($relParams);
    }

    private function _siteContextsHtml($args, $options=array())
    {
        if(isset($options['h-level'])) {
            $hlevel = $options['h-level'];
        } else {
            $hlevel = 2;
        }


        $item = $args['item'];
        $db = get_db();

        $site = $db->getTable('SiteItem')->findSiteForItem($item->id);
        $html = "<div id='site-contexts'>";
        $html .= "<h$hlevel>" . __('Original Context') . "</h$hlevel>";
        if(!$site) {
            $html .= __('The original site is no longer part of the Omeka Commons.');
            $html .= '</div>';
            return $html;
        }
        $siteItem = get_db()->getTable('SiteItem')->findByItemId($item->id);
        if(!$siteItem) {
            return;
        }
        $has_container = $db->getTable('RecordRelationsProperty')->findByVocabAndPropertyName(SIOC, 'has_container');
        $collections = $this->_findSiteContexts($has_container, 'SiteContext_Collection', $siteItem->id);
        $site_url = $site->url;
        $html .= "<p><a href='{$site_url}'>". $site->title . "</a></p>";
        $html .= "<p>". $site->description . "</p>";
        $nextHlevel = $hlevel +1;
        if(!empty($collections)) {
            $html .= "<h$nextHlevel>Collection(s)</h$nextHlevel>";
            foreach($collections as $collection) {
                $html .= link_to_items_browse(metadata($collection, 'title'), array('site_collection_id'=>$collection->id));
                $html .= "<br>";
                $html .= snippet($collection->description, 0, 100) . "</p>";
            }
        }
        $html .= "<p><a href='{$siteItem->url}'>View Original</a>";
        $html .= "</div>";
        return $html;
    }

    private function _siteInfoHtml($args, $options=array())
    {
        if(isset($options['h-level'])) {
            $hlevel = $options['h-level'];
        } else {
            $hlevel = 2;
        }

        $item = $args['item'];
        $db = get_db();
        $site = $db->getTable('SiteItem')->findSiteForItem($item->id);
        if(!$site) {
            return;
        }
        $html = "<div id='site-info'>";
        $html .= "<h$hlevel>Explore in Omeka Commons</h$hlevel>";
        $html .= "<p>From " . link_to($site, 'show', $site->title) . "</p>";
        $html .= sites_site_logo($site);
        $html .= "</div>";
        return $html;
    }
}

