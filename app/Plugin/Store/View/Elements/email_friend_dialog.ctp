<div class="modal-content">
    <div class="title-modal">
        <?php echo  __d('store', 'Email your friend') ?>              
        <button data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
    </div>
    <div class="modal-body">
        <div class="error-message" style="display:none;" id="emailFriendMessage"></div>
        <div class="create_form" style="margin-top: 5px;">
            <form id="emailFriendForm">
                <?php echo __d('store', 'Enter your friends\' emails below (separated by commas). Limit 10 email addresses per request');?>
                <?php echo $this->Form->hidden('product_id', array(
                    'value' => $product_id
                ));?>
                <ul style="position:relative" class="list6 list6sm2">
                    <li>
                        <div class="col-md-2">
                            <label><?php echo  __d('store', 'Recipients') ?></label>
                        </div>
                        <div class="col-md-10">
                            <?php echo $this->Form->textarea('recipients', array(
                                'div' => false,
                                'label' => false,
                            ));?>
                        </div>
                        <div class="clear"></div>
                    </li>
                    <li>
                        <div class="col-md-2">
                            <label><?php echo  __d('store', 'Message') ?></label>
                        </div>
                        <div class="col-md-10">
                            <?php echo $this->Form->textarea('message', array(
                                'div' => false,
                                'label' => false,
                            ));?>
                        </div>
                        <div class="clear"></div>
                    </li>
                    <li>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                        </div>
                        <div class="col-md-10 text-left">
                            <a id="emailFriendButton" class="btn btn-action padding-button" href="javascript:void(0)">
                                <?php echo  __d('store', 'Send') ?>
                            </a>
                            <a id="cancelEmailFriendButton" class="btn btn-action padding-button" href="javascript:void(0)" data-dismiss="modal">
                                <?php echo  __d('store', 'Cancel') ?>
                            </a>
                        </div>
                        <div class="clear"></div>
                    </li>
                </ul>
            </form>
        </div>
    </div>
</div>
