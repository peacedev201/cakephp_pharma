<div class="title-modal">
    <?php echo __d('business', 'Reject business');?>    
    <button data-dismiss="modal" class="close" type="button">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body">
    <div style="display:none;" class="error-message" id="rejectMessage"></div>
    <div class="create_form">
        <?php echo __d('business', 'Please enter reason why you want to reject this business');?>
        <form id="rejectForm">
            <?php echo $this->Form->hidden('business_id', array(
                'value' => $business_id
            ));?>
            <ul style="position:relative" class="list6 list6sm2">
                <li>
                    <?php echo $this->Form->textarea('reason');?>
                </li>
                <li>
                    <a id="rejectButton" class="button" href="javascript:void(0)">
                        <?php echo __d('business', 'Reject');?>
                    </a>
                    <a id="cancelRejectButton" class="button" href="javascript:void(0)" data-dismiss="modal">
                        <?php echo __d('business', 'Cancel');?>
                    </a>
                    <div class="clear"></div>
                </li>
            </ul>
        </form>
    </div>
</div>