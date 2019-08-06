<?php

App::uses('MooPlugin', 'Lib');

class ContestPlugin implements MooPlugin {

    public function install() {
        //Permission
        $roleModel = MooCore::getInstance()->getModel('Role');
        $roles = $roleModel->find('all');
        $role_ids = array();
        foreach ($roles as $role) {
            $role_ids[] = $role['Role']['id'];
            $params = explode(',', $role['Role']['params']);
            $params = array_unique(array_merge($params, array('contest_create', 'contest_view')));
            $roleModel->id = $role['Role']['id'];
            $roleModel->save(array('params' => implode(',', $params)));
        }
        
        //Add page
        $pageModel = MooCore::getInstance()->getModel('Page.Page');
        $blockModel = MooCore::getInstance()->getModel('CoreBlock');
        $contentModel = MooCore::getInstance()->getModel('CoreContent');
        $i18nModel = MooCore::getInstance()->getModel('I18nModel');
        $languageModel = MooCore::getInstance()->getModel('Language');
        $languages = $languageModel->find('all');

        //Add Menu
        $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $menu = $menuModel->findByUrl('/contests');
        if (!$menu) {
            $menuModel->clear();
            $menuModel->save(array(
                'role_access' => json_encode($role_ids),
                'name' => 'Contests',
                'original_name' => 'Contests',
                'url' => '/contests',
                'type' => 'plugin',
                'is_active' => 1,
                'menu_order' => 999,
                'menu_id' => 1
            ));
            $menu = $menuModel->read();
            foreach ($languages as $language)
              {
               if ($language['Language']['key'] == Configure::read('Config.language'))
                continue;
               
               $i18nModel->clear();
               $i18nModel->save(array(
                 'locale' => $language['Language']['key'],
                 'model' => 'CoreMenuItem',
                 'foreign_key' => $menu['CoreMenuItem']['id'],
                 'field' => 'name',
                 'content' => 'Contests'
               ));
               
              }
        }
        //Setting
        $settingModel = MooCore::getInstance()->getModel('Setting');
        $setting = $settingModel->findByName('contest_enabled');
        if ($setting) {
            $settingModel->id = $setting['Setting']['id'];
            $settingModel->save(array('is_boot' => 1));
        }
        $f_setting = $settingModel->findByName('contest_by_pass_force_login');
        if ($f_setting)
        {
            $settingModel->id = $f_setting['Setting']['id'];
            $settingModel->save(array('is_boot'=>1));
        }

        //add translate page
        $pageModel->Behaviors->unload('Translate');
        $pages = $pageModel->find('all', array(
            'conditions' => array(
                'uri' => array('contests.view', 'contests.index', 'contests.entry')
            )
        ));

        foreach ($pages as $page) {
            foreach ($languages as $language) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $language['Language']['key'],
                    'model' => 'Page',
                    'foreign_key' => $page['Page']['id'],
                    'field' => 'title',
                    'content' => $page['Page']['title']
                ));

                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $language['Language']['key'],
                    'model' => 'Page',
                    'foreign_key' => $page['Page']['id'],
                    'field' => 'content',
                    'content' => $page['Page']['content']
                ));
            }
        }

        $tmp = array();
        foreach ($languages as $language) {
            if ($language['Language']['key'] == Configure::read('Config.language'))
                continue;

            $tmp[$language['Language']['key']] = $language;
        }
        $languages = $tmp;
        //add block to browse page
        $block = $blockModel->find('first', array(
            'conditions' => array('CoreBlock.path_view' => 'contest.featured')
        ));
        $block_featured_id = $block['CoreBlock']['id'];
        $block = $blockModel->find('first', array(
            'conditions' => array('CoreBlock.path_view' => 'core.tags')
        ));
        $block_tag_id = $block['CoreBlock']['id'];
        $block = $blockModel->find('first', array(
            'conditions' => array('CoreBlock.path_view' => 'contest.recent')
        ));
        $block_recent_id = $block['CoreBlock']['id'];
        $block = $blockModel->find('first', array(
            'conditions' => array('CoreBlock.path_view' => 'contest.top')
        ));
        $block_top_id = $block['CoreBlock']['id'];

        $block = $blockModel->find('first', array(
            'conditions' => array('CoreBlock.path_view' => 'contest.popular')
        ));
        $block_popular_id = $block['CoreBlock']['id'];

        $block = $blockModel->find('first', array(
            'conditions' => array('CoreBlock.path_view' => 'contest.view')
        ));
        $block_view_id = $block['CoreBlock']['id'];

        $block = $blockModel->find('first', array(
            'conditions' => array('CoreBlock.path_view' => 'contest.vote')
        ));
        $block_vote_id = $block['CoreBlock']['id'];

        $block = $blockModel->find('first', array(
            'conditions' => array('CoreBlock.path_view' => 'contest.winner')
        ));
        $block_winner_id = $block['CoreBlock']['id'];

        /* 
        $block = $blockModel->find('first', array(
            'conditions' => array('CoreBlock.path_view' => 'contest.countdown')
        )); 
        $block_countdown_id = $block['CoreBlock']['id'];
        */

        $block = $blockModel->find('first', array(
            'conditions' => array('CoreBlock.path_view' => 'contest.menu')
        ));
        $block_menu_id = $block['CoreBlock']['id'];

        $block = $blockModel->find('first', array(
            'conditions' => array('CoreBlock.path_view' => 'contest.browse')
        ));
        $block_browse_id = $block['CoreBlock']['id'];

        $browse_page = $pageModel->findByUri('contests.index');
        if ($browse_page) {
            $page_id = $browse_page['Page']['id'];

            //insert west
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'west'
            ));
            $west_id = $contentModel->id;
            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $west_id,
                    'field' => 'core_block_title',
                    'content' => ''
                ));
            }

            // add menu and search to west
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'contest.menu',
                'parent_id' => $west_id,
                'params' => '{"title":"Menu contest & Search","num_item_show":"20","plugin":"Contest","title_enable":"1"}',
                'plugin' => 'Contest',
                'core_block_id' => $block_menu_id,
                'core_block_title' => 'Menu contest & Search',
                'order' => 1
            ));

            $menu_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $menu_id,
                    'field' => 'core_block_title',
                    'content' => 'Menu contest & Search'
                ));
            }
            //insert tags
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.tags',
                'parent_id' => $west_id,
                'params' => '{"title":"Popular Tags","num_item_show":"10","type":"Contest_Contest","order_by":"popular","title_enable":"1"}',
                'plugin' => '',
                'core_block_id' => $block_tag_id,
                'core_block_title' => 'Popular Tags',
                'order' => 2
            ));
            $tag_id = $contentModel->id;
            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $tag_id,
                    'field' => 'core_block_title',
                    'content' => 'Popular Tags'
                ));
            }
            //add recent to west
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'contest.recent',
                'parent_id' => $west_id,
                'params' => '{"title":"Recent Contests","num_item_show":"20","plugin":"Contest","title_enable":"1"}',
                'plugin' => 'Contest',
                'core_block_id' => $block_recent_id,
                'core_block_title' => 'Recent Contests',
                'order' => 3
            ));

            $recent_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $recent_id,
                    'field' => 'core_block_title',
                    'content' => 'Recent Contests'
                ));
            }

            //insert center
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'center',
            ));
            $center_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $center_id,
                    'field' => 'core_block_title',
                    'content' => ''
                ));
            }
            // add feature
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'contest.featured',
                'parent_id' => $center_id,
                'params' => '{"title":"Featured Contests","num_item_show":"20","plugin":"Contest","title_enable":"1"}',
                'plugin' => 'Contest',
                'core_block_id' => $block_featured_id,
                'core_block_title' => 'Featured Contests',
                'order' => 1
            ));

            $featured_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $featured_id,
                    'field' => 'core_block_title',
                    'content' => 'Featured Contests'
                ));
            }
            //insert browse to center
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'contest.browse',
                'parent_id' => $center_id,
                'params' => '{"title":"Browse Contest","plugin":"Contest"}',
                'plugin' => 'Contest',
                'core_block_id' => $block_browse_id,
                'core_block_title' => 'Browse Contest',
                'order' => 2
            ));

            $browse_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $browse_id,
                    'field' => 'core_block_title',
                    'content' => 'Browse Contest'
                ));
            }

            //insert east
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'east'
            ));
            $east_id = $contentModel->id;
            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $east_id,
                    'field' => 'core_block_title',
                    'content' => ''
                ));
            }
            // add popular 
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'contest.popular',
                'parent_id' => $east_id,
                'params' => '{"title":"Popular Contests","num_item_show":"5","plugin":"Contest","title_enable":"1"}',
                'plugin' => 'Contest',
                'core_block_id' => $block_popular_id,
                'core_block_title' => 'Popular Contests',
                'order' => 4
            ));

            $popular_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $popular_id,
                    'field' => 'core_block_title',
                    'content' => 'Popular Contests'
                ));
            }
            // add top 
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'contest.top',
                'parent_id' => $east_id,
                'params' => '{"title":"Top Contests","num_item_show":"5","plugin":"Contest","title_enable":"1"}',
                'plugin' => 'Contest',
                'core_block_id' => $block_top_id,
                'core_block_title' => 'Top Contests',
                'order' => 3
            ));

            $top_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $top_id,
                    'field' => 'core_block_title',
                    'content' => 'Top Contests'
                ));
            }
        }


        //Add block to detail page
        $detail_page = $pageModel->findByUri('contests.view');
        if ($detail_page) {
            $page_id = $detail_page['Page']['id'];

            //insert west
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'west'
            ));
            $west_id = $contentModel->id;
            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $west_id,
                    'field' => 'core_block_title',
                    'content' => ''
                ));
            }
            //insert invisiblecontent to left
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'invisiblecontent',
                'parent_id' => $west_id,
                'params' => '{"title":"Entry Menu & Info","maincontent":"1"}',
                'plugin' => 'Contest',
                'core_block_title' => 'Entry Menu & Info',
                'order' => 1
            ));

            $invisiblecontent_left_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $invisiblecontent_left_id,
                    'field' => 'core_block_title',
                    'content' => 'Entry Menu & Info'
                ));
            }
            // insert winner block
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'contest.winner',
                'parent_id' => $west_id,
                'params' => '{"title":"Contest Winners","num_item_show":"20","plugin":"Contest","title_enable":"1"}',
                'plugin' => 'Contest',
                'core_block_id' => $block_winner_id,
                'core_block_title' => 'Contest Winners',
                'order' => 2
            ));

            $winner_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $winner_id,
                    'field' => 'core_block_title',
                    'content' => 'Contest Winners'
                ));
            }
            //insert center
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'center',
            ));
            $center_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $center_id,
                    'field' => 'core_block_title',
                    'content' => ''
                ));
            }
            //insert invisiblecontent to center
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'invisiblecontent',
                'parent_id' => $center_id,
                'params' => '{"title":"Contest Detail","maincontent":"1"}',
                'plugin' => 'Contest',
                'core_block_title' => 'Contest Detail'
            ));

            $invisiblecontent_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $invisiblecontent_id,
                    'field' => 'core_block_title',
                    'content' => 'Contest Detail'
                ));
            }
        }
        //Add block to detail page
        $entry_page = $pageModel->findByUri('contests.entry');
        if ($entry_page) {
            $page_id = $entry_page['Page']['id'];

            //insert west
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'west'
            ));
            $west_id = $contentModel->id;
            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $west_id,
                    'field' => 'core_block_title',
                    'content' => ''
                ));
            }

            //insert center
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'center',
            ));
            $center_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $center_id,
                    'field' => 'core_block_title',
                    'content' => ''
                ));
            }
            //insert invisiblecontent to center
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'invisiblecontent',
                'parent_id' => $center_id,
                'params' => '{"title":"Entry Detail","maincontent":"1"}',
                'plugin' => 'Contest',
                'core_block_title' => 'Entry Detail',
                'order' => 1
            ));

            $invisiblecontent_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $invisiblecontent_id,
                    'field' => 'core_block_title',
                    'content' => 'Entry Detail'
                ));
            }

            //insert east
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'east'
            ));
            $east_id = $contentModel->id;
            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $east_id,
                    'field' => 'core_block_title',
                    'content' => ''
                ));
            }
            // add voted entries 
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'contest.vote',
                'parent_id' => $east_id,
                'params' => '{"title":"Most Voted Entries","num_item_show":"5","plugin":"Contest","title_enable":"1"}',
                'plugin' => 'Contest',
                'core_block_id' => $block_vote_id,
                'core_block_title' => 'Most Voted Entries',
                'order' => 1
            ));

            $vote_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $vote_id,
                    'field' => 'core_block_title',
                    'content' => 'Most Voted Entries'
                ));
            }
            // add top 
            $contentModel->clear();
            $contentModel->save(array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'contest.view',
                'parent_id' => $east_id,
                'params' => '{"title":"Most Viewed Entries","num_item_show":"5","plugin":"Contest","title_enable":"1"}',
                'plugin' => 'Contest',
                'core_block_id' => $block_view_id,
                'core_block_title' => 'Most Viewed Entries',
                'order' => 2
            ));

            $view_id = $contentModel->id;

            foreach (array_keys($languages) as $key) {
                $i18nModel->clear();
                $i18nModel->save(array(
                    'locale' => $key,
                    'model' => 'CoreContent',
                    'foreign_key' => $view_id,
                    'field' => 'core_block_title',
                    'content' => 'Most Viewed Entries'
                ));
            }
        }
        // insert default category
        $mCategory = MooCore::getInstance()->getModel('Category');
        $mCategory->save(array(
            'name' => __d('contest', 'Default Category'),
            'type' => 'Contest',
            'active' => 1,
            'everyone' => 1));
        foreach (array_keys($languages) as $lKey) {
            $mCategory->locale = $lKey;
            $mCategory->saveField('name', __d('contest', 'Default Category'));
        }
        //end save
        $contest_invite_email = '
    <p>[header]</p>
    <p>We are proud to announce and invite you to participate in the <a href="[contest_link]">[contest_name]</a>.</p>
    <p>Subject: [subject]</p>
    <p>Message:</p>
    <p>[message]</p>
    <p>[footer]</p>
