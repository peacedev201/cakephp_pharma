<style>
    .userTagging-userTagging .bootstrap-tagsinput{
        border-top: 1px solid #ccc;
    }
</style>
<div class="title-modal">
    <?php echo __d('business', 'Check-In');?>    
    <button data-dismiss="modal" class="close" type="button">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body">
    <div class="create_form">
        <?php if($uid > 0):?>
            <form id="checkInForm">
                <?php echo $this->Form->hidden('business_id', array(
                    'value' => $business_id
                ));?>
                <ul style="position:relative" class="list6 list6sm2">
                    <li class="checkin_main_popup">
                        <?php echo $this->Moo->getImage(array(
                            'User' => $cuser
                        ), array(
                            "class" => "user_avatar_96 pull-left", 
                            "alt" => $cuser['name'], 
                            'prefix' => '100_square'
                        ))?>
                        <div>
                        <?php echo $this->Form->textarea('content', array(
                            'placeholder' => __d('business', 'What are you doing at %s?', $business['Business']['name'])
                        ));?>
                        </div>
                        <div class="clear">
                    </li>                 
                    <li class="checkin_action">
                       <link rel="stylesheet" type="text/css" href="<?php echo $this->request->base?>/css/global/typehead/bootstrap-tagsinput.css"/>
                     <div class="user-tagging-container" id="userTagging-id-userTagging"><i style="display:none;" class="icon-user-add" onclick="$(this).parent().find('.userTagging-userTagging').toggleClass('hidden')"></i> <div class="userTagging-userTagging"><input name="data[userTagging]" id="userTagging1" value="" placeholder="<?php echo __d('business', 'Who are you with ?'); ?>" type="text"></div></div>                     
                     <?php if($this->request->is('ajax')): ?>
                     <script>
            requirejs.config({
                "baseUrl": "js",
                "shim":{
                    "typeahead":{"deps":["jquery"],"exports":"typeahead"},
                    "tagsinput":{"deps":["jquery","typeahead","bloodhound"]}
                },
                "paths": {
                    "typeahead":"<?php echo $this->request->base?>/js/global/typeahead/typeahead.jquery",
                    "bloodhound": "<?php echo $this->request->base?>/js/global/typeahead/bloodhound.min",
                    "tagsinput": "<?php echo $this->request->base?>/js/global/typeahead/bootstrap-tagsinput"
                }              
            });             
            require(['jquery','tagsinput'], function($){ $(document).ready(function(){     
        var friends_userTagging = new Bloodhound({
                        datumTokenizer:function(d){
                            return Bloodhound.tokenizers.whitespace(d.name);
                        },
                        queryTokenizer: Bloodhound.tokenizers.whitespace,
                        prefetch: {
                            url: "<?php echo $this->Html->url(array("controller"=>"users","action"=>"friends","plugin"=>false),true) ?>.json",
                            cache: false,
                            filter: function(list) {
                                
                                return $.map(list.data, function(obj) {
                                    return obj;
                                });
                            }
                        },
                        
                        identify: function(obj) { return obj.id; },
        });
            
        friends_userTagging.initialize();
        
        $('#userTagging1').tagsinput({
            freeInput: false,
            itemValue: 'id',
            itemText: 'name',
            typeaheadjs: {
                name: 'friends_userTagging',
                displayKey: 'name',
                highlight: true,
                limit:10,
                source: friends_userTagging.ttAdapter(),
                templates:{
                    notFound:[
                        '<div class="empty-message">',
                            'unable to find any friend',
                        '</div>'
                    ].join(' '),
                    suggestion: function(data){
                    if($('#userTagging1').val() != '')
                    {
                        var ids = $('#userTagging1').val().split(',');
                        if(ids.indexOf(data.id) != -1 )
                        {
                            return '<div class="empty-message" style="display:none">unable to find any friend</div>';
                        }
                    }
                        return [
                            '<div class="suggestion-item">',
                                '<img alt src="'+data.avatar+'"/>',
                                '<span class="text">'+data.name+'</span>',
                            '</div>',
                        ].join('')
                    }
                }
            }
        });}); });</script>  
                     <?php endif; ?>
                        </li>
                    <li class="checkin_action">
                        <a id="checkInButton" class="button" href="javascript:void(0)">
                            <?php echo __d('business', 'Check In');?>
                        </a>
                        <a id="cancelReportReviewButton" class="button" href="javascript:void(0)" data-dismiss="modal">
                            <?php echo __d('business', 'Cancel');?>
                        </a>
                        <div class="clear"></div>
                    </li>
                </ul>
            </form>
            <div style="display:none;" class="error-message" id="checkInMessage"></div>
        <?php else:?>
            <div class="simple-modal-body modal-body">                 
                <div class="contents" id="simple-modal-body"><?php echo __d('business', 'Login or register to continue');?></div>             
            </div>
            <div class="simple-modal-footer">
                <a title="<?php echo __d('business', 'Ok');?>" class="button button-action primary"><?php echo __d('business', 'Ok');?></a>
            </div>
        <?php endif;?>
    </div>
</div>