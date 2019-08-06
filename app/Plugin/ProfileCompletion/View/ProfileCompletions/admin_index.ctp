<?php
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->Html->addCrumb(__d('profile_completion', 'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('profile_completion', 'Profile Completion Manager'), array('controller' => 'profile_completions', 'action' => 'admin_index'));
    
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Profile Completion'));
    $this->end();
?>


<div class="portlet-body">
	<div class=" portlet-tabs">
		<div class="tabbable tabbable-custom boxless tabbable-reversed">
			<?php echo $this->Moo->renderMenu('ProfileCompletion', __d('profile_completion', 'General'));?>
		</div>
	</div>
    <div class="row">
        <div class="col-md-12">
        	<h4 class="title">
        		<b>
        			<?php echo __d('profile_completion', 'Profile field important settings'); ?>
        		</b>
        	</h4>            	
        	<p>
        		<?php echo __d('profile_completion', 'You can define how importance each field will be here by enter % value info all fields below. Please enter 0 if you don\'t want to include a specific field into % profile completion'); ?>
        	</p>
        	<p>
        		<b style="color: red;">
        			<?php echo __d('profile_completion', 'Importance: if you deleted, add new or disable/enable a custom field, you need to manually update % value again to make sure that total will be 100%.'); ?>
        		</b>
        	</p>
        </div>
    </div>

	<form method="post" action="<?php echo $this->request->base?>/admin/profile_completion/profile_completions?profile_type_id=<?php echo isset($_GET['profile_type_id']) ? $_GET['profile_type_id'] : 1;?>">

		<div class="row">
			<div class="col-md-12">
				<hr>
			</div>
			<div class="col-md-12">
				<div class="form-group required">
	                <label class="col-md-2 control-label" for="username">
	                    <?php echo __d('profile_completion', 'Profile Type')?>
	                </label>
	                <div class="col-md-4">
	                    <?php echo $this->Form->select( 'profile_type_id', $profile_type, array('class' => 'form-control', 'value' => (isset($_GET['profile_type_id']) ? $_GET['profile_type_id'] : 1)) ); ?>
	                </div>
	            </div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<hr>
				<h5 class="title">
					<b>
	        			<?php echo __d('profile_completion', 'Required Infomation'); ?>
	        		</b>
				</h5>
			</div>
			<div class="col-md-12">
				<div class="form-group required">
	                <label class="col-md-2 control-label" for="full-name">
	                    <?php echo __d('profile_completion', 'Full Name')?>
	                </label>
	                <div class="col-md-4">
	                    <?php echo $this->Form->text('name',array('class'=>'form-control', 'value' => (isset($data['name']) ? $data['name'] : (isset($profile_completion['name']) ? $profile_completion['name'] : '0.00')))) ?>
	                </div>
	                <div class="col-md-2 pc-percent">
	                	%
	                </div>
	            </div>

	            <div class="form-group required">
	                <label class="col-md-2 control-label" for="email">
	                    <?php echo __d('profile_completion', 'Email Address')?>
	                </label>
	                <div class="col-md-4">
	                    <?php echo $this->Form->text('email',array('class'=>'form-control', 'value' => (isset($data['email']) ? $data['email'] : (isset($profile_completion['email']) ? $profile_completion['email'] : '0.00') ))) ?>
	                </div>
	                <div class="col-md-2 pc-percent">
	                	%
	                </div>
	            </div>

	            <div class="form-group required">
	                <label class="col-md-2 control-label" for="email">
	                    <?php echo __d('profile_completion', 'Birthday')?>
	                </label>
	                <div class="col-md-4">
	                    <?php echo $this->Form->text('birthday',array('class'=>'form-control', 'value' => (isset($data['birthday']) ? $data['birthday']: (isset($profile_completion['birthday']) ? $profile_completion['birthday'] : '0.00') ))) ?>
	                </div>
	                <div class="col-md-2 pc-percent">
	                	%
	                </div>
	            </div>

	            <div class="form-group required">
	                <label class="col-md-2 control-label" for="email">
	                    <?php echo __d('profile_completion', 'Gender')?>
	                </label>
	                <div class="col-md-4">
	                    <?php echo $this->Form->text('gender',array('class'=>'form-control', 'value' => (isset($data['gender']) ? $data['gender'] : (isset($profile_completion['gender']) ? $profile_completion['gender'] : '0.00') ))) ?>
	                </div>
	                <div class="col-md-2 pc-percent">
	                	%
	                </div>
	            </div>

	            <div class="form-group required">
	                <label class="col-md-2 control-label" for="email">
	                    <?php echo __d('profile_completion', 'Timezone')?>
	                </label>
	                <div class="col-md-4">
	                    <?php echo $this->Form->text('timezone',array('class'=>'form-control', 'value' => (isset($data['timezone']) ? $data['timezone'] : (isset($profile_completion['timezone']) ? $profile_completion['timezone'] : '0.00') ) )) ?>
	                </div>
	                <div class="col-md-2 pc-percent">
	                	%
	                </div>
	            </div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<hr>
				<h5 class="title">
					<b>
	        			<?php echo __d('profile_completion', 'Optional Infomation'); ?>
	        		</b>
				</h5>
			</div>
			<div class="col-md-12">
				<div class="form-group required">
	                <label class="col-md-2 control-label" for="username">
	                    <?php echo __d('profile_completion', 'UserName')?>
	                </label>
	                <div class="col-md-4">
	                    <?php echo $this->Form->text('username',array('class'=>'form-control', 'value' => (isset($data['username']) ? $data['username'] : (isset($profile_completion['username']) ? $profile_completion['username'] : '0.00') ))) ?>
	                </div>
	                <div class="col-md-2 pc-percent">
	                	%
	                </div>
	            </div>

	            <div class="form-group required">
	                <label class="col-md-2 control-label" for="about">
	                    <?php echo __d('profile_completion', 'About')?>
	                </label>
	                <div class="col-md-4">
	                    <?php echo $this->Form->text('about',array('class'=>'form-control', 'value' => (isset($data['about']) ? $data['about'] : (isset($profile_completion['about']) ? $profile_completion['about'] : '0.00') ))) ?>
	                </div>
	                <div class="col-md-2 pc-percent">
	                	%
	                </div>
	            </div>

	            <div class="form-group required">
	                <label class="col-md-2 control-label" for="avatar">
	                    <?php echo __d('profile_completion', 'Profile Avatar')?>
	                </label>
	                <div class="col-md-4">
	                    <?php echo $this->Form->text('avatar',array('class'=>'form-control', 'value' => (isset($data['avatar']) ? $data['avatar'] : (isset($profile_completion['avatar']) ? $profile_completion['avatar'] : '0.00') ))) ?>
	                </div>
	                <div class="col-md-2 pc-percent">
	                	%
	                </div>
	            </div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<hr>
				<h5 class="title">
					<b>
	        			<?php echo __d('profile_completion', 'Additional Infomation'); ?>
	        		</b>
				</h5>
			</div>
			<div class="col-md-12">
				<div class="custom-field">
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="specialty">
                            <?php echo __d('profile_completion', 'Specialty')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('specialty',array('class'=>'form-control', 'value' => (isset($data['specialty']) ? $data['specialty'] : (isset($profile_completion['specialty']) ? $profile_completion['specialty'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="university_id">
                            <?php echo __d('profile_completion', 'University')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('university_id',array('class'=>'form-control', 'value' => (isset($data['university_id']) ? $data['university_id'] : (isset($profile_completion['university_id']) ? $profile_completion['university_id'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="admission_year">
                            <?php echo __d('profile_completion', 'Admission year')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('admission_year',array('class'=>'form-control', 'value' => (isset($data['admission_year']) ? $data['admission_year'] : (isset($profile_completion['admission_year']) ? $profile_completion['admission_year'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="mobile">
                            <?php echo __d('profile_completion', 'Mobile')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('mobile',array('class'=>'form-control', 'value' => (isset($data['mobile']) ? $data['mobile'] : (isset($profile_completion['mobile']) ? $profile_completion['mobile'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="sub_mail">
                            <?php echo __d('profile_completion', 'Sub email')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('sub_mail',array('class'=>'form-control', 'value' => (isset($data['sub_mail']) ? $data['sub_mail'] : (isset($profile_completion['sub_mail']) ? $profile_completion['sub_mail'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="submail_confirmed">
                            <?php echo __d('profile_completion', 'Sub email confirmed')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('submail_confirmed',array('class'=>'form-control', 'value' => (isset($data['submail_confirmed']) ? $data['submail_confirmed'] : (isset($profile_completion['submail_confirmed']) ? $profile_completion['submail_confirmed'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="job_belong_to">
                            <?php echo __d('profile_completion', 'Job belong to')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('job_belong_to',array('class'=>'form-control', 'value' => (isset($data['job_belong_to']) ? $data['job_belong_to'] : (isset($profile_completion['job_belong_to']) ? $profile_completion['job_belong_to'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="com_phone">
                            <?php echo __d('profile_completion', 'Company phone')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('com_phone',array('class'=>'form-control', 'value' => (isset($data['com_phone']) ? $data['com_phone'] : (isset($profile_completion['com_phone']) ? $profile_completion['com_phone'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="com_name">
                            <?php echo __d('profile_completion', 'Company name')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('com_name',array('class'=>'form-control', 'value' => (isset($data['com_name']) ? $data['com_name'] : (isset($profile_completion['com_name']) ? $profile_completion['com_name'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="com_zip">
                            <?php echo __d('profile_completion', 'Zip code')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('com_zip',array('class'=>'form-control', 'value' => (isset($data['com_zip']) ? $data['com_zip'] : (isset($profile_completion['com_zip']) ? $profile_completion['com_zip'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="com_address_1">
                            <?php echo __d('profile_completion', 'Address 1')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('com_address_1',array('class'=>'form-control', 'value' => (isset($data['com_address_1']) ? $data['com_address_1'] : (isset($profile_completion['com_address_1']) ? $profile_completion['com_address_1'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="com_address_2">
                            <?php echo __d('profile_completion', 'Address 2')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('com_address_2',array('class'=>'form-control', 'value' => (isset($data['com_address_2']) ? $data['com_address_2'] : (isset($profile_completion['com_address_2']) ? $profile_completion['com_address_2'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="com_fax">
                            <?php echo __d('profile_completion', 'Fax')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('com_fax',array('class'=>'form-control', 'value' => (isset($data['com_fax']) ? $data['com_fax'] : (isset($profile_completion['com_fax']) ? $profile_completion['com_fax'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="com_title">
                            <?php echo __d('profile_completion', 'Title')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('com_title',array('class'=>'form-control', 'value' => (isset($data['com_title']) ? $data['com_title'] : (isset($profile_completion['com_title']) ? $profile_completion['com_title'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="com_department">
                            <?php echo __d('profile_completion', 'Company department')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('com_department',array('class'=>'form-control', 'value' => (isset($data['com_department']) ? $data['com_department'] : (isset($profile_completion['com_department']) ? $profile_completion['com_department'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="	sale_area">
                            <?php echo __d('profile_completion', 'Sale area')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('sale_area',array('class'=>'form-control', 'value' => (isset($data['	sale_area']) ? $data['	sale_area'] : (isset($profile_completion['	sale_area']) ? $profile_completion['	sale_area'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="job_interest">
                            <?php echo __d('profile_completion', 'Major job of interests')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('job_interest',array('class'=>'form-control', 'value' => (isset($data['job_interest']) ? $data['job_interest'] : (isset($profile_completion['job_interest']) ? $profile_completion['job_interest'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="major_place">
                            <?php echo __d('profile_completion', 'Major place of employment')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('major_place',array('class'=>'form-control', 'value' => (isset($data['major_place']) ? $data['major_place'] : (isset($profile_completion['major_place']) ? $profile_completion['major_place'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="com_homepage">
                            <?php echo __d('profile_completion', 'Home page')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('com_homepage',array('class'=>'form-control', 'value' => (isset($data['com_homepage']) ? $data['specialty'] : (isset($profile_completion['com_homepage']) ? $profile_completion['com_homepage'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="uni_grade">
                            <?php echo __d('profile_completion', 'Grade')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('uni_grade',array('class'=>'form-control', 'value' => (isset($data['uni_grade']) ? $data['uni_grade'] : (isset($profile_completion['uni_grade']) ? $profile_completion['uni_grade'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-md-2 control-label" for="mail_to">
                            <?php echo __d('profile_completion', 'Confirm email')?>
                        </label>
                        <div class="col-md-4">
                            <?php echo $this->Form->text('mail_to',array('class'=>'form-control', 'value' => (isset($data['mail_to']) ? $data['mail_to'] : (isset($profile_completion['mail_to']) ? $profile_completion['mail_to'] : '0.00') ))) ?>
                        </div>
                        <div class="col-md-2 pc-percent">
                            %
                        </div>
                    </div>
					<?php 
	            		$xhtml = '';
		            	if(count($profile_fields) > 0){
		            		foreach ($profile_fields as $k => $val) {
		            			$xhtml .= '<div class="form-group required">
					                <label class="col-md-2 control-label" for="fields_">
					                    '.$val['ProfileField']['name'].'
					                </label>
					                <div class="col-md-4">
					                    '.$this->Form->text('fields_'.$val['ProfileField']['id'],array('class'=>'form-control', 'value' => (isset($data['fields_'.$val['ProfileField']['id']]) ? $data['fields_'.$val['ProfileField']['id']] : (isset($profile_completion['fields_'.$val['ProfileField']['id']]) ? $profile_completion['fields_'.$val['ProfileField']['id']] : '0.00') ) )).'
					                </div>
					                <div class="col-md-2 pc-percent">
					                	%
					                </div>
					            </div>';
		            		}
		            	}

		            	echo $xhtml;
		             ?>
				</div>            
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<hr>
				<button type='submit' id='saveBtn' class='btn btn-action'><?php echo __d('profile_completion', 'Save'); ?></button>
			</div>
		</div>

	</form>

</div>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
	jQuery(document).ready(function($) {
		$('#profile_type_id').change(function(){
			window.location.href = '<?php echo $this->request->base?>/admin/profile_completion/profile_completions?profile_type_id='+$(this).val();
		});
	});
<?php $this->Html->scriptEnd(); ?>

<style type="text/css" media="screen">
	.pc-percent{
		font-size: 14px; 
	    font-weight: normal;
	    color: #333333;
	    background-color: white;
	    height: 34px;
	    padding: 6px 12px;
	}
	div.error-message{
		padding: 15px;
		font-size: 13px;
	}
</style>