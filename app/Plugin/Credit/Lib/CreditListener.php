<?php
App::uses('CakeEventListener', 'Event');

class CreditListener implements CakeEventListener
{

    public function implementedEvents()
    {
        return array(
            'Model.afterSave' => 'doAfterSave',
            'Model.beforeDelete' => 'doBeforeDelete',
            'MooView.beforeRender' => 'beforeRender',
            'Controller.Share.afterShare' => 'afterShare',
            'UserController.deleteUserContent' => 'deleteUserContent',

            'StorageHelper.credit_ranks.getUrl.local' => 'storage_geturl_local',
            'StorageHelper.credit_ranks.getUrl.amazon' => 'storage_geturl_amazon',
            'StorageAmazon.credit_ranks.getFilePath' => 'storage_amazon_get_file_path',
            'StorageTaskAwsCronTransfer.execute' => 'storage_task_transfer',
            'ActivitesController.beforeShare' => 'beforeShareActivity',
            // 'Plugin.Controller.Photo.beforeSavePhoto' => 'beforeSavePhoto',
            // 'Plugin.Controller.Video.beforeSaveVideo'    => 'beforeSaveVideo'
        );
    }
    public $array_ignore = array("processe","task","menu", "cake_sessions", "setting_group", "plugin","credit_actiontypes","credit_balances","credit_faqs","credit_logs","credit_orders","credit_ranks","credit_sells","credit_withdraws");

    public function beforeSaveVideo($event){
        $destination = $event->data['destination'];

        $uid = MooCore::getInstance()->getViewer(true);

        //video
        App::import('Model', 'Credit.CreditBalances');
        $this->CreditBalances = new CreditBalances();

        App::import('Model', 'Credit.CreditActiontypes');
        $this->CreditActiontypes = new CreditActiontypes();

        $current_balance = $this->CreditBalances->getBalancesUser($uid);

        $type = 'video';
        $action_type = $this->CreditActiontypes->getActionTypeFormModule($type,true);

        if($destination != '' && !empty($action_type) && $action_type['CreditActiontypes']['credit'] < 0){
            if( ($action_type['CreditActiontypes']['credit'] + $current_balance['CreditBalances']['current_credit']) < 0 ){
                $event->result['status'] = false;
                $event->result['msg'] = __d('credit', "Your account is not enough credit to make transactions");
            }
        }
    }

    public function beforeSavePhoto($event){
        $photoList = $event->data['photoList'];

        $photoList = array_filter($photoList);

        $uid = MooCore::getInstance()->getViewer(true);

        App::import('Model', 'Credit.CreditActiontypes');
        $this->CreditActiontypes = new CreditActiontypes();

        App::import('Model', 'Credit.CreditBalances');
        $this->CreditBalances = new CreditBalances();

        $current_balance = $this->CreditBalances->getBalancesUser($uid);

        //photo
        $type = 'photo';
        $action_type = $this->CreditActiontypes->getActionTypeFormModule($type,true);

        if(!empty($action_type) && $action_type['CreditActiontypes']['credit'] < 0 && count($photoList) > 0){
            if( (count($photoList)*$action_type['CreditActiontypes']['credit'] + $current_balance['CreditBalances']['current_credit']) < 0 ){
                $event->result['status'] = false;
                $event->result['msg'] = __d('credit', "Your account is not enough credit to make transactions");
            }
        }
    }

