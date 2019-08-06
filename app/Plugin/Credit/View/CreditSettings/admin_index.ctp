<?php
__d('credit','Item per pages');
__d('credit','Enable credits');
__d('credit','Default sort by');
__d('credit','Total Earned Credits');
__d('credit','Test mode');
__d('credit','Disable');
__d('credit','Enable');
__d('credit','Current Balance');
__d('credit','Enable credits Plugin');
__d('credit','Paypal Email');
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->Html->addCrumb(__d('credit','Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('credit','Settings'), array('controller' => 'credit_settings', 'action' => 'admin_index'));

    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Credit'));
    $this->end();
?>
<?php
    $setting_id = null;
    foreach($settings as $setting)
    {
        if ($setting['Setting']['name'] == 'credit_currency_exchange')
        {
            $setting_id = $setting['Setting']['id'];
        }
    }
?>
<?php echo $this->Moo->renderMenu('Credit', __d('credit','Settings'));?>
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
<?php
echo $this->Html->scriptStart(array('inline' => false));
if ($setting_id)
?>
    <?php $currency = Configure::read('Config.currency'); ?>
    $('#text<?php echo $setting_id ?>').attr('style','display: initial;width:120px;');
    $( "<span style='padding-right:5px;'>1 <?php echo  $currency['Currency']['symbol'] ?> = </span>" ).insertBefore( "#text<?php echo $setting_id ?>" );
    $( "<span style='padding-left:5px;'><?php echo __('credits') ?></span>" ).insertAfter( "#text<?php echo $setting_id ?>" );
    $('.intergration-setting').attr('action', '<?php echo $this->request->base?>/admin/credit/credit_settings/quick_save');
<?php
echo $this->Html->scriptEnd();
?>
