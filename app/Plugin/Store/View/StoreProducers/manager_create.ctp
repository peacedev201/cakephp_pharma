<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_manager'), 
    'object' => array('$', 'store_manager')
));?>
    store_manager.initCreateProducer();
<?php $this->Html->scriptEnd(); ?>
    
<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
    <?php echo $this->Element('manager_menu'); ?>
<?php $this->end(); ?>
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
                <a href="<?php echo STORE_MANAGER_URL;?>producers/">
                    <?php echo __d('store', "Manage Producers");?>
                </a>
                <span class="divider"></span>
            </li>
            <li class="first">
                <a class="active" href="<?php echo STORE_MANAGER_URL;?>producers/create">
                    <?php if($producer['StoreProducer']['id'] > 0):?>
                        <?php echo __d('store', "Edit Producer");?>
                    <?php else:?>
                        <?php echo __d('store', "Create Producer");?>
                    <?php endif;?>
                </a>
                <span class="divider-last"></span>
            </li>
        </ul>
        <form class="form-horizontal" id='createForm' action="<?php echo  $this->request->base; ?>/stores/producers/save" method="post">
            <?php echo $this->Form->hidden('save_type', array(
                'value' => 0
            ));?>
            <?php echo $this->Form->hidden('id', array('value' => $producer['StoreProducer']['id']));?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php if($producer['StoreProducer']['id'] > 0):?>
                        <?php echo __d('store', "Edit Producer");?>
                    <?php else:?>
                        <?php echo __d('store', "Create Producer");?>
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
                                'checked' => $producer['StoreProducer']['id'] > 0 ? $producer['StoreProducer']['enable'] : true
                            ) ); ?>
                        </div>
                        <div class="clear"></div>
                    </div>		
                    <div class="form-group">
                        <div class="col-md-2">
                            <label><?php echo __d('store',  'Name')?> <span class="required">*</span></label>
                        </div>
                        <div class="col-sm-4">
                            <?php echo $this->Form->text('name', array('value' => $producer['StoreProducer']['name'])); ?>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-2">
                            <label><?php echo __d('store',  'Phone')?></label>
                        </div>
                        <div class="col-sm-4">
                            <?php echo $this->Form->text('phone', array('value' => $producer['StoreProducer']['phone'])); ?>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-2">
                            <label><?php echo __d('store',  'Email')?></label>
                        </div>
                       <div class="col-sm-4">
                            <?php echo $this->Form->text('email', array('value' => $producer['StoreProducer']['email']) ); ?>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-2">
                            <label><?php echo __d('store',  'Address')?></label>
                        </div>
                        <div class="col-sm-4">
                            <?php echo $this->Form->text('address', array('value' => $producer['StoreProducer']['address']) ); ?>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>		
            </div>
        </form>
    </div>
</div>
    
<?php
if($is_app)
{
    $this->MooGzip->script(array('zip'=>'mobile.action.bundle.js.gz','unzip'=>'MooApp.mobile.action.bundle'));
}
?>