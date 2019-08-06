
<?php $creditHelper = MooCore::getInstance()->getHelper('Credit_Credit');?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo __d('credit','Add new rank');?></h4>
</div>
<div class="modal-body">
    <form id="createForm" class="form-horizontal" role="form">
        <?php echo $this->Form->hidden('id', array('value' => $rank['CreditRanks']['id'])); ?>
        <?php echo $this->Form->hidden('photo', array('value' => $rank['CreditRanks']['photo'])); ?>
        <div class="form-body">
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('credit','Name');?> <span style="color:red">*</span></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('name', array('placeholder' => __d('credit','Enter text'), 'class' => 'form-control', 'value' => $rank['CreditRanks']['name'])); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('credit','Credit');?> <span style="color:red">*</span></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('credit', array('placeholder' => __d('credit','Enter number'), 'class' => 'form-control', 'value' => $rank['CreditRanks']['credit'])); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('credit','Description')?> <span style="color:red">*</span></label>
                <div class="col-md-9">
                    <?php echo $this->Form->textarea('description', array('class' => 'form-control', 'value' => $rank['CreditRanks']['description'])); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('credit','Icon');?></label>
                <div class="col-md-6">
                    <div id="select-0" style="margin: 10px 0 0 0px;"></div>
                    <?php if (!empty($rank['CreditRanks']['photo'])): ?>
                        <img width="100" src="<?php echo $creditHelper->getImageRank($rank, array('prefix' => '150_square'))?>" id="item-avatar" class="img_wrapper">
                    <?php else: ?>
                        <img width="100" src="" id="item-avatar" class="img_wrapper" style="display: none;">
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group" style="display: none;">
                <label class="col-md-3 control-label"></label>
                <div class="col-md-9">
                    <label class="checkbox-inline">
                        <?php echo $this->Form->checkbox('enable', array('checked' => 1)); ?>
                        <?php echo __d('credit','Enable');?>
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"></label>
                <div class="col-md-9">
                        <label class="checkbox-inline">
                            <?php echo $this->Form->checkbox('notify', array('checked' => $rank['CreditRanks']['notify'])); ?>
                            <?php echo __d('credit','Notify the members who reached to this new rank');?>
                        </label>
                </div>
            </div>
        </div>
        <div class="alert alert-danger error-message" style="display:none;margin-top:10px;"></div>
        <div class="modal-footer">
            <button type="button" class="btn default" data-dismiss="modal"><?php echo  __d('credit','Close') ?></button>
            <a href="#" id="createButton" class="btn btn-action"><?php echo  __d('credit','Save') ?></a>
        </div>
    </form>
</div>

<script type="text/javascript">
$(document).ready(function () {
    var errorHandler = function(event, id, fileName, reason) {
        if ($('.qq-upload-list .errorUploadMsg').length > 0){
            $('.qq-upload-list .errorUploadMsg').html('<?php echo __d('credit','Can not upload file more than ') . $file_max_upload?>');
        }else {
            $('.qq-upload-list').prepend('<div class="errorUploadMsg"><?php echo __d('credit','Can not upload file more than ') . $file_max_upload?></div>');
        }
        $('.qq-upload-fail').remove();
    };
    var uploader = new qq.FineUploader({
        element: $('#select-0')[0],
        multiple: false,
        text: {
            uploadButton: '<div class="upload-section"><i class="icon-camera"></i><?php echo __d('credit', 'Drag or click here to upload photo')?></div>'
        },
        validation: {
            allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
        },
        request: {
            endpoint: "<?php echo $this->request->base?>/credit/credit_upload/avatar"
        },
        callbacks: {
            onError: errorHandler,
            onComplete: function(id, fileName, response) {
                $('#item-avatar').attr('src', response.file_url);
                $('#item-avatar').show();
                $('#photo').val(response.file_path);
            }
        }
    });
    $('#createButton').click(function () {
        disableButton('createButton');
        $.post("<?php echo  $this->request->base ?>/admin/credit/ranks/save", $("#createForm").serialize(), function (data) {
            enableButton('createButton');
            var json = $.parseJSON(data);

            if (json.result == 1)
                location.reload();
            else
            {
                $(".error-message").show();
                $(".error-message").html('<strong><?php echo __d('credit','Error');?>! </strong>' + json.message);
            }
        });

        return false;
    });
    function toggleField()
    {
        $('.opt_field').toggle();
    }
});
</script>
<style type="text/css">
    .qq-drop-processing,
    .qq-upload-list  .qq-upload-fail {
        display: none;
    }
</style>
