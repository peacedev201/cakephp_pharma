<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8"/>
        <title>
            <?php if (Configure::read('core.site_offline')) echo __('[OFFLINE]'); ?>

            <?php if (isset($title_for_layout) && $title_for_layout) {
                echo $title_for_layout;
            } else if (isset($mooPageTitle) && $mooPageTitle) {
                echo $mooPageTitle;
            } ?>
        </title>
        <?php echo $this->Html->css(array('https://fonts.googleapis.com/icon?family=Material+Icons'), array('block' => 'mooAppOptimizedCss','minify'=>false)); ?>
        <?php $this->getEventManager()->dispatch(new CakeEvent('MooView.beforeMooAppOptimizedCssRender', $this));?>
        <?php echo $this->fetch('mooAppOptimizedCss'); ?>
    </head>
    <body> <?php //echo $this->fetch('content'); ?>
    <?php echo $this->fetch('mooAppOptimizedContent'); ?>
    <script type="text/javascript">
        <?php
        $mooConfig = array(
            'url' => array(
                'base' => $this->request->base,
                'webroot' => $this->request->webroot,
            	'domain' => $domain_url,
                'full' => FULL_BASE_URL),
            'language' => Configure::read('Config.language'),
            'language_2letter' => MooLangISOConvert::getInstance()->lang_iso639_2t_to_1(Configure::read('Config.language')),
            'autoLoadMore' => Configure::read('core.auto_load_more'),
            'sizeLimit' => $this->sizeLimit,
            'videoMaxUpload' => $this->videoMaxUpload,
            'isMobile' => $this->isMobile,
            'photoExt' => $this->photoExt,
            'videoExt' => $this->videoExt,
            'attachmentExt' => $this->attachmentExt,
            'comment_sort_style' => Configure::read('core.comment_sort_style'),
            'tinyMCE_language' => $this->tinyMCELanguageCode(Configure::read('Config.language')),
            'time_format' => Configure::read('core.time_format'),
            'isShowDislike'=>!Configure::read('core.hide_dislike'),
            'google_dev_key'=>Configure::read('core.google_dev_key'),
            'rtl' => isset($this->viewVars['site_rtl']) ? $this->viewVars['site_rtl'] : 0);
        if(isset($accessTokenData)){
               $mooConfig['access_token'] = $accessTokenData['access_token'];
        }
        $this->mooConfig =  $mooConfig;
        $this->getEventManager()->dispatch(new CakeEvent('MooView.beforeMooConfigJSRender', $this));
        $mooConfig = $this->mooConfig;
        $this->addPhraseJs(array(
            'notFound' => __("No more results found"),
            'myWall' => __("My Wall"),
            'friendWall' => __("Friend Wall"),
            'groupWall' => __("Group Wall"),
            'email' => __("Email"),
            'viewMoreComment' => __("View More Comments"),
                )
        );
        echo "var mooConfig = ".json_encode($mooConfig,true).";";
        echo "var mooPharse = ".json_encode($this->phraseJs,true).";";
        
        ?>
        <?php if (!empty($cuser) && !$cuser['confirmed'] && Configure::read('core.email_validation')) : ?>
            document.getElementById("resend_validation_link").addEventListener("click", resendValidation);
           function resendValidation() {

               var xhr = new XMLHttpRequest();
               xhr.open('POST', mooConfig.url.base + '/users/resend_validation', true);
               xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
               xhr.onload = function () {
                   var div = '<div class="confirm_send_email_validate" style="font-size: 15px;color: #f00;font-weight: 600;margin-top: 19px;"> <?php echo  __("Validation link has been resent.") ?> </div>';
                   document.getElementById('confirm_remindMessage').innerHTML += div;
               };
               xhr.send();
           }
           <?php endif; ?>    
            
    </script>
    <?php $this->getEventManager()->dispatch(new CakeEvent('MooView.beforeMooAppOptimizedScriptRender', $this));?>
    <?php echo $this->fetch('mooAppOptimizedScript'); ?>
    </body>
    </html>