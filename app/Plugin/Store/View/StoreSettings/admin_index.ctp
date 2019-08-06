<?php
    __d('store', 'Auto Approve Seller');
    __d('store', 'Auto Approve Product');
    __d('store', 'Product Per Page');
    __d('store', 'Currency position');
    __d('store', 'Wishlist item per page');
    __d('store', 'My Orders item per page');
    __d('store', 'Number of Most viewed products');
    __d('store', 'Number of Latest products');
    __d('store', 'Number of Related products');
    __d('store', 'Number of Sale products');
    __d('store', 'Order code prefix');
    __d('store', 'Site Profit');
    __d('store', 'Site profit percentage per order (for online transaction)');
	__d('store', 'Yes');
	__d('store', 'No');
	__d('store', 'Left');
	__d('store', 'Right');
    __d('store', 'Number of Featured products');
    __d('store', 'Number of Featured stores');
    __d('store', 'Show store list');
    __d('store', 'Allow to select seller to checkout separately at check out step');
    __d('store', 'Store Per Page');
    __d('store', 'Enable shipping');
    __d('store', 'Buy Featured Product');
    __d('store', 'Buy Featured Store');
    __d('store', 'By pass force login');
    __d('store', 'My files item per page');
    __d('store', 'Allow digital file extensions');
    __d('store', 'Separate by comma');
    __d('store', 'Allow video extensions');
    __d('store', 'Integrate credit for check-out process');
    __d('store', 'Show money type for product');
    __d('store','Buy Product');
    __d('store','Sell Product');
    __d('store','Store Profit');
    __d('store','Normal value');
    __d('store','Credit value');
    __d('store','All values');
    __d('store','Video item per page');
    __d('store','Product review item per page');
    __d('store','Number of Same Product Images');
    __d('store','Number of Same Product Videos');
    __d('store','Paypal type');
    __d('store','PayPal Adaptive');
    __d('store','PayPal Express Checkout');
    __d('store','Only users who buy product can review');
    __d('store',"Select 'No' if you want anyone can review product");
    __d('store',"Integrate with business directory plugin");
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->Html->addCrumb(__d('store',  'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('store',  'Settings Manager'), array(
        'controller' => 'store_settings', 
        'action' => 'admin_index'));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Stores'));
    $this->end();
?>
<?php echo$this->Moo->renderMenu('Store', __d('store', 'Settings'));?>

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