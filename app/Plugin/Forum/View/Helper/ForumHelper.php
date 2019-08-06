<?php
App::uses('AppHelper', 'View/Helper');
class ForumHelper extends AppHelper
{
    public $helpers = array('Storage.Storage');

    public function support_extention()
    {
        $ext = Configure::read('Forum.forum_allowed_extentions');
        $ext = explode(',', $ext);
        if(empty($ext)){
            $ext = array('txt','doc','docx','xls','xlsx','ppt','pptx','pdf','ai','psd');
        }
        return $ext;
    }

    public function getIconForum($item) {
        return $this->Storage->getUrl($item['Forum']['id'], '', $item['Forum']['thumb'], "forum_thumb");
    }
    public function getIconForumCategory($item)
    {
        return $this->Storage->getUrl($item['ForumCategory']['id'], '', $item['ForumCategory']['thumb'], "forum_category_thumb");
    }

    public function getDocument($item)
    {
        return $this->Storage->getUrl($item[key($item)]['id'], '', $item[key($item)]['file_name'], "forum_files");
    }

    public function getTopicImage($item, $options = array())
    {
        $prefix = '100_';
        if (isset($options['prefix'])) {
            if ($options['prefix']) {
                $prefix = $options['prefix'] . '_';
            }else{
                $prefix = '';
            }
        }
        return $this->Storage->getUrl($item[key($item)]['id'], $prefix, $item[key($item)]['thumb'],"forum_topic_thumb");
    }

    public function getLockIcon($item)
    {
        return (!$item['Forum']['status'])? '<i class="material-icons" data-toggle="tooltip" title="'. __d('forum',"Locked forum, member can't post topic except moderators and site admin") .'">lock_outline</i>' : '';

    }

    public function getDisableIcon($item)
    {
        //link for edit
        //$link = '<a data-toggle="modal" data-target="#ajax"  href="';
        //$link .= "{$this->request->base}/admin/forum/forums/create/{$item['ForumCategory']['id']}/{$item['Forum']['id']}";
        //link for set status
        $link = '<a href="';
        $link .= "{$this->request->base}/admin/forum/forums/set_status_forum/{$item['Forum']['id']}"; //
        $link .= '"><i class="material-icons" data-toggle="tooltip" title="'. __d('forum',"No one can access the forum because of permission settings") .'">clear</i></a>';
        return (!$item['Forum']['status'])? $link : '';
    }
	
	public function getParamsPayment($item) {
    	$url = Router::url('/', true);
    	$params = array(
    		'cancel_url' => FULL_BASE_URL.$item['ForumTopic']['moo_href'],
    		'return_url' => $url . 'forum/forum_pins/success/'.$item['ForumTopic']['id'],
    		'currency' => $item['ForumPin']['currency'],
    		'description' => __d('forum', 'Pin topic with %s %s for %s days', $item['ForumPin']['amount'], $item['ForumPin']['currency'],$item['ForumPin']['time']),
    		'type' => 'Forum_Forum_Pin',
    		'id' => $item['ForumPin']['id'],
    		'is_recurring' => 0,
    		'amount' => $item['ForumPin']['amount'],
    		'first_amount' => $item['ForumPin']['amount'],
    		'total_amount' => $item['ForumPin']['amount']
    	);
    	
    	return $params;
    }
    
