<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_manager'), 
    'object' => array('$', 'store_manager')
));?>
    store_manager.initCreateShipping();
<?php $this->Html->scriptEnd(); ?>
    
<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
    <?php echo $this->Element('manager_menu'); ?>
<?php $this->end(); ?>
    
<?php 
    $shipping_detail = $shipping_method['StoreShippingDetail'];
    $shipping_method = $shipping_method['StoreShippingMethod'];
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
                <a href="<?php echo STORE_MANAGER_URL;?>shippings/">
                    <?php echo __d('store', "Manage Shippings");?>
                </a>
                <span class="divider"></span>
            </li>
            <li class="first">
                <a class="active" href="<?php echo STORE_MANAGER_URL;?>shippings/create/<?php echo $shipping_method['id'];?>">
                    <?php echo __d('store', "Edit Shipping");?>
                </a>
                <span class="divider-last"></span>
            </li>
        </ul>
        <form class="form-horizontal" id='createForm' action="<?php echo  $this->request->base; ?>/stores/shippings/save" method="post">
            <?php echo $this->Form->hidden('save_type', array(
                'value' => 0
            ));?>
            <?php echo $this->Form->hidden('id', array('value' => $shipping_method['id']));?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php echo __d('store', "Edit Shipping");?>
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
                                'checked' => $shipping_detail['enable'] ? true : false
                            ) ); ?>
                        </div>
                        <div class="clear"></div>
                    </div>		
                    <div class="form-group">
                        <div class="col-md-2">
                            <label><?php echo __d('store',  'Method')?> <span class="required">*</span></label>
                        </div>
                        <div class="col-sm-4">
                            <?php echo $shipping_method['name'];?>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-2">
                            <label><?php echo __d('store',  'Zone')?> <span class="required">*</span></label>
                        </div>
                        <div class="col-sm-10">
                            <?php $zone_list = $this->Store->getShippingZoneList(Configure::read('store.store_id'));?>
                            <a href="javascript:void(0)" class="btn btn-primary" id="btnAddZone" <?php if(count($zone_list) == 0):?>disabled="disabled"<?php endif;?>>
                                <?php echo __d('store', 'Add');?>                    
                            </a>
                            <?php if(count($zone_list) == 0):?>
                                <?php echo sprintf(__d('store', 'Click %s to create zone'), '<a href="'.STORE_MANAGER_URL.'shipping_zones/create">'.__d('store', 'here').'</a>');?>
                            <?php endif;?>
                            <br/><br/>
                            <div class="div-full-breabcrum">
                                <div class="<?php if($shipping_method['key_name'] == STORE_SHIPPING_WEIGHT):?>col-md-5<?php else:?>col-md-6<?php endif;?> col-custom-4">
                                    <div class="group-group text-left">
                                        <i class="text-app"><?php echo __d('store', 'Name');?></i>
                                    </div>
                                </div>
                                <?php if($shipping_method['key_name'] == STORE_SHIPPING_WEIGHT):?>
                                <div class="col-md-2 col-custom-4">
                                    <div class="group-group text-left">
                                        <i class="text-app"><?php echo __d('store', 'Weight(kg)');?></i>
                                    </div>
                                </div>
                                <?php endif;?>
                                <div class="<?php if($shipping_method['key_name'] == STORE_SHIPPING_WEIGHT):?>col-md-2<?php else:?>col-md-3<?php endif;?> col-custom-4">
                                    <div class="group-group text-left">
                                        <i class="text-app"><?php echo __d('store', 'Price');?></i>
                                        (<?php echo Configure::read('store.currency_symbol');?>)
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
                            <div id="zone_content"></div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>		
            </div>
        </form>
    </div>
</div>
    
    
<script type="text/template" id="zoneData">
    <?php echo json_encode($shippings);?>
</script>
<script type="text/template" id="zoneDataTemplate">
    <div class="div-detail-row" style="border:none">
        <div class="<?php if($shipping_method['key_name'] == STORE_SHIPPING_WEIGHT):?>col-md-5<?php else:?>col-md-6<?php endif;?>">
            <div class="group-group group-group-name text-left">
                <i class="visible-sm visible-xs icon-app material-icons">title</i>
                <?php echo $this->Form->select('store_shipping_zone_id.', $this->Store->getShippingZoneList(Configure::read('store.store_id')), array(
                    'empty' => false,
                    'class' => 'store_shipping_zone_id form-control',
                    'id' => ''
                )); ?>
            </div>
        </div>
        <?php if($shipping_method['key_name'] == STORE_SHIPPING_WEIGHT):?>
        <div class="col-md-2">
            <div class="group-group group-group-name text-left">
                <i class="visible-sm visible-xs icon-app icon-app-text"><?php echo __d('store', 'Weight(kg)');?></i>
                <?php echo $this->Form->input("weight.", array(
                    'div' => false,
                    'label' => false,
                    'class' => 'weight form-control',
                    'id' => ''
                ));?>
            </div>
        </div>
        <?php endif;?>
        <div class="<?php if($shipping_method['key_name'] == STORE_SHIPPING_WEIGHT):?>col-md-2<?php else:?>col-md-3<?php endif;?>">
            <div class="group-group group-group-name text-center">
                <i class="visible-sm visible-xs icon-app material-icons">monetization_on</i>
                <?php if($shipping_method['key_name'] == STORE_SHIPPING_FREE || $shipping_method['key_name'] == STORE_SHIPPING_PICKUP):?>
                    <?php echo $this->Store->formatMoney('0');?>
                <?php else:?>
                    <?php echo $this->Form->input("price.", array(
                        'div' => false,
                        'label' => false,
                        'class' => 'price form-control',
                        'id' => ''
                    ));?>
                <?php endif;?>
            </div>
        </div>
        <div class="col-md-1">
            <div class="group-group group-group-name text-center">
                <div class="visible-sm visible-xs icon-app icon-app-text"><?php echo __d('store',  'Enable')?></div>
                <?php echo $this->Form->checkbox('enable_zone.', array(
                    'class' => 'enable_zone',
                    'id' => ''
                )); ?>
            </div>
        </div>
        <div class="col-md-2 padding-0 no-border-right">
            <a href="javascript:void(0)" class="btn btn-primary remove_zone">
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