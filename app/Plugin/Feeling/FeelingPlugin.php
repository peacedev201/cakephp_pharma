<?php 
App::uses('MooPlugin','Lib');
class FeelingPlugin implements MooPlugin{
    public function install(){
        /*$src = WWW_ROOT . 'uploads' . DS . 'feeling';
        if(!is_dir($src)){
            mkdir($src, 0777);
        }*/

        // Setting
        $oSettingModel = MooCore::getInstance()->getModel('Setting');
        $aSetting = $oSettingModel->findByName('feeling_enabled');
        if ($aSetting) {
            $oSettingModel->id = $aSetting['Setting']['id'];
            $oSettingModel->save(array('is_boot' => 1));
        }
        $this->updateLanguage();
    }
    public function uninstall(){
        /*$src = WWW_ROOT . 'uploads' . DS . 'feeling';
        if(is_dir($src)){
            $dir = opendir($src);
            while(false !== ( $file = readdir($dir)) ) {
                if (( $file != '.' ) && ( $file != '..' )) {
                    $full = $src . '/' . $file;
                    if ( is_dir($full) ) {
                        rrmdir($full);
                    }
                    else {
                        unlink($full);
                    }
                }
            }
            closedir($dir);
            rmdir($src);
        }*/

        //delete language
        $i18nModel = MooCore::getInstance()->getModel('I18nModel');
        $i18nModel->deleteAll(array(
            'I18nModel.model' => array('FeelingCategory', 'Feeling')
        ));

        //delete activity cache
        $activityModel = MooCore::getInstance()->getModel("Activity");
        $activityModel->getDataSource()->flushMethodCache();

        //delete s3
        $mStorageAwsTask = MooCore::getInstance()->getModel('Storage.StorageAwsTask');
        $mStorageAwsObjectMap = MooCore::getInstance()->getModel('Storage.StorageAwsObjectMap');

        $mStorageAwsTask->deleteAll(array(
            "StorageAwsTask.type" => array("feelings", "feeling_categories")
        ));

        $mStorageAwsTask->deleteAll(array(
            "StorageAwsTask.name LIKE '%feeling/css%' OR StorageAwsTask.name LIKE '%feeling/img%' OR StorageAwsTask.name LIKE '%feeling/js%'"
        ));

        $mStorageAwsObjectMap->deleteAll(array(
            "StorageAwsObjectMap.type" => array("feelings", "feeling_categories")
        ));

        $mStorageAwsObjectMap->deleteAll(array(
            "StorageAwsObjectMap.key LIKE '%feeling/css%' OR StorageAwsObjectMap.key LIKE '%feeling/img%' OR StorageAwsObjectMap.key LIKE '%feeling/js%'"
        ));

    }
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __d('feeling', 'General') => array('plugin' => 'feeling', 'controller' => 'feeling_categories', 'action' => 'admin_index'),
            __d('feeling', 'Feeling Status') => array('plugin' => 'feeling', 'controller' => 'feelings', 'action' => 'admin_index'),
            __d('feeling', 'Settings') => array('plugin' => 'feeling', 'controller' => 'feeling_settings', 'action' => 'admin_index'),
        );
    }
    /*
    Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
    public function callback_1_0(){}
    */

    private function updateLanguage(){
        //$i18nModel = MooCore::getInstance()->getModel('I18nModel');
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $languagesModel = MooCore::getInstance()->getModel('Language');
        //$categoryModel = MooCore::getInstance()->getModel('FeelingCategory');
        $db = ConnectionManager::getDataSource("default");
        $table_prefix = $mSetting->tablePrefix;
        $languages = $languagesModel->find('list', array('fields' => array('Language.key')));

        $categories = $db->query("SELECT * FROM `".$table_prefix."feeling_categories`;");
        foreach ($categories As $category){
            $db->query($this->queryInsertLanguage($languages, $table_prefix, 'FeelingCategory', $category[$table_prefix.'feeling_categories']['id'], 'label', $category[$table_prefix.'feeling_categories']['label']));
        }

        $feelings = $db->query("SELECT * FROM `".$table_prefix."feelings`;");
        foreach ($feelings As $feeling){
            $db->query($this->queryInsertLanguage($languages, $table_prefix, 'Feeling', $feeling[$table_prefix.'feelings']['id'], 'label', $feeling[$table_prefix.'feelings']['label']));
        }
    }

    private function queryInsertLanguage($languages, $table_prefix, $model, $foreign_key, $field, $content){
        $query = "insert  into `" . $table_prefix . "i18n`(`locale`,`model`,`foreign_key`,`field`,`content`) values";
        $count = 0;
        foreach ($languages As $keyLang){
            if($count > 0){
                $query .= ",";
                ;            }
            $query .= " ('" . $keyLang . "', '".$model."', ".$foreign_key.",'".$field."','".str_replace("'",'&#39;', $content)."')";
            $count++;
        }
        $query .= ";";
        return $query;
    }

    private function updateSortOrder(){
        $db = ConnectionManager::getDataSource("default");
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $table_prefix = $mSetting->tablePrefix;
        $db->query("UPDATE `".$table_prefix."feelings` SET `order`= `id`");
        $db->query("UPDATE `".$table_prefix."feeling_categories` SET `order`= `id`");
    }

    public function callback_1_1(){
        $this->updateLanguage();
        $this->updateSortOrder();
    }

    public function callback_1_2(){
        $activityModel = MooCore::getInstance()->getModel("Activity");
        $activityModel->getDataSource()->flushMethodCache();
    }
}