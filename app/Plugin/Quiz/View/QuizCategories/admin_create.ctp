<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<style>
    .quiz_permissions .checkbox-inline div.checker{
        margin: 0;
    }
    
    .quiz_permissions .checkbox-inline{
        padding: 0;
    }
</style>

<script type="text/javascript">
    $(document).ready(function() {
        $('#everyone').click(function() {
            if ($('#everyone').is(':checked')) {
                $('#permission_list li').hide();
                $('#permission_list li').first().show();
            }
            else
                $('#permission_list li').show();
        });
        
        $('#createButton').click(function() {
            var checked = false;
            $('#permission_list :checkbox').each(function() {
                if ($(this).is(':checked'))
                    checked = true;
            });

            if (!checked) {
                $(".error-message").show();
                $('.error-message').html('<?php echo __d('quiz', 'Please check at least one user role in the Permissions tab'); ?>');
                return;
            }

            disableButton('createButton');
            $.post("<?php echo  $this->request->base . '/admin/quiz/quiz_categories/save' ?>", $("#createForm").serialize(), function(data) {
                enableButton('createButton');
                var json = $.parseJSON(data);

                if (json.result === 1)
                    location.reload();
                else {
                    $(".error-message").show();
                    $(".error-message").html('<strong>Error!</strong> ' + json.message);
                }
            });

            return false;
        });
        
        $('#type').change(function () {
            mooAjax.post({
                url: '<?php echo  $this->request->base ?>/admin/categories/load_parent_categories/' + $('#type').val()
            }, function (data) {
                $('#parent_id').html(data);
            });
        });
    });

    function toggleField() {
        $('.opt_field').toggle();
    }
</script>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo __d('quiz', 'Add New'); ?></h4>
</div>
<div class="modal-body quiz_permissions">
    <form id="createForm" class="form-horizontal" role="form">
        <?php echo $this->Form->hidden('id', array('value' => $category['Category']['id'])); ?>
        <div class="form-body">
            <h4><?php echo __d('quiz', 'Settings'); ?></h4>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('quiz', 'Name'); ?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('name', array('placeholder' => __d('quiz', 'Enter text'), 'class' => 'form-control', 'value' => $category['Category']['name'])); ?>
                    <?php if (!$bIsEdit) : ?>
                    <div class="tips">*<?php echo  __d('quiz', 'You can add translation language after creating category'); ?></div>
                    <?php else : ?>
                    <div class="tips">
                        <a href="<?php echo  $this->request->base . '/admin/categories/ajax_translate/' . $category['Category']['id']; ?>" data-toggle="modal" data-target="#ajax-translate" ><?php echo  __d('quiz', 'Translation'); ?></a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $this->Form->hidden('type', array('value' => 'Quiz_Quiz')); ?>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('quiz', 'Header'); ?></label>
                <div class="col-md-9">
                    <div class="checkbox-list">
                        <label class="checkbox-inline">
                            <?php echo $this->Form->checkbox('header', array('checked' => $category['Category']['header'], 'onclick' => 'toggleField()', 'id' => 'cat_header')); ?>
                            <span class="help-block">
                                <?php echo __d('quiz', 'Category header is top level category which does not allow items to be posted'); ?>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group opt_field"<?php if ($category['Category']['header']): ?> style="display: none"<?php endif; ?>>
                <label class="col-md-3 control-label"><?php echo __d('quiz', 'Parent Category'); ?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->select('parent_id', $headers, array('class' => 'form-control', 'value' => $headers, 'empty' => array(0 => ''))); ?>
                </div>
            </div>
            <div class="form-group opt_field"<?php if ($category['Category']['header']): ?> style="display: none"<?php endif; ?>>
                <label class="col-md-3 control-label"><?php echo __d('quiz', 'Description');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->textarea('description', array('class' => 'form-control', 'value' => $category['Category']['description'])); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('quiz', 'Active');?></label>
                <div class="col-md-9">
                    <div class="checkbox-list">
                        <label class="checkbox-inline">
                            <?php echo $this->Form->checkbox('active', array('checked' => $category['Category']['active'])); ?>
                        </label>
                    </div>
                </div>
            </div>
            <hr>
            <h4><?php echo __d('quiz', 'Post Permission');?></h4>
            <?php echo $this->element('admin/permissions', array('permission' => $category['Category']['create_permission'])); ?>
        </div>
    </form>
    <div class="alert alert-danger error-message" style="display: none; margin-top: 10px;"></div>
</div>
<div class="modal-footer">
    <button type="button" class="btn default" data-dismiss="modal"><?php echo  __d('quiz', 'Close'); ?></button>
    <a href="#" id="createButton" class="btn btn-action"><?php echo  __d('quiz', 'Save'); ?></a>
</div>