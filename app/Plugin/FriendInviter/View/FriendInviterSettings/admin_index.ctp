<style>
    .intergration-setting .input.checkbox .checker{
        padding-top: 0px;
    }
    .admin-guide{
        overflow: hidden;
    }
</style>
<?php
echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
$this->Html->addCrumb(__d('friend_inviter','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('friend_inviter', 'Friend Inviter'), '/admin/friend_inviter/friend_inviter_settings');
$this->Html->addCrumb(__d('friend_inviter', 'Settings'), '/admin/friend_inviter/friend_inviter_settings');
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Friend Inviter'));
$this->end();
echo $this->Html->css(array('FriendInviter.setting'), array('plugin' => true), array('inline' => false));
?>
<?php echo $this->Moo->renderMenu('FriendInviter', __d('friend_inviter','Settings')); ?>
<form class="form-horizontal intergration-setting" method="post" enctype="multipart/form-data" action="<?php echo $this->request->base?>/admin/friend_inviter/friend_inviter_settings/quick_save">
    <div class="form-body">
        <h3><?php echo __d('friend_inviter','Global Settings') ?></h3>
        <?php if($friendinviter_enabled): ?>
            <?php echo $this->Form->hidden('setting_id.', array('value' => $friendinviter_enabled['id'], 'id' => false)); ?>
            <?php echo $this->Form->hidden('type_id.'.$friendinviter_enabled['id'], array('value' => $friendinviter_enabled['type_id'], 'id' => false)); ?>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('friend_inviter',$friendinviter_enabled['label']) ?></label>
                <div class="col-md-7">
                    <?php
                        $options = array();
                        $checked = '';
                        $multis = json_decode($friendinviter_enabled['value_actual'], true);
                        foreach($multis as $multi){
                            $options[$multi['value']] = __d('friend_inviter',$multi['name']);
                            if($multi['select'] == 1){
                                $checked = $multi['value'];
                            }
                        }
                        echo $this->Form->radio('multi.'.$friendinviter_enabled['id'], $options, array('separator' => '<br/>', 'value' => $checked, 'legend' => false, 'label' => array('class' => 'radio-setting')));            
                    ?>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if($maximum_emails): ?>
            <?php echo $this->Form->hidden('setting_id.', array('value' => $maximum_emails['id'], 'id' => false)); ?>
            <?php echo $this->Form->hidden('type_id.'.$maximum_emails['id'], array('value' => $maximum_emails['type_id'], 'id' => false)); ?>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('friend_inviter','Maximum selected emails for each invite time') ?></label>
                <div class="col-md-7">
                    <?php
                    echo $this->Form->text('text.'.$maximum_emails['id'], array(
									'value' => $maximum_emails['value_actual'],
									'class' => 'form-control',
									'label' => false
								));   
                   ?>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if($auto_friend): ?>
            <?php echo $this->Form->hidden('setting_id.', array('value' => $auto_friend['id'], 'id' => false)); ?>
            <?php echo $this->Form->hidden('type_id.'.$auto_friend['id'], array('value' => $auto_friend['type_id'], 'id' => false)); ?>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('friend_inviter','Automatic adding friends') ?>  <?php
						if($auto_friend['description'] != ''):
							$href = "";
							$target = "";
							preg_match('/href="(.*)"/i',__d('friend_inviter',trim($auto_friend['description'])),$match);
							preg_match('/target="(.*)"/i',__d('friend_inviter',trim($auto_friend['description'])),$target);
							if(!empty($match))
								{
									$href = (strpos($match[1],'http')!== false) ? $match[1]:$this->request->base.$match[1];
								}
							if(!empty($target))
								$target = $target[1];
					?>
						(<a data-html="true"  href="<?php echo (empty($href))?"javascript:void(0)":$href; ?>" <?php echo (empty($target))?"":'target="'.$target.'"' ?> class="tooltips" data-original-title="<?php echo (str_replace('"','\'',__d('friend_inviter',trim($auto_friend['description']))));?>" data-placement="top">?</a>)
					<?php endif;?></label>             
                <div class="col-md-7">
                    <?php
                        $options = array();
                        $checked = '';
                        $multis = json_decode($auto_friend['value_actual'], true);
                        foreach($multis as $multi){
                            if (__d('friend_inviter',$multi['name']) != $multi['name']){
                                $options[$multi['value']] = __d('friend_inviter',$multi['name']);
                            } else {
                                $options[$multi['value']] = __d('setting',$multi['name']);
                            }

                            if($multi['select'] == 1){
                                $checked = $multi['value'];
                            }
                        }
                        echo $this->Form->radio('multi.'.$auto_friend['id'], $options, array('separator' => '<br/>', 'value' => $checked, 'legend' => false, 'label' => array('class' => 'radio-setting')));  
                   ?>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if(isset($enable_referral_code_field)): ?>
            <?php   echo $this->Form->hidden('setting_id.', array('value' => $enable_referral_code_field['id'], 'id' => false)); ?>
            <?php echo $this->Form->hidden('type_id.'.$enable_referral_code_field['id'], array('value' => $enable_referral_code_field['type_id'], 'id' => false)); ?>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('friend_inviter',$enable_referral_code_field['label']) ?>  <?php
						if($enable_referral_code_field['description'] != ''):
							$href = "";
							$target = "";
							preg_match('/href="(.*)"/i',__d('friend_inviter',trim($enable_referral_code_field['description'])),$match);
							preg_match('/target="(.*)"/i',__d('friend_inviter',trim($enable_referral_code_field['description'])),$target);
							if(!empty($match))
								{
									$href = (strpos($match[1],'http')!== false) ? $match[1]:$this->request->base.$match[1];
								}
							if(!empty($target))
								$target = $target[1];
					?>
						(<a data-html="true"  href="<?php echo (empty($href))?"javascript:void(0)":$href; ?>" <?php echo (empty($target))?"":'target="'.$target.'"' ?> class="tooltips" data-original-title="<?php echo (str_replace('"','\'',__d('friend_inviter',trim($enable_referral_code_field['description']))));?>" data-placement="top">?</a>)
					<?php endif;?></label>             
                <div class="col-md-7">
                    <?php
                        $options = array();
                        $checked = '';
                        $multis = json_decode($enable_referral_code_field['value_actual'], true);
                        foreach($multis as $multi){
                            if (__d('friend_inviter',$multi['name']) != $multi['name']){
                                $options[$multi['value']] = __d('friend_inviter',$multi['name']);
                            } else {
                                $options[$multi['value']] = __d('setting',$multi['name']);
                            }

                            if($multi['select'] == 1){
                                $checked = $multi['value'];
                            }
                        }
                        echo $this->Form->radio('multi.'.$enable_referral_code_field['id'], $options, array('separator' => '<br/>', 'value' => $checked, 'legend' => false, 'label' => array('class' => 'radio-setting')));  
                   ?>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if(isset($invites_greate_angel)): ?>
            <?php echo $this->Form->hidden('setting_id.', array('value' => $invites_greate_angel['id'], 'id' => false)); ?>
            <?php echo $this->Form->hidden('type_id.'.$invites_greate_angel['id'], array('value' => $invites_greate_angel['type_id'], 'id' => false)); ?>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('friend_inviter','Number of successful invites to gain Great Angel badge') ?>  <?php
						if($invites_greate_angel['description'] != ''):
							$href = "";
							$target = "";
							preg_match('/href="(.*)"/i',__d('setting',trim($invites_greate_angel['description'])),$match);
							preg_match('/target="(.*)"/i',__d('setting',trim($invites_greate_angel['description'])),$target);
							if(!empty($match))
								{
									$href = (strpos($match[1],'http')!== false) ? $match[1]:$this->request->base.$match[1];
								}
							if(!empty($target))
								$target = $target[1];
					?>
						(<a data-html="true"  href="<?php echo (empty($href))?"javascript:void(0)":$href; ?>" <?php echo (empty($target))?"":'target="'.$target.'"' ?> class="tooltips" data-original-title="<?php echo (str_replace('"','\'',__d('setting',trim($invites_greate_angel['description']))));?>" data-placement="top">?</a>)
					<?php endif;?></label>
                <div class="col-md-7">
                    <?php
                    echo $this->Form->text('text.'.$invites_greate_angel['id'], array(
									'value' => $invites_greate_angel['value_actual'],
									'class' => 'form-control',
									'label' => false
								));   
                   ?>
                </div>
            </div>
        <?php endif; ?>
        
        <h3><?php echo __d('friend_inviter','Provider Settings') ?></h3>
        <div class="form-sub-heading">
            <div class="provider_title"><?php echo __d('friend_inviter','Yahoo Contact Importer Settings') ?></div>
            <div><a href="javascript:void(0);" onclick="show_instruction('id_yahoo');"><img src="<?php echo $this->request->webroot ?>friend_inviter/img/help.gif"></a></div>
             <div style="display:none" id="id_yahoo">
                        <div style="margin:5px 15px 5px 5px;">
                            <div style="font-weight:bold;"><?php echo __d('friend_inviter','Getting Yahoo API Key :') ?></div>

                            <?php echo __d('friend_inviter', '1) Go here to register your application:'); ?> <a href="https://developer.yahoo.com/apps/create/" target="_blank" style="color:#5BA1CD;">https://developer.yahoo.com/apps/create/</a><br>
                            <div style="font-weight:bold;"><?php echo __d('friend_inviter','About creating your application :') ?></div>
                            <?php echo __d('friend_inviter','a) Write your application name. You can make this the same as your site name.') ?><br>
                            <?php echo __d('friend_inviter',"b) In the 'Application Type' field, choose : 'Web Application'.") ?><br>
                            <?php echo __d('friend_inviter',"c) Write a short description about your application/website.") ?><br>
                            <?php echo __d('friend_inviter',"d) In the 'Home Page URL' field, write the URL of your site (example : http://www.mysite.com).") ?><br>

                            <div style="font-weight:bold;"><?php echo __d('friend_inviter',"Security &amp; Privacy:") ?></div>
                            <?php echo __d('friend_inviter',"a) In the 'Callback Domain' field, write your site's domain (example : mysite.com)") ?><br>
                            <?php echo __d('friend_inviter',"b) In the 'API Permissions' field, select 'Contacts' option, then choose 'Read' option") ?><br> 
                            <?php echo __d('friend_inviter',"c) Now click on the 'Create App' button. After clicking on this button you will be redirected to a success page where you will get your 'Client ID' and 'Client Secret'. Copy them and paste these values in your site's Yahoo contact importer settings fields.") ?><br>
                        </div>
            </div>
        </div>
        
        <?php if($yahoo_app_key): ?>
            <?php echo $this->Form->hidden('setting_id.', array('value' => $yahoo_app_key['id'], 'id' => false)); ?>
            <?php echo $this->Form->hidden('type_id.'.$yahoo_app_key['id'], array('value' => $yahoo_app_key['type_id'], 'id' => false)); ?>
            <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('friend_inviter','Client ID') ?></label>
                    <div class="col-md-7">
                         <?php
                            echo $this->Form->text('text.'.$yahoo_app_key['id'], array(
									'value' => $yahoo_app_key['value_actual'],
									'class' => 'form-control',
									'label' => false
								));   
                        ?>
                    </div>
            </div>
        <?php endif; ?>
        
        <?php if($yahoo_shared_secret): ?>
            <?php echo $this->Form->hidden('setting_id.', array('value' => $yahoo_shared_secret['id'], 'id' => false)); ?>
            <?php echo $this->Form->hidden('type_id.'.$yahoo_shared_secret['id'], array('value' => $yahoo_shared_secret['type_id'], 'id' => false)); ?>
         <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('friend_inviter','Client Secret') ?></label>
                <div class="col-md-7">
                      <?php
                            echo $this->Form->text('text.'.$yahoo_shared_secret['id'], array(
									'value' => $yahoo_shared_secret['value_actual'],
									'class' => 'form-control',
									'label' => false
								));   
                        ?>
                </div>              
        </div>
        <?php endif; ?>
        
        <div class="form-sub-heading">
            <div class="provider_title"><?php echo __d('friend_inviter','Windows Live Contact Importer Settings') ?></div>
            <div><a href="javascript:void(0);" onclick="show_instruction('id_windowlive');"><img src="<?php echo $this->request->webroot ?>friend_inviter/img/help.gif"></a></div>
            <div style="display:none" id="id_windowlive">
                    <div class="admin-guide">
                        <div style="font-weight:bold;"><?php echo __d('friend_inviter','Getting your Windows Live Application ID :') ?></div>
                        
                        <?php echo __d('friend_inviter', '1) Go here to register your application:'); ?> <a href="https://apps.dev.microsoft.com/portal/register-app" target="_blank" style="color:#5BA1CD;">https://apps.dev.microsoft.com/portal/register-app</a><br>
                        <div style="font-weight:bold;"><?php echo __d('friend_inviter','About creating your application :') ?></div>
                        
                        <?php echo __d('friend_inviter','a) Write your application name. You can make this the same as your site name.') ?><br>
                        <?php echo __d('friend_inviter',"b) Now click on the 'I accept' button. After clicking on this button you will be redirected to a page where you will configure your app.") ?><br>
                        
                        <div style="font-weight:bold;"><?php echo __d('friend_inviter',"About configuring your application :") ?></div>
                        <?php echo __d('friend_inviter',"a) In Basic Information tab, you can add Application Logo, Terms of service URL, Privacy URL or leave these fields.") ?><br>
                        <?php echo __d('friend_inviter',"b) Now click 'API Settings' tab (in the left menu) to set your API.") ?><br>
                        <?php echo __d('friend_inviter',"c) In the 'Mobile or desktop client app', selecting 'Yes' will allow mobile clients to use this authentication flow.") ?><br>
                        <?php echo __d('friend_inviter',"d) In the 'Target domain' field, write your site's domain (example : mysite.com)") ?><br>
                        <?php echo __d('friend_inviter',"e) In 'Redirect URLs' field, write the exact URL: [url_your_site]/friend_inviters/getcontacts/livedone (example : http://www.mysite.com/friend_inviters/getcontacts/livedone)") ?><br>
                        <?php echo __d('friend_inviter',"f) Click Save to saving your API Settings") ?><br>
                        <?php echo __d('friend_inviter',"g) Now click 'App Settings' you will get your 'Client ID' and 'Client Secret'. Copy them and paste these values in your site's Window Live contact importer settings fields.") ?><br>
                    </div>
            </div>
        </div>
        
        <?php if($windows_live_appid): ?>
            <?php echo $this->Form->hidden('setting_id.', array('value' => $windows_live_appid['id'], 'id' => false)); ?>
            <?php echo $this->Form->hidden('type_id.'.$windows_live_appid['id'], array('value' => $windows_live_appid['type_id'], 'id' => false)); ?>
            <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('friend_inviter','Client ID') ?></label>
                    <div class="col-md-7">
                        <?php
                            echo $this->Form->text('text.'.$windows_live_appid['id'], array(
									'value' => $windows_live_appid['value_actual'],
									'class' => 'form-control',
									'label' => false
								));   
                        ?>
                    </div>
            </div>
        <?php endif; ?>
        
        <?php if($windows_live_secret): ?>
            <?php echo $this->Form->hidden('setting_id.', array('value' => $windows_live_secret['id'], 'id' => false)); ?>
            <?php echo $this->Form->hidden('type_id.'.$windows_live_secret['id'], array('value' => $windows_live_secret['type_id'], 'id' => false)); ?>
            <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('friend_inviter','Client Secret') ?></label>
                    <div class="col-md-7">
                        <?php
                            echo $this->Form->text('text.'.$windows_live_secret['id'], array(
									'value' => $windows_live_secret['value_actual'],
									'class' => 'form-control',
									'label' => false
								));   
                        ?>
                    </div>              
            </div>
        <?php endif; ?>
        
        <div class="form-sub-heading">
            <div class="provider_title"><?php echo __d('friend_inviter','Google Contact Importer Setting') ?></div>
            <div><a href="javascript:void(0);" onclick="show_instruction('id_google');"><img src="<?php echo $this->request->webroot ?>friend_inviter/img/help.gif"></a></div>
            <div style="display:none" id="id_google">
                <div class="admin-guide">
                        <div style="font-weight:bold;"><?php echo __d('friend_inviter',"Getting your Google API keys :") ?></div>
                        
                        <?php echo __d('friend_inviter',"1) To see intructions and create your project, go here :") ?> <a href="https://moosocial.com/wiki/doku.php?id=admin_dashboard:system_admin:system_settings:integration_settings#google_app_integration_tutorial" target="_blank" style="color:#5BA1CD;">https://moosocial.com/wiki/doku.php?id=admin_dashboard:system_admin:system_settings:integration_settings#google_app_integration_tutorial</a><br><br>
                        <?php echo __d('friend_inviter',"2) After creating your project successful, go to 'Overview' panel of your project.") ?><br>
                        <?php echo __d('friend_inviter',"3) Now click on 'Contacts API' link.") ?><br>
                        <?php echo __d('friend_inviter',"4) Click 'Enable' button to enable this api.") ?><br>
                        <?php echo __d('friend_inviter',"5) Now Click 'Credentials' tab in the sidebar on the left") ?><br>
                        <?php echo __d('friend_inviter',"6) Click on the OAuth 2.0 client ID was created before") ?><br>
                        <?php echo __d('friend_inviter',"7) Adding the exact Authorized redirect URI: [url_your_site]/friend_inviters/getcontacts?hauth_done=Google (example : http://www.mysite.com/friend_inviters/getcontacts?hauth_done=Google)") ?><br>
                        <?php echo __d('friend_inviter',"8) Click 'Save' button to saving this Authorized redirect URI") ?><br>
                        <?php echo __d('friend_inviter',"9) Back to the OAuth 2.0 client ID, copy 'Client ID' and 'Client Secret' and paste these values in your site's Google contact importer settings fields.") ?><br>
                    </div>
            </div>
        </div>
        
        <?php if($google_client_id): ?>
            <?php echo $this->Form->hidden('setting_id.', array('value' => $google_client_id['id'], 'id' => false)); ?>
            <?php echo $this->Form->hidden('type_id.'.$google_client_id['id'], array('value' => $google_client_id['type_id'], 'id' => false)); ?>
            <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('friend_inviter','Client ID') ?></label>
                    <div class="col-md-7">
                        <?php
                            echo $this->Form->text('text.'.$google_client_id['id'], array(
									'value' => $google_client_id['value_actual'],
									'class' => 'form-control',
									'label' => false
								));   
                        ?>
                    </div>
            </div>
        <?php endif; ?>
        
        <?php if($google_client_secret): ?>
            <?php echo $this->Form->hidden('setting_id.', array('value' => $google_client_secret['id'], 'id' => false)); ?>
            <?php echo $this->Form->hidden('type_id.'.$google_client_secret['id'], array('value' => $google_client_secret['type_id'], 'id' => false)); ?>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('friend_inviter','Client Secret') ?></label>
                <div class="col-md-7">
                    <?php
                            echo $this->Form->text('text.'.$google_client_secret['id'], array(
									'value' => $google_client_secret['value_actual'],
									'class' => 'form-control',
									'label' => false
								));   
                        ?>
                </div>
            </div>       
        <?php endif; ?>
        
        
         <?php if($web_account_services): ?>
            <?php echo $this->Form->hidden('setting_id.', array('value' => $web_account_services['id'], 'id' => false)); ?>
            <?php echo $this->Form->hidden('type_id.'.$web_account_services['id'], array('value' => $web_account_services['type_id'], 'id' => false)); ?>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('friend_inviter','Enable Providers') ?></label>
                <div class="col-md-7">
                    <?php
                            $options = array();
							$checked = '';
							$multis = json_decode($web_account_services['value_actual'], true);
							foreach($multis as $multi)
							{
								echo $this->Form->input('multi.'.$web_account_services['id'].'.'.$multi['value'], array(
									'type' => 'checkbox', 
									'checked' => $multi['select'],
									'label' => __d('friend_inviter',$multi['name']),
									'id' => 'ch'.$web_account_services['id'].$multi['value']
								));
							}  
                        ?>
                </div>
            </div>       
        <?php endif; ?>
    </div>
    
    <div class="form-actions">
        <div class="row">
            <div class="col-md-offset-3 col-md-9">
                <input type="submit" class="btn btn-circle btn-action" value="<?php echo __d('friend_inviter','Save Settings') ?>">
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    function show_instruction(id) {
        var show_instruct = $('#' + id).html();
        $.fn.SimpleModal({
            btn_ok: '<?php echo addslashes(__d('friend_inviter','OK')) ?>',
            callback: function () {
            },
            title: '<?php echo addslashes(__d('friend_inviter','Guidelines')) ?>',
            contents: show_instruct,
            model: 'modal', hideFooter: true, closeButton: false
        }).showModal();
    }
</script>
