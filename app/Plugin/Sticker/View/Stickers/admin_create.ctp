<?php
    echo $this->Html->css(array(
        'footable.core.min', 
        'Sticker.jquery-ui',
        'Sticker.admin',
        'Sticker.fileuploader'
    ), null, array('inline' => false));
    echo $this->Html->script(array(
        'footable',
        'Sticker.admin',
        'Sticker.jquery.fileuploader'
    ), array('inline' => false));
?>
<?php
    $this->Html->addCrumb(__d('sticker',  'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('sticker',  "Manage Stickers"), array(
        'controller' => 'Stickers', 
        'action' => 'admin_index'
    ));
    if($sticker['id'] > 0)
    {
        $this->Html->addCrumb(__d('sticker',  "Edit Sticker"));
    }
    else 
    {
        $this->Html->addCrumb(__d('sticker',  "Create Sticker"));
    }
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Sticker'));
    $this->end();
    
    $languages = $this->Sticker->loadLanguage();
?>
<?php echo$this->Moo->renderMenu('Sticker', __d('sticker', 'Stickers'));?>
<div id="page-wrapper">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?php if($sticker['id'] > 0):?>
                <?php echo __d('sticker', "Edit Stickers");?>
            <?php else:?>
                <?php echo __d('sticker', "Create Stickers");?>
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
                    'value' => $sticker['id']
                ));?>
                <div class="form-group">
                    <div class="col-md-1">
                        <label><?php echo __d('sticker',  'Enable')?></label>
                    </div>
                    <div class="col-sm-3">
                        <?php echo $this->Form->checkbox('enabled', array(
                            'checked' => $sticker['id'] > 0 ? $sticker['enabled'] : true
                        ));?>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="form-group">
                    <div class="col-md-1">
                        <label><?php echo __d('sticker',  'Icon')?> <span class="required">*</span></label>
                    </div>
                    <div class="col-sm-3">
                        <?php echo $this->Form->hidden("icon", array(
                            'value' => $sticker['icon']
                        ));?>
                        <div id="sticker_icon"></div>
                        <div id="sticker_icon_preview">
                            <img style="width:<?php echo STICKER_STICKER_ICON_WIDTH;?>px;height:<?php echo STICKER_STICKER_ICON_HEIGHT;?>px;<?php if (empty($sticker['icon'])): ?>display: none;<?php endif; ?>" src="<?php echo !empty($sticker['icon']) ? $this->Sticker->getStickerIcon($sticker) : "" ?>" />
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="form-group">
                    <div class="col-md-1">
                        <label><?php echo __d('sticker',  'Animation interval')?></label>
                    </div>
                    <div class="col-sm-2">
                        <?php echo $this->Form->text('animation_interval', array(
                            'class' => "form-control",
                            'value' => $sticker['animation_interval']
                        ));?>
                        <?php echo __d('sticker',  'If value is 0, it will get value from setting.');?>
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
                                <div class="col-md-1">
                                    <label><?php echo __d('sticker',  'Name')?> <span class="required">*</span></label>
                                </div>
                                <div class="col-sm-5">
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
                <div class="form-group">
                    <div class="col-md-1">
                        <label><?php echo __d('sticker',  'Images')?></label>
                    </div>
                    <div class="col-sm-11">
                        <p>
                            <?php echo sprintf(__d('sticker',  'Click %s to see a sample animation image.'), '<a href="'.$this->request->base.'/admin/sticker/stickers/sample" data-target="#stickerModal" data-toggle="modal">'.__d('sticker',  'here').'</a>');?>
                        </p>
                        <div class="sticker_animation"></div>
                        <div id="sticker_image"></div>
                        <div class="table-responsive" style="margin-top: 10px;">
                            <table class="table table-bordered" id="tbImage">
                                <thead>
                                    <tr>
                                        <th><?php echo __d('sticker', 'Image');?></th>
                                        <th style="width: 15%" class="text-center"><?php echo __d('sticker', 'Category');?></th>
                                        <th style="width: 10%" class="text-center"><?php echo __d('sticker', 'Block');?></th>
                                        <th style="width: 10%" class="text-center"><?php echo __d('sticker', 'Quantity');?></th>
                                        <th style="width: 10%" class="text-center"><?php echo __d('sticker', 'Enable');?></th>
                                        <th style="width: 10%" class="text-center"><?php echo __d('sticker', 'Ordering');?></th>
                                        <th style="width: 10%" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>	
    </div>
</div>

<script type="text/template" id="imageDataTemplate">
    <?php echo json_encode($sticker_images);?>
</script>

<script type="text/template" id="imageTemplate">
    <tr>
        <td>
            <div style="width:100px;height:100px;overflow:hidden">
                <div class="sticker_preview sticker_animation"></div>
                <?php echo $this->Form->hidden('image_id.', array(
                    'id' => false,
                    'class' => 'image_id',
                ));?>
                <?php echo $this->Form->hidden('image_filename.', array(
                    'id' => false,
                    'class' => 'image_filename',
                ));?>
                <?php echo $this->Form->hidden('image_url.', array(
                    'id' => false,
                    'class' => 'image_url',
                ));?>
                <?php echo $this->Form->hidden('image_width.', array(
                    'id' => false,
                    'class' => 'image_width',
                ));?>
                <?php echo $this->Form->hidden('image_height.', array(
                    'id' => false,
                    'class' => 'image_height',
                ));?>
          </div>
        </td>
        <td class="text-center" style="vertical-align: middle">
            <?php echo $this->Form->select('image_category_id.', $category_list, array(
                'id' => false,
                'class' => 'image_category_id form-control',
                'empty' => array("" => __d('sticker', 'None'))
            ));?>
        </td>
        <td class="text-center" style="vertical-align: middle">
            <?php echo $this->Form->number('block.', array(
                'id' => false,
                'class' => 'image_block form-control text-center',
                'min' => 1,
                'value' => 1
            ));?>
        </td>
        <td class="text-center" style="vertical-align: middle">
            <?php echo $this->Form->number('quantity.', array(
                'id' => false,
                'class' => 'image_quantity form-control text-center',
                'min' => 1,
                'value' => 1
            ));?>
        </td>
        <td class="text-center" style="vertical-align: middle">
            <?php echo $this->Form->checkbox('image_enable.', array(
                'hiddenField' => false,
                'id' => false,
                'checked' => true,
                'class' => 'image_enable'
            ));?>
        </td>
        <td class="text-center" style="vertical-align: middle">
            <input class="btn btn-up" type="button" title="<?php echo __d('sticker', 'Up');?>" style="display: none;">
            <input class="btn btn-down" type="button" title="<?php echo __d('sticker', 'Down');?>" style="display: none;">
        </td>
        <td class="text-center" style="vertical-align: middle">
            <a href="javascript:void(0)" class="btn btn-primary delete_image">
                <?php echo __d('sticker', 'Delete');?>
            </a>
        </td>
    </tr>
</script>

<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>
    
    $(document).ready(function () {
        $.admin.initCreateSticker();
    });
    
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>