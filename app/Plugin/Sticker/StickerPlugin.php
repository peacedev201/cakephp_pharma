<?php 
App::uses('MooPlugin','Lib');
class StickerPlugin implements MooPlugin{
    public function install()
    {
        //Setting
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $setting = $mSetting->findByName('sticker_enabled');
        if ($setting) {
            $mSetting->id = $setting['Setting']['id'];
            $mSetting->save(array('is_boot' => 1));
        }
        
        //install sample data
        $this->installSampleData();
    }
    
    public function uninstall()
    {
        $mSettingGroup = MooCore::getInstance()->getModel('SettingGroup');
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $mI18n = MooCore::getInstance()->getModel('I18nModel');
        $mStorageAwsTask = MooCore::getInstance()->getModel('Storage.StorageAwsTask');
        $mStorageAwsObjectMap = MooCore::getInstance()->getModel('Storage.StorageAwsObjectMap');
        $mActivity = MooCore::getInstance()->getModel('Activity');
        $mActivityComment = MooCore::getInstance()->getModel('ActivityComment');
        $mComment = MooCore::getInstance()->getModel('Comment');
        $mCommentHistory = MooCore::getInstance()->getModel('CommentHistory');
        $db = ConnectionManager::getDataSource("default");
        
        //delete language
        $mI18n->deleteAll(array(
            'I18nModel.model' => array(
                'StickerCategory', 'Sticker'
            )
        ));
        
        //delete setting
        $settingGroup = $mSettingGroup->findByModuleId('Sticker');
        if ($settingGroup != null) {
            $mSetting->deleteAll(array(
                'Setting.group_id' => $settingGroup['SettingGroup']['id']
            ));
            $mSettingGroup->delete($settingGroup['SettingGroup']['id']);
        }
        
        //delete s3
        $mStorageAwsTask->deleteAll(array(
            "StorageAwsTask.type" => array("sticker_category", "sticker_icon", "sticker_image")
        ));

        $mStorageAwsTask->deleteAll(array(
            "StorageAwsTask.name LIKE '%sticker/css%' OR StorageAwsTask.name LIKE '%sticker/images%' OR StorageAwsTask.name LIKE '%sticker/js%'"
        ));

        $mStorageAwsObjectMap->deleteAll(array(
            "StorageAwsObjectMap.type" => array("sticker_category", "sticker_icon", "sticker_image")
        ));

        $mStorageAwsObjectMap->deleteAll(array(
            "StorageAwsObjectMap.key LIKE '%sticker/css%' OR StorageAwsObjectMap.key LIKE '%sticker/images%' OR StorageAwsObjectMap.key LIKE '%sticker/js%'"
        ));
        
        //delete all activities or comments that contain only sticker
        $mActivityComment->deleteAll(array(
            "ActivityComment.activity_id IN(SELECT id FROM ".$mActivityComment->tablePrefix."activities WHERE sticker_image_id > 0 AND (content IS NULL OR content = ''))"
        ));
        
        $mActivity->deleteAll(array(
            "Activity.sticker_image_id > 0",
            "(Activity.content IS NULL OR Activity.content = '')"
        ));
        
        $mActivityComment->deleteAll(array(
            "ActivityComment.sticker_image_id > 0",
            "(ActivityComment.comment IS NULL OR ActivityComment.comment = '')"
        ));
        
        $mComment->deleteAll(array(
            "Comment.sticker_image_id > 0",
            "(Comment.message IS NULL OR Comment.message = '')"
        ));
        
        $mCommentHistory->deleteAll(array(
            "CommentHistory.sticker_image_id > 0",
            "(CommentHistory.content IS NULL OR CommentHistory.content = '')"
        ));
        
        $db->query("ALTER TABLE `".$mCommentHistory->tablePrefix."activity_comments` DROP sticker_image_id;");
        $db->query("ALTER TABLE `".$mCommentHistory->tablePrefix."comments` DROP sticker_image_id;");
        $db->query("ALTER TABLE `".$mCommentHistory->tablePrefix."comment_histories` DROP sticker_image_id;");
        $db->query("ALTER TABLE `".$mCommentHistory->tablePrefix."activities` DROP sticker_image_id;");
    }
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __d('sticker', 'Categories') => array('plugin' => 'Sticker', 'controller' => 'StickerCategories', 'action' => 'admin_index'),
            __d('sticker', 'Stickers') => array('plugin' => 'Sticker', 'controller' => 'Stickers', 'action' => 'admin_index'),
            __d('sticker', 'Settings') => array('plugin' => 'Sticker', 'controller' => 'StickerSettings', 'action' => 'admin_index'),
        );
    }
    
    private function installSampleData()
    {
        $mI18n = MooCore::getInstance()->getModel('I18nModel');
        $mLanguage = MooCore::getInstance()->getModel('Language');
        $db = ConnectionManager::getDataSource("default");
        
        //language
        $langs = $mLanguage->find('all');
        
        //stickers
        $stickers = $db->query("SELECT * FROM `".$mI18n->tablePrefix."sticker_stickers`;");
        
        //sticker category
        $categories = $db->query("SELECT * FROM `".$mI18n->tablePrefix."sticker_categories`;");

        foreach($langs as $lang)
        {
            $lang = $lang['Language'];
            foreach($stickers as $sticker)
            {
                $sticker = $sticker[$mI18n->tablePrefix.'sticker_stickers'];
                
                $mI18n->create();
                $mI18n->save(array(
                    'locale' => $lang['key'],
                    'model' => 'Sticker',
                    'content' => $sticker['name'],
                    'field' => 'name',
                    'foreign_key' => $sticker['id']
                ));
            }
            
            foreach($categories as $category)
            {
                $category = $category[$mI18n->tablePrefix.'sticker_categories'];
                
                $mI18n->create();
                $mI18n->save(array(
                    'locale' => $lang['key'],
                    'model' => 'StickerCategory',
                    'content' => $category['name'],
                    'field' => 'name',
                    'foreign_key' => $category['id']
                ));
            }
        }
    }
}