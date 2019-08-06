<div class="form-group">
	<label class="col-md-3 control-label"><?php echo __d('sms_verify','API Key');?></label>
	<div class="col-md-9">
		<?php echo $this->Form->input('key', array('type'=>'text','class' => 'form-control', 'label'=>false,'value' => isset($params['key']) ? $params['key'] : '')); ?>
	</div>
</div>