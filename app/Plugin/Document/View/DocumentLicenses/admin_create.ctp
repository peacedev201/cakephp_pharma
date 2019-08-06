<script>
$(document).ready(function () {
    $('#createButton').click(function () {        
        disableButton('createButton');
        $.post("<?php echo  $this->request->base ?>/admin/document/document_licenses/save", $("#createForm").serialize(), function (data) {
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
});
</script>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo __d('document','Add New');?></h4>
</div>
<div class="modal-body">
    <form id="createForm" class="form-horizontal" role="form">
        <?php echo $this->Form->hidden('id', array('value' => $license['DocumentLicense']['id'])); ?>
        <div class="form-body">
            <h4><?php echo __d('document','License');?></h4>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('document','Title');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('title', array('placeholder' => __d('document','Enter text'), 'class' => 'form-control', 'value' => $license['DocumentLicense']['title'])); ?>
                </div>
            </div>   
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('document','Url');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('url', array('placeholder' => __d('document','Enter text'), 'class' => 'form-control', 'value' => $license['DocumentLicense']['url'])); ?>
                </div>
            </div> 
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('document','Name');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('name', array('placeholder' => __d('document','Enter text'), 'class' => 'form-control', 'value' => $license['DocumentLicense']['name'])); ?>
                </div>
            </div>            
        </div>
    </form>
    <div class="alert alert-danger error-message" style="display:none;margin-top:10px;">
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn default" data-dismiss="modal"><?php echo  __d('document','Close') ?></button>
    <a href="#" id="createButton" class="btn btn-action"><?php echo  __d('document','Save') ?></a>
</div>