    public function beforeShareActivity($event){
        $data = $event->data['data'];
        $event->result['status'] = true;
        $list_photo = array();
        if($data['wall_photo'] != '') $list_photo = array_filter(explode(',', $data['wall_photo']));

        $uid = MooCore::getInstance()->getViewer(true);

        App::import('Model', 'Credit.CreditActiontypes');
        $this->CreditActiontypes = new CreditActiontypes();

        App::import('Model', 'Credit.CreditBalances');
        $this->CreditBalances = new CreditBalances();

        $current_balance = $this->CreditBalances->getBalancesUser($uid);

        //photo
        $type = 'photo';
        $action_type = $this->CreditActiontypes->getActionTypeFormModule($type,true);

        if(!empty($action_type) && $action_type['CreditActiontypes']['credit'] < 0 && count($list_photo) > 0){
            if( (count($list_photo)*$action_type['CreditActiontypes']['credit'] + $current_balance['CreditBalances']['current_credit']) < 0 ){
                $event->result['status'] = false;
            }
        }

        //video
        $type = 'video';

        $action_type = $this->CreditActiontypes->getActionTypeFormModule($type,true);

        if(!empty($action_type) && $action_type['CreditActiontypes']['credit'] < 0 && $data['video_destination'] != ''){
            if( ($action_type['CreditActiontypes']['credit'] + $current_balance['CreditBalances']['current_credit']) < 0 ){
                $event->result['status'] = false;
            }
        }

    }

    public function doAfterSave($event)
    {
        $permission = $this->_checkPermission();
        if(!$permission) {
            return;
        }

        $model = $event->subject();

        $uid = MooCore::getInstance()->getViewer(true);

        if ($model->name == 'User') {
            $uid = $model->id;
        }
        if (!$uid) {
            return;
        }
        $created = $event->data['0'];

        if ($created && $model->name) {
            $name = $model->name;
            $name = $this->splitAtUpperCase($name);
            $name = implode('_',$name);
            $type = strtolower($name);

            if (in_array($type,$this->array_ignore))
            {
                return;
            }

            $name = ($model->plugin ? $model->plugin : 'Core').$model->name;
            $name = $this->splitAtUpperCase($name);
            $name = implode('_',$name);

            $object_type = strtolower($name);
            $object_id = $model->id;
            if ($model->plugin) {
                if ($type == 'group_user') {
                    $group_arr = $model->data;

                    if (empty($group_arr)) {
                        return;
                    }
                    $groupModel = MooCore::getInstance()->getModel('Group.Group',false);
                    $group = $groupModel->findById($group_arr['GroupUser']['group_id']);
                    $groupModel->id = $group_arr['GroupUser']['group_id'];
                    $groupUserModel = MooCore::getInstance()->getModel('Group.GroupUser', false);

                    $groupUserAdmin = $groupUserModel->findByGroupIdAndUserIdAndStatus($group_arr['GroupUser']['group_id'],$uid, GROUP_USER_ADMIN);
                    if (empty($group) || !empty($groupUserAdmin)) {
                        return;
                    }
                    if ($group['Group']['user_id'] == $group_arr['GroupUser']['user_id']) {
                        return;
                    }

                    $object_type = 'group_group';
                    $object_id = $group_arr['GroupUser']['group_id'];
                }
            } else {
                if ($type == 'friend') {
                    $item_arr = $model->data;
                    $uid = $item_arr['Friend']['user_id'];
                    $object_type = 'core_user';
                    $object_id = $item_arr['Friend']['friend_id'];
                }
            }

            if($type == 'activity' && $model->data['Activity']['action'] != 'wall_post'){
                return;
            }

            if($type == 'activity' && $model->data['Activity']['action'] == 'wall_post'){
                $type = 'post_new_feed';
            }

            App::import('Model', 'Credit.CreditActiontypes');
            $this->CreditActiontypes = new CreditActiontypes();
            $action_type = $this->CreditActiontypes->getActionTypeFormModule($type,true);

            if (!empty($action_type) && $action_type['CreditActiontypes']['credit'] != 0) {
                App::import('Model', 'Credit.CreditLogs');
                $this->CreditLogs = new CreditLogs();
                // check credit max
                if ($this->CreditLogs->checkCredit($action_type, $uid)) {

                    $action_id = $action_type['CreditActiontypes']['id'];

                    $all_credits = $this->CreditLogs->getCredit($action_type,$uid);

                    if ($action_type['CreditActiontypes']['max_credit'] - $all_credits >= $action_type['CreditActiontypes']['credit'])
                        $credit = $action_type['CreditActiontypes']['credit'];
                    else
                        $credit = $action_type['CreditActiontypes']['max_credit'] - $all_credits;

                    if($credit < 0 && ($type == 'video' || $type == 'photo')){
                        $this->CreditLogs->addLog($action_id, $credit, $object_type, $uid, $object_id);
                        App::import('Model', 'Credit.CreditBalances');
                        $this->CreditBalances = new CreditBalances();
                        $this->CreditBalances->addCredit($uid, $credit);
                    }elseif($credit > 0){
                        $this->CreditLogs->addLog($action_id, $credit, $object_type, $uid, $object_id);
                        App::import('Model', 'Credit.CreditBalances');
                        $this->CreditBalances = new CreditBalances();
                        $this->CreditBalances->addCredit($uid, $credit);
                    }
                    
                }
            }

        }
    }

