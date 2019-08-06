
<?php
    echo $this->Html->css(array('jquery-ui', 'footable.core.min', 'Business.business-admin.css'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable', 'Business.admin'), array('inline' => false));
    $this->Html->addCrumb(__d('business', 'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('business', 'Business Types'), array('controller' => 'business_types', 'action' => 'admin_index'));
    
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Business'));
    $this->end();
?>
<?php echo$this->Moo->renderMenu('Business', __d('business', 'Business Types'));?>
<?php
$this->Paginator->options(array('url' => $this->passedArgs));
?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
    jQuery.admin.initOptions();
<?php $this->Html->scriptEnd(); ?>
<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group">
                    <button class="btn btn-gray" data-toggle="modal" data-target="#ajax" href="<?php echo $this->request->base ?>/admin/business/business_types/create">
                        <?php echo __d('business', 'Add New Business Type'); ?>
                    </button>
                    <a style="margin-left: 10px" onclick="jQuery.admin.saveOrder('<?php echo $this->request->base?>/admin/business/business_types/save_order/')" class="btn btn-gray" >
                        <?php echo __d('business', 'Save order'); ?>
                    </a>
                </div>
            </div>
            <div class="col-md-6">    
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" style="padding-top: 5px;">
                <div class="note note-info hide">
                    <p>
                        <?php echo __d('business', 'You can enable Spam Challenge to force user to answer a challenge question in order to register.'); ?> <br/>
                        <?php echo __d('business', 'To enable this feature, click System Settings -> Security -> Enable Spam Challenge'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php  if(!empty($types)) : ?>
    <table class="table table-striped table-bordered table-hover" id="sample_1">
        <thead>
            <tr class="tbl_head">
                <th width="50px"><?php echo __d('business', 'ID'); ?></th>
                <th><?php echo __d('business', 'Name'); ?></th>
                <th width="50px" data-hide="phone"><?php echo __d('business', 'Enable');?></th>
                <th width="50px"><?php echo __d('business', 'Order'); ?></th>
                <th width="50px" data-hide="phone"><?php echo __d('business', 'Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            foreach ($types as $type):
                ?>
                <tr class="gradeX <?php ( ++$count % 2 ? "odd" : "even") ?>" id="<?php echo $type['BusinessType']['id'] ?>">
                    <td width="50px"><?php echo $type['BusinessType']['id'] ?></td>
                    <td class="reorder">
                        <?php
                        $this->MooPopup->tag(array(
                            'href' => $this->Html->url(array("controller" => "business_types",
                                "action" => "admin_create",
                                "plugin" => "business",
                                $type['BusinessType']['id'],
                            )),
                            'title' => $type['BusinessType']['name'],
                            'innerHtml' => $type['BusinessType']['name'],
                            'target' => 'ajax'
                        ));
                        ?>
                    </td>
                    <td width="50px" class="reorder"><?php echo  ($type['BusinessType']['enable']) ? __d('business', 'Yes') : __d('business', 'No') ?></td>
                    <td width="50px" class="reorder"><input data-id="<?php echo $type['BusinessType']['id'] ?>" style="width:50px" type="text" name="data[ordering]" value="<?php echo $type['BusinessType']['ordering'] ?>" /> </td>
                    <td width="50px"><a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('business', 'Are you sure you want to delete this business type?') ?>', '<?php echo $this->request->base ?>/admin/business/business_types/delete/<?php echo $type['BusinessType']['id'] ?>')"><i class="icon-trash icon-small"></i></a></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <div class="row">
        <div class="col-sm-3">
        </div>
        <div class="col-sm-9">
            <div id="dataTables-example_paginate" class="dataTables_paginate paging_simple_numbers">
                <ul class="pagination">
                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('business', 'First'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('business', 'Previous'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                    <?php echo $this->Paginator->numbers(array('class' => 'paginate_button', 'tag' => 'li', 'separator' => '')); ?>
                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('business', 'Next'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('business', 'Last'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                </ul>
            </div>
        </div>
    </div>
    <?php else : ?>
        <div class="message_empty">
            <?php echo __d('business', 'There are no business type found!') ?>
        </div>
    <?php endif; ?>
</div>