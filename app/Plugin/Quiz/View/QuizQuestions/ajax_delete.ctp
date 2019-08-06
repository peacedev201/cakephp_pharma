<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>
<div class="modal-content">
    <div class="modal-header">
        <button data-dismiss="modal" class="close" type="button"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo __d('quiz', 'Please Confirm'); ?></h4>
    </div>
    <div class="modal-body">
        <?php echo $sMessage; ?>
    </div>
    <div class="modal-footer">
        <a class="btn btn-action" href="<?php echo $this->request->base . '/quizzes/question/delete/' . $aQuiz['Quiz']['id'] . '/' . $aQuestion['QuizQuestion']['id']; ?>"><?php echo __d('quiz', 'OK'); ?></a>
        <button data-dismiss="modal" class="btn" type="button"><?php echo __d('quiz', 'Cancel'); ?></button>
    </div>
</div>