    public function doBeforeDelete($event)
    {

        $permission = $this->_checkPermission();
        if(!$permission) {
            return;
        }

        $model = $event->subject();

        if ($model->name) {
            $name = $model->name;
            $name = $this->splitAtUpperCase($name);
            $name = implode('_',$name);
            $type = strtolower($name);
            if (in_array($type,$this->array_ignore))
            {
                return;
            }

            $name = ($model->plugin ? $model->plugin : 'Core').$model->name;
            $name = $this->splitAtUpperCase($name);
            $name = implode('_',$name);

            $object_type = strtolower($name);
            $object_id = $model->id;
            $uid = MooCore::getInstance()->getViewer(true);

            if ($model->plugin) {

                if ($type == 'group_user') {
                    $groupUserModel = MooCore::getInstance()->getModel('Group.GroupUser',false);
                    $groupUser = $groupUserModel->findById($object_id);
                    if (empty($groupUser)) {
                        return;
                    }
                    $object_id = $groupUser['GroupUser']['group_id'];
                    $object_type = 'group_group';
                }
            } else {
                if ($type == 'friend') {
                    App::import('Model', 'Friend');
                    $this->Friend = new Friend();
                    $item_arr = $this->Friend->findById($model->id);
                    $uid = $item_arr['Friend']['user_id'];
                    $object_id = $item_arr['Friend']['friend_id'];
                    $object_type = 'core_user';
                }

                if ($type == 'user')
                {
                    $model->query("DELETE FROM ".$model->tablePrefix."credit_logs WHERE user_id=" . intval($object_id));
                    $model->query("DELETE FROM ".$model->tablePrefix."credit_orders WHERE user_id=" . intval($object_id));
                    $model->query("DELETE FROM ".$model->tablePrefix."credit_balances WHERE id=" . intval($object_id));
                }
            }

            if($type == 'activity'){
                App::import('Model', 'Activity');
                $this->Activity = new Activity();
                $item_activity = $this->Activity->findById($model->id);
                if($item_activity && $item_activity['Activity']['action'] == 'wall_post'){
                    $type = 'post_new_feed';
                    $object_type = 'core_activity';
                }
            }

            App::import('Model', 'Credit.CreditLogs');
            $this->CreditLogs = new CreditLogs();

            App::import('Model', 'Credit.CreditActiontypes');
            $this->CreditActiontypes = new CreditActiontypes();
            $action_type = $this->CreditActiontypes->getActionTypeFormModule($type,true);

            if (!empty($action_type)) {
                // check log delete
                $item = $this->CreditLogs->checkDeleteItem($action_type['CreditActiontypes']['id'], $uid, $object_type, $object_id);
                CakeLog::write('credit', print_r($item,true));
                if ($item) {
                    $credit = intval(-$item['CreditLogs']['credit']);
                    $this->CreditLogs->addLog($action_type['CreditActiontypes']['id'], $credit, $object_type, $uid, $object_id,true);
                    $this->CreditLogs->updateAll(array('deleted'=>1),array('id'=>$item['CreditLogs']['id']));
                    App::import('Model', 'Credit.CreditBalances');
                    $this->CreditBalances = new CreditBalances();
                    $this->CreditBalances->addCredit($uid, $credit);
                }
            }
        }
        return;
    }

