<?php
__d('reminder','Reminder Plugin');
__d('reminder','Reminder');

echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb(__d('reminder','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('reminder','Reminder Settings'), array('controller' => 'reminder_settings', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Reminder"));
$this->end();
?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).ready(function(){
	$('.wrapPermission .checkAll').each(function(item, index){ 
        $(this).click(function(){
            if ($(this).is(':checked')){
                $(this).parents('.wrapPermission').find('.check').attr('checked', 'checked');
                $(this).parents('.wrapPermission').find('.check').parent('span').addClass('checked');
                $(this).parents('.wrapPermission').find('.wrapCheck:first').hide();
            }else{
                $(this).parents('.wrapPermission').find('.check').attr('checked', false);
                $(this).parents('.wrapPermission').find('.check').parent('span').removeClass('checked');
                $(this).parents('.wrapPermission').find('.wrapCheck:first').show();
            }
        });
    });
});
<?php $this->Html->scriptEnd(); ?>

<div class="portlet-body form">
    <div class=" portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <?php echo$this->Moo->renderMenu('Reminder', __d('reminder','Settings'));?>
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-12">
                    <div class="tab-content">
                        <div class="tab-pane active" id="portlet_tab1">                           
                           <form class="form-horizontal intergration-setting" method="post" enctype="multipart/form-data">                           	   
							   <div class="form-body">
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('reminder','Enable Reminder Plugin');?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                            	echo $this->Form->input('enabled', array(
			                                    'type' => 'checkbox', 
			                                    'checked' => Configure::read('Reminder.reminder_enabled'),
			                                    'label' => '',                                    
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('reminder','User role');?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
							            	<div class="wrapPermission">
							                    <input type="checkbox" value="0" <?php if (count($role_select) == 0) echo 'checked'; ?> class="checkAll" /><?php echo  __( 'Everyone') ?><br/>
							                    <div <?php if (count($role_select) == 0): ?> style="display:none;" <?php endif; ?> class="wrapCheck">
							                    <?php foreach ($rule_options as $key_role => $name): ?>
							                        <input class="check"
							                        type="checkbox" <?php if (in_array($key_role, $role_select)) {
							                            echo 'checked';
							                        } ?> value="<?php echo $key_role; ?>"
							                        name="role[<?php echo  $key_role ?>]"/><?php echo $name?>
							                        <br/>
							                    <?php endforeach; ?>
							                    </div>
						                    </div>                                                                                      
                                        </div>								            
							        </div>
							        <hr>
									<div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('reminder','Enable email verification reminder');?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                            	echo $this->Form->input('enable_email_verification', array(
			                                    'type' => 'checkbox', 
			                                    'checked' => Configure::read('Reminder.reminder_enable_email_verification'),
			                                    'label' => '',                                    
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('reminder','How many times do you want to send reminder email verification (0 is unlimited- not recommended) (times)');?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                            	echo $this->Form->input('time_email_verification', array(
			                                    'value' => Configure::read('Reminder.reminder_time_email_verification'),
			                                    'label' => '',    
                                            	'class' => 'form-control'
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							        
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('reminder','Reminder email will send to users who has not verified email for (days)');?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                            	echo $this->Form->input('day_email_verification', array(
			                                    'value' => Configure::read('Reminder.reminder_day_email_verification'),
			                                    'label' => '',    
                                            	'class' => 'form-control'
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							        
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('reminder','If all verification reminder emails have been sent, system will auto disable account after (Enter 0 if you want to disable this option) (days)');?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;"> 
                                            <?php
                                            	echo $this->Form->input('disable_email_verification', array(
			                                    'value' => Configure::read('Reminder.reminder_disable_email_verification'),
			                                    'label' => '',    
                                            	'class' => 'form-control'
			                                )); 
                                            ?>                                                                              
                                        </div>								            
							        </div>
							        
							        
							        
							        <?php if(Configure::read('SmsVerify.sms_verify_enable')):?>
								        <div class="form-group">
								            <label class="col-md-3 control-label">
								                <?php echo __d('reminder','Enable sms verification reminder');?>                          
								            </label>
								            <div class="col-md-7" style="padding-left: 24px;">
	                                            <?php
	                                            	echo $this->Form->input('enable_sms_verification', array(
				                                    'type' => 'checkbox', 
				                                    'checked' => Configure::read('Reminder.reminder_enable_sms_verification'),
				                                    'label' => '',                                    
				                                )); 
	                                            ?>                                                                                    
	                                        </div>								            
								        </div>
								        
								        <div class="form-group">
								            <label class="col-md-3 control-label">
								                <?php echo __d('reminder','How many times do you want to send reminder sms verification (0 is unlimited- not recommended) (times)');?>                          
								            </label>
								            <div class="col-md-7" style="padding-left: 24px;">
	                                            <?php
	                                            	echo $this->Form->input('time_sms_verification', array(
				                                    'value' => Configure::read('Reminder.reminder_time_sms_verification'),
				                                    'label' => '',    
	                                            	'class' => 'form-control'
				                                )); 
	                                            ?>                                                                                    
	                                        </div>								            
								        </div>
								        
								        <div class="form-group">
								            <label class="col-md-3 control-label">
								                <?php echo __d('reminder','Reminder email will send to users who has not verified sms for (days)');?>                          
								            </label>
								            <div class="col-md-7" style="padding-left: 24px;">
	                                            <?php
	                                            	echo $this->Form->input('day_sms_verification', array(
				                                    'value' => Configure::read('Reminder.reminder_day_sms_verification'),
				                                    'label' => '',    
	                                            	'class' => 'form-control'
				                                )); 
	                                            ?>                                                                                    
	                                        </div>								            
								        </div>
								        
								        <div class="form-group">
								            <label class="col-md-3 control-label">
								                <?php echo __d('reminder','If all verification reminder sms have been sent, system will auto disable account after (Enter 0 if you want to disable this option) (days)');?>                          
								            </label>
								            <div class="col-md-7" style="padding-left: 24px;">
								            	<?php
	                                            	echo $this->Form->input('disable_sms_verification', array(
				                                    'value' => Configure::read('Reminder.reminder_disable_sms_verification'),
				                                    'label' => '',    
	                                            	'class' => 'form-control'
				                                )); 
	                                            ?>                                                                                 
	                                        </div>								            
								        </div>
							        <?php endif;?>
							        
							        
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('reminder',"Enable login reminder");?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                            	echo $this->Form->input('enable_login', array(
			                                    'type' => 'checkbox', 
			                                    'checked' => Configure::read('Reminder.reminder_enable_login'),
			                                    'label' => '',                                    
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							        
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('reminder',"How many times do you want to send login reminder email (0 is unlimited- not recommended) (times)");?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                            	echo $this->Form->input('time_login', array(
			                                    'value' => Configure::read('Reminder.reminder_time_login'),
			                                    'label' => '',    
                                            	'class' => 'form-control'
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							        
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('reminder','Reminder email will send to users who has loggedin for (days)');?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                            	echo $this->Form->input('day_login', array(
			                                    'value' => Configure::read('Reminder.reminder_day_login'),
			                                    'label' => '',    
                                            	'class' => 'form-control'
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							        
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('reminder',"If all login reminder emails have been sent, system will auto disable account after (Enter 0 if you want to disable this option) (days)");?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
							             	<?php
                                            	echo $this->Form->input('disable_login', array(
			                                    'value' => Configure::read('Reminder.reminder_disable_login'),
			                                    'label' => '',    
                                            	'class' => 'form-control'
			                                )); 
                                            ?>                                                                                  
                                        </div>								            
							        </div>
							        
							        						        
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('reminder',"Enable 'post status, like or comment' email reminder");?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                            	echo $this->Form->input('enable_share', array(
			                                    'type' => 'checkbox', 
			                                    'checked' => Configure::read('Reminder.reminder_enable_share'),
			                                    'label' => '',                                    
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							        
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('reminder',"How many times do you want to send 'post status, like or comment' email reminder (0 is unlimited- not recommended) (times)");?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                            	echo $this->Form->input('time_share', array(
			                                    'value' => Configure::read('Reminder.reminder_time_share'),
			                                    'label' => '',    
                                            	'class' => 'form-control'
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							        
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('reminder','Reminder email will send to user if he/she has not posted status, liked, or commente for (days)');?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                            	echo $this->Form->input('day_share', array(
			                                    'value' => Configure::read('Reminder.reminder_day_share'),
			                                    'label' => '',    
                                            	'class' => 'form-control'
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							        
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('reminder',"If all 'post status, like or comment' email reminder emails have been sent, system will auto disable account after (Enter 0 if you want to disable this option) (days)");?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
							            	<?php
                                            	echo $this->Form->input('disable_share', array(
			                                    'value' => Configure::read('Reminder.reminder_disable_share'),
			                                    'label' => '',    
                                            	'class' => 'form-control'
			                                )); 
                                            ?>                                                                                
                                        </div>								            
							        </div>
							         
								    <div class="form-actions">
								        <div class="row">
								            <div class="col-md-offset-3 col-md-9">
								                <input type="submit" class="btn btn-circle btn-action" value="<?php echo __d('reminder','Save Settings');?>">
								            </div>
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