<?php $base_url = $this->request->base; ?>
<style>
    .ads_detail_content{
        margin-bottom: 15px;
        font-size:13px;
        overflow: hidden;
    }
    .ads_detail_content h5{
        font-weight:bold;
        font-size: 15px;
    }
    .ads_header{
        margin-bottom:15px
    }
    .ads_bold{
        font-weight:bold;
    }
    .ads_message{
        font-size:13px;
        color:green;
        font-style: italic;
    }
</style>



<div class="bar-content ads-content" style="padding: 5px;">
    <?php if($aAds): ?>
    <div class="content_center">
        <div class="ads-title-header" style="font-size:24px;">
            <div class="col-md-6 text-left" style="font-weight: bold;"><?php echo __d('ads', 'My Ads') ?></div>
            <div class="col-md-6 text-right"><a  class="button button-action" href="<?php echo $base_url . '/ads/create' ?>"><?php echo __d('ads', 'Create New Ad') ?></a></div>
        </div>
        <div class="ads_header" >
            <div class="ads-search text-right">
                <div class="ads-search-form">
                    <?php
                    echo $this->Form->input("ads_keyword", array(
                        'div' => false,
                        'label' => false,
                        'class' => 'form-control',
                        'placeholder' => __d('ads', 'Enter a name'),
                        'name' => 'keyword',
                    ));
                    ?>
                </div>
                <button class="btn btn-gray btn-search-ads" type="submit" onclick="searchMyAds();"><?php echo __d('ads', "Search"); ?></button>
            </div>   
        </div>
        <!--load element--> 
        <input type="hidden" id="ads_user_id" value="<?php echo $user_id; ?>">
        <?php echo $this->element('myAds/load_more_ads'); ?>
        <p class="ads_no_result" style="display: none;"><?php echo __d('ads','No result found') ?><p>
        <div class="row ads_load_more">
            <div class="col-md-12 text-center">
                <?php if($is_load_more): ?>
                <a class="button button-action" onclick="loadMoreAds();"><?php echo __d('ads','LOAD MORE') ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php else: ?>
    <h5><?php echo __d('ads','There is no ads') ?></h5>
    <?php endif; ?>
</div>

<script>
    var offset = 3;
    function loadMoreAds(){
        $.ajax({
            url:'<?php echo $base_url.'/ads/my_ads_load_more' ?>',
            type:'GET',
            data:{'offset':offset},
            success: function(data){
                if(data == ''){
                    $('.ads_load_more').remove();
                }else{
                     $('.ads_load_more').before(data);
                     offset+=1;
                }
            } 
        });
    }
    
    function searchMyAds(){
        var keywork = $('#ads_keyword').val();
        if(keywork.trim() == ''){
            return;
        }
         $('.ads_no_result').hide();
         $('.ads_load_more').hide();
        $.ajax({
            url:'<?php echo $base_url . '/ads/my_ads_load_more' ?>',
            type:'GET',
            data:{'offset':offset,'search':keywork},
            success: function(data){
                if(data == ''){
                    
                    $('.ads_detail_content').remove();
                    $('.ads_load_more').before(data);
                    $('.ads_no_result').show();
                }else{
                    $('.ads_detail_content').remove();
                    $('.ads_load_more').before(data);
                }
            } 
        });
    }
    function handleAdsAction(id,action,e){
     if(action == 'delete'){
         var r = confirm("<?php echo __d('ads','Do you really want to delete this ad ?') ?>");
         if(r == false){
             return;
         }
     }
     $(e).closest('.ads_detail_content').children('.ads_message').show().html('');
     $(e).closest('.ads_detail_content').children('.ads_message').show().spin('small');
     var user_id = $("#ads_user_id").val();
        $.ajax({
            url:'<?php echo $base_url.'/ads/handle_ads_action/' ?>',
            data:{'action':action,'id':id,'user_id':user_id},
            type:'GET',
            success:function(data){
                data = $.parseJSON(data);
                var result = data.result;
                var message = data.message;
                switch(action){
                    case 'delete':
                         if(data.result == 0){
                             $(e).closest('.ads_detail_content').children('.ads_message').show().html(message);
                         }else{
                             $(e).closest('.ads_detail_content').empty().html('<p class="ads_message">'+message+'</p>');
                             $("#browse li.current .badge_counter").text(data.total_ads);
                             offset-=1;
                         }
                        break;
                    case 'show':
                        if(result == 1){
                            $('.ads_show_'+id.toString()).hide();
                            $('.ads_hide_'+id.toString()).show();
                            $(e).closest('.ads_detail_content').find('.ads_cur_status').html('/'+"<?php echo __d('ads','Show') ?>");
                        }
                        $(e).closest('.ads_detail_content').children('.ads_message').show().html(message);
                        break;
                    case 'hide':
                        if(result == 1){
                            $('.ads_show_'+id.toString()).show();
                            $('.ads_hide_'+id.toString()).hide();  
                            $(e).closest('.ads_detail_content').find('.ads_cur_status').html('/'+"<?php echo __d('ads','Hide') ?>");
                        }
                         $(e).closest('.ads_detail_content').children('.ads_message').show().html(message);
                        break;
                }
            }
        });
    }
</script>