    public function onSuccessful($item, $data = array(), $price = 0, $txn = '', $recurring = false, $admin = 0) {
    	$pinModel = MooCore::getInstance()->getModel("Forum.ForumPin");
    	$pinModel->id = $item['ForumPin']['id'];
    	$pinModel->save(array(
    		'active' => 1,
    		'data' => json_encode($data)
    	));
    	
    	$topicModel = MooCore::getInstance()->getModel("Forum.ForumTopic");
    	$topicModel->id = $item['ForumPin']['forum_topic_id'];
    	$ping_expire = $item['ForumTopic']['ping_expire'];
    	if (!$ping_expire || (strtotime($ping_expire) < time()))
    	{
    		$ping_expire = date('Y-m-d H:i:s',strtotime('+ '.$item['ForumPin']['time'].' days'));
    	}
    	elseif ($ping_expire)
    	{
    		$ping_expire = date('Y-m-d H:i:s',strtotime($ping_expire.' + '.$item['ForumPin']['time'].' days'));
    	}
    	$topicModel->save(array(
    		'ping' => 1,
    		'ping_expire' => $ping_expire,
    		'sort_date' => date("Y-m-d H:i:s")
    	));
    	
    	$topic = $topicModel->findById($item['ForumPin']['forum_topic_id']);
    	
    	//send mail
    	$ssl_mode = Configure::read('core.ssl_mode');
    	$http = (!empty($ssl_mode)) ? 'https' :  'http';
    	
    	$mailComponent = MooCore::getInstance()->getComponent('Mail.MooMail');
    	$params = array(    		
    		'topic_link' => $http.'://'.$_SERVER['SERVER_NAME'].$topic['ForumTopic']['moo_href'],
    		'topic_title' => $topic['ForumTopic']['moo_title']
    	);
    	if($item['User']['id'] != $item['ForumTopic']['user_id']){
            $userModel = MooCore::getInstance()->getModel("User");
            $owner = $userModel->findById($item['ForumTopic']['user_id']);
            if(!empty($owner)) {
                $email = $owner['User']['email'];
            }else{
                $email = '';
            }
        }else{
    	    $email = $item['User']['email'];
        }
        if($email) {
            $mailComponent->send($email, 'forum_ping_topic', $params);
        }
    }

    public function onExpire($topic){
        if(!empty($topic)){
            $topicModel = MooCore::getInstance()->getModel("Forum.ForumTopic");
            $topicModel->id = $topic['ForumTopic']['id'];
            $topicModel->save(array(
                'ping' => 0,
                'ping_expire' => NULL,
                'sort_date' => NULL,
            ));

            $pinModel = MooCore::getInstance()->getModel("Forum.ForumPin");
            $pinInfo = $pinModel->find('first', array(
                'conditions' => array(
                    'ForumPin.forum_topic_id' => $topic['ForumTopic']['id'],
                    'ForumPin.active' => 1,
                )
            ));
            if(!empty($pinInfo)) {
                $pinModel->id = $pinInfo['ForumPin']['id'];
                $pinModel->save(array(
                    'active' => 0,
                ));
            }
        }
    }


