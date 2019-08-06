<div class="modal-header">
    <?php echo __d('business', 'Reject business');?>    
    <button data-dismiss="modal" class="close" type="button">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body">
    <div style="display:none;" class="error-message" id="rejectMessage"></div>
    <div class="create_form">
        <?php echo __d('business', 'Please enter reason why you want to reject this business');?>
        <form id="rejectForm" class="form-horizontal">
            <?php echo $this->Form->hidden('business_id', array(
                'value' => $business_id
            ));?>
            <div class="form-body">
                <div class="form-group">
                    <div class="col-md-12">
                        <?php echo $this->Form->textarea('reason', array(
                            'class' => 'form-control'
                        ));?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal-footer">
    <a id="rejectButton" class="btn btn-action" href="javascript:void(0)" onclick="jQuery.admin.doRejectbusines()">
        <?php echo __d('business', 'Reject');?>
    </a>
    <a id="cancelRejectButton" class="btn default" href="javascript:void(0)" data-dismiss="modal">
        <?php echo __d('business', 'Cancel');?>
    </a>
</div>