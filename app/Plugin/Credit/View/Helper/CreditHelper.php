<?php
App::uses('AppHelper', 'View/Helper');

class CreditHelper extends AppHelper
{
    public $helpers = array('Time', 'Text', 'Html', 'Form', 'Storage.Storage');

    private $_userTaggingScript = <<<javaScript
    
        var friends_str_replace_userTagging = new Bloodhound({
                        datumTokenizer:function(d){
                            return Bloodhound.tokenizers.whitespace(d.name);
                        },
                        queryTokenizer: Bloodhound.tokenizers.whitespace,
                        prefetch: {
                            url: '#urlSuggestion',
                            cache: false,
                            filter: function(list) {
            
                                return $.map(list.data, function(obj) {
                                    return obj;
                                });
                            }
                        },
                        
                        identify: function(obj) { return obj.id; },
        });
            
        friends_str_replace_userTagging.initialize();

        $('#str_replace_userTagging').tagsinput({
            freeInput: false,
            itemValue: 'id',
            itemText: 'name',
            typeaheadjs: {
                name: 'friends_str_replace_userTagging',
                displayKey: 'name',
                highlight: true,
                limit:10,
                source: friends_str_replace_userTagging.ttAdapter(),
                templates:{
                    notFound:[
                        '<div class="empty-message">',
                            '#str_replace_typeadadjs_notFound',
                        '</div>'
                    ].join(' '),
                    suggestion: function(data){
                    if($('#userTagging').val() != '')
                    {
                        var ids = $('#str_replace_userTagging').val().split(',');
                        if(ids.indexOf(data.id) != -1 )
                        {
                            return '<div class="empty-message" style="display:none">#str_replace_typeadadjs_notFound</div>';
                        }
                    }
                        return [
                            '<div class="suggestion-item">',
                                '<img alt src="'+data.avatar+'"/>',
                                '<span class="text">'+data.name+'</span>',
                            '</div>',
                        ].join('')
                    }
                }
            }
        });
javaScript;

    public function getImageRank($item, $options)
    {
        $request = Router::getRequest();
        $view = MooCore::getInstance()->getMooView();
        $prefix = '';
        if (isset($options['prefix'])) {
            $prefix = $options['prefix'] . '_';
        }
        $url = '';
        if (!empty($item) && $item[key($item)]['photo']) {
            return $this->Storage->getUrl($item[key($item)]['id'], $prefix, $item[key($item)]['photo'], "credit_ranks");
        } else {
            $url = FULL_BASE_URL . $this->assetUrl('Credit.noimage/credit.png', $options + array('pathPrefix' => Configure::read('App.imageBaseUrl')));
        }

        return $url;

    }

    public function getTextLikeItem($item)
    {
        if (empty($item)) {
            return '<span class="notice_red">' . __d('credit', 'deleted') . '</span>';
        }
        if ($item['Like']['type'] == 'activity' || $item['Like']['type'] == 'core_activity_comment' ) {
            return __d('credit', 'on feed');
        } elseif ($item['Like']['type'] == 'comment') {
            return __d('credit', 'comment');
        } elseif ($item['Like']['type'] == 'Photo_Photo') {
            return __d('credit', 'photo');
        } else {
            $model_item = MooCore::getInstance()->getModel($item['Like']['type']);
            $item = $model_item->findById($item['Like']['target_id']);
            if (empty($item)) {
                return '<span class="notice_red">' . __d('credit', 'deleted') . '</span>';
            }
            return '<a href="'. $item[$model_item->name]['moo_href'].'">'.$item[$model_item->name]['moo_title'].'</a>';
        }
    }

    public function getTextActivityCommentItem($item)
    {
        if (empty($item)) {
            return '<span class="notice_red">' . __d('credit', 'deleted') . '</span>';
        }
        App::import('Model', 'Activity');
        $model_activity = new Activity();
        $item_activity = $model_activity->findById($item['ActivityComment']['activity_id']);

        $content = "";

        if($item_activity['Activity']['parent_id'] != 0){
            $item_activity = $model_activity->findById($item_activity['Activity']['parent_id']);
        }

        switch($item_activity['Activity']['plugin']){
            case 'Group':
                if($item_activity['Activity']['content'] == "")
                    $content = $item_activity['Activity']['plugin'];
                else
                    $content = $item_activity['Activity']['content'];
                break;
            default :
                if($item_activity['Activity']['content'] == "")
                    $content = $item_activity['Activity']['plugin'];
                else
                    $content = $item_activity['Activity']['content'];
        }
        return $content;
    }

