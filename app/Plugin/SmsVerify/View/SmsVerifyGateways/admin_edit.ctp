<script>
    $(document).ready(function () {
        $('#createButton').click(function () {
            disableButton('createButton');
            $.post("<?php echo  $this->request->base ?>/admin/sms_verify/sms_verify_gateways/save/<?php echo $gateway['SmsVerifyGateway']['id']?>", $("#createForm").serialize(), function (data) {
                enableButton('createButton');
                var json = $.parseJSON(data);

                if (json.result == 1)
                    location.reload();
                else
                {
                    $(".error-message").show();
                    $(".error-message").html('<strong>Error!</strong>' + json.message);
                }
            });

            return false;
        });
    });
</script>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo __d('sms_verify','Edit gateway').' '.$gateway['SmsVerifyGateway']['name'];?></h4>
</div>
<div class="modal-body">
    <form id="createForm" class="form-horizontal" role="form">
        <div class="form-body">
        	<?php 
        		$params = json_decode($gateway['SmsVerifyGateway']['params'],true);
        		if (!$params)
        			$params = array();
        		
        	?>
        	<?php echo $this->element('gateway/'.$gateway['SmsVerifyGateway']['element'],array('params'=>$params));?>
        </div>
    </form>
    <div class="alert alert-danger error-message" style="display:none;margin-top:10px;">
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn default" data-dismiss="modal"><?php echo  __d('sms_verify','Close') ?></button>
    <a href="#" id="createButton" class="btn btn-action"><?php echo  __d('sms_verify','Save') ?></a>
</div>