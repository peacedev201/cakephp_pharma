
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo __d('credit','Change Credit Balance');?></h4>
</div>
<div class="modal-body">
    <form id="createForm" class="form-horizontal" role="form">
        <?php echo $this->Form->hidden('id', array('value' => $id)); ?>
        <div class="form-body">
            <p><?php echo __d('credit',"You can enter '+' or '-' to change current balance. The transaction will also transaction list");?></p>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('credit','Credit');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('credit', array('placeholder' => 'Enter number', 'class' => 'form-control')); ?>
                </div>
            </div>
        </div>
        <div class="alert alert-danger error-message" style="display:none;margin-top:10px;"></div>
        <div class="modal-footer">
            <button type="button" class="btn default" data-dismiss="modal"><?php echo  __d('credit','Close') ?></button>
            <a href="#" id="createButton" class="btn btn-action"><?php echo  __d('credit','Change') ?></a>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#createButton').click(function () {
            disableButton('createButton');
            $.post("<?php echo  $this->request->base ?>/admin/credit/credits/save", $("#createForm").serialize(), function (data) {
                enableButton('createButton');
                var json = $.parseJSON(data);

                if (json.result == 1)
                    location.reload();
                else
                {
                    $(".error-message").show();
                    $(".error-message").html('<strong>Error! </strong>' + json.message);
                }
            });

            return false;
        });
    });
</script>