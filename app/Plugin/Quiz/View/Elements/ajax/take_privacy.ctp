<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<div class="modal-header">
    <button data-dismiss="modal" class="close" type="button"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title"><?php echo __d('quiz', 'Please Confirm'); ?></h4>
</div>
<div class="modal-body">
    <div class="create_form">
        <form id="takePrivacyForm">
            <ul class="list6">
                <?php echo $this->Form->hidden('id', array('value' => $quiz['Quiz']['id'])); ?>
                <li>
                    <div class="col-sm-12">
                        <strong><?php echo __d('quiz', 'Please select view results privacy'); ?></strong>
                    </div>
                </li>
                <li>
                    <div class="col-sm-12">
                        <?php
                        echo $this->Form->select('privacy', array(
                            PRIVACY_PUBLIC => __d('quiz', 'Show me in participant list and share results'),
                            PRIVACY_PRIVATE => __d('quiz', "Show me in participant list but don't share results"),
                            PRIVACY_RESTRICTED => __d('quiz', 'Do not show in participant list')), 
                            array('empty' => false)
                        );
                        ?>
                    </div>
                    <div class="clear"></div>
                </li>
                <li>
                    <div class="col-sm-12">
                        <a href="javascript:void(0);" class="btn btn-action" id="startBtn"><?php echo __d('quiz', 'Start'); ?></a>
                        <a href="javascript:void(0);" class="button button-action" data-dismiss="modal"><?php echo __d('quiz', 'Cancel'); ?></a>
                    </div>
                    <div class="clear"></div>
                </li>
            </ul>
        </form>
    </div>
    <div style="display:none;" class="error-message"></div>
</div>
<div class="modal-footer">
    <strong><?php echo __d('quiz', 'Importance!'); ?></strong> <?php echo __d('quiz', 'You only can take quiz one time.'); ?>
</div>

<script type="text/javascript">
    require(["mooQuiz"], function(mooQuiz) {
        mooQuiz.initTakePrivacy();
    });
</script>