    public function afterShare($event)
    {
        $permission = $this->_checkPermission();
        if(!$permission) {
            return;
        }

        $data = $event->data['data'];
        if ($data['plugin']) {
            
            $object_id = $data['parent_id'];

            if(empty($data['item_type'])){
                // if($data['plugin'] == 'Business')
                    $object_type = 'core_activity';
            }else{
                $object_type = strtolower($data['item_type']);
            }
        }
        else {
            $object_type = 'core_activity';
            $object_id = $data['parent_id'];
        }


        $uid = MooCore::getInstance()->getViewer(true);
        App::import('Model', 'Credit.CreditActiontypes');
        $this->CreditActiontypes = new CreditActiontypes();
        $type = 'share';
        $action_type = $this->CreditActiontypes->getActionTypeFormModule($type);
        if (!empty($action_type) && $action_type['CreditActiontypes']['credit'] != 0) {
            App::import('Model', 'Credit.CreditLogs');
            $this->CreditLogs = new CreditLogs();
            // check credit max
            if ($this->CreditLogs->checkCredit($action_type, $uid)) {
                $action_id = $action_type['CreditActiontypes']['id'];

                $all_credits = $this->CreditLogs->getCredit($action_type,$uid);

                if ($action_type['CreditActiontypes']['max_credit'] - $all_credits >= $action_type['CreditActiontypes']['credit'])
                    $credit = $action_type['CreditActiontypes']['credit'];
                else
                    $credit = $action_type['CreditActiontypes']['max_credit'] - $all_credits;

                $this->CreditLogs->addLog($action_id, $credit, $object_type, $uid, $object_id);
                App::import('Model', 'Credit.CreditBalances');
                $this->CreditBalances = new CreditBalances();
                $this->CreditBalances->addCredit($uid, $credit);
            }
        }
    }

    public function beforeRender($event)
    {
        if (Configure::read('Credit.credit_enabled')) {
            $e = $event->subject();
            $min = '';
            if (Configure::read('debug') == 0) {
                $min = "min.";
            }
            $e->Helpers->Html->css(array(
                'Credit.main',
            ),
                array('block' => 'css')
            );

            $e->Helpers->Html->css(array(
                'global/typehead/bootstrap-tagsinput.css',
            ),
                array('block' => 'css','minify'=>false)
            );
            
            $e->Helpers->MooRequirejs->addPath(array(
                "mooCredit" => $e->Helpers->MooRequirejs->assetUrlJS("Credit.js/main.{$min}js"),
                "mooValidate" => $e->Helpers->MooRequirejs->assetUrlJS("js/jquery.validate.min.js")
            ));

            $e->Helpers->Html->scriptBlock(
                "require(['jquery','mooCredit'], function($,mooCredit) {\$(document).ready(function(){ mooCredit.init(); });});",
                array(
                    'inline' => false,
                )
            );

            $e->addPhraseJs(array(
                'validate_between' => __d('credit', 'Widthdraw amount must between Maximum and Minimum'),
                'credit_amount_not_enought' => __d('credit', "You do not have enough credit to post, please earn more credits to continue!"),
            ));

            if(ENABLE_WITHDRAW) {
                $config_formula = Configure::read("Credit.credit_convertion_formula");
                $minimum_withdrawal_amount = Configure::read("Credit.minimum_withdrawal_amount");
                $maximum_withdrawal_amount = Configure::read("Credit.maximum_withdrawal_amount");
                if ($config_formula) {
                    $tmp = explode('/', $config_formula);
                    $formula_credit = $tmp[0];
                    $formula_money = $tmp[1];
                } else {
                    $formula_credit = 0;
                    $formula_money = 0;
                }
                $e->Helpers->Html->scriptBlock(
                    'var formula_credit = ' . $formula_credit . ',
                     minimum_withdrawal_amount = ' . $minimum_withdrawal_amount . ',
                     maximum_withdrawal_amount = ' . $maximum_withdrawal_amount . ',
                     formula_money = ' . $formula_money . ',
                     url = "' . $e->request->base . '";'
                    ,
                    array(
                        'inline' => false,
                        'block' => 'script'
                    )
                );
            }else{
                $e->Helpers->Html->scriptBlock(
                    'var  url = " . $e->request->base . "'
                    ,
                    array(
                        'inline' => false,
                        'block' => 'script'
                    )
                );
            }

            $e->Helpers->MooPopup->register('themeModal');
        }
    }

