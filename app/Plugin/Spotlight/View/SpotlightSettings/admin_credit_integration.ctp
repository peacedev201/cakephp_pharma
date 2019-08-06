<?php
$this->Html->addCrumb(__('Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('spotlight','Credits Integration'), array('controller' => 'spotlight_settings', 'action' => 'admin_credit_integration'));
echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Spotlight'));
$this->end();
?>

<?php echo  $this->Moo->renderMenu('Spotlight', __d('spotlight','Credits Integration')); ?>

<div class="portlet-body form">
    <div class=" portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-12">
                    <div class="tab-content">
                        <div id="portlet_tab1" class="tab-pane active">
                        <?php if(!$credit_enable):?>
                            <?php echo __d( 'spotlight', 'You need to install credit plugin first. Please purchase credits plugin at' );?>
                            <a target="_blank" href="https://moosocial.com/addons/">https://moosocial.com/addons/</a>
                        <?php else:?>
                        <form id="createForm" class="form-horizontal" role="form" action="" method="post">
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">
                                        <?php echo __d('spotlight', 'Integrate with credits system');?>
                                    </label>
                                    <div class="col-md-7">
                                        <?php
                                            echo $this->Form->radio('spotlight_integrate_credit',array('1'=>__d('spotlight', 'Yes'),'0'=>__d('spotlight', 'No')), array('value'=> $value,'legend' => false,'separator' => '<br/>', 'label' => array('class' => 'radio-setting')));
                                        ?>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-offset-3 col-md-9">
                                            <input type="submit" class="btn btn-circle btn-action" value="<?php echo __d('spotlight', 'Save Changes');?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <?php endif;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>