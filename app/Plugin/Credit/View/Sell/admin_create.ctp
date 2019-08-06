
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">
        <?php if($bIsEdit){echo __d('credit','Edit');}else{echo __d('credit','Add new');}?>
    </h4>
</div>
<div class="modal-body">
    <form id="createForm" class="form-horizontal" role="form">
        <?php echo $this->Form->hidden('id', array('value' => $sell['CreditSells']['id'])); ?>
        <div class="form-body">
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('credit','Credit');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('credit', array('placeholder' => __d('credit','Enter number'), 'class' => 'form-control', 'value' => $sell['CreditSells']['credit'])); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('credit','Price');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('price', array('placeholder' => __d('credit','Enter number'), 'class' => 'form-control', 'value' => $sell['CreditSells']['price'])); ?>
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
        $('#createButton').click(function () {
            disableButton('createButton');
            $.post("<?php echo  $this->request->base ?>/admin/credit/sell/save", $("#createForm").serialize(), function (data) {
                enableButton('createButton');
                var json = $.parseJSON(data);

                if (json.result == 1)
                    location.reload();
                else
                {
                    $(".error-message").show();
                    $(".error-message").html('<strong><?php echo __d('credit','Error') ?>! </strong>' + json.message);
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