    public function getTextCommentItem($item)
    {
        if (empty($item)) {
            return '<span class="notice_red">' . __d('credit', 'deleted') . '</span>';
        }
        if ($item['Comment']['type'] == 'Photo_Photo') {
            return __d('credit', 'photo');
        }
        $model_item = MooCore::getInstance()->getModel($item['Comment']['type']);
        $item = $model_item->findById($item['Comment']['target_id']);
        if (!$item)
            return '<span class="notice_red">' . __d('credit', 'deleted') . '</span>';

        return '<a href="'. $item[$model_item->name]['moo_href'].'">'.$item[$model_item->name]['moo_title'].'</a>';
    }

    public function getObject($object_type, $object_id)
    {
        if ($object_type == 'like') {
            App::import('Model', 'Like');
            $model_like = new Like();
            $item = $model_like->findById($object_id);
            if (empty($item)) {
                return '<span class="notice_red">' . __d('credit', 'deleted') . '</span>';
            }
            if ($item['Like']['type'] == 'activity' || $item['Like']['type'] == 'core_activity_comment' ) {
                return __d('credit', 'on feed');
            } elseif ($item['Like']['type'] == 'comment') {
                return __d('credit', 'comment');
            } elseif ($item['Like']['type'] == 'Photo_Photo') {
                return __d('credit', 'photo');
            } else {
                $model_item = MooCore::getInstance()->getModel($item['Like']['type']);
                $item = $model_item->findById($item['Like']['target_id']);
                if (empty($item)) {
                    return '<span class="notice_red">' . __d('credit', 'deleted') . '</span>';
                }
                return array('title' => $item[$model_item->name]['moo_title'], 'href' => $item[$model_item->name]['moo_href']);
            }
        } elseif ($object_type == 'comment') {
            App::import('Model', 'Comment');
            $model_comment = new Comment();
            $item = $model_comment->findById($object_id);
            if (empty($item)) {
                return '<span class="notice_red">' . __d('credit', 'deleted') . '</span>';
            }
            if ($item['Comment']['type'] == 'Photo_Photo') {
                return __d('credit', 'photo');
            }
            $model_item = MooCore::getInstance()->getModel($item['Comment']['type']);
            $item = $model_item->findById($item['Comment']['target_id']);
            return array('title' => $item[$model_item->name]['moo_title'], 'href' => $item[$model_item->name]['moo_href']);
        } elseif ($object_type == 'activitycomment') {
            App::import('Model', 'ActivityComment');
            $model_comment = new ActivityComment();
            $item = $model_comment->findById($object_id);

            if (empty($item)) {
                return '<span class="notice_red">' . __d('credit', 'deleted') . '</span>';
            }
            App::import('Model', 'Activity');
            $model_activity = new Activity();
            $item_activity = $model_activity->findById($item['ActivityComment']['activity_id']);

            $content = "";

            if($item_activity['Activity']['parent_id'] != 0){
                $item_activity = $model_activity->findById($item_activity['Activity']['parent_id']);
            }

            switch($item_activity['Activity']['plugin']){
                case 'Group':
                    if($item_activity['Activity']['content'] == "")
                        $content = $item_activity['Activity']['plugin'];
                    else
                        $content = $item_activity['Activity']['content'];
                    break;
                default :
                    if($item_activity['Activity']['content'] == "")
                        $content = $item_activity['Activity']['plugin'];
                    else
                        $content = $item_activity['Activity']['content'];
            }
            return $content;
        } elseif ($object_type == 'friend') {
            App::import('Model', 'User');
            $model_user = new User();
            $user = $model_user->findById($object_id);
            return array('title' => $user[$model_user->name]['moo_title'], 'href' => $user[$model_user->name]['moo_href']);
        } else {
            if (!$object_id) {
                return;
            }
            if ($object_type == 'friendrequest') {
                return;
            }
            if ($object_type == 'groupuser') {
                $object_type = 'group';
            }
            $type = ucfirst($object_type) . '.' . ucfirst($object_type);
            if ($object_type == 'Activity') {
                $type = 'Activity';
            }
            $model = ucfirst($object_type);
            App::import('Model', $type);
            $model_item = new $model();
            $item = $model_item->findById($object_id);

            // nhson219
            $title = "";
            switch ($model){
                case 'Photo':
                    if( isset($item[$model_item->name]['title']) == "")
                        $title = __('Photo');
                    else
                        $title = $item[$model_item->name]['moo_title'];
                    break;
                case 'Blog':

                    if( isset($item[$model_item->name]['title']) == "")
                        $title = __('Blog');
                    else
                        $title = $item[$model_item->name]['moo_title'];
                    break;
                default :
                    if($type == 'Activity')
                        $title = $item[$model_item->name]['content'];
                    else
                        $title = $item[$model_item->name]['moo_title'];

            }

            if (empty($item)) {
                return '<span class="notice_red">' . __d('credit', 'deleted') . '</span>';
            } elseif ($type == 'Activity') {
                return array('title' => $title, 'href' => $this->request->base . '/users/view/' . $item[$model_item->name]['user_id'] . "/activity_id:" . $item[$model_item->name]['id']);
            } else {
                return array('title' => $title, 'href' => $item[$model_item->name]['moo_href']);
            }
        }
        return;
    }

