<?php
    echo $this->Html->css(array(
        'jquery-ui', 
        'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array(
        'jquery-ui', 
        'footable',
        '/store/js/admin'), array('inline' => false));
    $this->Html->addCrumb(__d('store',  'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('store',  "Manage Store Packages"), array('plugin' => 'store', 'controller' => 'store_packages', 'action' => 'admin_index'));
    $this->Html->addCrumb(__d('store',  "Edit Store Package"));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Stores'));
    $this->end();
    $store_package = $store_package['StorePackage'];
?>
<?php echo$this->Moo->renderMenu('Store', __d('store', 'Packages'));?>
<div id="page-wrapper">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?php echo __d('store', "Edit Store Package");?>
            <div class="pull-right">
                <input id="btnSave" type="button" class="btn btn-primary btn-xs" value="<?php echo __d('store', 'Save');?>" onclick="saveStorePackage()"/>
                <input id="btnApply" type="button" class="btn btn-primary btn-xs" value="<?php echo __d('store', 'Apply');?>" onclick="saveStorePackage(1)"/>
                <input id="btnCancel" type="button" class="btn btn-primary btn-xs" value="<?php echo __d('store', 'Cancel');?>" onclick="window.location = '<?php echo $admin_url;?>'"/>
            </div>
            <div class="clear"></div>
        </div>
        <div class="panel-body">
            <div class="Metronic-alerts alert alert-danger fade in" id="errorMessage" style="display: none"></div>
            <div class="Metronic-alerts alert alert-success fade in" style="display: none"></div>
            <form class="form-horizontal" id='createForm' method="post">
                <?php echo $this->Form->hidden('save_type', array(
                    'value' => 0
                ));?>
                <?php echo $this->Form->hidden('id', array(
                    'value' => $store_package['id']
                ));?>
                <div class="form-group">
                    <div class="col-md-2">
                        <label><?php echo __d('store',  'Enable')?></label>
                    </div>
                    <div class="col-sm-3">
                        <?php echo $this->Form->checkbox('enable', array('checked' => $store_package['enable']) ); ?>
                    </div>
                    <div class="clear"></div>
                </div>	
                <div class="form-group">
                    <div class="col-md-2">
                        <label><?php echo __d('store',  'Name')?> <span class="required">*</span></label>
                    </div>
                    <div class="col-sm-4">
                        <?php echo $this->Form->text('name', array('class'=>"form-control",'value' => $store_package['name'])); ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <label><?php echo __d('store',  'Price')?> (<?php echo $currency['Currency']['symbol'];?>) <span class="required">*</span></label>
                    </div>
                    <div class="col-sm-4">
                        <?php echo $this->Form->text('price', array('class'=>"form-control",'value' => $store_package['price'])); ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <label><?php echo __d('store',  'Period')?> (<?php echo __d('store',  'Days')?>) <span class="required">*</span></label>
                    </div>
                    <div class="col-sm-4">
                        <?php echo $this->Form->text('period', array('class'=>"form-control",'value' => $store_package['period'])); ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <label><?php echo __d('store',  'Remind before')?> (<?php echo __d('store',  'Days')?>) <span class="required">*</span></label>
                    </div>
                    <div class="col-sm-4">
                        <?php echo $this->Form->text('reminder', array('class'=>"form-control",'value' => $store_package['reminder'])); ?>
                    </div>
                </div>
                <!--<div class="form-group">
                    <div class="col-md-2">
                        <label><?php //echo __d('store',  'Description')?></label>
                    </div>
                    <div class="col-sm-4">
                        <?php //echo $this->Form->textarea('description', array('class'=>"form-control",'value' => $store_package['description'])); ?>
                    </div>
                </div>-->
            </form>
        </div>	
    </div>
</div>

<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>
    
jQuery(document).on("#createForm", "submit", function(e){
    e.preventDefault();
})

function saveStorePackage(apply)
{
    disableButton('btnSave');
    disableButton('btnApply');
    disableButton('btnCancel');
    $("#errorMessage").hide();
    if(apply == 1)
    {
        jQuery('#save_type').val(1);
    }
    
    //save data
    $.post("<?php echo $admin_url;?>save", $("#createForm").serialize(), function(data){
        var json = $.parseJSON(data);
        if(json.result == 0)
        {
            $("#errorMessage").html(json.message).show();
            enableButton('btnSave');
            enableButton('btnApply');
            enableButton('btnCancel');
        }
        else
        {
            window.location = json.location;
        } 
    });
}

<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>