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
    <h4 class="modal-title"><?php echo __d('quiz', 'Please Confirm'); ?></h4>
</div>
<div class="modal-body">
    <form>
        <p><?php echo $sMessage; ?></p>
        
        <a href="javascript:void(0)" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored" id="deleteQuestion" data-url="<?php echo $this->request->base . '/quizzes/question/delete/' . $aQuiz['Quiz']['id'] . '/' . $aQuestion['QuizQuestion']['id']; ?>"><?php echo __d('quiz', 'Delete'); ?></a>
        <a href="javascript:void(0)" class="button button-action" type="button" data-dismiss="modal"><?php echo __d('quiz', 'Cancel'); ?></a>
    </form>
</div>

<script type="text/javascript">
    require(["mooQuiz"], function(mooQuiz) {
        mooQuiz.initOnDeleteQuestion();
    });
</script>