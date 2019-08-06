<?php if (!empty($uid)): ?>
<div id="fb-root"></div>
<?php if ($this->Moo->socialIntegrationEnable('facebook')): ?>
<script type="text/javascript">
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '<?php echo Configure::read('FacebookIntegration.facebook_app_id')?>',
            cookie     : true,  // enable cookies to allow the server to access
            // the session
            xfbml      : true,  // parse social plugins on this page
            version    : 'v2.1' // use version 2.1
        });
    };

    // Load the SDK asynchronously
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    function FBLogout(){

        FB.getLoginStatus(function(response) {
            if (response && response.status === 'connected') {
                FB.logout(function(response) {
                    
                    window.location ="<?php echo $this->request->base?>/users/do_logout";
                });
            }else{
                // To do:
                gapi.auth.signOut();
                window.location ="<?php echo $this->request->base?>/users/do_logout";
            }
        });
    }
</script>
<?php endif; ?>
<?php endif; ?>

<?php if (!empty($uid)): ?>
<div class="global-search">
    <input type="text" id="global-search" placeholder="<?php echo __('Search')?>">
    <ul id="display-suggestion" style="display: none" class="suggestionInitSlimScroll">

    </ul>
</div>
<div class="menu_large">
<div class="btn-group menu_acc_content">
    <a href="<?php echo $this->Moo->getProfileUrl( $cuser )?>">
        <?php echo $this->Moo->getImage(array('User' => $cuser), array("width" => "45px", "id" => "member-avatar", "alt" => $cuser['name'], 'prefix' => '50_square'))?>
    </a>
    <a class="dropdown-user-box dropdown-toggle" data-toggle="dropdown" href="#" >
        <i class="material-icons">expand_more</i>
    </a>

    <ul class="dropdown-menu" role="menu">
        <span class="arr-down"></span>
        <li class="user_ava_acc visible-xs">
            <?php echo $this->Moo->getImage(array('User' => $cuser), array("width" => "50px", "id" => "member-avatar", "alt" => $cuser['name'], 'prefix' => '50_square'))?>
        </li>
        <li>
            <ul class="menu_acc_option">
                <li class="visible-xs menu_acc_username"><?php echo h($this->Text->truncate($cuser['name'], 30, array('exact' => false)))?></li>
                <?php $hide_admin_link = Configure::read('core.hide_admin_link');
                    if ( $cuser['Role']['is_admin'] && empty( $hide_admin_link ) ): ?>
                <li class="hidden-xs hidden-sm"><a href="<?php echo $this->request->base?>/admin/home"><?php echo __('Admin Dashboard')?></a></li>
                <?php endif; ?>
                <li><a href="<?php echo $this->request->base?>/users/profile"><?php echo __('Profile Information')?></a></li>                        
<?php
	                        	$helperSubscription = MooCore::getInstance()->getHelper('Subscription_Subscription');
	                        	if ($helperSubscription->checkEnableSubscription() && $cuser['Role']['is_super'] != 1):
	                        ?>
	                        	<li><?php echo $this->Html->link(__('Subscription Management'), array('plugin' => 'subscription', 'controller' => 'subscribes', 'action' => 'upgrade')) ?></li>
	                        <?php endif;?>  
                <li><a href="<?php echo $this->request->base?>/users/avatar"><?php echo __('Change Profile Picture')?></a></li>
        <!--                        <li><a href="<?php echo  $this->Html->url(array('plugin' => 'social_integration', 'controller' => 'connect')) ?>"><?php echo __('Social Connect')?></a></li>-->
                <?php if ( $cuser['conversation_user_count'] > 0 ): ?>
                <li><a href="<?php echo $this->request->base?>/home/index/tab:messages"><?php echo __('New Messages (%s)', $cuser['conversation_user_count'])?></a></li>
                <?php endif; ?>
                <?php if ( $cuser['friend_request_count'] > 0 ): ?>
                <li><a href="<?php echo $this->request->base?>/home/index/tab:friend-requests"><?php echo __('Friend Requests') . " (<span id='friend_request_count'>" . $cuser['friend_request_count'] . "</span>)" ?></a></li>
                <?php endif; ?>
                <li><a href="<?php echo $this->request->base?>/home/index/tab:invite-friends"><?php echo __('Invite Friends')?></a>
                <li><a href="<?php echo $this->request->base?>/users/do_logout"><?php echo __('Log Out')?></a></li>
            </ul>
        </li>
    </ul>
    <div id="gSignOutWrapper" style="display:none">
        <div id="customBtn" class="customGPlusSignIn">
            <span class="icon"></span>
            <span class="buttonText">Sign out</span>
        </div>
    </div>
