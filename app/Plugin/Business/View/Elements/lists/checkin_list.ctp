<?php if($checkins != null):
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $businessHelper->is_app = $is_app;
?>
    <?php if ($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery", "mooBehavior", "mooBusiness"], function($, mooBehavior, mooBusiness) {
            mooBehavior.initMoreResults();
        });
    </script>
    <?php endif?>

    <?php foreach($checkins as $checkin):
        $business = $checkin['Business'];
        $user = $checkin['User'];
        $user_tagging = $checkin['UserTagging'];
        $checkin = $checkin['BusinessCheckin'];
    ?>
        <div class="col-md-12">
            <div class="checkin-round">
                <?php
                    echo $this->Moo->getItemPhoto(array(
                        'User' => $user), 
                    array( 
                        'prefix' => '50_square'
                    ), array(
                        'class' => 'img_wrapper2'
                    ));
                ?>
                <div class="bus_review_info">
                    <div class="user_review">
                        <?php echo $this->Moo->getName($user)?>
                        <?php echo __d('business', ' checked in');?>
                        <?php //echo $business['name'];?>
                        <?php if(!empty($user_tagging['users_taggings'])){ $businessHelper->with($user_tagging['id'], $user_tagging['users_taggings']);}; ?>
                    </div>
                    <span class="feed-time date">
                        <?php if(!empty($just_now)):?>
                            <?php echo __d('business', 'Just now')?>
                        <?php else:?>
                            <?php echo $this->Moo->getTime($checkin['created'], Configure::read('core.date_format'), $utz )?>
                        <?php endif;?>
                    </span>
                    <div class="bus_user_info">
                        <?php echo $this->viewMore(h($checkin['content']),null, null, null, true, array('no_replace_ssl' => 1));?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;?>
    <?php if(count($checkins) == Configure::read('Business.business_people_checkin_items')):?>
        <?php $this->Html->viewMore($more_url, 'checkin-content') ?>
    <?php endif;?>
<?php else:?>
	<?php echo '<div class="clear text-center">' . __d('business', 'No more results found') . '</div>';?>
<?php endif;?>