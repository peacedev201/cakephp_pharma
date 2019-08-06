<div class="modal-content">    
    <div class="title-modal">
        <?php echo __d('gift', 'Send Gift');?>
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
    </div>
    <div class="modal-body">
        <?php 
            $gift = $gift['Gift'];
            echo $this->Form->create('Gift', array(
                'class' => 'form-horizontal', 
                'id' => 'createForm', 
                'role' => 'form'
            )); 
            echo $this->Form->hidden('id', array(
                'value' => $gift['id']
            ));
            ?>
            <ul class="list6 list6sm2" style="position:relative">
                <li>
                    <div class="col-md-2">
                        <label>
                            <?php echo __d('gift', 'Selected gift') ?>
                        </label>
                    </div>
                    <div class="col-md-10">
                        <?php echo $gift['title']; ?>
                    </div>
                    <div class="clear"></div>
                </li>
                <li>
                    <div class="col-md-2">
                        <label>
                            <?php echo __d('gift', 'Select a friend') ?>
                        </label>
                    </div>
                    <div class="col-md-10">
                        <?php echo $this->Form->hidden('friend_id'); ?>
                        <?php echo $this->Form->text('friend', array('placeholder' => 'Select a friend')); ?>
                    </div>
                    <div class="clear"></div>
                </li>
                <li>
                    <div class="col-md-2">
                        <label>
                            <?php echo __d('gift', 'Message') ?>
                        </label>
                    </div>
                    <div class="col-md-10">
                        <?php echo $this->Form->textarea('message', array('placeholder' => 'Message')); ?>
                    </div>
                    <div class="clear"></div>
                </li>
                <li>
                    <div class="col-md-12">
                        <a href="javascript:void(0)" onclick="jQuery.gift.sendGift()" class="button button-action post-gift" id="sendButton">
                            <?php echo __d('gift', 'Send now') ?>
                        </a>
                        <a href="javascript:void(0)" onclick="jQuery.gift.viewGift('', <?php echo $gift['id'];?>)" class="button button-action post-gift" id="previewButton">
                            <?php echo __d('gift', 'Preview') ?>
                        </a>
                        <a data-dismiss="modal" href="javascript:void(0)" class="button button-action post-gift">
                            <?php echo __d('gift', 'cancel');?>
                        </a>
                    </div>
                    <div class="clear"></div>
                </li>
            </ul>   
            <div style="display: none" class="error-message"></div>
        </form>
    </div>
</div>

<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>

jQuery.gift.initSuggestFriend();

<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>