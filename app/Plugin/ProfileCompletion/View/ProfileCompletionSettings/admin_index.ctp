<?php 
    echo $this->Html->css(array('jquery-ui','ProfileCompletion.admin','ProfileCompletion.jquery.miniColors', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui','ProfileCompletion.jquery.miniColors.min', 'footable'), array('inline' => false));

    __d('profile_completion','Progress Bar Color');
    __d('profile_completion','Do not show the widget then 100% completed');
    __d('profile_completion','Enter Gauge Color In Hex');
    __d('profile_completion','The widget will hide if all fields are filled in (100% completed)');
    __d('profile_completion','Profile Completion');

    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

    $this->Html->addCrumb(__d('profile_completion', 'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('profile_completion', 'Profile Completion Settings'), array('controller' => 'profile_completion_settings', 'action' => 'admin_index'));

    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array("cmenu" => "Profile Completion"));
    $this->end();
?>

<div class="portlet-body form">
    <div class=" portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <?php echo $this->Moo->renderMenu('ProfileCompletion', __d('profile_completion','Settings'));?>
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-12">
                    <div class="tab-content">
                        <div class="tab-pane active" id="portlet_tab1">
                            <form class="form-horizontal intergration-setting" method="post" enctype="multipart/form-data">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">
                                            <?php echo __d('profile_completion','Enable Profile Completion Plugin'); ?>           
                                        </label>
                                        <div class="col-md-7">
                                            <div>
                                                <span class="checked"><input type="radio" name="profile_completion_enabled" value="0" <?php echo (Configure::read('ProfileCompletion.profile_completion_enabled') == 0) ? 'checked' : '';?> id="profile_completion_enabled_0"></span>
                                                <label for="profile_completion_enabled_0" class="radio-setting"><?php echo __d('profile_completion','Disable'); ?></label>
                                            </div>
                                            <div>
                                                <span class="checked"><input type="radio" name="profile_completion_enabled" value="1" <?php echo (Configure::read('ProfileCompletion.profile_completion_enabled') == 1) ? 'checked' : '';?> id="profile_completion_enabled_1"></span>
                                                <label for="profile_completion_enabled_1" class="radio-setting"><?php echo __d('profile_completion','Enable'); ?></label> 
                                            </div>           
                                        </div>
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">
                                            <?php echo __d('profile_completion','Progress Bar Color'); ?>
                                        (<a data-html="true" href="javascript:void(0)" class="tooltips" data-original-title="<?php echo __d('profile_completion','Enter Gauge Color In Hex'); ?>" data-placement="top">?</a>)
                                        </label>
                                        <div class="col-md-7">
                                            <input name="progress_bar_color" value="<?php echo (Configure::read('ProfileCompletion.progress_bar_color') ? Configure::read('ProfileCompletion.progress_bar_color') : '#d8601f'); ?>" class="form-control color-picker" type="text" id="progress_bar_color">         
                                        </div>
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">
                                            <?php echo __d('profile_completion','% Remaining Bar Color'); ?>               
                                        (<a data-html="true" href="javascript:void(0)" class="tooltips" data-original-title="<?php echo __d('profile_completion','Enter Gauge Color In Hex'); ?>" data-placement="top">?</a>)
                                        </label>
                                        <div class="col-md-7">
                                            <input name="remaining_bar_color" value="<?php echo (Configure::read('ProfileCompletion.remaining_bar_color') ? Configure::read('ProfileCompletion.remaining_bar_color') : '#ccc'); ?>" class="form-control color-picker" type="text" id="remaining_bar_color">          
                                        </div>
                                    </div>     
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">
                                            <?php echo __d('profile_completion','Show update profile warning message'); ?>               
                                        (<a data-html="true" href="javascript:void(0)" class="tooltips" data-original-title="<?php echo __d('profile_completion','You members will see warning to update profile information if they have not reached to % entered here. Enter 0 to disable this option.'); ?>" data-placement="top">?</a>)
                                        </label>
                                        <div class="col-md-3">
                                            <input name="force_update_profile_info" value="<?php echo (Configure::read('ProfileCompletion.force_update_profile_info') ? Configure::read('ProfileCompletion.force_update_profile_info') : 0); ?>" class="form-control" type="number" id="force_update_profile_info" min = "0" max = "100"> 

                                        </div>
                                        <div class="col-md-1" style="padding-left: 0;">
                                            <div style="padding: 8px; height: 34px;">
                                                %
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">
                                            <?php echo __d('profile_completion','Do not show the widget when 100% completed'); ?>
                                        (<a data-html="true" href="javascript:void(0)" class="tooltips" data-original-title="<?php echo __d('profile_completion','The widget will hide if all fields are filled in (100% completed)'); ?>" data-placement="top">?</a>)
                                        </label>
                                        <div class="col-md-7">
                                            <div class="input checkbox">
                                                <span class="checked">
                                                    <input type="checkbox" name="not_show_widget_100" <?php echo (Configure::read('ProfileCompletion.not_show_widget_100') == 1) ? 'checked' : '';?> id="not_show_widget_100" value="1">
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-offset-3 col-md-9">
                                            <input type="submit" class="btn btn-circle btn-action" value="Save Settings">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
jQuery('.color-picker').miniColors({
    change:function(hex, rgb){
        jQuery('#console').prepend('change: ' + hex + ', rgb(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')<br>');
    },
    open:function(hex, rgb) {
        jQuery('#console').prepend('open: ' + hex + ', rgb(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')<br>');
    },
    close:function(hex, rgb) {
        jQuery('#console').prepend('close: ' + hex + ', rgb(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')<br>');
    }
});
<?php $this->Html->scriptEnd(); ?>