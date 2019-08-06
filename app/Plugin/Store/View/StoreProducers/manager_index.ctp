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
                <a href="<?php echo STORE_MANAGER_URL.'producers/';?>">
                    <?php echo __d('store', "Manage Producers");?>
                </a>
                <span class="divider-last"></span>
            </li>
        </ul>
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php echo __d('store', "Manage Producers");?>
                <div class="pull-right">
                    <div class="btn-group">
                        <button aria-expanded="false" type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown">
                            <?php echo __d('store', "Actions");?>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li>
                                <a title="<?php echo __d('store', "Add New");?>" href="<?php echo STORE_MANAGER_URL?>producers/create">
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
                <form id="searchForm" method="get" action="<?php echo STORE_MANAGER_URL.'producers/index';?>">
                    <div class="form-group form-search-app">
                        <div class="col-md-5">

                        </div>
                        <div class="col-md-6">
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
                <?php if(!empty($producers)):?>
                    <form class="form-horizontal" id="adminForm" method="post" action="<?php echo STORE_MANAGER_URL.'producers/';?>">
                        <div class="div-detail-app manage-producer">
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
                            <?php foreach($producers as $producer):
                                $producer = $producer['StoreProducer'];
                            ?>
                            <div class="div-detail-row ">
                                <div class="top-list-brb">
                                    <?php echo __d('store', "Producer Listing");?>
                                </div>
                                <div class="col-xs-12 col-md-1 col-custom-1">
                                    <div class="group-group">
                                        <i class="visible-sm visible-xs icon-app material-icons">check_circle</i>
                                        <i class="text-app">
                                            <?php echo $this->Form->checkbox('cid.', array(
                                                'hiddenField' => false,
                                                'id' => 'cb'.$producer['id'],
                                                'class' => 'multi_cb',
                                                'value' => $producer['id']
                                            ));?>
                                        </i>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-8 col-custom-4">
                                    <div class="group-group group-group-name text-left">
                                        <i class="visible-sm visible-xs icon-app material-icons">title</i>
                                        <i class="text-app"><?php echo $producer['name'];?></i>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-1">
                                    <div class="group-group">
                                        <i class="visible-sm visible-xs icon-app material-icons">done_all</i>
                                            <?php if($producer['enable']):?>
                                        <a href="javascript:void(0)" class="action_disable" data-id="<?php echo $producer['id']?>">
                                            <i class="text-app material-icons" title="<?php echo __d('store', "Disable");?>">done</i>
                                        </a>
                                            <?php else:?> 
                                        <a href="javascript:void(0)" class="action_enable" data-id="<?php echo $producer['id']?>">
                                            <i class="text-app material-icons" title="<?php echo __d('store', "Enable");?>">clear</i>
                                        </a>
                                            <?php endif;?>
                                    </div>
                                </div>
                                <div class="hidden-xs hidden-sm col-md-2 padding-0 no-border-right">
                                    <a class="action-manage" href="<?php echo $url;?>create/<?php echo $producer['id'];?>">
                                        <i class="text-full"> <?php echo __d('store', 'Edit');?></i>
                                    </a>

                                    <a class="action-manage action_delete" href="javascript:void(0)" data-id="<?php echo $producer['id']?>">
                                        <i class="text-full "><?php echo __d('store', 'Delete') ?></i>                                
                                    </a>
                                </div>
                                <div class="visible-sm visible-xs col-xs-6 iconnottext isleft">
                                    <a href="<?php echo $url;?>create/<?php echo $producer['id'];?>">
                                        <i class="material-icons">create</i>
                                    </a>
                                </div>
                                <div class="visible-sm visible-xs col-xs-6 iconnottext">
                                    <a class="action_delete" href="javascript:void(0)" data-id="<?php echo $producer['id']?>">
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
                    <?php echo __d('store', "No Producer");?>
                <?php endif;?>
            </div>

        </div>
    </div>
</div>