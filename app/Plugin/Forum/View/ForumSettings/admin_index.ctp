<?php
__d('forum','Forum Enable');


echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb(__d('forum','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('forum', 'Forum'), '/admin/forum/forums');
$this->Html->addCrumb(__d('forum','Forum Settings'), array('controller' => 'forum_settings', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Forum"));
$this->end();

$currency = Configure::read('Config.currency');
?>

<div class="portlet-body form">
    <div class=" portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <?php echo$this->Moo->renderMenu('Forum', __d('forum','Settings'));?>
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-12">
                    <div class="tab-content">
                        <div class="tab-pane active" id="portlet_tab1">                           
                           <form class="form-horizontal intergration-setting" method="post" enctype="multipart/form-data">                           	   
							   <div class="form-body">
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('forum','Enable Forum Plugin');?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                            	echo $this->Form->input('enabled', array(
			                                    'type' => 'checkbox', 
			                                    'checked' => Configure::read('Forum.forum_enabled'),
			                                    'label' => '',                                    
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							        
								    <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('forum','Enable topic pin for user?');?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                           	echo $this->Form->radio('enable_user_pin_topic',array('1'=>__d('forum','Yes'),'0'=>__d('forum','No')), array(
			                                    'value' => Configure::read('Forum.forum_enable_user_pin_topic'),
                                           		'separator' => '<br/>', 'legend' => false, 'label' => array('class' => 'radio-setting')
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							        
							         <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('forum','Pin topic to top price per day').' ('.$currency['Currency']['currency_code'].')';?>
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                            	echo $this->Form->input('price_pin_per_day', array(
			                                    'type' => 'Number', 
			                                    'value' => Configure::read('Forum.forum_price_pin_per_day'),
			                                    'label' => '',       
                                            	'min' => 0,
                                                'required'
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							        
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('forum','Show expand and collapse?');?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                           	echo $this->Form->radio('show_expand',array('1'=>__d('forum','Yes'),'0'=>__d('forum','No')), array( 
			                                    'value' => Configure::read('Forum.forum_show_expand'),
                                           		'separator' => '<br/>', 'legend' => false, 'label' => array('class' => 'radio-setting')
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							        
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('forum','Locked forum background color');?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                            	echo $this->Form->input('locked_bg_color', array(
			                                    'type' => 'Color', 
                                            			'value' => Configure::read('Forum.forum_locked_bg_color') ? Configure::read('Forum.forum_locked_bg_color') : '#ffffff',
			                                    'label' => '',                                    
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							        
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('forum','Pinned forum background color');?>
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                            	echo $this->Form->input('pined_bg_color', array(
			                                    'type' => 'Color', 
                                            	'value' => Configure::read('Forum.forum_pined_bg_color') ? Configure::read('Forum.forum_pined_bg_color'): '#ffffff',
			                                    'label' => '',                                    
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							        
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('forum','Locked topic text color');?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                            	echo $this->Form->input('topic_locked_text_color', array(
			                                    'type' => 'Color', 
                                            	'value' => Configure::read('Forum.forum_topic_locked_text_color') ? Configure::read('Forum.forum_topic_locked_text_color'): '#ffffff',
			                                    'label' => '',                                    
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							        
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('forum','Allowed file extentions');?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                            	echo $this->Form->input('allowed_extentions', array(
			                                    'type' => 'Text', 
			                                    'value' => Configure::read('Forum.forum_allowed_extentions'),
			                                    'label' => '',                                    
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							        
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('forum','Number topics per page');?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                            	echo $this->Form->input('number_topic_per_page', array(
			                                    'type' => 'Text', 
			                                    'value' => Configure::read('Forum.forum_number_topic_per_page'),
			                                    'label' => '',                                    
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>

                                   <div class="form-group">
                                       <label class="col-md-3 control-label">
                                           <?php echo __d('forum','Number replies per page');?>
                                       </label>
                                       <div class="col-md-7" style="padding-left: 24px;">
                                           <?php
                                           echo $this->Form->input('number_reply_per_page', array(
                                               'type' => 'Text',
                                               'value' => Configure::read('Forum.forum_number_reply_per_page'),
                                               'label' => '',
                                           ));
                                           ?>
                                       </div>
                                   </div>
							        
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('forum','Mention notification limit');?> (<a data-html="true" href="javascript:void(0)" class="tooltips" data-original-title="<?php echo __d('forum','If you enter 10 for example, only first 10 members who are mentioned in a reply of a topic will get notification.'); ?>" data-placement="top">?</a>)
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                            	echo $this->Form->input('limit_notification_mention', array(
			                                    'type' => 'Text', 
			                                    'value' => Configure::read('Forum.forum_limit_notification_mention'),
			                                    'label' => '',                                    
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
                                   <div class="form-group">
                                       <label class="col-md-3 control-label">
                                           <?php echo __d('forum','Enable Google reCaptcha on reply form');?>
                                       </label>
                                       <div class="col-md-7" style="padding-left: 24px;">
                                           <?php
                                           echo $this->Form->radio('enable_reply_captcha',array('1'=>__d('forum','Yes'),'0'=>__d('forum','No')), array(
                                               'value' => Configure::read('Forum.forum_enable_reply_captcha'),
                                               'separator' => '<br/>', 'legend' => false, 'label' => array('class' => 'radio-setting')
                                           ));
                                           ?>
                                       </div>
                                   </div>
                                   <div class="form-group">
                                       <label class="col-md-3 control-label">
                                           <?php echo __d('forum','Enable Google reCaptcha on create new topic form');?>
                                       </label>
                                       <div class="col-md-7" style="padding-left: 24px;">
                                           <?php
                                           echo $this->Form->radio('enable_create_topic_captcha',array('1'=>__d('forum','Yes'),'0'=>__d('forum','No')), array(
                                               'value' => Configure::read('Forum.forum_enable_create_topic_captcha'),
                                               'separator' => '<br/>', 'legend' => false, 'label' => array('class' => 'radio-setting')
                                           ));
                                           ?>
                                       </div>
                                   </div>

                                   <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('forum','Enable plugin hashtag');?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                           	echo $this->Form->radio('enable_hashtag',array('1'=>__d('forum','Enable'),'0'=>__d('forum','Disable')), array( 
			                                    'value' => Configure::read('Forum.forum_enable_hashtag'),
                                           		'separator' => '<br/>', 'legend' => false, 'label' => array('class' => 'radio-setting')
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							        
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('forum','By pass force login');?>                          
							            </label>
							            <div class="col-md-7" style="padding-left: 24px;">
                                            <?php
                                            	echo $this->Form->input('by_pass_force_login', array(
			                                    'type' => 'checkbox', 
			                                    'checked' => Configure::read('Forum.forum_by_pass_force_login'),
			                                    'label' => '',                                    
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							       
								    <div class="form-actions">
								        <div class="row">
								            <div class="col-md-offset-3 col-md-9">
								                <input type="submit" class="btn btn-circle btn-action" value="<?php echo __d('visual_notification','Save Settings');?>">
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