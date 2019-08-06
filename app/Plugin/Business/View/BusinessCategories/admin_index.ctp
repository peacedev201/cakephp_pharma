<?php
echo $this->Html->css(array('jquery-ui', 'footable.core.min', 'Business.business-admin.css', 'fineuploader'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable', 'Business.jquery.fileuploader', 'Business.admin'), array('inline' => false));
echo $this->addPhraseJs(array(
    'tmaxsize' => __d('business', 'Can not upload file more than ' . $file_max_upload),
    'tdesc' => __d('business', 'Drag or click here to upload photo'),
    'please_confirm' => __d('business', 'Please Confirm')
));
$businessHelper = MooCore::getInstance()->getHelper('Business_Business');

$this->Html->addCrumb(__d('business', 'Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('business', 'Manage Categories'), array('controller' => 'business_categories', 'action' => 'admin_index'));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Business'));
$this->end();
?>
<?php echo  $this->Moo->renderMenu('Business', __d('business', 'Categories')); ?>

<?php
$this->Paginator->options(array('url' => $this->passedArgs));
?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
    jQuery.admin.initOptions();
<?php $this->Html->scriptEnd(); ?>
<div class="portlet-body">
    <?php if(!empty($cat_paths)): ?>
        <div class="category_path">
            <?php echo __d('business', 'Path:'); ?>
        </div>
        <ul class="category_path_list">
            <?php foreach($cat_paths as $key => $cat_path): ?>
                <li>
                    <a href="<?php echo $this->Html->url(array("controller" => "business_categories","action" => "admin_index","plugin" => 'business', $cat_path['BusinessCategory']['id'])) ?>"><?php echo $cat_path['BusinessCategory']['name']; ?></a>
                    <?php if($key < count($cat_paths)-1): ?>
                        <i class="fa fa-angle-right"></i>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <div class="table-toolbar">
        <div class="">
            <div class="col-md-6">
                <div class="btn-group">
                    <button class="btn btn-gray" data-toggle="modal" data-target="#ajax" href="<?php echo  $this->request->base ?>/admin/business/business_categories/create/<?php  echo $parent_id ?>">
                        <?php echo __d('business', 'Add New');?>
                    </button>
                    <a style="margin-left: 10px" onclick="jQuery.admin.saveOrder('<?php echo $this->request->base?>/admin/business/business_categories/save_order/');" class="btn btn-gray" >
                        <?php echo __d('business', 'Save order');?>
                    </a>
                </div>
            </div>
            <div class="col-md-6">   
                <form id="searchForm" method="get" action="<?php echo $this->request->base?>/admin/business/business_categories/<?php echo $parent_id > 0 ? 'index/'.$parent_id : '';?>">
                    <div class="">
                        <div class="col-md-4"></div>
                        <div class="col-md-6">
                            <?php echo $this->Form->select('filter', array(
                                '' => __d('business', 'All'),
                                '0' => __d('business', 'Enable'),
                                '1' => __d('business', 'Disable'),
                                '2' => __d('business', 'Created by admin'),
                                '3' => __d('business', 'Created by user'),
                            ) , array(
                                'empty' => false,
                                'class' => 'form-control',
                                'name' => 'filter',
                                'value' => isset($search['filter']) ? $search['filter'] : '',
                            ));?>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-gray" type="submit"><?php echo __d('business', "Search");?></button>
                        </div>
                        <div class="clear"></div>
                    </div>
                </form>
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
    <?php if(!empty($cats)): ?>
        <table class="table table-striped table-bordered table-hover" id="sample_1">
            <thead>
                <tr class="tbl_head">
                    <th width="50px"><?php echo __d('business', 'ID'); ?></th>
                    <th width="20px"></th>
                    <!-- <th width="50px"><?php echo __d('business', 'Icon');?></th> -->
                    <th><?php echo __d('business', 'Name');?></th>
                    <th width="50px"><?php echo __d('business', 'Order');?></th>
                    <th width="50px" data-hide="phone"><?php echo __d('business', 'Enable');?></th>
                    <th width="100px" data-hide="phone"><?php echo __d('business', 'User Create');?></th>
                    <th width="50px" data-hide="phone"><?php echo __d('business', 'Actions');?></th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 0;
                foreach ($cats as $cat): ?>
                    <tr class="gradeX <?php ( ++$count % 2 ? "odd" : "even") ?>" id="<?php echo  $cat['BusinessCategory']['id'] ?>">
                        <td ><?php echo  $cat['BusinessCategory']['id'] ?></td>
                        <td >
                            <a href="javascript::void(0);" class="js_drop_down_link">
                                <i class="fa fa-sort-desc"></i>
                            </a>
                            <div class="link_menu" style="display:none;" >
                                <ul class="sub-menu" >
                                    <li>

                                         <?php
                                            $this->MooPopup->tag(array(
                                                'href' => $this->Html->url(array("controller" => "business_categories",
                                                    "action" => "admin_create",
                                                    "plugin" => "business",
                                                    $cat['BusinessCategory']['parent_id'],
                                                    $cat['BusinessCategory']['id']
                                                )),
                                                'title' => __d('business', "Edit"),
                                                'innerHtml' => __d('business', "Edit"),
                                                'target' => 'ajax'
                                            ));
                                        ?>
                                    </li>
                                    <?php if($cat['BusinessCategory']['parent_id'] == 0):?>
                                    <li>
                                        <?php $this->MooPopup->tag(array(
                                            'href' => $this->Html->url(array(   
                                                "controller" => "business_categories",
                                                "action" => "admin_create",
                                                "plugin" => 'business',
                                                $cat['BusinessCategory']['id'],
                                                )),
                                                'title' => __d('business', 'Add Sub Category'),
                                                'innerHtml' => __d('business', 'Add Sub Category'),
                                                'target' => 'ajax'
                                            ));?>

                                    </li>
                                    <li>
                                        <a href="<?php echo $this->Html->url(array("controller" => "business_categories","action" => "admin_index","plugin" => 'business', $cat['BusinessCategory']['id'])) ?>"><?php echo __d('business', "Manage Sub Categories") ?></a>
                                    </li>
                                    <?php endif;?>
                                </ul>
                            </div>
                        </td>
                        <td class="reorder">
                            <?php
                                $this->MooPopup->tag(array(
                                    'href'=>$this->Html->url(array(     
                                        "controller" => "business_categories",
                                        "action" => "create",
                                        "plugin" => 'business',
                                        $cat['BusinessCategory']['parent_id'],
                                        $cat['BusinessCategory']['id']
                                    )),
                                    'title' => $cat['BusinessCategory']['name'],
                                    'innerHtml'=> $cat['BusinessCategory']['name'],
                                    'target' => 'ajax'
                                 ));
                             ?>
                        </td>
                        <td width="50px" class="reorder"><input data-id="<?php echo $cat['BusinessCategory']['id']?>" style="width:50px" type="text" name="data[ordering]" value="<?php echo $cat['BusinessCategory']['ordering']?>" /> </td>
                        <td width="50px" class="reorder"><?php echo  ($cat['BusinessCategory']['enable']) ? __d('business', 'Yes') : __d('business', 'No') ?></td>
                        <td width="100px" class="reorder"><?php echo  ($cat['BusinessCategory']['user_create']) ? __d('business', 'Yes') : __d('business', 'No') ?></td>
                        <td width="50px"><a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('business', 'Are you sure you want to delete this category?');?>', '<?php echo  $this->request->base ?>/admin/business/business_categories/delete/<?php echo  $cat['BusinessCategory']['id'] ?>')"><i class="icon-trash icon-small"></i></a></td>
                    </tr>
            <?php endforeach ?>           
            </tbody>
        </table>
        <div class="pagination pull-right">
                <?php echo $this->Paginator->prev('« '.__d('business', 'Previous'), null, null, array('class' => 'disabled')); ?>
                <?php echo $this->Paginator->numbers(); ?>
                <?php echo $this->Paginator->next(__d('business', 'Next').' »', null, null, array('class' => 'disabled')); ?>
        </div>
    <?php else: ?>
        <p><?php echo __d('business', 'No results found'); ?></p>
    <?php endif ?>
</div>
