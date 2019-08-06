<?php
    $this->Html->addCrumb(__d('ads','Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('ads','Manage Placements'),'/admin/ads/ads_placement');
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Ads'));
    $this->end();
?>
<style>
    .tooltip{
        max-width: 300px;
    }
</style>

<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>
    $(document).ready(function(){
        selectSampleType();
        $('#saveBtn').click(function(){
            createItem();
        });
         var period = $("#period").val();
         if(period.length > 0){
             if(period < 2){
                day = '<?php echo __d('ads','day') ?>';
            }else{
                day = '<?php echo __d('ads','days') ?>';
            }
            $(".per_time p").html("/ "+period +' ' + day);
         }
        $("#period").keyup(function(e){
            var value = $(this).val();
            var day = '';
            if(value < 2){
                day = '<?php echo __d('ads','day') ?>';
            }else{
                day = '<?php echo __d('ads','days') ?>';
            }
            $(".per_time p").html("/ "+value +' ' + day);
        });
        
        // triger period
        $('[data-toggle="tooltip"]').tooltip({
            container : '.table-toolbar'
          });
    });
    function createItem(  )
{
    disableButton('saveBtn');
    jQuery.ajax({
        url : '<?php echo Router::url('/',true).'admin/commercial/commercial_placement/save/' ?>',
        data: jQuery("#createForm").serialize(),
        type: 'POST',
        success: function(data){
            var json = $.parseJSON(data);

        if ( json.result == 0)
        {
            enableButton('saveBtn');
            $(".error-message").show();
            $(".error-message").html(json.message);
            if ($('.spinner').length > 0){
                $('.spinner').remove();
            }
        }else{
             window.location ='<?php echo $admin_url.'ads_placement' ?>';
        }
        }
    });
}

function selectSampleType()
{
    var value = jQuery('#placement_type').val();
    var img = '';
      jQuery('.lb_dimension_width').empty();
    if(value == 'image')
    {
        img = '<img src="<?php echo $this->request->base.'/'.ADS_SAMPLE_IMAGE_URL;?>banner_commercial.png" />';
        jQuery('.list_feed_position').hide();
    }else if(value == 'feed'){
        jQuery('.lb_dimension_width').html('(>= 800px)').css('font-style','italic');
         img = '<img style="margin-top:10px;" src="<?php echo $this->request->base.'/'.ADS_SAMPLE_IMAGE_URL;?>feed_commercial.png" />';
          jQuery('.list_feed_position').show();
    }
    else 
    {
        img = '<img src="<?php echo $this->request->base.'/'.ADS_SAMPLE_IMAGE_URL;?>html_commercial.png" />';
         jQuery('.list_feed_position').hide();
    }
    jQuery('#sampleTypeImage').empty().append(img);
}
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<?php echo$this->Moo->renderMenu('Ads', __d('ads','Manage Placements'));?>
<div class="portlet-body">
    <div class="table-toolbar">
        <form class="form-horizontal" id="createForm" role="form" method="post" action="<?php echo  $this->request->base ?>/admin/ads/ads_placement/create">
            <div class="form-group">
                <label class="control-label col-sm-2" for="placement_type"><?php echo __d('ads','Placement type') ?></label>
                <div class="col-sm-5">
                     <?php echo $this->Form->hidden('id', array('value' => $placement['AdsPlacement']['id'])); ?>
                    <?php  echo $this->Form->select('placement_type',$options_type,array(
                        'class' => 'form-control', 
                        'value' =>$placement['AdsPlacement']['placement_type'] , 
                        'empty' => false,
                        'onchange' => 'selectSampleType()'
                    )); ?>
                    <div id="sampleTypeImage" style="overflow: hidden;"></div>
                </div>
            </div>


            <div class="form-group">
                <label class="control-label col-sm-2" for="name"><?php echo __d('ads','Placement name') ?></label>
                <div class="col-sm-5">
                    <?php  echo $this->Form->text('name',array('class' => 'form-control', 'value' =>$placement['AdsPlacement']['name'] , 'empty' => false)); ?>
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-sm-2" for="name"><?php echo __d('ads','Description') ?></label>
                <div class="col-sm-5">
                    <?php  echo $this->Form->textarea('description',array('class' => 'form-control', 'value' =>$placement['AdsPlacement']['description'] , 'empty' => false)); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" for="dimension"><?php echo __d('ads','Dimension') ?><span class="lb_dimension_width"></span></label>
                <div class="col-sm-2">
                 <?php  echo $this->Form->text('dimension_width',array('class' => 'form-control', 'value' =>$placement['AdsPlacement']['dimension_width'] ,'placeholder'=>__d('ads','width'), 'empty' => false)); ?>
                    <?php echo __d('ads','Ads dimension are in pixel') ?>
                </div>
                <div class="col-sm-2">
                    <?php  echo $this->Form->text('dimension_height',array('class' => 'form-control', 'value' =>$placement['AdsPlacement']['dimension_height'] ,'placeholder'=>__d('ads','height'), 'empty' => false)); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" for="price"><?php echo __d('ads','Price') ?>(<?php echo Configure::read('Ads.currency_symbol');?>)</label>
                <div class="col-sm-2">
                   <?php  echo $this->Form->text('price',array('class' => 'form-control', 'value' =>$placement['AdsPlacement']['price'] , 'empty' => false)); ?>
                </div>
                <div class="col-sm-2">
                     <?php  echo $this->Form->number('period',array('class' => 'form-control', 'value' =>$placement['AdsPlacement']['period'],'placeholder'=>__d('ads','days') , 'empty' => false,'min'=>'0')); ?>
                </div>
            </div>
            
              <div class="form-group">
                <label class="control-label col-sm-2" for="view_limit"><?php echo __d('ads','Total Views Allowed') ?>
                (<a data-toggle="tooltip" title="<?php echo __d('ads', 'The campaign will end when this number of views is reached. Enter \'0\' for unlimited views.')?>" class="tip" href="javascript:void(0);">?</a>)
              </label>
                <div class="col-sm-2">
                    <?php  echo $this->Form->text('view_limit',array('class' => 'form-control', 'value' =>$placement['AdsPlacement']['view_limit'] , 'empty' => false)); ?>
                </div>
                
                 <div class="col-sm-2">
                     <div class="per_time"><p style="padding-top:10px"></p></div>
                </div>
            </div>
            
               <div class="form-group">
                <label class="control-label col-sm-2" for="click_limit">
                    <?php echo __d('ads','Total Clicks Allowed') ?>
                       (<a data-toggle="tooltip" title="<?php echo __d('ads', 'The campaign will end when this number of clicks is reached. Enter \'0\' for unlimited clicks.')?>" class="tip" href="javascript:void(0);">?</a>)
               
                                </label>
                <div class="col-sm-2">
                     <?php  echo $this->Form->text('click_limit',array('class' => 'form-control', 'value' =>$placement['AdsPlacement']['click_limit'] , 'empty' => false)); ?>
                </div>
                 
                 <div class="col-sm-2">
                     <div class="per_time"><p style="padding-top:10px"></p> </div>
                </div>
                
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" for="total_ads">
                    <?php echo __d('ads','Ads appears') ?>
                    (<a data-toggle="tooltip" title="<?php echo __d('ads', 'Maximum ads can be placed at this placement at a time. Ex: \'1\' mean that only one advertiser can purchase this place. Others can purchase until the ad at this place is expired or reached limitation (views/clicks)')?>" class="tip" href="javascript:void(0);">?</a>)
                </label>
                <div class="col-sm-2">
                    <?php  echo $this->Form->text('total_ads',array('class' => 'form-control', 'value' =>$placement['AdsPlacement']['total_ads'] , 'empty' => false)); ?>
                </div>
            </div>
            
             <div class="form-group">
                <label class="control-label col-sm-2" for="number_of_ads"><?php echo __d('ads','Number of ads') ?>
                 (<a data-toggle="tooltip" title="<?php echo __d('ads', "Number of ads will appear at the same time in this placement.")?>" class="tip" href="javascript:void(0);">?</a>)
                </label>
                <div class="col-sm-2">
                    <?php  echo $this->Form->text('number_of_ads',array('class' => 'form-control', 'value' =>$placement['AdsPlacement']['number_of_ads'] , 'empty' => false)); ?>
                </div>
                
            </div>
            
            <div class="form-group">
                <label class="control-label col-sm-2" for="enable"><?php echo __d('ads','Status')?></label>
                <div class="col-sm-5">
                    <?php $status = $placement['AdsPlacement']['enable']?1:0 ?>
                     <?php  echo $this->Form->select('enable',$options_status,array('class' => 'form-control', 'value' =>$status , 'empty' => false)); ?>
               
                </div>
                
            </div>
            <div class="form-group list_feed_position">
                <label class="control-label col-sm-2" for="feed_position">
                    <?php echo __d('ads','Where the placement will appear on each feed page on home feed')?>
                    (<a data-toggle="tooltip" title="<?php echo __d('ads', 'If you selected 3 for example, the ad will appear after activity number 3 on each feed page after loading more')?>" class="tip" href="javascript:void(0);">?</a>)
                    </label>
                    <div class="col-md-8 ">
                        <?php 

                         
                        ?>
                       <?php for($i = 1; $i<=15;$i++): ?>
                        <div class="row"><div class="col-md-1" style="width:5px !important"><?php echo $i; ?></div><div class="col-md-1">
                                <input type="checkbox" name="data[feed_position][]" value="<?php echo $i; ?>" class="form-control" <?php if(in_array($i, $feed_position))echo 'checked'; ?>></div></div>
                       <?php endfor; ?>
                    </div>
                
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" for="placement">
                    <?php echo __d('ads','Placement')?></br>
                    <?php echo __d('ads','Sample Position')?>
                    (<a data-toggle="tooltip" title="<?php echo __d('ads', 'Sample position where the ad in this placement will appear with target users. Note that the ad widget does not added into the selected position auto after the placement is created. You need to drag and drop it at Layout Editor section after creating the placement.Do no need to drag and drop ads widgets into layout editer if the placement type is feed')?>" class="tip" href="javascript:void(0);">?</a>)
                </label>
                <div class="col-md-8 ">
                 <?php
                 $i = 1;
                 foreach($positions as $key=>$position): ?>
                   <?php if($key % 4 == 0): ?>
                        <div class="row">
                   <?php endif; ?>   
                        <div class="col-md-2">
                            <?php if($position['AdsPosition']['id'] ==$placement['AdsPlacement']['ads_position_id']): ?>
                            <input type="radio" style="float: left" name="data[ads_position_id]" value="<?php echo $position['AdsPosition']['id'] ?>" checked="checked">
                             <?php else: ?>
                             <input type="radio" style="float: left" name="data[ads_position_id]" value="<?php echo $position['AdsPosition']['id'] ?>">
                             <?php endif; ?>
                            <img style="padding-bottom: 10px;" width="100px" height="100px" src="<?php echo Router::url('/', true).ADS_POSITION_IMAGE_URL.$position['AdsPosition']['image'] ?>">
                        </div>
                   <?php if(($key+1) % 4 == 0 ||  $key == count($positions)-1): ?>
                        </div>
                   <?php endif; ?>   
                    
                  <?php endforeach; ?>  
                </div>
               
            </div>
            <div class="form-group">
                <div class="col-sm-2"></div>
                <div class="col-md-5"> 
                     <div class="error-message" id="errorMessage" style="display:none"></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-2">
                </div>
                <div class="col-sm-5">
                     <button type='button' class='btn btn-success' id="saveBtn"><?php echo __( 'Save')?></button>
                </div>
            </div>
        </form>
    </div>
</div>