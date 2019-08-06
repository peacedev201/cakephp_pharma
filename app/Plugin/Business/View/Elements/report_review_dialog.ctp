<div class="title-modal">
    <?php echo __d('business', 'Report Review');?>    
    <button data-dismiss="modal" class="close" type="button">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body">
    <div class="create_form">
        <?php echo __d('business', 'Please enter reason why you want to report this review');?>
        <form id="reportReviewForm">
            <?php echo $this->Form->hidden('review_id', array(
                'value' => $review_id
            ));?>
            <ul style="position:relative" class="list6 list6sm2">
                <li>
                    <?php echo $this->Form->textarea('reason');?>
                </li>
                <li>
                    <a id="reportReviewButton" class="button" href="javascript:void(0)">
                        <?php echo __d('business', 'Report');?>
                    </a>
                    <a id="cancelReportReviewButton" class="button" href="javascript:void(0)" data-dismiss="modal">
                        <?php echo __d('business', 'Cancel');?>
                    </a>
                    <div class="clear"></div>
                </li>
            </ul>
        </form>
        <div style="display:none;" class="error-message" id="reportReviewMessage"></div>
    </div>
</div>