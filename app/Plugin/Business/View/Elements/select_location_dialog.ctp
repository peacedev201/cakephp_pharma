<div class="title-modal">
    <?php echo __d('business', 'Discover something new in your city!');?>
    <?php if($close_button == 1):?>
    <button data-dismiss="modal" class="close" type="button">
        <span aria-hidden="true">×</span>
    </button>
    <?php endif;?>
</div>
<div class="modal-body">
    <div class="create_form select_location_bus">
        <?php if($locations != null):?>
            <?php foreach($locations as $location):
                $location = $location['BusinessLocation'];
            ?>
                <div class="col-md-6">
                    <a href="<?php echo $this->request->base.'/business_locations/select_location/'.urlencode($location['name']);?>">
                        <i class="material-icons dp-24">location_on</i>
                        <?php echo $location['name'];?>
                    </a>
                </div>
            <?php endforeach;?>
        <?php endif;?>
        <form id="formSelectLocation">
            <?php echo $this->Form->text('location', array(
                'id' => 'default_location_address',
                'placeholder' => __d('business', 'Please enter your city or select one from above')
            ));?>
            <a id="btnSelectLocation" class="btn btn-action" href="javascript:void(0)">
                <?php echo __d('business', 'Go');?>
            </a>
            <div style="display:none;" class="error-message" id="locationMessage"></div>
        </form>
    </div>
</div>
