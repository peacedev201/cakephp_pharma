<?php
$this->Html->addCrumb(__d('credit', 'Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('credit', 'Give mass credits'), array('controller' => 'gives', 'action' => 'admin_index'));
echo $this->Html->css(array('token-input'),null,array('inline' => false));
echo $this->Html->script(array('jquery.tokeninput'), array('inline' => false));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Credit"));
$this->end();
?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
    $(document).on('loaded.bs.modal', function (e) {
        Metronic.init();
    });
    $(document).on('hidden.bs.modal', function (e) {
        $(e.target).removeData('bs.modal');
    });

    $(document).ready(function(){       
		 $("#friends").tokenInput(
            function() {
                return "<?php echo $this->request->base?>/admin/credit/gives/do_get_json?ids=" + $('#user_ids').val();
            },
            { preventDuplicates: true, 
              hintText: "<?php echo addslashes(__d('credit', 'Enter a member\'s name'))?>",
              noResultsText: "<?php echo addslashes(__d('credit', 'No results'))?>",
              tokenLimit: 20,
              resultsFormatter: function(item)
              {

                    return '<li>' + item.avatar + item.name + '</li>';
              },
              onAdd : function (item){
                    $('#user_ids').val($('#user_ids').val() + item.id + ',');
              },
              onDelete: function (item) {
                $('#user_ids').val($('#user_ids').val().replace(item.id + ',',''));
              }
            }
        );


        $('#sendButton').click(function(){
            $(".alert-danger").hide();
            disableButton('sendButton');
            $('#sendButton').spin('small');
            //console.log(sModal);
            $.post("<?php echo $this->request->base?>/admin/credit/gives/ajax_do_send", jQuery("#sendCredits").serialize(), function(data){
                enableButton('sendButton');
                $('#sendButton').spin(false);
                var json = $.parseJSON(data);
                console.log(json);
                if ( json.result == 1 )
                {
                    $("#friend").val('');
                    $("#credit").val('');
                    $(".error-message").hide();
                    $(".alert-success").show();
                    $(".alert-success").html(json.message);
                }
                else
                {
                    $(".alert-success").hide();
                    $(".alert-danger").show();
                    $(".alert-danger").html(json.message);
                }       
            });
            
            return false;
        });

        $('input[name="data[group_type]"]').click(function(){
            if($(this).val() == 'user_group'){
                $('#role_list').show();
                $('#select_member').hide();
                $("#friends").tokenInput("clear"); 
            }else{
                $('input[name="role[]"]').each(function($k, $val){
                    $(this).parent().removeClass('checked');
                });
                $('#role_list').hide();
                $('#select_member').show();
            }            
        });

    });


<?php $this->Html->scriptEnd(); ?>
<?php echo $this->Moo->renderMenu('Credit', __d('credit', 'Give mass credits'));?>

<div id="center">
    <input type="hidden" id="user_ids" value="">
	<div class="Metronic-alerts alert alert-danger fade in" style="display:none;"></div>
    <div class="Metronic-alerts alert alert-success fade in" style="display:none;"></div>
	<div class="create_form">
        <form id="sendCredits">
        	<div class="form-body">

            <div class="form-group">
                <label class="col-md-2 control-label"></label>
                <div class="col-md-8">
                    <div class="checkbox-list">
                        <label class="checkbox-inline list_group_type">
                            <?php
                                echo $this->Form->radio('group_type',array('user_group'=>__d('credit', 'User groups'),'specific_member'=>__d('credit', 'Specific members')), array('value'=> 'specific_member','legend' => false));
                            ?> 
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label"></label>
                <div class="col-md-8">
                    <ul class="list6" id="role_list" style="display:none;">
                        <?php 
                        $stt = 0;
                        foreach ( $roles as $role ): 
                            $stt++;
                        ?>
                        <li>
                            <input type="checkbox" name="role[]" value="<?php echo $role['Role']['id']?>" id="role-<?php echo $stt;?>" ><label for="role-<?php echo $stt;?>"><?php echo $role['Role']['name']?></label>
                        </li>
                        <?php 
                        endforeach; 
                        ?>
                    </ul>
                </div>
            </div>

            <div class="form-group" id="select_member">
                <label class="col-md-2 control-label"><?php echo __d('credit', 'Select member');?></label>
                <div class="col-md-8">
                    <?php echo $this->Form->text('friends' ,array('class' => 'form-control','id' => 'friends')); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label"><?php echo __d('credit', 'Number of credits');?></label>
                <div class="col-md-8">
                    <?php echo $this->Form->text('credit',array('class' => 'form-control')); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label"><?php echo __d('credit', 'Notify user');?></label>
                <div class="col-md-8">
                    <div class="checkbox-list">
                        <label class="checkbox-inline">
                            <?php
                            	echo $this->Form->radio('select',array('0'=>__d('credit', 'No'),'1'=>__d('credit', 'Yes')), array('value'=> 1,'legend' => false));
                            ?> 

                        </label>
                    </div>
                </div>
            </div>
            <div class="col-sm-1">
                    <a href="#" class="btn btn-action" id="sendButton"><?php echo __d('credit','Send Credits')?>
                    </a>
                </div>
        </div>       
        </form>
    </div>
    
</div>
<style type="text/css" media="screen">
    #uniform-group_typeSpecificMember{
        margin-left: 30px;
    }
</style>