    function splitAtUpperCase($s) {
        return preg_split('/(?=[A-Z])/', $s, -1, PREG_SPLIT_NO_EMPTY);
    }

    public function deleteUserContent($event){

        $permission = $this->_checkPermission();
        if(!$permission) {
            return;
        }

        $uid = $event->data['aUser']['User']['id'];

        // delete balances
        App::import('Model', 'Credit.CreditBalances');
        $this->CreditBalances = new CreditBalances();
        $this->CreditBalances->delete($uid);
        // delete logs
        App::import('Model', 'Credit.CreditLogs');
        $this->CreditLogs = new CreditLogs();
        $this->CreditLogs->deleteAll( array( 'user_id' => $uid ), true, true );
    }

    protected function _checkPermission() {
        $user = MooCore::getInstance()->getViewer();
        if ($user)
        {
            $params = explode(',', $user['Role']['params']);
            if (!in_array('credit_use', $params)) {
                return false;
            }
        }
        return true;

    }

    public function storage_geturl_local($e)
    {
        $v = $e->subject();
        $request = Router::getRequest();
        $oid = $e->data['oid'];
        $type = $e->data['type'];
        $thumb = $e->data['thumb'];
        $prefix = $e->data['prefix'];

        if ($e->data['thumb']) {
            $url = FULL_BASE_LOCAL_URL . $request->webroot . 'uploads/credit_ranks/photo/' . $oid . '/' . $prefix . $thumb;
        } else {
            $url = $v->getImage("credit/img/noimage/credit.png");
        }
        $e->result['url'] = $url;
    }

    public function storage_geturl_amazon($e)
    {
        $v = $e->subject();
        $type = $e->data['type'];
        $e->result['url'] = $v->getAwsURL($e->data['oid'], "credit_ranks", $e->data['prefix'], $e->data['thumb']);
    }

    public function storage_amazon_get_file_path($e)
    {
        $objectId = $e->data['oid'];
        $name = $e->data['name'];
        $thumb = $e->data['thumb'];
        $type = $e->data['type'];;
        $path = false;
        if (!empty($thumb)) {
            $path = WWW_ROOT . "uploads" . DS . "credit_ranks" . DS . "photo" . DS . $objectId . DS . $name . $thumb;
        }

        $e->result['path'] = $path;
    }

    public function storage_task_transfer($e)
    {
        $v = $e->subject();
        $ranksModel = MooCore::getInstance()->getModel('Credit.CreditRanks');
        $ranks = $ranksModel->find('all', array(
                'conditions' => array("CreditRanks.id > " => $v->getMaxTransferredItemId("credit_ranks")),
                'limit' => 10,
                'fields'=>array('CreditRanks.id'),
                'order' => array('CreditRanks.id'),
            )
        );

        if($ranks){
            foreach($ranks as $rank){
                if (!empty($rank["CreditRanks"]["thumbnail"])) {
                    $v->transferObject($rank["CreditRanks"]['id'],"credit_ranks",'',$rank["CreditRanks"]["photo"]);
                }
            }
        }
    }
}