</div>
	                <?php if (empty($cuser['notification_count'])): ?>
	                <?php $this->Html->scriptStart(array('inline' => false,'requires'=>array('jquery','tinycon'),'object'=>array('$','Tinycon'))); ?>
	                $(document).ready(function(){Tinycon.setBubble(<?php echo $cuser['notification_count']?>);});
	                <?php $this->Html->scriptEnd(); ?>
                	<?php endif; ?>
</div>
<?php endif; ?>
<?php if (empty($uid)): ?>
<div class="guest-action">
    <?php if(Configure::read('core.disable_registration') != 1): ?>
         <a class="btn btn-success" href="<?php echo $this->request->base . '/users/register' ?>"> <?php echo __('Sign Up')?></a>
    <?php endif; ?>
         <a class="button" href="<?php echo $this->request->base . '/users/member_login' ?>"> <?php echo __('Login')?></a>

 </div>
<?php endif; ?>
<div class="mobile_menu_head">
<div class="show_menu_mobile">
<a id="openMenu" href="#" data-toggle="modal" data-target="#mobi_menu">
    
    <span class='line'></span>
    <span class='line'></span>
    <span class='line'></span>
</a><br/>
    <?php echo __('Menu') ?>
</div>
<?php if (!empty($uid)): ?>
<?php
	$helper = MooCore::getInstance()->getHelper('Subscription_Subscription');
	$subscribe = $helper->getSubscribeActive($cuser); 
	if ($subscribe):
?>
<div class="notify_section" class="btn-group">

    <div class="dropdown notify_content notification_show">
        <a class="dropdown-toggle <?php if (!empty($cuser['notification_count'])): ?>hasNotify<?php endif; ?>" href="#" id="notificationDropdown">
            <i class="material-icons">notifications</i>
            <span class="visible-xs visible-sm"><?php echo __('Notifications') ?></span>
            <?php if (!empty($cuser['notification_count'])): ?>
            <span class="notification_count">
            <?php echo $cuser['notification_count']?>
            </span>
            <?php endif; ?>

        </a>

        <ul class="dropdown-menu notification_list keep_open" id="notifications_list">

        </ul>

    </div>
    <!-- END GET NOTIFICATION -->
</div>

<div class="message_notify_section" class="btn-group">
     <!-- GET MSG -->
    <div class="dropdown notify_content conversation_content">
        <a class="dropdown-toggle <?php if (!empty($cuser['conversation_user_count'])): ?>hasNotify<?php endif; ?>" href="#" id="conversationDropdown">
            <i class="material-icons">forum</i>
            <span class="visible-xs visible-sm"><?php echo __('Messages') ?></span>
            <?php if (!empty($cuser['conversation_user_count'])): ?>
            <span class="conversation_count">
            <?php echo $cuser['conversation_user_count']?>
             </span>
            <?php endif; ?>

        </a>
        <ul class="dropdown-menu" id="conversation_list">
        </ul>

    </div>
    <!-- END GET MSG -->
</div>
<?php endif; ?>
<?php endif; ?>
<?php echo $this->element('footer_mobi'); ?>
</div>

<div id="mobi_menu">
    <!--Userbox-->
    <div class="navbar-form navbar-right main-menu-content">


            <?php if (!empty($uid)): ?>
            <!-- GET NOTIFICATION -->
		            <?php $this->Html->scriptStart(array('inline' => false,'requires'=>array('jquery','mooNotification'),'object'=>array('$','mooNotification'))); ?>
		            mooNotification.setUrl({
		            'show_notification': "<?php echo $this->request->base.'/notifications/show';?>",
		            'show_conversation': "<?php echo $this->request->base.'/conversations/show';?>",
		            'refresh_notification_url': "<?php echo $this->request->base.'/notifications/refresh';?>",
		            });
		            mooNotification.setInterval(<?php echo Configure::read('core.notification_interval'); ?>);
		            <?php $this->Html->scriptEnd(); ?>
            <?php else:?>
	            	<!-- GET NOTIFICATION -->
		            <?php $this->Html->scriptStart(array('inline' => false,'requires'=>array('jquery','mooNotification'),'object'=>array('$','mooNotification'))); ?>
		            mooNotification.setActive(false);
		            <?php $this->Html->scriptEnd(); ?>


              
                       
                

          
       
            <?php endif; ?>
        </div>
    
    <!--End  userbox-->
    <a class="btn_open_large" href="javascript:void(0)">
        <span class='arr-menu'></span>
            <span class='line'></span>
            <span class='line'></span>
            <span class='line'></span>
    </a>
    <div class="open_large_menu">
        <?php
        echo $this->Menu->generate('main-menu', null, array('class' => 'nav navbar-nav menu_top_list', 'id' => 'main_menu'));
        ?>
    </div>
</div>
