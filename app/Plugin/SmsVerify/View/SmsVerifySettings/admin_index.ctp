<?php
__d('sms_verify','Enable Sms Verify');
__d('sms_verify','Enable captcha');
__d('sms_verify','By pass sms verify for all existing members');
__d('sms_verify','Enable captcha');
__d('sms_verify','Please check this option if you want all members who signed up before this plugin is installed can continue using site without go thru sms verification process');

echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb(__d('sms_verify','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('sms_verify', 'Sms Verify'), '/admin/sms_verify/sms_verifys');
$this->Html->addCrumb(__d('sms_verify','Sms Verify Settings'), array('controller' => 'sms_verify_settings', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Sms Verify"));
$this->end();
?>

<div class="portlet-body form">
    <div class=" portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <?php echo $this->Moo->renderMenu('SmsVerify', __d('sms_verify','Settings'));?>
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