    public function getObjectRemove($object_type, $object_id)
    {
        if ($object_type == 'groupuser') {
            $object_type = 'group';
            $type = ucfirst($object_type) . '.' . ucfirst($object_type);
            $model = ucfirst($object_type);
            App::import('Model', $type);
            $model_item = new $model();
            $item = $model_item->findById($object_id);
            if (empty($item)) {
                return __d('credit', 'Leaved group');
            }
            return __d('credit', 'Leaved group') . ' <a href="' . $item[$model_item->name]['moo_href'] . '">' . $item[$model_item->name]['moo_title'] . '</a>';

        }
        return __d('credit', 'Removed') . ' ' . __d('credit', $object_type);
    }

    public function memberOfRank($credit)
    {
        App::import('Model', 'Credit.CreditBalances');
        $model_balance = new CreditBalances();
        $count = $model_balance->find('count', array(
            'conditions' => array('current_credit >= ' => $credit)
        ));
        return $count;
    }

    public function doUpdateRankUser($credit = 0, $user_id = 0)
    {
        if ($credit == 0 || $user_id == 0)
            return false;

        App::import('Model', 'Credit.CreditRanks');
        App::import('Model', 'Credit.CreditBalances');

        $rank = new CreditRanks();
        $credit_balances = new CreditBalances();
        $credit_balances->clear();

        $ranks = $rank->find('all', array(
            'conditions' => array('enable' => 1)
        ));

        if ($ranks) {
            $rank_before = 0;
            $rank_after = 0;
            $rank_id = 0;
            foreach ($ranks as $item) {

                $rank_before = $item['CreditRanks']['credit'];

                if (($credit >= $rank_before)) {
                    $rank_id = $item['CreditRanks']['id'];
                } else if (($credit >= $rank_before && $credit <= $rank_after) || ($credit >= $rank_after && $credit <= $rank_before)) {
                    $rank_id = $item['CreditRanks']['id'] - 1;
                }
                $rank_after = $item['CreditRanks']['credit'];
            }

            $user_rank = $credit_balances->findById($user_id);

//            echo "<pre>";
//            print_r($user_rank);
//            print_r($rank_id);

//            $credit_balances->id = $user_id;
            $credit_balances->updateAll(
                    array('CreditBalances.rank_id' => $rank_id),
                    array('CreditBalances.id' => $user_id)

            );

            $rank_detail = $rank->find('first', array(
                'conditions' => array('id' => $rank_id)
            ));

            return (!empty($rank_detail) && ($user_rank['CreditBalances']['rank_id'] != $rank_id)) ? $rank_detail : false;
        }
        return false;


    }

