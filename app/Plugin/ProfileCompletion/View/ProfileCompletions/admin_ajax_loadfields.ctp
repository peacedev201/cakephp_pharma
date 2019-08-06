<?php 
	$xhtml = '';
	if(count($profile_fields) > 0){		
		foreach ($profile_fields as $k => $val) {
			$xhtml .= '<div class="form-group required">
                <label class="col-md-2 control-label" for="fields_">
                    '.$val['ProfileField']['name'].'
                </label>
                <div class="col-md-4">
                    '.$this->Form->text('fields_'.$val['ProfileField']['id'],array('class'=>'form-control', 'value' => (isset($profile_completion['fields_'.$val['ProfileField']['id']]) ? $profile_completion['fields_'.$val['ProfileField']['id']] : '0.00'))).'
                </div>
                <div class="col-md-2 pc-percent">
                	%
                </div>
            </div>';
		}
	}

	echo $xhtml;
 ?>