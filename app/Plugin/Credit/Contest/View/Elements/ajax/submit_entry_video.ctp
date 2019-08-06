<?php

if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery", "mooContest"], function ($, mooContest) {
        mooContest.initSubmitVideo();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooContest'), 'object' => array('$', 'mooContest'))); ?>
mooContest.initSubmitVideo();
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<?php if($this->request->is('ajax')) $this->setCurrentStyle(4); ?>
<?php $helper = MooCore::getInstance()->getHelper('Contest_Contest'); ?>

<div class="title-modal">
    <?php echo __d('contest', 'Video Submission')?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <div class="bar-content full_content p_m_10">
        <div class="content_center">
            <div class="create_form">
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
                    <form id="createForm" action="<?php echo $this->request->base ?>/contests/entry_upload" method="post">
                        <input type="hidden" name="contest_id" value="<?php echo $contest['Contest']['id']; ?>">
                        <div id="fetchForm">
                        <?php echo __d('contest', 'Copy and paste the video url in the text field below'); ?><br /><br />
                            <ul class="list6 list6sm2">
                                <li>
                                    <div class="col-md-2">
                                        <label><?php echo __( 'Source')?></label>
                                    </div>
                                    <div class="col-md-10">
                                    <?php echo $this->Form->select( 'source', 
                                            array( VIDEO_TYPE_YOUTUBE => __d('contest', 'YouTube'), VIDEO_TYPE_VIMEO   => __d('contest', 'Vimeo') ),
                                            array( 'empty' => false )
                                      );
                                    ?>
                                    </div>
                                </li>
                                <li>
                                    <div class="col-md-2">
                                        <label><?php echo __d('contest', 'URL')?></label>
                                    </div>
                                    <div class="col-md-10">
                                    <?php echo $this->Form->text('url'); ?>
                                    </div>
                                </li>
                                <li>
                                    <div class="col-md-2">
                                        <label>&nbsp;</label>
                                    </div>
                                    <div class="col-md-10">
                                        <a href="#" class="button button-action" id="fetchButton"><?php echo __d('contest', 'Fetch Video')?></a>
                                    </div>

                                </li>
                            </ul>
                            <div class="error-message" style="display:none;margin-top:10px;"></div>
                        </div>
                        <div id="videoForm"></div>
                        <div class="clear"></div>
                    </form>
                    <?php if ($video_count > 0): ?>
                    <div style="margin-top: 8px;">
                        <p><?php echo __d('contest', 'Or') ?> <a href="<?php echo $this->request->base ?>/contests/select_videos/<?php echo $contest['Contest']['id']; ?>" class="btn btn-action"><?php echo __d('contest', 'Select your videos'); ?></a></p>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else: ?>
                <form id="createForm" action="<?php echo $this->request->base ?>/contests/entry_upload" method="post">
                    <div id="fetchForm">
                    <input type="hidden" name="contest_id" value="<?php echo $contest['Contest']['id']; ?>">
                    <?php echo __d('contest', 'Copy and paste the video url in the text field below'); ?><br /><br />
                        <ul class="list6 list6sm2">
                            <li>
                                <div class="col-md-2">
                                    <label><?php echo __( 'Source')?></label>
                                </div>
                                <div class="col-md-10">
                                <?php echo $this->Form->select( 'source', 
                                        array( 'youtube' => __d('contest', 'YouTube'), 'vimeo'   => __d('contest', 'Vimeo') ),
                                        array( 'empty' => false )
                                  );
                                ?>
                                </div>
                            </li>
                            <li>
                                <div class="col-md-2">
                                    <label><?php echo __d('contest', 'URL')?></label>
                                </div>
                                <div class="col-md-10">
                                <?php echo $this->Form->text('url'); ?>
                                </div>
                            </li>
                            <li>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                </div>
                                <div class="col-md-10">
                                    <a href="#" class="button button-action" id="fetchButton"><?php echo __d('contest', 'Fetch Video')?></a>
                                </div>

                            </li>
                        </ul>
                        <div class="error-message" style="display:none;margin-top:10px;"></div>
                    </div>
                    <div id="videoForm"></div>
                    <div class="clear"></div>
                </form>
                <?php if ($video_count > 0): ?>
                <div style="margin-top: 8px;">
                    <p><?php echo __d('contest', 'Or') ?> <a href="<?php echo $this->request->base ?>/contests/select_videos/<?php echo $contest['Contest']['id']; ?>" class="btn btn-action"><?php echo __d('contest', 'Select your videos'); ?></a></p>
                </div>
                <?php endif; ?>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>
