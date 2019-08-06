<?php $helper = MooCore::getInstance()->getHelper("Question_Question");?>
<script>
$(document).ready(function () {
    $('#createButton').click(function () {        
        disableButton('createButton');
        $.post("<?php echo  $this->request->base ?>/admin/question/question_badges/save", $("#createForm").serialize(), function (data) {
            enableButton('createButton');
            var json = $.parseJSON(data);

            if (json.result == 1)
                location.reload();
            else
            {
                $(".error-message").show();
                $(".error-message").html(json.message);
            }
        });
        return false;
    });

    jQuery('.color-picker').miniColors({
    	change:function(hex, rgb){
    		jQuery('#console').prepend('change: ' + hex + ', rgb(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')<br>');
    	},
    	open:function(hex, rgb) {
    		jQuery('#console').prepend('open: ' + hex + ', rgb(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')<br>');
    	},
    	close:function(hex, rgb) {
    		jQuery('#console').prepend('close: ' + hex + ', rgb(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')<br>');
    	}
    });
});
</script>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo __d('question','Add a new Badge');?></h4>
</div>
<div class="modal-body">
    <form id="createForm" class="form-horizontal form_create_badge" role="form">
        <?php echo $this->Form->hidden('id', array('value' => $badge['QuestionBadge']['id'])); ?>
        <div class="form-body">
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('question','Name');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('name', array('placeholder' => __d('question','Enter text'), 'class' => 'form-control', 'value' => $badge['QuestionBadge']['name'])); ?>
                </div>
            </div>   
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('question','Background Color');?></label>
                <div class="col-md-7">
                    <?php echo $this->Form->text('background_color', array('class' => 'color-picker form-control', 'value' => $badge['QuestionBadge']['background_color'])); ?>
                </div>
            </div> 
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('question','Text Color');?></label>
                <div class="col-md-7">
                    <?php echo $this->Form->text('text_color', array('class' => 'color-picker form-control', 'value' => $badge['QuestionBadge']['text_color'])); ?>
                </div>
            </div> 
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('question','Point');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('point', array('class' => 'form-control', 'value' => $badge['QuestionBadge']['point'])); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('question','Permission');?></label>
                <div class="col-md-9">
                	<?php echo $this->Form->input('permission', array('label'=>false,'multiple' => 'checkbox', 'options' => $helper->_permissions,'class' => 'form-horizontal', 'selected' => explode(',',$badge['QuestionBadge']['permission']))); ?>
                </div>
            </div>          
        </div>
    </form>
    <div class="alert alert-danger error-message" style="display:none;margin-top:10px;">
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn default" data-dismiss="modal"><?php echo  __d('question','Close') ?></button>
    <a href="#" id="createButton" class="btn btn-action"><?php echo  __d('question','Save') ?></a>
</div>