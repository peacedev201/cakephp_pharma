<?php

echo $this->Html->css(array(
        'jquery-ui', 
        'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array(
        'jquery-ui', 
        'footable',
        'Store.admin'), array('inline' => false));
    $this->Html->addCrumb(__d('store',  'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('store',  "Manage Store Shipping Methods"), array('plugin' => 'store', 'controller' => 'store_shipping_methods', 'action' => 'admin_index'));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Stores'));
    $this->end();
?>
<?php echo$this->Moo->renderMenu('Store', __d('store', 'Shipping Methods'));?>
<div id="page-wrapper">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?php echo __d('store', "Manage Shipping Methods");?>
        </div>
        <div class="panel-body">
            <?php if(!empty($store_shipping_methods)):?>
            <form class="form-horizontal" id="adminForm" method="post" action="<?php echo $admin_url?>">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="sample_1">
                        <thead>
                            <tr>
                                <th>
                                    <?php echo __d('store',  'Name') ?>
                                </th>
                                <th style="width: 6%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $count = 0;
                                foreach ($store_shipping_methods as $store_shipping_method): 
                                    $store_shipping_method = $store_shipping_method['StoreShippingMethod'];
                            ?>
                                <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                                    <td>
                                        <div style="display: none;">
                                            <input type="checkbox" value="<?php echo $store_shipping_method['id']?>" class="multi_cb" id="cb<?php echo $store_shipping_method['id']?>" name="data[cid][]">
                                        </div>
                                        <?php echo $store_shipping_method['name']; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo $admin_url.'create/'.$store_shipping_method['id'];?>">
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
                <?php echo __d('store', "No Shipping Methods");?>
            <?php endif;?>
        </div>
    </div>
</div>
