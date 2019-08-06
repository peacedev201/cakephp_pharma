<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_manager'), 
    'object' => array('$', 'store_manager')
));?>
    store_manager.initManage();
<?php $this->Html->scriptEnd(); ?>

<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
    <?php echo $this->Element('manager_menu'); ?>
<?php $this->end(); ?>

<?php
    $parent_attribute = $parent_attribute != null ? $parent_attribute['StoreAttribute'] : null;
?>
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
                <?php if($parent_attribute == null):?>
                <span class="divider-last"></span>
                <?php endif;?>
            </li>
            <?php if($parent_attribute != null):?>
            <li>
                <a href="">
                    <?php echo $parent_attribute['name'];?>
                </a>
                <span class="divider-last"></span>
            </li>
            <?php endif;?>
        </ul>
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php echo __d('store', "Manage Attributes");?>
                <div class="pull-right">
                    <div class="btn-group">
                        <button aria-expanded="false" type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown">
                            <?php echo __d('store', "Actions");?>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li>
                                <a title="<?php echo __d('store', "Add New");?>" href="<?php echo $url;?>create">
                                    <?php echo __d('store', "Add New");?>
                                </a>
                            </li>
                            <li>
                                <a title="<?php echo __d('store', "Enable");?>" href="javascript:void(0)" id="enable_all">
                                    <?php echo __d('store', "Enable");?>
                                </a> 
                            </li>
                            <li>
                                <a title="<?php echo __d('store', "Disable");?>" href="javascript:void(0)" id="disable_all">
                                    <?php echo __d('store', "Disable");?>
                                </a> 
                            </li>
                            <li>
                                <a title="<?php echo __d('store', "Delete");?>" href="javascript:void(0)" id="delete_all">
                                    <?php echo __d('store', "Delete");?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="panel-body">
                <p>
                    <?php echo __d('store', 'These attributes will be visible to buyers on the product page and they will be able to select desired attribute values while adding the product to cart. The price of the product will be changed based on selected attributes. You can configure price of product based on attributes when adding/editting product.');?>
                </p>
                <form id="searchForm" method="get" action="<?php echo $url;?>">
                    <div class="form-group form-search-app">
                        <div class="col-md-3">

                        </div>
                        <div class="col-md-4">
                            <select name="attribute_id" class="form-control">
                                <option value="0"><?php echo __d('store', "All");?></option>
                                <?php echo $this->Category->outputOptionType($listAttributes, 'StoreAttribute', null, !empty($search['attribute_id']) ? $search['attribute_id'] : '');?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <?php echo $this->Form->input("keyword", array(
                                'div' => false,
                                'label' => false,
                                'class' => 'form-control',
                                'placeholder' => __d('store', 'Keyword'),
                                'name' => 'keyword',
                                'value' => !empty($search['keyword']) ? $search['keyword'] : ''
                            ));?>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-primary btn-lg" type="submit"><?php echo __d('store', "Search");?></button>
                        </div>
                        <div class="clear"></div>
                    </div>
                </form>
                <?php if($attributes != null):?>
                    <form class="form-horizontal" id="adminForm" method="post" action="<?php echo $url;?>">
                        <div class="div-detail-app manage-attribute">
                            <div class="div-full-breabcrum">
                                <div class="col-md-1 col-custom-1">
                                    <?php echo $this->Form->checkbox('', array(
                                        'hiddenField' => false,
                                        'class' => 'group_checkbox'
                                    ));?>
                                </div>
                                <div class="col-md-8 col-custom-4">
                                    <div class="group-group text-left">
                                        <i class="text-app"><?php echo __d('store', 'Name');?></i>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="group-group">
                                        <i class="text-app"><?php echo __d('store', 'Enable');?></i>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="group-group">
                                        <i class="text-app"></i>
                                    </div>
                                </div>
                            </div>
                            <?php foreach($attributes as $attribute):
                                $attribute = $attribute['StoreAttribute'];
                            ?>
                            <div class="div-detail-row ">
                                <div class="top-list-brb">
                                    <?php echo __d('store', "Listing");?>
                                </div>
                                <div class="col-xs-12 col-md-1 col-custom-1">
                                    <div class="group-group">
                                        <i class="visible-sm visible-xs icon-app material-icons">check_circle</i>
                                        <i class="text-app">
                                            <?php echo $this->Form->checkbox('cid.', array(
                                                'hiddenField' => false,
                                                'id' => 'cb'.$attribute['id'],
                                                'class' => 'multi_cb',
                                                'value' => $attribute['id']
                                            ));?>
                                        </i>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-8 col-custom-4">
                                    <div class="group-group group-group-name text-left">
                                        <i class="visible-sm visible-xs icon-app material-icons">title</i>
                                        <i class="text-app"><?php echo $attribute['name'];?></i>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-1">
                                    <div class="group-group">
                                        <i class="visible-sm visible-xs icon-app material-icons">done_all</i>
                                            <?php if($attribute['enable']):?>
                                        <a href="javascript:void(0)" class="action_disable" data-id="<?php echo $attribute['id']?>">
                                            <i class="text-app material-icons" title="<?php echo __d('store', "Disable");?>">done</i>
                                        </a>
                                            <?php else:?> 
                                        <a href="javascript:void(0)" class="action_enable" data-id="<?php echo $attribute['id']?>">
                                            <i class="text-app material-icons" title="<?php echo __d('store', "Enable");?>">clear</i>
                                        </a>
                                            <?php endif;?>
                                    </div>
                                </div>
                                <div class="hidden-xs hidden-sm col-md-2 padding-0 no-border-right">
                                    <?php if($attribute['parent_id'] == 0):?>
                                        <a class="action-manage" href="<?php echo $url;?>index/<?php echo $attribute['id'];?>">
                                            <i class="text-full"><?php echo __d('store', "Sub");?></i>
                                        </a>
                                    <?php endif;?>
                                    <a class="action-manage" href="<?php echo $url;?>create/<?php echo $attribute['id'];?>">
                                        <i class="text-full"> <?php echo __d('store', 'Edit');?></i>
                                    </a>
                                    <a class="action-manage action_delete" href="javascript:void(0)" data-id="<?php echo $attribute['id']?>">
                                        <i class="text-full "><?php echo __d('store', 'Delete') ?></i>                                
                                    </a>
                                </div>
                                <?php if($attribute['parent_id'] == 0):?>
                                <div class="visible-sm visible-xs col-xs-4 iconnottext isleft">
                                    <a class="action-manage" href="<?php echo $url;?>index/<?php echo $attribute['id'];?>">
                                        <i class="material-icons">subdirectory_arrow_right</i>
                                    </a>
                                </div>
                                <?php endif;?>
                                <div class="visible-sm visible-xs <?php if($attribute['parent_id'] == 0):?>col-xs-4<?php else:?>col-xs-6<?php endif;?> iconnottext isleft">
                                    <a href="<?php echo $url;?>create/<?php echo $attribute['id'];?>">
                                        <i class="material-icons">create</i>
                                    </a>
                                </div>
                                <div class="visible-sm visible-xs <?php if($attribute['parent_id'] == 0):?>col-xs-4<?php else:?>col-xs-6<?php endif;?> iconnottext">
                                    <a class="action_delete" href="javascript:void(0)" data-id="<?php echo $attribute['id']?>">
                                        <i class="material-icons">delete_sweep</i>                                  
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-sm-3">
                            <?php echo $this->Paginator->counter(array(
                                'separator' => __d('store', ' of a total of ')
                            ));?>
                        </div>
                        <div class="col-sm-9">
                            <div id="dataTables-example_paginate" class="dataTables_paginate paging_simple_numbers">
                                <ul class="pagination">
                                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('store', 'First'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('store', 'Previous'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                                    <?php echo $this->Paginator->numbers(array('class' => 'paginate_button', 'tag' => 'li', 'separator' => '')); ?>
                                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('store', 'Next'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('store', 'Last'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php else:?>
                    <?php if($parent_attribute != null):?>
                        <?php echo __d('store', "No Sub Attributes");?>
                    <?php else:?>
                        <?php echo __d('store', "No Attributes");?>
                    <?php endif;?>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>