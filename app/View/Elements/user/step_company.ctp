<?php
$this->addPhraseJs(array(
    'phar_phone'=>__('Pharmacy phone'),
    'phar_name'=>__('Pharmacy name'),
    'com_phone'=>__('Company phone'),
    'com_name'=>__('Company name'),
    'hosp_phone'=>__('Hospital phone'),
    'hosp_name'=>__('Hospital name'),
));

$phone_1 = $phone_2 = $phone_3 = '';
if(!empty($cuser['com_phone'])){
    list($phone_1, $phone_2, $phone_3) = explode('-',$cuser['com_phone']);
}

$pharma = array('pharmacy');
$hospital = array('hosp_pharmacy');
$gov = array('government','newspaper');
$other = array('others','other_company');
$it = array('pharmacy_chain','it_software');
$show_department = 0;

if(in_array($cuser['job_belong_to'], $pharma)){
    $type = __('Pharmacy');
}else if(in_array($cuser['job_belong_to'], $hospital)){
    $type = __('Hospital');
}else if(in_array($cuser['job_belong_to'], $gov)){
    $type = __('Company');
}else if(in_array($cuser['job_belong_to'], $other)){
    $type = __('Company');
}else if($cuser['job_belong_to'] != ''){
    $type = __('Company');
    $show_department = 1;
}else{
    $type = __('Pharmacy');
}

