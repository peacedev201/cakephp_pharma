<?php
    __d('sticker', 'Sticker');
    __d('sticker', 'Enable plugin');
    __d('sticker', 'Yes');
    __d('sticker', 'No');
    __d('sticker', 'Animation interval');
    __d('sticker', 'millisecond');
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->Html->addCrumb(__d('sticker',  'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('sticker',  'Sticker Settings'), array(
        'controller' => 'StickerSettings', 
        'action' => 'admin_index'));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Sticker'));
    $this->end();
?>
<?php echo$this->Moo->renderMenu('Sticker', __d('sticker', 'Settings'));?>

<div class="portlet-body form">
    <div class=" portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-12">
                    <div class="tab-content">
                        <div class="tab-pane active" id="portlet_tab1">
                            <?php echo $this->element('admin/setting');?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>