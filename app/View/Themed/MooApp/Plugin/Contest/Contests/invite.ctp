<?php if ($this->request->is('ajax')): $this->setCurrentStyle(4) ?>
    <script type="text/javascript">
        require(["jquery", "mooContest"], function ($, mooContest) {
            mooContest.initContestInvite();
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooContest'), 'object' => array('$', 'mooContest'))); ?>
    mooContest.initContestInvite();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
<div class="title-modal">
    <?php echo __d('contest', 'Invite') ?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <p><?php echo __d("contest", "Enter your friends' emails below (separated by commas). Limit 10 email addresses per request") ?></p>
    <div class="create_form">

        <form id="sendMessage">
            <ul class="list6 list6sm2" style="position:relative">
                <?php echo $this->Form->hidden('contest_id', array('value' => $contest_id)); ?>
                <li>
                    <div class="col-sm-2">
                        <label><?php echo __d('contest', 'Emails') ?></label>
                    </div>
                    <div class="col-sm-10">
                        <?php echo $this->Form->textarea('emails'); ?>
                    </div>
                    <div class="clear"></div>
                </li>
                <li>
                    <div class="col-sm-2">
                        <label><?php echo __d('contest', 'Subject') ?></label>
                    </div>
                    <div class="col-sm-10">
                        <?php echo $this->Form->text('subject'); ?>
                    </div>
                    <div class="clear"></div>
                </li>
                <li>
                    <div class="col-sm-2">
                        <label><?php echo __d('contest', 'Message') ?></label>
                    </div>
                    <div class="col-sm-10">
                        <?php echo $this->Form->textarea('message', array('style' => 'height:120px')); ?>
                    </div>
                    <div class="clear"></div>
                </li>
                <li>
                    <div class="col-sm-2">
                        <label>&nbsp;</label>
                    </div>
                    <div class="col-sm-10">
                        <a href="javascript:void(0);" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored" id="sendButton"><?php echo __d('contest', 'Send Message') ?>
                        </a>
                    </div>
                    <div class="clear"></div>
                </li>
            </ul>
        </form>
    </div>
    <div class="error-message" style="display:none;"></div>
</div>