<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_manager'), 
    'object' => array('$', 'store_manager')
));?>
    store_manager.initCreateAttribute();
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
                <a href="<?php echo $url;?>">
                    <?php echo __d('store', "Manage Attributes");?>
                </a>
                <span class="divider"></span>
            </li>
            <li class="first">
                <a class="active" href="<?php echo $url;?>create">
                    <?php if($attribute['id'] > 0):?>
                        <?php echo __d('store', "Edit Attribute");?>
                    <?php else:?>
                        <?php echo __d('store', "Create Attribute");?>
                    <?php endif;?>
                </a>
                <span class="divider-last"></span>
            </li>
        </ul>
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php if($attribute['id'] > 0):?>
                    <?php echo __d('store', "Edit Attribute");?>
                <?php else:?>
                    <?php echo __d('store', "Create Attribute");?>
                <?php endif;?>
                <div class="pull-right">
                    <input id="btnSave" type="button" class="btn btn-primary" value="<?php echo __d('store', 'Save');?>"/>
                    <input id="btnApply" type="button" class="btn btn-primary" value="<?php echo __d('store', 'Apply');?>"/>
                    <input id="btnCancel" type="button" class="btn btn-primary" value="<?php echo __d('store', 'Cancel');?>" onclick="<?php echo $is_app ? "window.mobileAction.backAndRefesh();" : "window.location = '".$url."'"?>"/>
                </div>
                <div class="clear"></div>
            </div>
            <div class="panel-body">
                <div id="errorMessage" class="error-message" style="display: none"></div>
                <div class="Metronic-alerts alert alert-success fade in" style="display: none"></div>
                <form class="form-horizontal" id="formAttribute" method="post">
                    <?php echo $this->Form->hidden('id', array(
                        'value' => $attribute['id']
                    ));?>
                    <?php echo $this->Form->hidden('save_type', array(
                        'value' => 0
                    ));?>
                    <div role="tabpanel" class="tab-pane active" id="general">
                        <div class="form-group">
                            <div class="col-md-2 col-sm-2 col-xs-2">
                                <label for="attribute_code"><?php echo __d('store', "Enable");?></label>
                            </div>
                            <div class="col-sm-3 col-sm-3 col-xs-3">
                                <?php echo $this->Form->checkbox('enable', array(
                                    'hiddenField' => false,
                                    'checked' => $attribute['id'] > 0 ? $attribute['enable'] : true
                                ));?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-2">
                                <labe><?php echo __d('store', "Name");?> <span class="required">*</span></label>
                            </div>
                            <div class="col-sm-4">
                                <?php echo $this->Form->input("name", array(
                                    'div' => false,
                                    'label' => false,
                                    'class' => 'form-control',
                                    'value' => $attribute['name']
                                ));?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-2">
                                <label><?php echo __d('store', "Category");?></label>
                            </div>
                            <div class="col-sm-4">
                                <select name="data[parent_id]" class="form-control">
                                    <option value="0"><?php echo __d('store', "Main attribute");?></option>
                                    <?php echo $this->Category->outputOptionType($attributeCats, 'StoreAttribute', null, $attribute['parent_id'], 1);?>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    
<?php
if($is_app)
{
    $this->MooGzip->script(array('zip'=>'mobile.action.bundle.js.gz','unzip'=>'MooApp.mobile.action.bundle'));
}
?>