<?php if(Configure::read('core.force_login') && $cuser == null):?>
    <a class="feedback_shortcut" href="<?php echo $this->request->base;?>/users/member_login">
        FEEDBACK
    </a>
<?php else:?>
    <a class="feedback_shortcut" data-backdrop="static" href="<?php echo $this->request->base;?>/feedback/feedbacks/ajax_create">
        FEEDBACK
    </a>
<?php endif;?>
<style>
    .feedback_shortcut{
        -webkit-transform: rotate(270deg);
        -moz-transform: rotate(270deg);
        -o-transform:rotate(270deg);
        padding:0;
        font-size: 24px;
        line-height: 50px;
        bottom: 0;
        height: 50px;
        <?php if(Configure::read('Feedback.feedback_position') == 0):?>
        left: -60px;
        <?php else:?> 
        right: -59px;
        <?php endif;?>
        margin: auto;
        position: fixed;
        text-align: center;
        top: 0;
        width: 170px;
        <?php if(Configure::read('Feedback.feedback_button_color') != ''):?>
        background-color: <?php echo Configure::read('Feedback.feedback_button_color');?>;
        <?php else:?> 
        background-color: #0f75bd;
        <?php endif;?>
        <?php if(Configure::read('Feedback.feedback_text_color') != ''):?>
        color: <?php echo Configure::read('Feedback.feedback_text_color');?>;
        <?php else:?> 
        color: #FFFFFF;
        <?php endif;?>

        display: block;
        z-index: 1000;
    }
    .feedback_shortcut:focus ,
    .feedback_shortcut:hover{
        <?php if(Configure::read('Feedback.feedback_button_color_hover') != ''):?>
        background-color: <?php echo Configure::read('Feedback.feedback_button_color_hover');?>;
        <?php else:?> 
        background-color: red;
        <?php endif;?>
        <?php if(Configure::read('Feedback.feedback_text_hover_color') != ''):?>
        color: <?php echo Configure::read('Feedback.feedback_text_hover_color');?>;
        <?php else:?> 
        color: #FFFFFF;
        <?php endif;?>
        text-decoration: none;
    }
</style>