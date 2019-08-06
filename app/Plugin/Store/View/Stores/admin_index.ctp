<?php
    echo $this->Html->css(array(
        'jquery-ui', 
        'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array(
        '/store/js/admin'), array('inline' => false));
    $this->Html->addCrumb(__d('store',  'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('store',  'Sellers Manager'), array(
        'controller' => 'stores', 
        'action' => 'admin_index'));
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
<?php echo$this->Moo->renderMenu('Store', __d('store', 'Sellers'));?>
<div id="page-wrapper">
	<div class="panel panel-default">
        <div class="panel-heading">
            <?php echo __d('store', "Manage Sellers");?>
            <div class="pull-right">
                <div class="btn-group">
                    <button aria-expanded="false" type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown">
                        <?php echo __d('store', "Actions");?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
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
                    </ul>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="panel-body">
            <form id="searchForm" method="get" action="<?php echo $admin_url;?>">
                <div class="form-group">
                    <div class="col-md-5"></div>
                    <div class="col-md-6">
                        <?php echo $this->Form->input("keyword", array(
                            'div' => false,
                            'label' => false,
                            'class' => 'form-control',
                            'placeholder' => __d('store', 'Keyword'),
                            'name' => 'keyword',
                            'value' => $keyword
                        ));?>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-primary btn-lg" type="submit"><?php echo __d('store', "Search");?></button>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
            <?php if(!empty($stores)):?>
                <form class="form-horizontal" id="adminForm" method="post" action="<?php echo $this->request->base?>/admin/store/stores/">
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="sample_1">
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
                                <th>
                                    <?php echo $this->Paginator->sort('name', __d('store',  'Name')); ?>
                                </th>
                                <th style="width: 300px">
                                    <?php echo $this->Paginator->sort('User.name', __d('store',  'Author')); ?>
                                </th>
                                <th style="width: 200px">
                                    <?php echo $this->Paginator->sort('created', __d('store',  'Create Date')); ?>
                                </th>
                                <th style="text-align: center;width: 15%">
                                    <?php echo __d('store',  'Featured');?>
                                </th>
                                <th style="text-align: center;width: 80px">
                                    <?php echo __d('store',  'Active');?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php 
                            $count = 0;
                            foreach ($stores as $store): 
                                $user = $store['User'];
                                $store = $store['Store'];
                        ?>
                            <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                                <td style="text-align: center">
                                    <input type="checkbox" value="<?php echo $store['id']?>" class="multi_cb" id="cb<?php echo $store['id']?>" name="data[cid][]">
                                </td>
                                <td>
                                    <?php echo $this->Text->truncate(h($store['name']), 100, array('eclipse' => '...')) ?>
                                </td>
                                <td>
                                    <a href="<?php echo $this->request->base?>/admin/users/edit/<?php echo $user['id']?>">
                                        <?php echo h($user['name'])?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo $this->Time->niceShort($store['created'])?>
                                </td>
                                <td style="text-align: center">
                                    <?php if($store['featured']):?>
                                        <a href="javascript:void(0)" onclick="jQuery.admin.action('<?php echo $store['id'];?>', 'unfeatured')">
                                            <i class="fa fa-check" title="<?php echo __d('store', "Un featured");?>"></i>
                                        </a>
                                        <?php if($store['unlimited_feature']):?>
                                            (<?php echo __d('store', 'Unlimited') ?>)
                                        <?php else:?>
                                            (<?php echo __d('store', 'Expire').": ".date('M d Y', strtotime($store['feature_expiration_date']));?>)
                                        <?php endif;?>
                                    <?php else:?>
                                        <a href="javascript:void(0)" data-id="<?php echo $store['id'];?>" class="set_feature">
                                            <i class="fa fa-close" title="<?php echo __d('store', "Feature");?>"></i>
                                        </a>
                                    <?php endif;?>
                                </td>
                                <td style="text-align: center">
                                    <?php if($store['enable']):?>
                                        <a href="javascript:void(0)" onclick="jQuery.admin.action('<?php echo $store['id'];?>', 'disable')">
                                            <i class="fa fa-check" title="<?php echo __d('store', "Disable");?>"></i>
                                        </a>
                                    <?php else:?> 
                                        <a href="javascript:void(0)" onclick="jQuery.admin.action('<?php echo $store['id'];?>', 'enable')">
                                            <i class="fa fa-close" title="<?php echo __d('store', "Enable");?>"></i>
                                        </a>
                                    <?php endif;?>
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
                <?php echo __d('store', "No Sellers");?>
            <?php endif;?>
        </div>
    </div>
</div>

<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>
    
    //view detail
    jQuery(document).on('click', '.set_feature', function(){
        jQuery.post("<?php echo $admin_url;?>featured_dialog/" + jQuery(this).data('id'), '', function(data){
            jQuery('#storeModal .modal-content').empty().append(data); 
            jQuery('#storeModal').modal();
        });
    })
    
    jQuery(document).on('click', '#setFeatureButton', function(){
        disableButton('setFeatureButton');
        $.post("<?php echo $admin_url;?>save_feature", $("#setFeatureForm").serialize(), function(data){
            var json = $.parseJSON(data);
            if(json.result == 0)
            {
                $("#setFeatureMessage").html(json.message).show();
                enableButton('setFeatureButton');
            }
            else
            {
                location.reload();
            } 
        });
    });
    
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>