    /**
     * Document: https://gist.github.com/neo22s/2584465
     * This function parses BBcode tag to HTML code (XHTML transitional 1.0)
     *
     * It parses (only if it is in valid format e.g. an email must to be
     * as example@example.ext or similar) the text with BBcode and
     * translates in the relative html code.
     *
     * @param string $text
     * @param boolean $advanced his var describes if the parser run in advanced mode (only *simple* bbcode is parsed).
     * @return string
     */
    public function bbcodetohtml($text, $advanced = FALSE, $charset = 'utf-8')
    {
        //special chars
        //$text = htmlspecialchars($text, ENT_QUOTES, $charset);
        /**
         * This array contains the main static bbcode
         * @var array $basic_bbcode
         */
        $basic_bbcode = array(
            '[b]', '[/b]',
            '[i]', '[/i]',
            '[u]', '[/u]',
            '[s]', '[/s]',
            '[ul]', '[/ul]',
            '[li]', '[/li]',
            '[ol]', '[/ol]',
        );
        /**
         * This array contains the main static bbcode's html
         * @var array $basic_html
         */
        $basic_html = array(
            '<b>', '</b>',
            '<i>', '</i>',
            '<u>', '</u>',
            '<s>', '</s>',
            '<ul>', '</ul>',
            '<li>', '</li>',
            '<ol>', '</ol>',
        );
        /**
         *
         * Parses basic bbcode, used str_replace since seems to be the fastest
         */
        $text = str_replace($basic_bbcode, $basic_html, $text);
        //advanced BBCODE
        if ($advanced) {
            /**
             * This array contains the advanced static bbcode
             * @var array $advanced_bbcode
             */
            $advanced_bbcode = array(
                '#\[color=([a-zA-Z]*|\#?[0-9a-fA-F]{6})](.+)\[/color\]#Usi',
                '#\[size=([0-9][0-9]?)](.+)\[/size\]#Usi',
                '#\[quote](\r\n)?(.+?)\[/quote]#si',
                //'#\[quote=(\d+);(\d+)](\r\n)?(.+?)\[/quote]#si',
                '#\[url](.+)\[/url]#Usi',
                '#\[url=(.+)](.+)\[/url\]#Usi',
                '#\[email]([\w\.\-]+@[a-zA-Z0-9\-]+\.?[a-zA-Z0-9\-]*\.\w{1,4})\[/email]#Usi',
                '#\[email=([\w\.\-]+@[a-zA-Z0-9\-]+\.?[a-zA-Z0-9\-]*\.\w{1,4})](.+)\[/email]#Usi',
                '#\[img](.+)\[/img]#Usi',
                '#\[img=(.+)](.+)\[/img]#Usi',
                '#\[code](\r\n)?(.+?)(\r\n)?\[/code]#si',
                '#\[youtube]http://[a-z]{0,3}.youtube.com/watch\?v=([0-9a-zA-Z]{1,11})\[/youtube]#Usi',
                '#\[youtube]([0-9a-zA-Z]{1,11})\[/youtube]#Usi'
            );



            /**
             * This array contains the advanced static bbcode's html
             * @var array $advanced_html
             */
            $advanced_html = array(
                '<span style="color: $1">$2</span>',
                '<span style="font-size: $1px">$2</span>',
                "<div class=\"quote\">\r\n$2</div>",
                //"<div class=\"quote\"><span class=\"quoteby\"><b>{$user}</b> <a>$2</a>:</span>\r\n$4</div>",
                '<a rel="nofollow" target="_blank" href="$1">$1</a>',
                '<a rel="nofollow" target="_blank" href="$1">$2</a>',
                '<a href="mailto: $1">$1</a>',
                '<a href="mailto: $1">$2</a>',
                '<img src="$1" alt="$1" />',
                '<img src="$1" alt="$2" />',
                '<div class="code">$2</div>',
                '<object type="application/x-shockwave-flash" style="width: 450px; height: 366px;" data="http://www.youtube.com/v/$1"><param name="movie" value="http://www.youtube.com/v/$1" /><param name="wmode" value="transparent" /></object>',
                '<object type="application/x-shockwave-flash" style="width: 450px; height: 366px;" data="http://www.youtube.com/v/$1"><param name="movie" value="http://www.youtube.com/v/$1" /><param name="wmode" value="transparent" /></object>'
            );
            $text = preg_replace($advanced_bbcode, $advanced_html, $text);

            /**
             * For Quote user
             */

            /**
             * @param $value 1 = topicID, 2 = null, 3 = description topic
             * @return string html quote
             */
            $user = function($value)
            {
                $topic = MooCore::getInstance()->getItemByType('Forum_Forum_Topic',$value[2]);
                $text_said = __d('forum','said');
                $text_from = __d('forum','From');
                if(!empty($topic) && !empty($topic['User']['id'])) {
                    $usr = "<a href='{$topic['User']['moo_href']}'>{$topic['User']['name']}</a>";
                }else{
                    $m_user = MooCore::getInstance()->getModel('User');
                    $user = $m_user->findById($value[1]);
                    if(!empty($user))
                    {
                        $usr = '<a href="'.$user['User']['moo_href'].'">'.$user['User']['name'].'</a>';
                    }
                    else
                    {
                        $usr = '<a><b>'. __d('forum','Deleted Account').'</b></a>';
                    }

                }

                if(!empty($topic))
                {
                    ($topic['ForumTopic']['parent_id'] == 0)? $link = "{$this->request->base}/forums/topic/view/{$topic['ForumTopic']['id']}":
                        $link = "{$this->request->base}/forums/topic/view/{$topic['ForumTopic']['parent_id']}/reply_id:{$topic['ForumTopic']['id']}";

                    return "<div class=\"quote\"><span class=\"quoteby\">{$text_from} <b>{$usr}</b> <a href='{$link}'>{$text_said}</a>:</span>\r\n{$value[4]}</div>";
                }
                else
                {
                    return "<div class=\"quote\"><span class=\"quoteby\">{$text_from} <b>{$usr}</b> {$text_said}:</span>\r\n{$value[4]}</div>";
                }

            };
            $pat = '#\[quote=(\d+);(\d+)](\r\n)?(.+?)\[/quote]#si';
            //'#\[quote=(\d+)](\r\n)?(.+?)\[/quote]#si';
            if(preg_match($pat,$text))
            {
                $text = preg_replace_callback($pat,$user,$text);
            }
            /**
             * end For Quote user
             */

            /**
             * For tag member
             */
            $member = function($value)
            {
                $user = MooCore::getInstance()->getItemByType('User',$value[1]);
                if(!empty($user))
                {
                    return "<b><a href='{$user['User']['moo_href']}' class='moocore_tooltip_link' data-item_type='user' data-item_id='{$user['User']['id']}'>@{$user['User']['name']}</a></b>";
                }
                else
                {
                    return "<b>@{$value[3]}</b>";
                }

            };
            $pat = '#\[user=(\d+)](\r\n)?(.+?)\[/user]#si';
            if(preg_match($pat,$text))
            {
                $text = preg_replace_callback($pat,$member,$text);
            }

        }
        //before return convert line breaks to HTML
        return $this->nl2br($text);
    }

