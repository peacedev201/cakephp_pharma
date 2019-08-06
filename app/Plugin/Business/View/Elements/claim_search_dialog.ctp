<div class="title-modal">
    <?php echo __d('business', 'Claim Your Business ');?>
    <button data-dismiss="modal" class="close" type="button">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body">
    <div class="extra_info">
    <?php echo __d('business', 'Please search your business using the search box below and claim if you are the owner.');?>
    </div>
    <form class="global-search-bus popup_claim_bus" id="formClaimBusinessSearch">
        <?php echo $this->Form->hidden('keyword_location', array(
            'value' => $default_location_name
        ));?>
        <?php echo $this->Form->hidden('remember', array(
            'value' => 1
        ));?>
        <div class="bus_main_form">
        <div class="col-xs-8">
        <input type="text" id="claim_search_business" name="data[keyword]" placeholder="<?php echo __d('business', 'Your business name')?>">
        </div>
        <input type="button" class="button" value="<?php echo __d('business', 'Search')?>" id="btn_search_claim_business"/>
        </div>
        <div class="clear"></div>
        <div style="display:none;" class="error-message" id="searchClaimBusinessMessage"></div>
        <div class="clear"></div>
    </form>
</div>
