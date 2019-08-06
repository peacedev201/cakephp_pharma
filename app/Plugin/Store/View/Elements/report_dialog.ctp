<div class="modal-content">
    <div class="title-modal">
        <?php echo  __d('store', 'Report') ?>              
        <button data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
    </div>
    <div class="modal-body">
        <div class="error-message" style="display:none;" id="reportMessage"></div>
        <div class="create_form" style="margin-top: 5px;">
            <form id="reportForm">
                <?php echo $this->Form->hidden('product_id', array(
                    'value' => $product_id
                ));?>
                <ul style="position:relative" class="list6 list6sm2">
                    <li>
                        <div class="col-md-2">
                            <label><?php echo  __d('store', 'Reason') ?></label>
                        </div>
                        <div class="col-md-10">
                            <?php echo $this->Form->textarea('content', array(
                                'div' => false,
                                'label' => false,
                                'id' => 'report_content'
                            ));?>
                        </div>
                        <div class="clear"></div>
                    </li>
                    <li>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                        </div>
                        <div class="col-md-10">
                            <a id="reportButton" class="btn btn-action padding-button" href="javascript:void(0)">
                                <?php echo  __d('store', 'Report') ?>
                            </a>
                            <a id="cancelReportButton" class="btn btn-action padding-button" href="javascript:void(0)" data-dismiss="modal">
                                <?php echo  __d('store', 'Cancel') ?>
                            </a>
                        </div>
                        <div class="clear"></div>
                    </li>
                </ul>
            </form>
        </div>
    </div>
</div>
