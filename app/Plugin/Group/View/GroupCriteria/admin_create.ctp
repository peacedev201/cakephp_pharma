<script>
    $(document).ready(function () {
        $('#everyone').click(function () {
            if ($('#everyone').is(':checked'))
            {
                $('#permission_list li').hide();
                $('#permission_list li').first().show();
            }
            else
                $('#permission_list li').show();
        });
        $('#createButton').click(function () {


            disableButton('createButton');
            $.post("<?php echo  $this->request->base ?>/admin/group/group_criteria/save", $("#createForm").serialize(), function (data) {
                enableButton('createButton');
                var json = $.parseJSON(data);

                if (json.result == 1)
                    location.reload();
                    // console.log(json.result);
                else
                {
                    $(".error-message").show();
                    $(".error-message").html('<strong>Error!</strong>' + json.message);
                }
            });

            return false;
        });

        $('#type').change(function () {
            mooAjax.post({
                url: '<?php echo  $this->request->base ?>/admin/categories/load_parent_categories/' + $('#type').val(),
            }, function (data) {
                
                $('#parent_id').html(data);
            });
        });

    });

    function toggleField()
    {
        $('.opt_field').toggle();
    }
</script>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo __('Create New Group');?></h4>
</div>
<div class="modal-body">
    <form id="createForm" class="form-horizontal" role="form">
        <div class="form-body">
            <h4><?php echo __('Settings')?></h4>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __('ID');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('id', array('placeholder' => __(''), 'class' => 'form-control', 'value' => $category['GroupsDefinition']['id'])); ?>

                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __('Community_ID');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('community', array('placeholder' => __('Enter Number'), 'class' => 'form-control', 'value' => $category['GroupsDefinition']['community'])); ?>

                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __('Name');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('name', array('placeholder' => __('Enter text'), 'class' => 'form-control', 'value' => $category['GroupsDefinition']['name'])); ?>

                </div>
                <?php if (!$bIsEdit) : ?>
                    <div class="tips" style="margin-left: 165px;">*<?php echo  __('You can add translation language after creating category') ?></div>
                <?php else : ?>
                    <div class="tips" style="margin-left: 165px;">
                        <?php
                            $this->MooPopup->tag(array(
                                    'href'=>$this->Html->url(array("controller" => "categories",
                                                                    "action" => "admin_ajax_translate",
                                                                    "plugin" => false,
                                                                    $category['GroupsDefinition']['id'],
                                                                                    
                                                                    )),
                                        'title' => __('Translation'),
                                        'innerHtml'=> __('Translation'),
                    ));
                ?>
                    
                    </div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __('Minimum No. of Member');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('minimum_no', array('placeholder' => __('Enter Number'), 'class' => 'form-control', 'value' => $category['GroupsDefinition']['minimum_no'])); ?>

                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __('Minimum aver points');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('minimum_ave_points', array('placeholder' => __('Enter Number'), 'class' => 'form-control', 'value' => $category['GroupsDefinition']['minimum_ave_points'])); ?>

                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __('Certificate');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('certificate', array('placeholder' => __('(0 = not required; 1= required)'), 'class' => 'form-control', 'value' => $category['GroupsDefinition']['certificate'])); ?>

                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __('Candidate List');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('candidate_list', array('placeholder' => __('(0 = not required; 1= required)'), 'class' => 'form-control', 'value' => $category['GroupsDefinition']['candidate_list'])); ?>

                </div>
            </div>
            
    </form>
    <div class="alert alert-danger error-message" style="display:none;margin-top:10px;">
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn default" data-dismiss="modal"><?php echo  __('Close') ?></button>
    <a href="#" id="createButton" class="btn btn-action"><?php echo  __('Save') ?></a>

</div>