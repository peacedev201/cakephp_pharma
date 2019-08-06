<?php
    echo $this->Html->css(array(
        'Store.jquery-ui', 
        'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array(
        'Store.jquery-ui', 
        'footable',
        'Store.admin'), array('inline' => false));
    $this->Html->addCrumb(__d('store',  'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('store',  "Manage Store Categories"), array('plugin' => 'store', 'controller' => 'store_categories', 'action' => 'admin_index'));
    if($storeCategory['id'] > 0)
    {
        $this->Html->addCrumb(__d('store',  "Edit Store Category"));
    }
    else 
    {
        $this->Html->addCrumb(__d('store',  "Create Store Category"));
    }
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Stores'));
    $this->end();
    
    $languages = $this->Store->loadLanguage();
?>
<?php echo$this->Moo->renderMenu('Store', __d('store', 'Store Categories'));?>
<div id="page-wrapper">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?php if($storeCategory['id'] > 0):?>
                <?php echo !empty($parentCategory) ? sprintf(__d('store', "Edit %s's Sub Categories"), $parentCategory['StoreCategory']['name']) : __d('store', "Edit Store Categories");?>
            <?php else:?>
                <?php echo !empty($parentCategory) ? sprintf(__d('store', "Create %s's Sub Categories"), $parentCategory['StoreCategory']['name']) : __d('store', "Create Store Categories");?>
            <?php endif;?>
            <div class="pull-right">
                <input id="btnSave" type="button" class="btn btn-primary btn-xs" value="<?php echo __d('store', 'Save');?>" onclick="saveStoreCategory()"/>
                <input id="btnApply" type="button" class="btn btn-primary btn-xs" value="<?php echo __d('store', 'Apply');?>" onclick="saveStoreCategory(1)"/>
                <input id="btnCancel" type="button" class="btn btn-primary btn-xs" value="<?php echo __d('store', 'Cancel');?>" onclick="window.location = '<?php echo $admin_link.(!empty($parent_id) ? 'index/'.$parent_id : '');?>'"/>
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
                    'value' => $storeCategory['id']
                ));?>
                <?php echo $this->Form->hidden('parent_id', array(
                    'value' => $parent_id
                ));?>
                <div class="form-group">
                    <div class="col-md-2">
                        <label><?php echo __d('store',  'Enable')?></label>
                    </div>
                    <div class="col-sm-3">
                        <?php echo $this->Form->checkbox('enable', array(
                            'checked' => $storeCategory['id'] > 0 ? $storeCategory['enable'] : true
                        ));?>
                    </div>
                    <div class="clear"></div>
                </div>	
                <div class="form-group">
                    <div class="col-md-2">
                        <label><?php echo __d('store',  'Parent')?></label>
                    </div>
                    <div class="col-sm-4">   
                        <?php echo !empty($parentCategory) ? $parentCategory['StoreCategory']['name'] : __d('store', 'Root');?>
                    </div>
                </div>
                <div id="lang-tabs">
                    <ul>
                        <?php foreach($languages as $language):
                            $language = $language['Language'];
                        ?>
                            <li>
                                <a href="#tab-<?php echo $language['id'];?>">
                                    <?php echo $language['name'];?>
                                </a>
                            </li>
                        <?php endforeach;?>
                    </ul>
                    <?php foreach($languages as $language):
                        $language = $language['Language'];
                    ?>
                        <div id="tab-<?php echo $language['id'];?>">
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label><?php echo __d('store',  'Name')?> <span class="required">*</span></label>
                                </div>
                                <div class="col-sm-4">
                                    <?php echo $this->Form->text('name', array(
                                        'class'=>"form-control",
                                        'value' => !empty($translate[$language['key']]['name']) ? $translate[$language['key']]['name'] : '',
                                        'name' => 'data[name]['.$language['key'].']'
                                    ));?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach;?>
                </div>
            </form>
        </div>	
    </div>
</div>

<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>
//tab
jQuery("#lang-tabs").tabs();
    
jQuery(document).on("#createForm", "submit", function(e){
    e.preventDefault();
})

function saveStoreCategory(apply)
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
    $.post("<?php echo $admin_link;?>save", $("#createForm").serialize(), function(data){
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