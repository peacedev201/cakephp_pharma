<?php
    echo $this->Html->css(array(
        'footable.core.min', 
        'Sticker.jquery-ui',
        'Sticker.admin',
        'Sticker.colorpicker',
        'Sticker.fileuploader'
    ), null, array('inline' => false));
    echo $this->Html->script(array(
        'footable',
        'Sticker.admin',
        'Sticker.colorpicker',
        'Sticker.jquery.fileuploader'
    ), array('inline' => false));
?>
<?php
    $this->Html->addCrumb(__d('sticker',  'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('sticker',  "Manage Categories"), array(
        'controller' => 'StickerCategories', 
        'action' => 'admin_index'
    ));
    if($category['id'] > 0)
    {
        $this->Html->addCrumb(__d('sticker',  "Edit Category"));
    }
    else 
    {
        $this->Html->addCrumb(__d('sticker',  "Create Category"));
    }
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Sticker'));
    $this->end();
    
    $languages = $this->Sticker->loadLanguage();
?>
<?php echo$this->Moo->renderMenu('Sticker', __d('sticker', 'Categories'));?>
<div id="page-wrapper">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?php if($category['id'] > 0):?>
                <?php echo __d('sticker', "Edit Categories");?>
            <?php else:?>
                <?php echo __d('sticker', "Create Categories");?>
            <?php endif;?>
            <div class="pull-right">
                <input id="btnSave" type="button" class="btn btn-primary btn-xs" value="<?php echo __d('sticker', 'Save');?>"/>
                <input id="btnApply" type="button" class="btn btn-primary btn-xs" value="<?php echo __d('sticker', 'Apply');?>"/>
                <input id="btnCancel" type="button" class="btn btn-primary btn-xs" value="<?php echo __d('sticker', 'Cancel');?>" onclick="window.location = '<?php echo $admin_link.(!empty($parent_id) ? 'index/'.$parent_id : '');?>'"/>
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
                    'value' => $category['id']
                ));?>
                <div class="form-group">
                    <div class="col-md-2">
                        <label><?php echo __d('sticker',  'Enable')?></label>
                    </div>
                    <div class="col-sm-3">
                        <?php echo $this->Form->checkbox('enabled', array(
                            'checked' => $category['id'] > 0 ? $category['enabled'] : true
                        ));?>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <label><?php echo __d('sticker',  'Icon')?> <span class="required">*</span></label>
                    </div>
                    <div class="col-sm-3">
                        <?php echo $this->Form->hidden("icon", array(
                            'value' => $category['icon']
                        ));?>
                        <div id="category_icon"></div>
                        <div id="category_icon_preview">
                            <img style="width:<?php echo STICKER_CATEGORY_ICON_WIDTH;?>px;height:<?php echo STICKER_CATEGORY_ICON_HEIGHT;?>px;<?php if (empty($category['icon'])): ?>display: none;<?php endif; ?>" src="<?php echo !empty($category['icon']) ? $this->Sticker->getCategoryIcon($category) : "" ?>" />
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <label><?php echo __d('sticker',  'Background color')?> <span class="required">*</span></label>
                    </div>
                    <div class="col-sm-3">
                        <?php echo $this->Form->text('background_color', array(
                            'class' => 'form-control',
                            'value' => $category['background_color']
                        ));?>
                    </div>
                    <div class="col-sm-3">
                        <div id="background_color_preview" <?php if(!empty($category['background_color'])):?>style="background:#<?php echo $category['background_color'];?>"<?php endif;?>></div>
                    </div>
                    <div class="clear"></div>
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
                                    <label><?php echo __d('sticker',  'Name')?> <span class="required">*</span></label>
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
    
    $(document).ready(function () {
        $.admin.initCreateCategory();
    });
    
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>