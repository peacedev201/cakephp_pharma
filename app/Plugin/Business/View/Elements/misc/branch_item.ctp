<?php if ($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery", "mooBehavior"], function($, mooBehavior) {
        mooBehavior.initMoreResults();
    });
</script>
<?php endif?>
<?php
$branchHelper = MooCore::getInstance()->getHelper('Business_Business');
?>
<?php if ($branch != null): ?>
    <ul class="bus_branch_list">
        <?php
            $branchPhotos = !empty($branch['Photos']) ? $branch['Photos'] : null;
            $branchPayments = !empty($branch['BusinessPayment']) ? $branch['BusinessPayment'] : null;
            $businessTime = !empty($branch['BusinessTime']) ? $branch['BusinessTime'] : null;
            $branch = $branch['Business'];
        ?>
        <li class="full_content bus_branch_item">
            <div class="branch_detail_top">
            <a class="bus_thumb" href="<?php echo $branch['moo_href'];?>">
                <?php echo $branchHelper->getPhoto($branch, array('prefix' => BUSINESS_IMAGE_THUMB_WIDTH.'_'));?>
            </a>
            <div class="bus-info">
                <a href="<?php echo $branch['moo_href'];?>" class="title">
                    <?php echo $branch['name'];?>                
                </a>
                <?php if($branch['user_id'] == $uid):?>
                <a href="<?php echo $this->request->base.'/businesses/dashboard/create_branch/'.$branch['parent_id'].'/'.$branch['id'];?>" class="btn btn-action btn-action-small">
                    <?php echo __d('business', 'Edit');?>
                </a>
                <?php endif;?>
                <div class="bus_extra_info">
                    <p>
                        <?php echo $branch['address']; ?>
                    </p>
                    <p>
                        <?php if(!empty($branch['website'])):?>
                            <?php echo __d('business', 'Website:');?>
                            <a href="<?php echo $this->Business->getFullUrl($branch['website']) ?>" target="blank">
                                <?php echo $branch['website'];?> 
                            </a>
                            <?php if(!empty($branch['phone']) || !empty($branch['fax'])):?>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                            <?php endif;?>
                        <?php endif;?>
                        <?php if(!empty($branch['phone'])):?>
                            <?php echo __d('business', 'Tel:');?>
                            <a class="business_info" href="javascript:void(0)" data-id="<?php echo $branch['id'];?>" data-task="tel">
                                <?php echo __d('business', 'view');?>
                            </a>
                            <?php if(!empty($branch['fax'])):?>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                            <?php endif;?>
                        <?php endif;?>                       
                        <?php if(!empty($branch['fax'])):?>
                            <?php echo __d('business', 'Fax:');?>
                            <a class="business_info" href="javascript:void(0)" data-id="<?php echo $branch['id'];?>" data-task="fax">
                                <?php echo __d('business', 'view');?>
                            </a>
                        <?php endif;?>
                    </p>
                    <?php if(!empty($branch['facebook']) || !empty($branch['twitter']) ||
                             !empty($branch['linkedin']) || !empty($branch['youtube']) ||
                             !empty($branch['instagram'])):
                    ?>
                    <p class="bus_social">
                        <span style="float: left;"><?php echo __d('business', 'Find us on:');?></span>
                        <?php if(!empty($branch['facebook'])):?>
                            <a href="<?php echo $this->Business->getFullUrl($branch['facebook']);?>" target="_blank">
                                <i class="bus_fb"></i>
                            </a>
                        <?php endif;?>
                        <?php if(!empty($branch['twitter'])):?>
                            <a href="<?php echo $this->Business->getFullUrl($branch['twitter']);?>" target="_blank">
                                <i class="bus_twitter"></i>
                            </a>
                        <?php endif;?>
                        <?php if(!empty($branch['linkedin'])):?>
                            <a href="<?php echo $this->Business->getFullUrl($branch['linkedin']);?>" target="_blank">
                                <i class="bus_linkedin"></i>
                            </a>
                        <?php endif;?>
                        <?php if(!empty($branch['youtube'])):?>
                            <a href="<?php echo $this->Business->getFullUrl($branch['youtube']);?>" target="_blank">
                                <i class="bus_utube"></i>
                            </a>
                        <?php endif;?>
                        <?php if(!empty($branch['instagram'])):?>
                            <a href="<?php echo $this->Business->getFullUrl($branch['instagram']);?>" target="_blank">
                                <i class="bus_instagram"></i>
                            </a>
                        <?php endif;?>
                    </p>
                    <?php endif;?>
                </div>
                
                
                
            </div>
            </div>
            <div class="clear"></div>
            <div class="col-md-6 p_r_10"> 
               
                
                 <div class="bus_description">
                    <?php echo $branch['description'];?>                                   
                </div>
                <?php if($branchPhotos != null):?>
                    <ul class="photo-list">
                        <?php echo $this->Element('Business.lists/photos_list', array(
                            'photos' => $branchPhotos
                        ));?>
                    </ul>
                <?php endif;?>
                <div class="clear"></div>
               
                
            </div>
            <div class="col-md-6">
                <div class="pull-right branch_list_action">
                    <div class="dropdown">
                        <!-- <a class="btn_show_hide btn_toggle" href="javascript:void(0)" data-hide_id="we_accept" data-hide_text="<?php echo __d('business','Hide Payments') ?>" data-show_text="<?php echo __d('business','Show Payments') ?>">
                            <?php echo __d('business','Payment methods') ?>
                        </a> -->
                        <a class="btn_toggle" href="javascript:void(0)" data-toggle="dropdown">
                            <?php echo __d('business','Payment methods') ?>
                        </a>
                        <div id="we_accept" class="dropdown-menu">
                            <p> <?php echo __d('business','We accept') ?></p>
                            <?php
                                echo $this->element('Business.misc/payment_type', array(
                                    'payments' => $branchPayments
                                ));
                            ?>
                        </div>
                    </div>
                    <div class="dropdown ">
                        <a class=" btn_toggle" href="javascript:void(0)" data-toggle="dropdown">
                            <?php echo __d('business','Working Hours') ?>
                               <!--  <i class="material-icons">keyboard_arrow_down</i> -->
                        </a>
                         <div class="dropdown-menu"  id="branch_time" >
                            <?php if($branch['always_open'] || empty($businessTime)):?>
                                <?php echo __d('business', 'Open 24/7');?>
                            <?php else:?>
                                <?php if(!empty($businessTime)):?>
                                <ul>
                                    <?php 
                                        $old_day = '';
                                        foreach($businessTime as $time):
                                            $shift = false;
                                            if($old_day != $time['day'])
                                            {
                                                $old_day = $time['day'];
                                            }
                                            else
                                            {
                                                $shift = true;
                                            }
                                    ?>
                                    <li <?php if(!empty($branch['open_today']['id']) && $branch['open_today']['id'] == $time['id']):?>style="font-weight: bold"<?php endif;?>>
                                        <div class="pull-left">
                                            <?php if(!$shift):?>
                                                <?php echo ucfirst($time['day']);?>
                                            <?php endif;?>
                                        </div>
                                        <div>
                                            <?php echo date('H:i', strtotime($time['time_open']));?> - <?php echo date('H:i', strtotime($time['time_close']));?>
                                            <?php if(!empty($branch['open_today']['id']) && $branch['open_today']['id'] == $time['id']):?>
                                                (<?php echo __d('business', 'Open now');?>)
                                            <?php endif;?>
                                        </div>
                                        <div class="clear"></div>
                                    </li>
                                    <?php endforeach;?>
                                </ul>
                                <?php endif;?>
                            <?php endif;?>
                        </div>                       
                   </div>
                    <span class="btn_toggle">
                        <!-- <i class="material-icons ">directions</i> -->
                        <a class="get_direction" data-address="<?php echo $branch['address']; ?>" href="javascript:void(0)">
                            <?php echo __d('business', 'Get directions');?>            
                        </a>
                    </span>
                </div>
                <div class="clear"></div>
                 <div class="rating_branch">
                <?php echo $this->element('Business.misc/rating_item', array(
                    'business' => array('Business' => $branch),
                    'rulers' => $rulers,
                    'hide_read_review' => 'review_content'
                ));?>
            </div>   
            </div>
            <div class="clear"></div>
            
                <ul id="review_content" class="list6 " style="display: none">
                    <?php echo $this->element('Business.lists/review_list', array(
                        'reviews' => $branch_reviews,
                        'more_review_url' => $more_review_url,
                        'can_reply_review' => $can_reply_review,
                        'can_delete_reply_review' => $can_delete_reply_review,
                        'can_edit_reply_review' => $can_edit_reply_review,
                    ));?>
                </ul>
        </li>
    </ul>
<?php else: ?>
    <?php echo __d('business', 'No sub pages found');?>
<?php endif; ?>
