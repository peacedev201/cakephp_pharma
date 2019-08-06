<?php if ($uid): ?>
    <?php
    echo $this->Html->css(array('jquery-ui', 'footable.core.min','token-input'), null, array('inline' => false));
//echo $this->Html->script(array('jquery-ui', 'footable','jquery.tokeninput'), array('inline' => false));

    if(empty($title)) $title = __d('credit', 'Send Credits');

    if(isset($title_enable)&&($title_enable)=== "") $title_enable = false; else $title_enable = true;
    ?>
    
    <?php if(Configure::read("Credit.member_can_send_credit") == 'send_friend_only'): ?>

        <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('mooCredit'), 'object' => array('mooCredit'))); ?>
            mooCredit.initCreditSendToFriend();
        <?php $this->Html->scriptEnd(); ?>

    <?php else: ?>

        <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('mooCredit'), 'object' => array('mooCredit'))); ?>
            mooCredit.initCreditSendToMember();
        <?php $this->Html->scriptEnd(); ?>

    <?php endif; ?>

    <div class="box2 send_credit">
        <?php if($title_enable): ?>
            <h3><?php echo $title; ?></h3>
        <?php endif; ?>
        <div class="box_content ">
            <div class="create_form">
                <form id="sendCredits">
                    <ul class="list6 list6sm2" style="position:relative">
                        <li>
                            <div class="col-sm-12">
                                <label style="width: 100%;text-align: left;">
                                    <?php 
                                        if(Configure::read("Credit.member_can_send_credit") == 'send_friend_only'){
                                            echo __d('credit','Your Friend Name');
                                        }else{
                                            echo __d('credit','Member Name');
                                        }
                                    ?>
                                </label>
                            </div>
                            <div class="col-sm-12 credit_suggest_friend">
                                <?php 
                                    if(Configure::read("Credit.member_can_send_credit") == 'send_friend_only'){
                                        echo $this->Form->friendSuggestion();
                                    }else{
                                        $helperPlugin = MooCore::getInstance()->getHelper($plugin . '_' . $plugin);
                                        if (!$helperPlugin)
                                        {
                                            $this->_showError(__('Helper does not exist'));
                                            return;
                                        }

                                        echo $helperPlugin->memberSuggestion();
                                    }
                                ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-sm-2">
                                <label><?php echo __d('credit', 'Credit')?></label>
                            </div>
                            <div class="col-sm-12">
                                <?php echo $this->Form->text('credit'); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-sm-1">
                                <a href="#" class="btn btn-action" id="sendButton"><?php echo __d('credit','Send Credits')?>
                                </a>
                            </div>
                            <div class="clear"></div>
                        </li>
                    </ul>
                </form>
            </div>
            <div class="error-message" style="display:none;"></div>
            <div class="alert-success" style="display:none;"></div>
        </div>
    </div>
<?php endif;?>
