<?php if($this->request->is('ajax')) $this->setCurrentStyle(4); ?>
<div class="title-modal">
    <?php echo __d('forum','Tag member');?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <script type="text/javascript">
        $(document).ready(function(){
            $("#tag-member").tokenInput("<?php echo $this->request->base?>/users/do_get_json",
                {
                    preventDuplicates: true,
                    hintText: "<?php echo addslashes(__d('forum','Enter one or more users'))?>",
                    noResultsText: "<?php echo addslashes(__d('forum','No results'))?>",
                    tokenLimit: 20,
                    <?php if(!empty($friends)): ?>
                    prePopulate: <?php echo  $friends; ?>,
                    <?php endif; ?>
                    resultsFormatter: function(item)
                    {
                        return '<li class="item-tag-username">' + item.avatar + item.name + '</li>';
                    }
                }
            );
            console.log(jQuery("#tagMemberForm").serialize());
            console.log($("#tagMemberForm"));
            $('#insert_into_reply').click(function(){

                $.post("<?php echo $this->request->base?>/forums/topic/get_tag_users",
                    jQuery("#tagMemberForm").serialize(),
                    function(data){
                        var json = $.parseJSON(data);
                        tinyMCE.activeEditor.insertContent(json.content);
                        $("#tag-member").tokenInput("clear");
                    });
            });
        });

    </script>
    <form id="tagMemberForm" name="tagMemberForm" class="form-horizontal" role="form">
        <div class="form-body">
            <label class="col-md-12"><?php echo __d('forum','Tag member into your reply');?></label>
            <div class="form-group">

                <div class="col-md-6">
                    <?php echo $this->Form->text('tag-member', array('class' => 'form-control', 'style' => 'display: none;')); ?>
                </div>
                <div class="col-md-6">
                    <a id="insert_into_reply" class="btn btn-action"><?php echo  __d('forum','Insert into reply') ?></a>
                </div>

            </div>
        </div>
    </form>
    <div class="alert alert-danger error-message" style="display:none;margin-top:10px;"></div>
</div>


