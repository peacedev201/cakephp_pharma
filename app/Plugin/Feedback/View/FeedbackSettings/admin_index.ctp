<?php 
    __d('feedback', 'Customize feedback button position');
    __d('feedback', 'Left side of screen');
    __d('feedback', 'Right side of screen');
    __d('feedback', 'Feedback enabled');
    __d('feedback', 'Disable');
    __d('feedback', 'Enable');
    __d('feedback', 'Enable feedback button on desktop');
    __d('feedback', 'Yes');
    __d('feedback', 'No');
    __d('feedback', 'Send mail to emails');
    __d('feedback', 'Enable captcha');
    __d('feedback', 'Enable Feedback Hashtag');
    __d('feedback', 'How many minutes should a user wait before they can submit another feedback?');
    __d('feedback', 'Maximum feedback user can create');
    __d('feedback', 'Feedback per pages');
    __d('feedback', 'The number of feedbacks to display per page');
    __d('feedback', 'Customize feedback button hover color');
    __d('feedback', 'Customize feedback button text color');
    __d('feedback', 'Customize feedback button text hover color');
    __d('feedback', 'Customize feedback button color');
    __d('feedback', 'By pass force login');
    __d('feedback', 'Enable feedback button on mobile');
    __d('feedback', 'Enable display activity feed');
?>

<?php
    $this->Html->addCrumb(__d('feedback', 'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('feedback', 'Settings'), array('controller' => 'feedback_settings'));
    echo $this->Html->css(array(
        'jquery-ui', 
        'footable.core.min',
        'Feedback.colorpicker/colorpicker'), null, array('inline' => false));
    echo $this->Html->script(array(
        'jquery-ui', 
        'footable',
        'Feedback.colorpicker/colorpicker',
        'Feedback.colorpicker/eye',
        'Feedback.colorpicker/utils'), array('inline' => false));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Feedback'));
    $this->end();
?>
<?php echo $this->Moo->renderMenu('Feedback', __d('feedback', 'Settings'));?>
<?php echo $this->element('admin/setting');?>



<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>
    
    var feedback_button_color_id = '<?php echo $feedback_button_color_id;?>';
    var feedback_button_color_hover_id = '<?php echo $feedback_button_color_hover_id;?>';
    var feedback_text_color_id = '<?php echo $feedback_text_color_id;?>';
    var feedback_text_color_hover_id = '<?php echo $feedback_text_color_hover_id;?>';
    jQuery(window).load(function(){
        jQuery('#text' + feedback_button_color_id).ColorPicker({
            flat: false,
            color: jQuery('#text' + feedback_button_color_id).val(),
            onChange: function (hsb, hex, rgb) {
                $('#text' + feedback_button_color_id).val('#' + hex);
            }
        });
        jQuery('#text' + feedback_button_color_hover_id).ColorPicker({
            flat: false,
            color: jQuery('#text' + feedback_button_color_hover_id).val(),
            onChange: function (hsb, hex, rgb) {
                $('#text' + feedback_button_color_hover_id).val('#' + hex);
            }
        });
        jQuery('#text' + feedback_text_color_id).ColorPicker({
            flat: false,
            color: jQuery('#text' + feedback_text_color_id).val(),
            onChange: function (hsb, hex, rgb) {
                $('#text' + feedback_text_color_id).val('#' + hex);
            }
        });
        jQuery('#text' + feedback_text_color_hover_id).ColorPicker({
            flat: false,
            color: jQuery('#text' + feedback_text_color_hover_id).val(),
            onChange: function (hsb, hex, rgb) {
                $('#text' + feedback_text_color_hover_id).val('#' + hex);
            }
        });
    })
    
    
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>