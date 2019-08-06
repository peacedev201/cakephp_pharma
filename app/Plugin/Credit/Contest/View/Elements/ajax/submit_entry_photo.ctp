<?php if ($this->request->is('ajax')) $this->setCurrentStyle(4); ?>
<?php if ($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery", "mooContest"], function ($, mooContest) {
            mooContest.initSubmitEntry();
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooContest'), 'object' => array('$', 'mooContest'))); ?>
    mooContest.initSubmitEntry();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
<?php $helper = MooCore::getInstance()->getHelper('Contest_Contest'); ?>
<div class="title-modal">
    <?php echo __d('contest', 'Photo Submission') ?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <div class="bar-content full_content p_m_10">
        <div class="content_center">
            <?php if ($helper->integrate_credit() && $contest['Contest']['submit_entry_fee'] > 0): ?>
                <p class="notice_entry_fee"><?php echo __d('contest', 'Entry Submission Fee: %s credit(s)', floatval($contest['Contest']['submit_entry_fee'])); ?></p>
                <?php
                $mCreditBalances = MooCore::getInstance()->getModel('Credit.CreditBalances');
                $result_current_balances = $mCreditBalances->getBalancesUser($viewer['User']['id']);
                $current_blances = 0;
                if (!empty($result_current_balances)) {
                    $current_blances = $result_current_balances['CreditBalances']['current_credit'];
                }
                if ($current_blances < $contest['Contest']['submit_entry_fee']):
                    ?>
                    <p class="notice_entry_fee"><?php echo __d('contest', 'Your current credit balance: %s not enough to submit entry on this contest, please check your credits balance', $current_blances); ?></p>
                <?php else: ?>
                    <form id="uploadEntryForm" action="<?php echo $this->request->base ?>/contests/entry_upload" method="post">
                        <div id="entry_upload"></div>
                        <div id="entry_preview">
                            <img width="150" style="display: none;" src="" />
                        </div>
                        <input type="hidden" name="thumbnail" id="thumbnail" value="">
                        <input type="hidden" name="contest_id" value="<?php echo $contest['Contest']['id']; ?>">
                        <?php echo $this->Form->textarea('caption', array('style' => 'margin-top: 10px;display:none; width: 100%;', 'value' => '', 'placeholder' => __d('contest', 'Caption'), 'class' => 'no-grow')) ?><br />
                        <input type="button" class="btn btn-action" id="nextStep" value="<?php echo __d('contest', 'Continue') ?>" style="display:none">
                        <div id="loadingSpin" style="display: inline-block; margin: 10px 0 10px 0;"></div>
                    </form>
                    <?php if ($photo_count > 0): ?>
                        <div style="margin-top: 8px;">
                            <p><?php echo __d('contest', 'Or') ?> <a href="<?php echo $this->request->base ?>/contests/select_photos/<?php echo $contest['Contest']['id']; ?>" class="btn btn-action"><?php echo __d('contest', 'Select your photos'); ?></a></p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else: ?>
                <form id="uploadEntryForm" action="<?php echo $this->request->base ?>/contests/entry_upload" method="post">
                    <div id="entry_upload"></div>
                    <div id="entry_preview">
                        <img width="150" style="display: none;" src="" />
                    </div>
                    <input type="hidden" name="thumbnail" id="thumbnail" value="">
                    <input type="hidden" name="contest_id" value="<?php echo $contest['Contest']['id']; ?>">
                    <?php echo $this->Form->textarea('caption', array('style' => 'margin-top: 10px;display:none; width: 100%;', 'value' => '', 'placeholder' => __d('contest', 'Caption'), 'class' => 'no-grow')) ?><br />
                    <input type="button" class="btn btn-action" id="nextStep" value="<?php echo __d('contest', 'Continue') ?>" style="display:none">
                    <div id="loadingSpin" style="display: inline-block; margin: 10px 0 10px 0;"></div>
                </form>
                <?php if ($photo_count > 0): ?>
                    <div style="margin-top: 8px;">
                        <p><?php echo __d('contest', 'Or') ?> <a href="<?php echo $this->request->base ?>/contests/select_photos/<?php echo $contest['Contest']['id']; ?>" class="btn btn-action"><?php echo __d('contest', 'Select your photos'); ?></a></p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>