';
        $this->createMailTemplate('contest_invite_email', '[subject][message][sender_name][sender_link][contest_name][contest_link]', 'Contest Invite By Email', $contest_invite_email);
        $contest_winner_email = '
    <p>[header]</p>
    <p>Congrats! You have won the <a href="[contest_link]">[contest_name]</a>!</p>
    <p>To claim your prize, please contact to contest owner: <a href="[contest_owner_link]">[contest_owner_name]</a>.</p>
    <p></p>
    <p>[footer]</p>
';
        $this->createMailTemplate('contest_winner_email', '[contest_name][contest_link][contest_owner_name][contest_owner_link]', 'Contest Winner Email', $contest_winner_email);


        $contest_owner_winner_email = '
    <p>[header]</p>
    <p>[winner] have won the <a href="[contest_link]">[contest_name]</a>!</p>
    <p></p>
    <p>[footer]</p>
';
        $this->createMailTemplate('contest_owner_winner_email', '[winner][contest_link][contest_name]', 'Contest Winner Email To Owner', $contest_owner_winner_email);
    }

    public function uninstall() {
        //Menu
        $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $menu = $menuModel->findByUrl('/contests');
        if ($menu) {
            $menuModel->delete($menu['CoreMenuItem']['id']);
        }
        
		//Delete S3
		$objectModel = MooCore::getInstance()->getModel("Storage.StorageAwsObjectMap");
		$types = array('contests','contest_entries', 'contest_musics');
		foreach ($types as $type)
			$objectModel->deleteAll(array('StorageAwsObjectMap.type' => $type), false,false);
        //delete credit
        if (CakePlugin::loaded("Credit")) {
            $mCreditLog = MooCore::getInstance()->getModel('Credit.CreditLog');
            $mCreditActiontype = MooCore::getInstance()->getModel('Credit.CreditActiontype');
            $mCreditLog->deleteAll(array(
                'CreditLog.object_type' => array('contest_contest', 'contest_contest_entry')
            ));
            $mCreditActiontype->deleteAll(array(
                'CreditActiontype.plugin' => "Contest"
            ));
        }
    }

    public function settingGuide() {
        
    }

    public function menu() {
        return array(
            __d('contest', 'Contests Manager') => array('plugin' => 'contest', 'controller' => 'contests', 'action' => 'admin_index'),
            __d('contest', 'Entries Manager') => array('plugin' => 'contest', 'controller' => 'contests', 'action' => 'admin_entry'),
            __d('contest', 'Categories') => array('plugin' => 'contest', 'controller' => 'contest_categories', 'action' => 'admin_index'),
            __d('contest', 'Settings') => array('plugin' => 'contest', 'controller' => 'contest_settings', 'action' => 'admin_index'),
           // __d('contest', 'Credits Integration') => array('plugin' => 'contest', 'controller' => 'contest_settings', 'action' => 'admin_credit_integration'),
        );
    }

    private function createMailTemplate($type, $vars, $subject, $content) {
        $languageModel = MooCore::getInstance()->getModel('Language');
        $mailModel = MooCore::getInstance()->getModel('Mail.Mailtemplate');
        $langs = $languageModel->find('all');
        $data['Mailtemplate'] = array(
            'type' => $type,
            'plugin' => 'Contest',
            'vars' => $vars
        );
        $mailModel->create();
        $mailModel->save($data);
        $id = $mailModel->id;
        foreach ($langs as $lang) {
            $language = $lang['Language']['key'];
            $mailModel->locale = $language;
            $data_translate['subject'] = $subject;
            $data_translate['content'] = $content;
            $mailModel->save($data_translate);
        }
    }

    /*
      Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
      public function callback_1_0(){}
     */
}
