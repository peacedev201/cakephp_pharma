
<script>
$(document).ready(function(){    
    $('#createButton').click(function(){
        disableButton('createButton');
        $.post("<?php echo $this->request->base.$admin_url.'ajax_save'?>", $("#createForm").serialize(), function(data){
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
});
</script>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">
        <?php if($this->request->data['GiftCategory']['id'] > 0):?>
            <?php echo __d('gift', 'Edit Category');?>
        <?php else:?>
            <?php echo __d('gift', 'Add Category');?>
        <?php endif;?>
    </h4>
</div>
<div class="modal-body">
<!-- <form id="createForm" class="form-horizontal" role="form"> -->
<?php echo  $this->Form->create('GiftCategory', array('class' => 'form-horizontal', 'id' => 'createForm', 'role' => 'form')); ?>
    <?php echo  $this->Form->hidden('id'); ?>
    <div class="form-body">
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo __d('gift', 'Name');?></label>
            <div class="col-md-9">
                <?php echo  $this->Form->text('name',array('placeholder'=>__d('gift', 'Name'),'class'=>'form-control')); ?>
            </div>
            <?php if (empty($this->request->data['GiftCategory']['id'])) : ?>
                <div class="tips" style="margin-left: 165px;">*<?php echo  __d('gift', 'You can add translation language after creating category') ?></div>
            <?php else : ?>
                <div class="tips" style="margin-left: 165px;">
                    <?php
                        $this->MooPopup->tag(array(
                            'href'=>$this->Html->url(array(
                                "controller" => "gift_categories",
                                "action" => "admin_ajax_translate",
                                "plugin" => "gift",
                                $this->request->data['GiftCategory']['id']
                            )),
                            'title' => __('Translation'),
                            'innerHtml'=> __('Translation') ,
                       ));
                   ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo __d('gift', 'Description');?></label>
            <div class="col-md-9">
                <?php echo  $this->Form->textarea('description',array('placeholder'=>__d('gift', 'Description'),'class'=>'form-control', 'rows' => 3)); ?>
            </div>
        </div> 
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo __d('gift', 'Enable');?></label>
            <div class="col-md-9">
                <?php echo  $this->Form->checkbox('enable'); ?>
            </div>
        </div>
        <!-- <hr>
        <h4>Post Permission</h4>
        <?php echo $this->element('admin/permissions', array('permission' => $category['Category']['create_permission'])); ?> -->
    </div>
</form>
    <div class="alert alert-danger error-message" style="display:none;margin-top:10px;">

    </div>

</div>
<div class="modal-footer">

    <button type="button" class="btn default" data-dismiss="modal"><?php echo __d('gift', 'Close');?></button>
    <a href="#" id="createButton" class="btn blue"><i class="icon-save"></i> <?php echo __d('gift', 'Save');?></a>

</div>