<?php
    echo $this->Html->script(array(
        '/store/js/admin'), array('inline' => false));
    $this->Html->addCrumb(__d('store',  'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('store',  "Manage Store Categories"), array(
        'controller' => 'store_categories', 
        'action' => 'admin_index'
    ));
    if(!empty($parentCategory)){
        $this->Html->addCrumb(html_entity_decode($parentCategory['StoreCategory']['name']), array(
            'controller' => 'store_categories', 
            'action' => 'admin_index',
            $parent_id > 0 ? $parent_id : ''
        )); 
    }
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Stores'));
    $this->end();
?>
<style type="text/css">
    .pagination > li.current.paginate_button ,
    .pagination > li.disabled {
        position: relative;
        float: left;
        padding: 6px 12px;
        margin-left: -1px;
        line-height: 1.42857143;
        color: #428bca;
        text-decoration: none;
        background-color: #eee;
        border: 1px solid #ddd;
    }
</style>
<?php echo$this->Moo->renderMenu('Store', __d('store', 'Store Categories'));?>
<div id="page-wrapper">
	<div class="panel panel-default">
        <div class="panel-heading">
            <?php echo !empty($parentCategory) ? sprintf(__d('store', "Manage %s's Sub Categories"), $parentCategory['StoreCategory']['name']) : __d('store', "Manage Store Categories");?>
            <div class="pull-right">
                <div class="btn-group">
                    <?php if(!empty($parentCategory)):?>
                    <a href="<?php echo $parentCategory['StoreCategory']['parent_id'] > 0 ? $admin_link.'index/'.$parentCategory['StoreCategory']['parent_id'] : $admin_link?>" class="btn btn-primary btn-xs" style="margin-right:10px">
                        <?php echo __d('store', "Back to parent category");?>
                    </a>
                    <?php endif;?>
                    <button aria-expanded="false" type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown">
                        <?php echo __d('store', "Actions");?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li>
                            <a title="<?php echo __d('store', "Add New");?>" href="<?php echo $admin_link?>create/<?php echo !empty($parentCategory) ? '?sub='.$parentCategory['StoreCategory']['id'] : "";?>">
                                <?php echo __d('store', "Add New");?>
                            </a>
                        </li>
                        <li>
                            <a title="<?php echo __d('store', "Ordering");?>" href="javascript:void(0)" onclick="jQuery.admin.saveAll('ordering')">
                                <?php echo __d('store', "Ordering");?>
                            </a>
                        </li>
                        <li>
                            <a title="<?php echo __d('store', "Enable");?>" href="javascript:void(0)" onclick="jQuery.admin.activeAll('enable')">
                                <?php echo __d('store', "Enable");?>
                            </a> 
                        </li>
                        <li>
                            <a title="<?php echo __d('store', "Disable");?>" href="javascript:void(0)" onclick="jQuery.admin.activeAll('disable')">
                                <?php echo __d('store', "Disable");?>
                            </a> 
                        </li>
                        <li>
                            <a title="<?php echo __d('store', "Delete");?>" href="javascript:void(0)" onclick="jQuery.admin.deleteAll('delete')">
                                <?php echo __d('store', "Delete");?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="panel-body">
            <form id="searchForm" method="get" action="<?php echo $parent_id > 0 ? $admin_link.'index/'.$parent_id : $admin_link?>">
                <div class="form-group">
                    <div class="col-md-5"></div>
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
            <?php if(!empty($storeCategories)):?>
                <form class="form-horizontal" id="adminForm" method="post" action="<?php echo $admin_link;?>">
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                            <tr>
                                <th style="width: 7px">
                                    <?php echo $this->Form->checkbox('', array(
                                        'hiddenField' => false,
                                        'div' => false,
                                        'label' => false,
                                        'onclick' => 'jQuery.admin.toggleCheckboxes(this)'
                                    ));?>
                                </th>      
                                <th width="20px"></th>
								<th class="text-left"><?php echo __d('store', 'Name');?></th>
                                <th style="width: 12%" class="text-left"><?php echo __d('store', 'Child Categories');?></th>
								<th style="width: 5%"><?php echo __d('store', 'Enable');?></th>
								<th style="width: 5%"><?php echo __d('store', 'Ordering');?></th>
                                <th style="width: 10%;text-align:center;"><?php echo __d('store', 'Action');?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $count = 0;
                                foreach ($storeCategories as $storeCategory): 
                                    $storeCategory = $storeCategory['StoreCategory'];
                            ?>
                                <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                                    <td style="text-align: center">
                                        <input type="checkbox" value="<?php echo $storeCategory['id']?>" class="multi_cb" id="cb<?php echo $storeCategory['id']?>" name="data[cid][]">
                                    </td>
                                    <td style="text-align:center">
                                        <a href="javascript:void(0);" class="js_drop_down_link">
                                            <i class="fa fa-sort-desc"></i>
                                        </a>
                                        <div class="link_menu" style="display:none;" >
                                            <ul class="sub-menu" >
                                                <li>
                                                    <a href="<?php echo $admin_link.'create/?sub='.$storeCategory['id'];?>">
                                                        <?php echo __d('store', "Add Sub Category");?>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="<?php echo $admin_link.'index/'.$storeCategory['id'];?>">
                                                        <?php echo __d('store', "Manage Sub Categories");?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <?php echo $storeCategory['name']; ?>
                                    </td>
                                    <td style="text-align: center">
                                        <?php echo $storeCategory['child_count']; ?>
                                    </td>
                                    <td style="text-align: center">
                                        <?php if($storeCategory['enable']):?>
                                        <a href="javascript:void(0)" onclick="jQuery.admin.action('<?php echo $storeCategory['id'];?>', 'disable')">
                                            <i class="fa fa fa-check" title="<?php echo __d('store', "Disable");?>"></i>
                                        </a>
                                        <?php else:?> 
                                        <a href="javascript:void(0)" onclick="jQuery.admin.action('<?php echo $storeCategory['id'];?>', 'enable')">
                                            <i class="fa fa fa-close" title="<?php echo __d('store', "Enable");?>"></i>
                                        </a>
                                        <?php endif;?>
                                    </td>
                                    <td>
                                        <?php echo $this->Form->text('ordering.', array(
                                            'class' => 'form-control',
                                            'div' => false,
                                            'label' => false,
                                            'value' => $storeCategory['ordering']
                                        ));?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo $admin_link.'create/'.$storeCategory['id'];?>">
                                            <?php echo __d('store', "Edit");?>
                                        </a>
                                        &#124;
                                        <a href="javascript:void(0)" onclick="jQuery.admin.action('<?php echo $storeCategory['id'];?>', 'delete')">
                                            <?php echo __d('store', "Delete");?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach ?>
						</tbody>
                    </table>
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
                <?php echo __d('store', "No Store Categories");?>
            <?php endif;?>
        </div>
    </div>
</div>

<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>

    $( document ).ready(function() {
        $('.js_drop_down_link').click(function()
        {
           eleOffset = $(this).offset();

           $('#js_drop_down_cache_menu').remove();

           $('body').prepend('<div id="js_drop_down_cache_menu" style="position:absolute; left:' + eleOffset.left + 'px; top:' + (eleOffset.top + 15) + 'px; z-index:9999;"><div class="link_menu" style="display:block;">' + $(this).parent().find('.link_menu:first').html() + '</div></div>');

                   $('#js_drop_down_cache_menu .link_menu').hover(function()
                   {

                   },
                   function()
                   {
                           $('#js_drop_down_cache_menu').remove();
                   });	    	

           return false;
       });
    });
    
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>