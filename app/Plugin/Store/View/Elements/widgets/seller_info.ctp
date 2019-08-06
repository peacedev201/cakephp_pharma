<?php if(!empty($store)):
    $business = $is_integrate_to_business && $store['business_id'] > 0 ? $this->Store->loadBusiness($store['business_id']) : array();
?>
<div class="box2">
    <div class="box_content seller_info">
        <?php if($is_integrate_to_business && !empty($business) && $business['Business']['status'] == 'approved'):
            $user = $business['User'];
            $business = $business['Business'];
            $hBusiness = MooCore::getInstance()->getHelper('Business_Business');
        ?>
            <div class="seller_image">
                <?php echo $hBusiness->getPhoto($business, array('class' => 'main_image', 'prefix' => '250_'));?>
                <div class="seller_verify">
                    <?php if($business['verify']):?>
                        <i class="verify_bus" title="<?php echo __d('store', 'Verify');?>"></i>
                    <?php else:?> 
                        <i class="unverify_bus" title="<?php echo __d('store', 'Unverify');?>"></i>
                    <?php endif;?>
                </div>
            </div>
            <ul class="list6">
                <li>
                    <label><?php echo  __d('store', 'Rating');?>:</label>
                    <div class="seller_review_star">
                        <span class="review_star">
                            <input readonly value="<?php echo $business['total_score']; ?>" type="number" class="rating form-control hide" data-stars="5" data-size="xs">
                        </span>
                        <a href="<?php echo $business['moo_hrefreview'];?>" class="review_counter">
                            (<?php echo $business['total_score'];?> / <?php echo $business['review_count'];?>)
                        </a>
                    </div>
                </li>
                <li>
                    <label><?php echo  __d('store', 'Seller');?>:</label>
                    <div>
                        <?php echo $store['name'];?>
                    </div>
                </li>
                <li>
                    <label><?php echo  __d('store', 'Business');?>:</label>
                    <div>
                        <a href="<?php echo $business['moo_href'];?>">
                            <?php echo $business['name'];?>
                        </a>
                    </div>
                </li>
                <?php if(!empty($business['email'])):?> 
                <li>
                    <label><?php echo  __d('store', 'Email');?>:</label>
                    <div>
                        <?php echo $business['email'];?>
                    </div>
                </li>
                <?php endif;?>
                <?php if(!empty($business['address'])):?> 
                <li>
                    <label><?php echo  __d('store', 'Address');?>:</label>
                    <div>
                        <?php echo $business['address'];?>
                    </div>
                </li>
                <?php endif;?>
                <?php if(!empty($business['phone'])):?> 
                <li>
                    <label><?php echo  __d('store', 'Phone');?>:</label>
                    <div>
                        <?php echo $business['phone'];?>
                    </div>
                </li>
                <?php endif;?>
                <?php if($uid > 0 && $uid != $store['user_id']):?>
                <li>
                    <?php if((!Configure::read('Chat.chat_disable') && Configure::read('Chat.chat_turn_on_notification') && Configure::read('core.send_message_to_non_friend') && $store_user['User']['receive_message_from_non_friend']) ||
                             (!Configure::read('Chat.chat_disable') && Configure::read('Chat.chat_turn_on_notification') && $are_friend)):
                    ?>
                        <a href="javascript:void(0)" class="btn btn-action padding-button" id="btn_contact_seller" onclick="require(['mooChat'],function(chat){chat.openChatWithOneUser(<?php echo $store['user_id'];?>)});">
                            <?php echo  __d('store', 'Contact Seller');?>
                        </a>
                    <?php else:?>
                        <a href="javascript:void(0)" class="btn btn-action padding-button ask_seller" id="btn_contact_seller" data-userid="<?php echo $store['user_id'];?>">
                            <?php echo  __d('store', 'Contact Seller');?>       
                        </a>
                    <?php endif;?>
                </li>
                <?php endif;?>
            </ul>
        <?php else:?>
            <div class="seller_image">
                <img alt="<?php echo $store['name'];?>" class="main_image" src="<?php echo $this->Store->getStoreImage($store);?>">
            </div>
            <ul class="list6">
                <li>
                    <label><?php echo  __d('store', 'Seller');?>:</label>
                    <div>
                        <?php echo $store['name'];?>
                    </div>
                </li>
                <?php if(!empty($store['email'])):?> 
                <li>
                    <label><?php echo  __d('store', 'Email');?>:</label>
                    <div>
                        <?php echo $store['email'];?>
                    </div>
                </li>
                <?php endif;?>
                <?php if(!empty($store['address'])):?> 
                <li>
                    <label><?php echo  __d('store', 'Address');?>:</label>
                    <div>
                        <?php echo $store['address'];?>
                    </div>
                </li>
                <?php endif;?>
                <?php if(!empty($store['phone'])):?> 
                <li>
                    <label><?php echo  __d('store', 'Phone');?>:</label>
                    <div>
                        <?php echo $store['phone'];?>
                    </div>
                </li>
                <?php endif;?>
                <?php if($uid > 0 && $uid != $store['user_id']):?>
                <li>
                    <?php if((!Configure::read('Chat.chat_disable') && Configure::read('Chat.chat_turn_on_notification') && Configure::read('core.send_message_to_non_friend') && $store_user['User']['receive_message_from_non_friend']) ||
                             (!Configure::read('Chat.chat_disable') && Configure::read('Chat.chat_turn_on_notification') && $are_friend)):
                    ?>
                        <a href="javascript:void(0)" class="btn btn-action padding-button" id="btn_contact_seller" onclick="require(['mooChat'],function(chat){chat.openChatWithOneUser(<?php echo $store['user_id'];?>)});">
                            <?php echo  __d('store', 'Contact Seller');?>
                        </a>
                    <?php else:?>
                        <a href="javascript:void(0)" class="btn btn-action padding-button ask_seller" id="btn_contact_seller" data-userid="<?php echo $store['user_id'];?>">
                            <?php echo  __d('store', 'Contact Seller');?>       
                        </a>
                    <?php endif;?>
                </li>
                <?php endif;?>
            </ul>
        <?php endif;?>
    </div>
</div>
<?php endif;?>