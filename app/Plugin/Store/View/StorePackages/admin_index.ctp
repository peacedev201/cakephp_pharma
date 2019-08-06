<?php

echo $this->Html->css(array(
        'jquery-ui', 
        'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array(
        'jquery-ui', 
        'footable',
        'Store.admin'), array('inline' => false));
    $this->Html->addCrumb(__d('store',  'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('store',  "Manage Store Packages"), array('plugin' => 'store', 'controller' => 'store_packages', 'action' => 'admin_index'));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Stores'));
    $this->end();
?>
<?php echo$this->Moo->renderMenu('Store', __d('store', 'Packages'));?>
<div id="page-wrapper">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?php echo __d('store', "Manage Packages");?>
        </div>
        <div class="panel-body">
            <?php if(!empty($store_packages)):?>
            <form class="form-horizontal" id="adminForm" method="post" action="<?php echo $admin_url?>">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="sample_1">
                        <thead>
                            <tr>
                                <th>
                                    <?php echo __d('store',  'Name') ?>
                                </th>
                                <th style="width: 10%">
                                    <?php echo __d('store',  'Price') ?> (<?php echo $currency['Currency']['symbol'];?>)
                                </th>
                                <th style="text-align: center;width: 10%">
                                    <?php echo __d('store',  'Period') ?> (<?php echo __d('store',  'Days') ?>)
                                </th>
                                <th style="text-align: center;width: 15%">
                                    <?php echo __d('store',  'Remind before') ?> (<?php echo __d('store',  'Days') ?>)
                                </th>
                                <th style="text-align: center;width: 10%">
                                    <?php echo __d('store',  'Enable');?>
                                </th>
                                <th style="width: 6%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $count = 0;
                                foreach ($store_packages as $store_package): 
                                    $store_package = $store_package['StorePackage'];
                            ?>
                                <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                                    <td>
                                        <div style="display: none;">
                                            <input type="checkbox" value="<?php echo $store_package['id']?>" class="multi_cb" id="cb<?php echo $store_package['id']?>" name="data[cid][]">
                                        </div>
                                        <?php echo $store_package['name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $store_package['price']; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $store_package['period']; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $store_package['reminder']; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if($store_package['enable']):?>
                                        <a href="javascript:void(0)" onclick="jQuery.admin.action('<?php echo $store_package['id'];?>', 'disable')">
                                            <i class="fa fa fa-check" title="<?php echo __d('store', "Disable");?>"></i>
                                        </a>
                                        <?php else:?> 
                                        <a href="javascript:void(0)" onclick="jQuery.admin.action('<?php echo $store_package['id'];?>', 'enable')">
                                            <i class="fa fa fa-close" title="<?php echo __d('store', "Enable");?>"></i>
                                        </a>
                                        <?php endif;?>
                                    </td>
                                    <td>
                                        <a href="<?php echo $admin_url.'create/'.$store_package['id'];?>">
                                            <?php echo __d('store', "Edit");?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </form>
            <?php else:?>
                <?php echo __d('store', "No Packages");?>
            <?php endif;?>
        </div>
    </div>
</div>
