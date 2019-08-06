<?php echo $this->Html->script(array('tinymce/tinymce.min'), array('inline' => true)); ?>
<script>
    $(document).ready(function(){
        $('#createButton').click(function(){
            disableButton('createButton');
            $('#body-textarea').val(tinyMCE.activeEditor.getContent());
            $.post("<?php echo $this->request->base.'/admin/feedback/feedbacks/ajax_save';?>", $("#createForm").serialize(), function(data){
                enableButton('createButton');
                var json = $.parseJSON(data);

                if ( json.result == 1 )
                    location.reload();
                else
                {
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
            return false;
        });
        
        tinymce.init({
                selector: "textarea",
                language : mooConfig.tinyMCE_language,
                theme: "modern",
                plugins: [
                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                    "searchreplace wordcount visualblocks visualchars code fullscreen",
                    "insertdatetime media nonbreaking save table contextmenu directionality",
                    "emoticons template paste textcolor"
                ],
                toolbar1: "styleselect | bold italic | bullist numlist outdent indent | forecolor backcolor emoticons | link unlink anchor image media",
                image_advtab: true,
                height: 200,
                relative_urls : false,
                remove_script_host : true,
                menubar: false,
                document_base_url : '<?php echo FULL_BASE_URL . $this->request->root?>'
            });
    });
</script>
<?php
$tags_value = '';
if (!empty($tags)) $tags_value = implode(', ', $tags);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo __d('feedback', 'Edit feedback');?></h4>
</div>
<div class="modal-body">
    <?php echo  $this->Form->create('Feedback', array('class' => 'form-horizontal', 'id' => 'createForm', 'role' => 'form')); ?>
    <?php echo  $this->Form->hidden('id'); ?>
    <div class="form-body">
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo __d('feedback', 'Title');?></label>
            <div class="col-md-9">
                <?php echo  $this->Form->text('title',array('placeholder'=>__d('feedback','Title'), 'class'=>'form-control')); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo __d('feedback', 'Description');?></label>
            <div class="col-md-9">
                <?php echo  $this->Form->textarea('body',array('placeholder'=>__d('feedback','Description'),'class'=>'form-control', 'rows' => 3, 'id' => 'body-textarea')); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo __d('feedback', 'Tags');?></label>
            <div class="col-md-9">
                <?php echo  $this->Form->text('tags',array(
                    'placeholder'=>__d('feedback','Tags'),
                    'class'=>'form-control',
                    'value' => !empty($tags) ? implode(',', $tags) : ''
                )); ?>
            </div>
        </div>
        <?php if($aCategories): ?>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo __d('feedback', 'Category');?></label>
            <div class="col-md-9">
                <?php echo  $this->Form->select('category_id', $aCategories, array('class'=>'form-control')); ?>
            </div>
        </div>
        <?php endif ?>
        <?php if($aSeverities): ?>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo __d('feedback', 'Severity');?></label>
            <div class="col-md-9">
                <?php echo  $this->Form->select('severity_id', $aSeverities, array('class'=>'form-control'));?>
            </div>
        </div>
        <?php endif ?>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo __d('feedback', 'Feedback Visibility');?></label>
            <div class="col-md-9">
                <?php
                echo $this->Form->select('privacy', array(PRIVACY_EVERYONE => __d('feedback', 'Everyone'),
                    PRIVACY_FRIENDS => __d('feedback', 'Friends Only'),
                    PRIVACY_ME => __d('feedback', 'Only Me')), array(
                        'class'=>'form-control',
                        'empty' => false
                ));
                ?>
            </div>
        </div>
    </div>
    </form>
    <div class="alert alert-danger error-message" style="display:none;margin-top:10px;">

    </div>
</div>
<div class="modal-footer">

    <button type="button" class="btn default" data-dismiss="modal"><?php echo __d('feedback', 'Close');?></button>
    <a href="#" id="createButton" class="btn blue"><i class="icon-save"></i> <?php echo __d('feedback', 'Save');?></a>

</div>