    /**
     *
     * Inserts HTML line breaks before all newlines in a string
     * @param string $var
     * @return text
     */
    private function nl2br($var)
    {
        return str_replace(array('\\r\\n', '\r\\n', 'r\\n', '\r\n', '\n', '\r'), '<br />', nl2br($var));
    }

    public function parseTagMember($text)
    {
        $member = function($value)
        {
            $user = MooCore::getInstance()->getItemByType('User',$value[1]);
            if(!empty($user))
            {
                return "[user={$user['User']['id']}]{$user['User']['name']}[/user]";
            }
            else
            {
                return "[user={$value[1]}]{$value[3]}[/user]";
            }

        };
        $pat = '#\[user=([0-9])](\r\n)?(.+?)\[/user]#si';
        if(preg_match($pat,$text))
        {
            $text = preg_replace_callback($pat,$member,$text);
        }
        return $text;
    }


    protected $_moderator_forums = array();
    
    public function getModeratorFromForum($forum)
    {
    	if (isset($this->_moderator_forums[$forum['Forum']['id']]))
    	{
    		return $this->_moderator_forums[$forum['Forum']['id']];
    	}
    	$moderators = array();
    	if ($forum['Forum']['moderator'])
    	{
    		$moderators = explode(',',$forum['Forum']['moderator']);
    	}
    	
    	if ($forum['Forum']['parent_id'])
    	{
    		$forumModel = MooCore::getInstance()->getModel("Forum.Forum");
    		$forum_parent = $forumModel->findById($forum['Forum']['parent_id']);
    		if ($forum_parent && $forum_parent['Forum']['moderator'])
    		{
    			$moderators = array_merge($moderators,explode(',',$forum_parent['Forum']['moderator']));
    		}
    	}
    	$this->_moderator_forums[$forum['Forum']['id']] = $moderators;
    	return $this->_moderator_forums[$forum['Forum']['id']];
    }
    
    public function checkModerator($user,$forum)
    {
    	if (!$user || empty($user['User']))
    		return false;
    	
    	if (isset($user['Role']) && $user['Role']['is_admin'])
    		return true;

        if (isset($user['User']['Role']) && $user['User']['Role']['is_admin'])
            return true;
    	
    	$user_ids = $this->getModeratorFromForum($forum);
    	return in_array($user['User']['id'],$user_ids);
    }

