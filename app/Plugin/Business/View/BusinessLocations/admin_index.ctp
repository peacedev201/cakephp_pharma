<?php
echo $this->Html->css(array('jquery-ui', 'footable.core.min', 'Business.business-admin'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable', 'Business.admin'), array('inline' => false));
$this->Html->addCrumb(__d('business', 'Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('business', 'Manage Locations'), array('controller' => 'business_locations', 'action' => 'admin_index'));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Business'));
$this->end();
?>
<?php echo  $this->Moo->renderMenu('Business', __d('business', 'Locations')); ?>

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
                    <button class="btn btn-gray" data-toggle="modal" data-target="#ajax" href="<?php echo  $this->request->base ?>/admin/business/business_locations/create">
                        <?php echo __d('business', 'Add New Location');?>
                    </button>
                    <a style="margin-left: 10px" onclick="jQuery.admin.saveOrder('<?php echo $this->request->base?>/admin/business/business_locations/save_order/')" class="btn btn-gray" >
                        <?php echo __d('business', 'Save order');?>
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
                        <?php echo __d('business', 'You can enable Spam Challenge to force user to answer a challenge question in order to register.');?><br/>
                        <?php echo __d('business', 'To enable this feature, click System Settings -> Security -> Enable Spam Challenge')?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover" id="sample_1">
        <thead>
            <tr class="tbl_head">
                <th width="50px"><?php echo __d('business', 'ID'); ?></th>
                <th width="20px"></th>
                <th><?php echo __d('business', 'Name');?></th>
                <th><?php echo __d('business', 'Enabled');?></th>
                <th width="50px"><?php echo __d('business', 'Order');?></th>
                <th width="50px" data-hide="phone"><?php echo __d('business', 'Actions');?></th>
            </tr>
        </thead>
        <tbody>
            <?php $count = 0;
            foreach ($countries as $location): ?>
                <tr class="gradeX <?php ( ++$count % 2 ? "odd" : "even") ?>" id="<?php echo  $location['BusinessLocation']['id'] ?>">
                    <td width="50px"><?php echo  $location['BusinessLocation']['id'] ?></td>
                    <td>
                        <a href="javascript::void(0);" class="js_drop_down_link">
                            <i class="fa fa-sort-desc"></i>
                        </a>
                        <div class="link_menu" style="display:none;" >
                            <ul class="sub-menu" >
                                <li>
                                    
                                     <?php
                                        $this->MooPopup->tag(array(
                                            'href' => $this->Html->url(array("controller" => "business_locations",
                                                "action" => "admin_create",
                                                "plugin" => "business",
                                                $location['BusinessLocation']['id'],
                                                '',
                                            )),
                                            'title' => __d('business', "Edit"),
                                            'innerHtml' => __d('business', "Edit"),
                                            'target' => 'ajax'
                                        ));
                                    ?>
                                </li>
                                <li>
                                    <?php $this->MooPopup->tag(array(
                                                                    'href' => $this->Html->url(array("controller" => "business_locations",
                                                                    "action" => "admin_create_state",
                                                                    "plugin" => 'business',
                                                                    $location['BusinessLocation']['id'],
                                                                    '',
                                                                    )),
                                                                    'title' => __d('business', 'Add State/City/Province'),
                                                                    'innerHtml' => __d('business', 'Add State/City/Province'),
                                                                    'target' => 'ajax'
                                                                ));?>

                                </li>
                                <li>
                                    <a href="<?php echo $this->Html->url(array("controller" => "business_locations","action" => "admin_state","plugin" => 'business', $location['BusinessLocation']['id'])) ?>"><?php echo __d('business', "Manage States/Cities/Provinces") ?></a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <td class="reorder">
                        <?php
                            $this->MooPopup->tag(array( 'href'=>$this->Html->url(array("controller" => "business_locations",
                                                                    "action" => "create",
                                                                    "plugin" => 'business',
                                                                    $location['BusinessLocation']['id'],
                                                                )),
                                                        'title' => $location['BusinessLocation']['name'],
                                                        'innerHtml'=> $location['BusinessLocation']['name'],
                                                        'target' => 'ajax'
                                                     ));
                         ?>
                    </td>
                    <td width="50px" class="reorder"><?php echo  ($location['BusinessLocation']['enabled']) ? __d('business', 'Yes') : __d('business', 'No') ?></td>
                    <td width="50px" class="reorder"><input data-id="<?php echo $location['BusinessLocation']['id']?>" style="width:50px" type="text" name="data[ordering]" value="<?php echo $location['BusinessLocation']['ordering']?>" /> </td>
                    <td width="50px"><a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('business', 'Are you sure you want to delete this location? <br/> All the business within it will also be deleted. This cannot be undone!');?>', '<?php echo  $this->request->base ?>/admin/business/business_locations/delete/<?php echo  $location['BusinessLocation']['id'] ?>')"><i class="icon-trash icon-small"></i></a></td>
                </tr>
        <?php endforeach ?>           
        </tbody>
    </table>
    <div class="pagination pull-right">
            <?php echo $this->Paginator->prev('« '.__d('business', 'Previous'), null, null, array('class' => 'disabled')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next(__d('business', 'Next').' »', null, null, array('class' => 'disabled')); ?>
    </div>
</div>