    public function supportRecurring()
    {
        return false;
    }

    public function supportTrial()
    {
        return false;
    }

    public function checkSupportCurrency($currency)
    {
        return true;
    }

    public function getUrlProcess()
    {
        return '/credit/gateway/process';
    }

    public function hasAccountRefund()
    {
        return false;
    }

    public function refund($params)
    {
        $amount = $params['amount'];
        $user_id = $params['user_id'];
        $balancesModel = MooCore::getInstance()->getModel("Credit.CreditBalances");
        $balancesModel->addCredit($user_id, $amount, 'refund_credits');

        $uid = MooCore::getInstance()->getViewer(true);
        $logsModel = MooCore::getInstance()->getModel("Credit.CreditLogs");
        $logsModel->addLogByType('refund_credits', $amount, $user_id, 'user', $uid);

        return 2;
    }

    public function getParamsPayment($item) {
        $creditOrders = $item['CreditOrder'];

        $url = Router::url('/', true);
        $first_amount = 0;
        $currency = Configure::read('Config.currency');
        $params = array(
            'cancel_url' => $url . 'credits/cancel',
            'return_url' => $url . 'credits/success',
            'currency' => $currency['Currency']['currency_code'],
            'description' => __d('credit',"Buy Credits"),
            'type' => 'Credit_Credit_Order',
            'id' => $creditOrders['id'],
            'amount' => $creditOrders['price'],
            'total_amount' => $creditOrders['price'],
        );
        return $params;
    }
    public function onFailure($item, $data) {
        $cTransModel = MooCore::getInstance()->getModel('Credit.CreditOrders');

        $data = array(
            'status' => 'failed'
        );
        $cTransModel->id = $item['CreditOrders']['id'];
        $cTransModel->save($data);
    }

    public function onSuccessful($item, $data = array(), $price = 0, $recurring = false, $admin = 0) {
        $creditOrders = $item['CreditOrder'];

        $cTransModel = MooCore::getInstance()->getModel('Credit.CreditOrders');
        $data = array(
            'status' => 'completed'
        );
        $cTransModel->id = $creditOrders['id'];
        $cTransModel->save($data);

        $cBalanceModel = MooCore::getInstance()->getModel('Credit.CreditBalances');
        $cBalanceModel->addCredit($creditOrders['user_id'], $creditOrders['credit']);

        $logModel = MooCore::getInstance()->getModel('Credit.CreditLogs');
        $logModel->addLogByType('buy_credits', $creditOrders['credit'], $creditOrders['user_id'], 'user', $creditOrders['user_id']);
    }

    public function getEnable()
    {
        return Configure::read('Credit.credit_enabled');
    }

    public function memberSuggestion($members="", $id = "friendSuggestion"){
        $this->_View->loadLibrary('userTagging');

        $urlSuggestion = $this->Html->url(array("controller"=>"credits","action"=>"do_get_member","plugin"=>true),true).".json";
        $jsReplace = str_replace('#urlSuggestion',$urlSuggestion,$this->_userTaggingScript);
        $jsReplace = str_replace('str_replace_userTagging',$id,$jsReplace);
        $jsReplace = str_replace('#str_replace_typeadadjs_notFound',  addslashes(__('unable to find any member')),$jsReplace);
        $jsReplace = str_replace('#str_replace_container_userTagging_id','#userTagging-id-'.$id,$jsReplace);
        
        if($this->_View->isEnableJS('Requirejs')){
            
            $jsReplace = "require(['jquery','typeahead','bloodhound','tagsinput'], function($){".$jsReplace."});";
        }
        $this->_View->addInitJs($jsReplace);
        $out = $this->Form->input('friendSuggestion', array(
            'id' => $id,
            'value' => $members,
            'type' => 'text',
            'label' => false,
            'placeholder'=>  __d('credit', 'Member Name ?'),
            'div' => array(
                'class' => 'user-tagging-container',
            ),
          //  'after' =>'</div>',
        ));
        return $out;
    }
}
