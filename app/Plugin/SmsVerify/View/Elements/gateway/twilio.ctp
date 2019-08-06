<div class="form-group">
	<label class="col-md-3 control-label"><?php echo __d('sms_verify','From');?></label>
	<div class="col-md-9">
		<?php echo $this->Form->input('from', array('type'=>'text','class' => 'form-control', 'label'=>false,'value' => isset($params['from']) ? $params['from'] : '')); ?>
	</div>
</div>
<div class="form-group">
	<label class="col-md-3 control-label"><?php echo __d('sms_verify','Sid');?></label>
	<div class="col-md-9">
		<?php echo $this->Form->input('user_name', array('class' => 'form-control','label'=>false, 'value' => isset($params['user_name']) ? $params['user_name'] : '')); ?>
	</div>
</div>
<div class="form-group">
	<label class="col-md-3 control-label"><?php echo __d('sms_verify','Token');?></label>
	<div class="col-md-9">
		<?php echo $this->Form->input('password', array('type'=>'text','class' => 'form-control', 'label'=>false,'value' => isset($params['password']) ? $params['password'] : '')); ?>
	</div>
</div>