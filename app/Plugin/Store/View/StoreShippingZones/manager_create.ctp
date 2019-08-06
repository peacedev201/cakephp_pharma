<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_manager'), 
    'object' => array('$', 'store_manager')
));?>
    store_manager.initCreateShippingZone();
<?php $this->Html->scriptEnd(); ?>
    
<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
    <?php echo $this->Element('manager_menu'); ?>
<?php $this->end(); ?>
    
<?php
    $shipping_zone_location = !empty($shipping_zone['StoreShippingZoneLocation']) ? $shipping_zone['StoreShippingZoneLocation'] : array();
    $shipping_zone = $shipping_zone['StoreShippingZone'];
?>
<div class="bar-content">
    <?php echo $this->Element('Store.mobile/mobile_manager_menu'); ?>
    <div class="content_center">
        <ul class="breadcrumb">
            <li>
                <a href="<?php echo STORE_MANAGER_URL;?>">
                    <i class="material-icons">home</i>
                </a>
                <span class="divider"></span>
            </li>
            <li>
                <a href="<?php echo STORE_MANAGER_URL;?>shipping_zones/">
                    <?php echo __d('store', "Manage Shipping Zones");?>
                </a>
                <span class="divider"></span>
            </li>
            <li class="first">
                <a class="active" href="<?php echo STORE_MANAGER_URL;?>shipping_zones/create">
                    <?php if($shipping_zone['id'] > 0):?>
                        <?php echo __d('store', "Edit Shipping Zone");?>
                    <?php else:?>
                        <?php echo __d('store', "Create Shipping Zone");?>
                    <?php endif;?>
                </a>
                <span class="divider-last"></span>
            </li>
        </ul>
        <form class="form-horizontal" id='createForm' action="<?php echo  $this->request->base; ?>/stores/shipping_zones/save" method="post">
            <?php echo $this->Form->hidden('save_type', array(
                'value' => 0
            ));?>
            <?php echo $this->Form->hidden('id', array('value' => $shipping_zone['id']));?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php if($shipping_zone['id'] > 0):?>
                        <?php echo __d('store', "Edit Shipping Zone");?>
                    <?php else:?>
                        <?php echo __d('store', "Create Shipping Zone");?>
                    <?php endif;?>
                    <div class="pull-right">
                        <input id="btnSave" type="button" class="btn btn-primary" value="<?php echo __d('store', 'Save');?>"/>
                        <input id="btnApply" type="button" class="btn btn-primary" value="<?php echo __d('store', 'Apply');?>"/>
                        <input id="btnCancel" type="button" class="btn btn-primary" value="<?php echo __d('store', 'Cancel');?>" onclick="<?php echo $is_app ? "window.mobileAction.backAndRefesh();" : "window.location = '".$url."'"?>"/>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="panel-body">
                    <div class="error-message" id="errorMessage" style="display: none"></div>
                    <div class="Metronic-alerts alert alert-success fade in" style="display: none"></div>
                    <div class="form-group">
                        <div class="col-md-2 col-sm-2 col-xs-2">
                            <label><?php echo __d('store',  'Enable')?></label>
                        </div>
                        <div class="col-sm-4">
                            <?php echo $this->Form->checkbox('enable', array(
                                'checked' => $shipping_zone['id'] > 0 ? $shipping_zone['enable'] : true
                            ) ); ?>
                        </div>
                        <div class="clear"></div>
                    </div>		
                    <div class="form-group">
                        <div class="col-md-2">
                            <label><?php echo __d('store',  'Name')?> <span class="required">*</span></label>
                        </div>
                        <div class="col-sm-4">
                            <?php echo $this->Form->text('name', array('value' => $shipping_zone['name'])); ?>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-2">
                            <label><?php echo __d('store',  'Country')?> <span class="required">*</span></label>
                        </div>
                        <div class="col-sm-10">
                            <a href="javascript:void(0)" class="btn btn-primary" id="btnAddLocation">
                                <?php echo __d('store', 'Add');?>                    
                            </a>
                            <br/><br/>
                            <div class="div-full-breabcrum">
                                <div class="col-md-9">
                                    <div class="group-group text-left">
                                        <i class="text-app"><?php echo __d('store', 'Name');?></i>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="group-group">
                                        <i class="text-app"><?php echo __d('store', 'Enable');?></i>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="group-group">
                                        <i class="text-app"></i>
                                    </div>
                                </div>
                            </div>
                            <div id="location_content" ></div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>		
            </div>
        </form>
    </div>
</div>
    
<script type="text/template" id="locationData">
    <?php echo json_encode($shipping_zone_location);?>
</script>
<script type="text/template" id="locationDataTemplate">
    <div class="div-detail-row" style="border:none">
        <div class="col-md-9">
            <div class="group-group group-group-name text-left">
                <i class="visible-sm visible-xs icon-app material-icons">title</i>
                <?php echo $this->Form->select('country_id.', $this->Store->getCountryList(), array(
                    'empty' => array('' => __d('store', 'Select country')),
                    'class' => 'country_id pull-left form-control',
                    'id' => ''
                )); ?>
            </div>
        </div>
        <div class="col-md-1">
            <div class="group-group group-group-name text-center">
                <div class="visible-sm visible-xs icon-app icon-app-text"><?php echo __d('store',  'Enable')?></div>
                <?php echo $this->Form->checkbox('enable_location.', array(
                    'class' => 'enable_location',
                    'id' => ''
                )); ?>
            </div>
        </div>
        <div class="col-md-2 padding-0 no-border-right">
            <a href="javascript:void(0)" class="btn btn-primary remove_location">
                <?php echo __d('store', 'Remove');?>                    
            </a>
        </div>
    </div>
</script>

<?php
if($is_app)
{
    $this->MooGzip->script(array('zip'=>'mobile.action.bundle.js.gz','unzip'=>'MooApp.mobile.action.bundle'));
}
?>