    public function getPinInfo($topic_id){
        $pinModel = MooCore::getInstance()->getModel("Forum.ForumPin");
        $pinInfo = $pinModel->find('first', array(
            'conditions' => array(
                'ForumPin.forum_topic_id' => $topic_id,
                'ForumPin.active' => 1,
            )
        ));
        return $pinInfo;
    }

    public function round($num)
    {
        $num = intval($num);

        if($num > 999){
            $num = $this->cutNum($num / 1000, 1) . ' K+';
        }else{
            $num = number_format($num);
        }
        return $num;
    }

    public function cutNum($num, $precision = 2){
        return floor($num).substr($num-floor($num),1,$precision+1);
    }
    public function getItemSitemMap($name,$limit,$offset)
    {
        if (!MooCore::getInstance()->checkPermission(null, 'forum_view'))
            return null;

        $forumTopicModel = MooCore::getInstance()->getModel("Forum.ForumTopic");
        $topics = $forumTopicModel->find('all',array(
            'conditions' => array('ForumTopic.parent_id'=>0),
            'limit' => $limit,
            'offset' => $offset
        ));

        $urls = array();
        foreach ($topics as $topic)
        {
            $urls[] = FULL_BASE_URL.$topic['ForumTopic']['moo_href'];
        }

        return $urls;
    }

    public function isReplyRecaptchaEnabled(){
        $recaptcha_publickey = Configure::read('core.recaptcha_publickey');
        $recaptcha_privatekey = Configure::read('core.recaptcha_privatekey');

        if (  Configure::read('Forum.forum_enable_reply_captcha') && !empty($recaptcha_publickey) && !empty($recaptcha_privatekey) ){
            return true;
        }

        return false;
    }

    public function isCreateTopicRecaptchaEnabled(){
        $recaptcha_publickey = Configure::read('core.recaptcha_publickey');
        $recaptcha_privatekey = Configure::read('core.recaptcha_privatekey');

        if ( Configure::read('Forum.forum_enable_create_topic_captcha') && !empty($recaptcha_publickey) && !empty($recaptcha_privatekey) ){
            return true;
        }

        return false;
    }

    public function viewMore($string, $maxLength = null, $nl2br = true)
    {

        $textHelper = MooCore::getInstance()->getHelper('Core_Text');

        $shortText = $textHelper->truncate($string,$maxLength,array(
            'html' => true,
            'exact' => false,
        ));
        $fullText = $string;
        // MOOSOCIAL-1588
        if ($shortText == $fullText){
            // limit to 10 lines
            $limit_lines = 10;
            if (count(explode("\n", $fullText)) > $limit_lines){
                $shortText = '';
                $arr = array_slice(explode("\n", $fullText), 0, 10);
                foreach ($arr as $line){
                    $shortText .= $line;
                }
            }
        }

        if (strlen($fullText) <= strlen($shortText))
            return nl2br($fullText);
        // Do nl2br
        if( $nl2br ) {
            $shortText = nl2br($shortText);
            $fullText = nl2br($fullText);
        }

        $tag = 'div';
        $strLen = strlen($string);

        $content = '<'
            . $tag
            . ' class="view_more"'
            . '>'
            . $shortText
            . __('... &nbsp;')
            . '<a class="view_more_link" href="javascript:void(0);" onclick="$(this).parent().next().show();$(this).parent().hide();">'.__('more').'</a>'
            . '</'
            . $tag
            . '>'
            . '<'
            . $tag
            . ' class="view_more"'
            . ' style="display:none;"'
            . '>'
            . $fullText
            . ' &nbsp;'
        ;
        $content .= '</'
            . $tag
            . '>'
        ;

        return $content;
    }

    function stripBBCode($text_to_search) {
        $pattern = '|[[\/\!]*?[^\[\]]*?]|si';
        $replace = '';
        return preg_replace($pattern, $replace, $text_to_search);
    }
}