$is_job_other = $cuser['job_belong_to'] == 'others' ? 1 : 0;
?>
<form id="form_reg_step_3">
    <?php echo $this->Form->hidden('sidocode', array('value' => '', 'class' => '')); ?>
    <?php echo $this->Form->hidden('kpasame', array('value' => '', 'class' => '')); ?>
    <div class="form-group required" id="job_belong_section" style="">
        <label class="col-md-3 control-label">
            <?php echo __('Job belong to')?>
        </label>
        <div class="col-md-9 form-inline">
            <input type="hidden" name="data[job_belong_to]" id="_" value="">
            <?php $i = 0; foreach($jobs as $key=>$job):?>
                <div class="col-md-3 <?php echo $job['class'];?>">
                    <input type="radio" name="data[job_belong_to]" <?php echo ($cuser['job_belong_to'] == $key || (empty($cuser['job_belong_to']) && $i == 0)) ? 'checked' : '';?> id="<?php echo $job['name'];?>" value="<?php echo $key;?>" class="job-item">
                    <label for="<?php echo $job['name'];?>"><?php echo $job['name'];?></label>
                </div>
                <?php $i++; endforeach;?>
        </div>
        <div class="clear"></div>
    </div>
    <div  id="section_work_at_home"  style="<?php echo !$is_job_other ? 'display:none': '';?>">
        <div class="form-group required hidden">
            <label class="col-md-3 control-label">
            </label>
            <div class="col-md-9">
                <span id="">
                        <label>
                        <?php echo $this->Form->checkbox('work_at_home', array('checked' => $cuser['work_at_home'],'hiddenField'=>true), array('class' => '','id' => 'work_at_home', 'label' => false, 'value' => 1)); ?>
                        <?php echo __('Work at home');?>
                        </label>
                    </span>
            </div>
            <div class="clear"></div>
        </div>

        <div class="form-group required">
            <label class="col-md-3 control-label">
                <?php echo __('Major job of interests');?>
            </label>
            <div class="col-md-9">
                <input type="hidden" name="data[job_interest]" id="_" value="">
                <?php $i = 0; $check_interest = 0; foreach($interest_jobs as $key=>$job):?>
                    <?php $checked = '';
                    if ($cuser['job_interest'] == $key){
                        $checked = 'checked';
                        $check_interest = 1;
                    }?>
                    <div class="col-md-3">
                        <input type="radio" name="data[job_interest]" id="<?php echo 'job_interest'.$i;?>"  <?php echo $checked;?> value="<?php echo $key;?>" class="job-interest" <?php echo !$is_job_other ? 'disabled=disabled' :'';?>>
                        <label for="<?php echo 'job_interest'.$i;?>"><?php echo $job;?></label>
                    </div>
                    <?php $i++; endforeach;?>
                <div class="clear"></div><div class="">
                    <div><?php echo __('Or please type in');?></div>
                    <?php echo $this->Form->text('job_interest_text', array('value' => !$check_interest ? $cuser['job_interest'] : '')); ?>
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="form-group required">
            <label class="col-md-3 control-label">
                <?php echo __('Major place of employment');?>
            </label>
            <div class="col-md-9">
                <?php $i = 0; foreach($sidos as $sido):?>
                    <div class="col-md-3">
                        <input type="radio" name="data[sidocode]" <?php echo ($cuser['sidocode'] == $sido['Sido']['sidocode'] || (empty($cuser['sidocode']) && $i == 0)) ? 'checked' : '';?> id="<?php echo 'major_place'.$i;?>" value="<?php echo $sido['Sido']['sidocode'];?>" class="" <?php echo !$is_job_other ? 'disabled=disabled' :'';?>>
                        <label for="<?php echo 'major_place'.$i;?>"><?php echo Configure::read('Config.language') == 'kor' ? $sido['Sido']['sido_kor'] : $sido['Sido']['sido_eng'];?></label>
                    </div>
                    <?php $i++; endforeach;?>
            </div>
            <div class="clear"></div>
        </div>

    </div>
    <div id="wrap_form_info" style="<?php echo $cuser['job_belong_to'] == 'others' ? 'display:none': '';?>">
        <div class="form-group required">
            <label class="col-md-3 control-label" id="type_phone">
                <?php echo __('%s phone',$type)?>
            </label>
            <div class="col-md-9">
                <?php echo $this->Form->text('com_phone.', array('value' => $phone_1, 'class' => 'com-phone', 'maxlength' =>3)); ?>-
                <?php echo $this->Form->text('com_phone.', array('value' => $phone_2, 'class' => 'com-phone', 'maxlength' =>4)); ?>-
                <?php echo $this->Form->text('com_phone.', array('value' => $phone_3, 'class' => 'com-phone', 'maxlength' =>4)); ?>
                <?php echo $this->Form->hidden('temp_phone', array('value' => '', 'class' => 'com-phone')); ?>
                <a class="btn btn-action" id="btn_find_phone"><?php echo __('Find');?></a>
            </div>
            <div class="clear"></div>
        </div>
        <div class="form-group required">
            <label class="col-md-3 control-label" id="type_name">
                <?php echo __('%s name',$type)?>
            </label>
            <div class="col-md-9 form-inline">
                <?php echo $this->Form->text('com_name', array('value' => $cuser['com_name'])); ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="form-group required">
            <label class="col-md-3 control-label" id="type_name">
                <?php echo __('Zipcode')?>
            </label>
            <div class="col-md-9 wrap-suggestion">
                <?php echo $this->Form->text('com_zip', array('value' => $cuser['com_zip'],'placeholder' => __('Zip code'))); ?>
                <a class="btn btn-action" onclick="openSearchAddessPopup()"><?php echo __('Find');?></a>
                <br>
                <ul id="zipcode-suggestion" style="display: none" class="zip-suggestion">
                </ul>
            </div>
            <div class="clear"></div>
        </div>
        <div class="form-group required">
            <label class="col-md-3 control-label">
                <?php echo __('Address')?>
            </label>
            <div class="col-md-9 form-inline form-address">
                <?php echo $this->Form->text('com_address_1', array('value' => $cuser['com_address_1'],'placeholder' => __('Address 1'))); ?><br>
                <?php echo $this->Form->text('com_address_2', array('value' => $cuser['com_address_2'],'placeholder' => __('Address 2'))); ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="form-group required">
            <label class="col-md-3 control-label">
                <?php echo __('Fax')?>
            </label>
            <div class="col-md-9 form-inline">
                <?php echo $this->Form->text('com_fax', array('value' => $cuser['com_fax'])); ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="form-group required">
            <label class="col-md-3 control-label">
                <?php echo __('Title')?>
            </label>
            <?php $show_title_radio = in_array($cuser['job_belong_to'], array('pharmacy', 'hosp_pharmacy')) || empty($cuser['job_belong_to']); ?>
            <div class="col-md-9 form-inline" id="title_input" style="<?php echo $show_title_radio ? 'display:none' : ''?>">
                <?php echo $this->Form->text('com_title', array('value' => $cuser['com_title'], 'disabled' => !$show_title_radio ? false : true)); ?>
            </div>
            <div class="col-md-9 form-inline" id="title_radio" style="<?php echo !$show_title_radio ? 'display:none' : ''?>">
                <?php
                $title_arr = array(__('Chief Pharmacist') => __('Chief Pharmacist'), __('Pharmacist') => __('Pharmacist'), __('Admin & Others') => __('Admin & Others'));
                ?>
                <?php echo $this->Form->radio('com_title', $title_arr, array('class' => ' ','id' => false, 'label' => true, 'value' => $cuser['com_title'],'hiddenField'=>true, 'legend'=> false, 'separator'=>'&nbsp;&nbsp;&nbsp;', 'disabled' => $show_title_radio ? false : true)); ?>
            </div>
            <div class="clear"></div>
        </div>
        <div id="section_department" style="<?php echo !$show_department ? 'display:none' : '';?>">
            <?php echo $this->Form->hidden('require_department', array('value' => !$show_department ? 0 : 1));?>
            <div class="form-group required">
                <label class="col-md-3 control-label">
                    <?php echo __('Department')?>
                </label>
                <div class="col-md-9 form-inline">
                    <input type="hidden" name="data[com_department]" id="_" value="">
                    <?php echo $this->Form->radio('com_department', array('sales' => __('Sales'), 'marketing' => __('Marketing'), 'others'=> __('Others')), array('class' => 'department','id' => false, 'label' => false, 'value' => $cuser['com_department'],'hiddenField'=>true, 'legend'=> false, 'separator'=>'&nbsp;&nbsp;&nbsp;')); ?>
                </div>
                <div class="clear"></div>
            </div>
            <div class="form-group required sale-area-section" style="<?php echo ($cuser['com_department'] != 'sales' || in_array($cuser['job_belong_to'], $it)) ? 'display:none' : ''?>">
                <label class="col-md-3 control-label">
                    <?php echo __('Sales area')?>
                </label>
                <div class="col-md-9 form-inline">
                    <?php echo $this->Form->hidden('prepopulate', array('value' => json_encode($sale_areas, JSON_UNESCAPED_UNICODE ))); ?>
                    <?php echo $this->Form->text('sale_area', array('value' => $cuser['sale_area'])); ?>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="form-group required">
            <label class="col-md-3 control-label">
                <?php echo __('Home page')?>
            </label>
            <div class="col-md-9 form-inline">
                <?php echo $this->Form->text('com_homepage', array('value' => $cuser['com_homepage'])); ?>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</form>

<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>
<script>
var openSearchAddessPopup = function() {
    new daum.Postcode({
        oncomplete: function(data) {
            $('#com_zip').val( data.zonecode );
            $('#com_address_1').val( data.roadAddress );
            $('#com_address_2').focus();

            searchCodeByZip( data.zonecode );
        }
    }).open();
}

var searchCodeByZip = function( zip_code ) {
    $.post( '/users/get_zipcode_api', {zip_code:zip_code}, function( data ) {
        var result = JSON.parse( data );

        if ( result.sidocode ) {
            $('input[name="data[sidocode]"]').val( result.sidocode );
            $('input[name="data[kpasame]"]').val( result.kpasame );
        }

        prevComZip = $('#com_zip').val();
    });
}

var prevComZip;
window.onload = function() {
    prevComZip = $('#com_zip').val();

    $('#com_zip').on('input',function(e){
        searchCodeByZip( $('#com_zip').val() );
    });
};
document.getElementById('btn_find_phone').addEventListener('click', function() {
    setTimeout( function() {
        if ( prevComZip != $('#com_zip').val() )
            searchCodeByZip( $('#com_zip').val() );
    }, 1000 * 1.5 );
});
</script>