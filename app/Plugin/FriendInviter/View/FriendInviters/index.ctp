<div class="bar-content">
<div class="content_center">

<div class="full_content p_m_10">

    <div id="id_manualcontacts" class="suggestion_inviter">
            <h1><?php echo __d('friend_inviter',"Invite friends by Email"); ?></h1>
        <form method="post" action="" id="invite_form">
            <textarea id="to" name="data[to]" cols="45" rows="1" placeholder="<?php echo __d('friend_inviter',"Add Email Addresses") ?>"></textarea>
            <input type="hidden" name="data[message]" id="message" value="" />
            <button class="btn btn-action" type="button" id="invite_btn"><?php echo __d('friend_inviter','Send') ?></button>
        </form>
        <div class="emaild_address_note"><?php echo __d('friend_inviter','Type email addresses above, separated by commas. Max 10 emails.') ?></div>
    </div>
    <div class="error-message" style="display: none;"></div>

    <div id="id_socialcontact">
        <div class="sub-title">
            <?php echo __d('friend_inviter',"Invite friends by Kakao Talk/Facebook"); ?>
        </div>
        <div class="share_form">
            <div class="share_copy_link">
<!--                <button class="btn btn-action" id="share_button">--><?php //echo __d('friend_inviter','Copy link') ?><!--</button>-->
                <a id="share_button""></a>
                <input id="share_url" type="text" value="<?php echo $invite_link ?>" />
                <p class="kakao-text"><?php echo __d('friend_inviter','Copy and paste to kakao talk or others') ?></p>
            </div>
            <div class="share-on-social">
                <div><a id="facebook_share" href="<?php echo $invite_link ?>"><?php echo __d('friend_inviter','Share on Facebook') ?></a></div>
                <div><a id="twitter_tweet" href="<?php echo $invite_link ?>"><?php echo __d('friend_inviter','Tweet on Twitter') ?></a></div>
            </div>
        </div>
    </div>
    <?php if (count($providers)): ?>
        <div id="id_show_networkcontacts" style="display:block"  class="suggestion_inviter">
            <div class="sub-title">
                <?php echo __d('friend_inviter',"Invite friend from your web account"); ?>
            </div>
            <div id="import_form">
                <?php
                if (isset($ers)) {
                    foreach ($ers as $key => $value) {
                        echo "<div><ul class='form-errors'><li><ul class='errors'><li>" . __d('friend_inviter',$value) . "</li></ul></li></ul></div>";
                    }
                }
                ?>
                <div class="provider_list">
                    <?php foreach ($providers as $p => $provider): ?>
                        <div>

                            <a class="logo_item smoothbox usingapi <?php echo $provider['title'] ?>"  title="<?php echo $provider['title'] ?>" href="<?php echo $this->request->base ?>/friend_inviters/getcontacts?provider=<?php echo $p ?>">
                                <img src='<?php echo $this->request->webroot ?>friend_inviter/img/<?php echo $provider['logo'] ?>.jpg'>
                            </a>
                            <span class="title"><?php echo __d('friend_inviter',$provider['title']) ?></span>

                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="sub-txt">
                <?php echo __d('friend_inviter',"Click on one of the above services to search from your Web Account."); ?>
                <br />
                <i class="icon-lock"></i>
                <?php echo __d('friend_inviter',"We will not store your account information."); ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="fi_referral_link">
        <div class="referral_link_title"><?php echo __d('friend_inviter',"Referral link"); ?></div>
        <div class="referral_link_content"><?php echo __d('friend_inviter',"This is your referral link you can copy and send it to your friend"); ?> <a href="javascript:void(0)"><?php echo $invite_link ?></a></div>
    </div>

<div id="id_csvcontacts" class="suggestion_inviter hidden">
    <div class="sub-title">
        <span><?php echo __d('friend_inviter','Contact Inviter') ?></span> 
        <?php echo __d('friend_inviter','Upload a contact files from an email application like Outlook, Apple Mail and others.') ?><br/>
        <?php echo __d('friend_inviter','File format must be .csv.') ?> <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "friend_inviters",
                                            "action" => "csv_guide",
                                            "plugin" => "FriendInviter"
                                        )),
             'title' => __d('friend_inviter','Learn more'),
             'innerHtml'=> __d('friend_inviter','Learn more'),
			 'class' => 'create_csv_guide'
     ));
 ?>		
    </div>
    <div class="upload-contact-file">
        <div class="op-cat"><?php echo __d('friend_inviter',"Contact file :"); ?></div>
        <div id="images-uploader">
            <div id="attachments_upload"></div>
            <a href="javascript:void(0)" class="button button-primary" id="getCSV"><?php echo __d('friend_inviter','Upload Queued Files') ?></a>
        </div>
        <form id='frm_getcsv' method="get" action="<?php echo $this->request->base ?>/friend_inviters/getcsvcontacts" enctype="multipart/form-data">
            <input type="hidden" value="" id="filename" name="filename">
        </form>

    </div>
</div>

<div id="" class="fi_referral_link">
    <div class="referral_link_title">
        <?php echo __d('friend_inviter',"View the progress of invite"); ?>
    </div>
    <div class="referral_link_content">
        <?php echo __d('friend_inviter',"Once you 've invited friends. You can %s",$this->html->link(__d('friend_inviter','view the status of your referrals.'),array('plugin' => 'friend_inviter', 'controller' => 'friend_inviters', 'action' => 'pending'))) ?>
    </div>    
</div>


<div id="id_success_frequ" style="display:none;">
    <ul class="form-notices" style="float:left;margin:0;"><li  style="width:350px;"><?php echo __d('friend_inviter',"Your friend request(s) have been successfully sent!"); ?></li></ul>
</div>
<div id="id_nonsite_success_mess" style="display:none;">
    <ul class="form-notices" style="float:left;margin:0;"><li style="width:680px;"><?php echo __d('friend_inviter',"Your invitation(s) were sent successfully. If the persons you invited decide to join, they will automatically receive a friend request from you."); ?></li></ul>
</div>		

<div class="suggestion_inviter" style="display:none;" id="network_friends">
    <div id="show_contacts"> </div>
</div>		

<div class="suggestion_inviter" style="display:none;" id="network_friends">
    <div id="show_contacts"> </div>
</div>		

<div class="suggestion_inviter" style="display:none;" id="csv_friends">
    <div id="show_contacts_csv"> </div>
</div>	
</div>
</div>
</div>
<?php
if($is232):
    $this->Html->css( array('FriendInviter.fi'),array('block' => 'css'));
    $this->MooRequirejs->addPath(array(
        "mooFriendinviter" => $this->MooRequirejs->assetUrlJS("FriendInviter.js/friendinviter.js", array('plugin' => true)),
        "mooFriendinviterTabContent" => $this->MooRequirejs->assetUrlJS("FriendInviter.js/TabContent.js", array('plugin' => true))
    ));

    $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooFriendinviter'), 'object' => array('$', 'mooFriendinviter')));
    ?>
        mooFriendinviter.initOnIndex();
    <?php $this->Html->scriptEnd(); ?>
<?php else: ?>    
    
    <?php  
        echo $this->Html->css(array('jquery.mp','FriendInviter.fi.css','fineuploader'), null, array('inline' => false));
        echo $this->Html->script(array('jquery.mp.min','jquery.fileuploader','FriendInviter.prev/friendinviter'), array('inline' => false)); 
    ?>
       
<?php endif; ?>
