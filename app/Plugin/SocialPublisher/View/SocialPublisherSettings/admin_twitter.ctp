<style>
    .intergration-setting .input.checkbox .checker {
         padding-top: 0 !important; 
    }
</style>
<?php
echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
$this->Html->addCrumb(__('Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb( __d('social_publisher','Social Publisher Settings'), array('controller' => 'social_publisher_settings', 'action' => 'admin_index'));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Social Publisher'));
$this->end();
?>
<?php echo $this->Moo->renderMenu('SocialPublisher', __d('social_publisher', 'Twitter'));?>

<div class="portlet-body form">
    <div class=" portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-12">
                    <div class="tab-content">
                        <div class="tab-pane active" id="portlet_tab1">
                            <p class="form-description"><?php echo __( 'You can now integrate mooSocial to Twitter. To do so, create an Application through the');?> <a target="_blank" href="https://dev.twitter.com/"><?php echo __( 'Twitter Developers');?></a> <?php echo __( 'page.');?></p>
                            <p><a target="_blank" href="https://www.moosocial.com/wiki/doku.php?id=admin_dashboard:system_admin:system_settings:integration_settings"><?php echo __( 'How to setup Twitter App') ?></a></p>
                            <?php echo $this->element('admin/setting');?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>