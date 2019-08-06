<?php
    $helper = MooCore::getInstance()->getHelper('Forum_Forum');
    echo $this->Html->css(array('jquery-ui', 'footable.core.min','token-input'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable','jquery.tokeninput'), array('inline' => false));
    echo $this->Html->css(array('fineuploader','button',), null, array('inline' => false));
    echo $this->Html->script(array('vendor/jquery.fileuploader'), array('inline' => false));
    echo $this->Html->script(array('tinymce/tinymce.min', 'vendor/jquery.fileuploader'), array('inline' => false));
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Forum'));
    $this->end();
    
    $this->Html->addCrumb(__d('forum','Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('forum', 'Forum'), '/admin/forum/forums');
    $this->Html->addCrumb(__d('forum', 'Forums Manager'), '/admin/forum/forums');
?>
<?php echo $this->Moo->renderMenu('Forum', __d('forum','Forums Manager'));?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
        $('.deleteForum').click(
            function (e) {
                e.preventDefault();
                console.log($(this).data("id"));
                mooAjax.post(
                    {
                        url: "<?php echo $this->request->base?>/admin/forum/forums/delete_confirm",
                        data: {"id": $(this).data("id")}
                    },
                    function (data) {
                        var json = $.parseJSON(data);
                        if (json.result == 0)
                        {
                            mooAlert(json.message);
                        }
						if (json.result == 1)
                        {
							var urlDelete = mooConfig.url.base + '/admin/forum/forums/delete/' + json.id;
							confirm_del(json.message,urlDelete);
                            
                        }
                    }
                );
            }
        );
		function confirm_del(msg,urlDelete){
			// Set title
			$($('#portlet-config  .modal-header .modal-title')[0]).html(mooPhrase.__('please_confirm'));
			// Set content
			$($('#portlet-config  .modal-body')[0]).html(msg);
			// OK callback
			$('#portlet-config  .modal-footer .ok').attr("data-dismiss","modal");
			$('#portlet-config  .modal-footer .ok').click(function(){
				$.post(urlDelete,
				function(data){
					var json = $.parseJSON(data);
					if (json.result == 1){
						window.location.href = mooConfig.url.base + '/admin/forum/forums/';
					}
					if (json.result == 0){
						mooAlert(json.message);
					}
				});
			});
			$('#portlet-config').modal('show');
		}
		$('.deleteForumCategory').click(
            function (e) {
                e.preventDefault();
                console.log($(this).data("id"));
                mooAjax.post(
                    {
                        url: "<?php echo $this->request->base?>/admin/forum/forum_categories/delete_confirm",
                        data: {"id": $(this).data("id")}
                    },
                    function (data) {
                        var json = $.parseJSON(data);
                        if (json.result == 0)
                        {
                            mooAlert(json.message);
                        }
						if (json.result == 1)
                        {
							var urlDelete = mooConfig.url.base + '/admin/forum/forum_categories/delete/' + json.id;
							confirm_del_category(json.message,urlDelete);
                            
                        }
                    }
                );
            }
        );
		function confirm_del_category(msg,urlDelete){
			// Set title
			$($('#portlet-config  .modal-header .modal-title')[0]).html(mooPhrase.__('please_confirm'));
			// Set content
			$($('#portlet-config  .modal-body')[0]).html(msg);
			// OK callback
			$('#portlet-config  .modal-footer .ok').attr("data-dismiss","modal");
			$('#portlet-config  .modal-footer .ok').click(function(){
				$.post(urlDelete,
				function(data){
					var json = $.parseJSON(data);
					if (json.result == 1){
						window.location.href = mooConfig.url.base + '/admin/forum/forums/';
					}
					if (json.result == 0){
						mooAlert(json.message);
					}
				});
			});
			$('#portlet-config').modal('show');
		}
    });
<?php $this->Html->scriptEnd(); ?>
<script type="text/javascript">

</script>
<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group">
                    <button class="btn btn-gray" data-toggle="modal" data-target="#ajax"  href="<?php echo $this->request->base?>/admin/forum/forum_categories/create/">
                        <?php echo __d('forum','Add New Forum Category');?>
                    </button>
                </div>
            </div>
            <div class="col-md-6">

            </div>
        </div>
    </div>
</div>
<style>
    .forum-logo{
        margin-top: 20px;
    }
    .forum-border-action{
        border-right: 1px solid #0f0eff;
    }
</style>
<?php
$m_forum = MooCore::getInstance()->getModel('Forum.Forum');
$m_user = MooCore::getInstance()->getModel('User');
?>
<?php foreach ($categories as $category): ?>

<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h2 class="panel-title">
                <img src="<?php echo $helper->getIconForumCategory($category);?>" class="img-rounded" width="32" height="32"/>
                <?php echo "{$category['ForumCategory']['name']} [order = {$category['ForumCategory']['order']}]";?>
                <ul class=" pull-right list-inline">
                    <li><a data-toggle="modal" data-target="#ajax"  href="<?php echo $this->request->base?>/admin/forum/forums/create/<?php echo $category['ForumCategory']['id'];?>" ><?php echo __d('forum','Add forum'); ?></a></li>
                    <li>|</li>
                    <li><a data-toggle="modal" data-target="#ajax"  href="<?php echo $this->request->base?>/admin/forum/forum_categories/create/<?php echo $category['ForumCategory']['id'];?>" ><?php echo __d('forum','Edit'); ?></a></li>
                    <?php if($category['ForumCategory']['id'] != 1):?>
                    <li>|</li>
					<li><a style="cursor:pointer;" class="deleteForumCategory" data-id="<?php echo $category['ForumCategory']['id'];?>"><?php echo __d('forum','Delete'); ?></a></li>
                    <?php endif;?>
                </ul>
            </h2>
        </div>
        <?php
        $forums = $m_forum->getForumByCatId($category['ForumCategory']['id']);
        if(count($forums) > 0):
        $show_move = true;
        $f = 0;
        foreach ($forums as $forum): $f++?>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-customize col-sm-12">
                    <tbody>
                    <tr>
                        <td class="col-sm-1">
                            <div class="row">
                                <div class="col-sm-12 text-center">
                                    <img src="<?php echo $helper->getIconForum($forum);?>" class="img-rounded forum-logo" width="48" height="48"/>
                                </div>
                                <div class="col-sm-12 text-center"><?php echo $helper->getLockIcon($forum);?> <?php echo $helper->getDisableIcon($forum);?></div>
                            </div>

                        </td>
                        <td class="col-sm-8">
                            <h2><?php echo $forum['Forum']['name'];?></h2>
                            <p><?php echo $forum['Forum']['description'];?></p>
                            <p><?php echo __d('forum','Moderator');?> <a data-toggle="modal" data-target="#ajax"  href="<?php echo $this->request->base?>/admin/forum/forums/create/<?php echo $category['ForumCategory']['id'];?>/<?php echo $forum['Forum']['id'];?>" ><?php echo __d('forum','Add'); ?></a></p>
                            <p>
                                <?php
                                if(!empty($forum['Forum']['moderator'])):
                                    $array_mods = explode(',',$forum['Forum']['moderator']);
                                    $count = count($array_mods);
                                    $i = 0;
                                ?>
                                <?php foreach ($array_mods as $n): $i++;?>
                                    <?php if(!empty($mods[$n])):?>
                                        <span><a href="<?php echo $this->request->webroot?>users/view/<?php echo $mods[$n]['User']['id'];?>"><?php echo $mods[$n]['User']['name'];?></a> (<a href="<?php echo $this->request->base?>/admin/forum/forums/remove_mod/<?php echo $forum['Forum']['id'];?>/<?php echo $mods[$n]['User']['id'];?>" ><?php echo __d('forum','remove'); ?></a>)
                                                <?php if($i != $count) echo ", ";?>
                                        </span>
                                    <?php endif;?>
                                <?php endforeach; unset($i);unset($count); endif;?>
                            </p>
                            <p>
                                <ul class="list-inline list-sub-forum">
                                    <li><a href="<?php echo $this->request->base?>/admin/forum/forums/sub/<?php echo $forum['Forum']['id'];?>" ><?php echo __d('forum','Sub-forum'); ?>:</a></li>
                                    <?php
                                    $subForums = $m_forum->getSubForumByParentId($forum['Forum']['id']);
                                    if(empty($subForums)):
                                        echo "<li>". __d('forum','None') ."</li>";
                                    else:
                                        $i = 0;
                                        $count = count($subForums);
                                        foreach($subForums as $subForum): $i++;?>
                                            <li><?php echo $subForum['Forum']['name'];?> <?php if(!$subForum['Forum']['status']):?> (<?php echo $helper->getLockIcon($subForum);?> <?php echo $helper->getDisableIcon($subForum);?>) <?php endif;?> <?php if($i != $count) echo ", ";?></li>
                                        <?php endforeach; unset($i);unset($count);?>
                                    <?php endif;?>
                                </ul>
                            </p>
                        </td>
                        <td class="col-sm-3">
                            <ul class="list-inline col-sm-12" style="margin-top: 20px;">
                                <?php if($f != 1): ?>
                                <li class="col-sm-4 text-center forum-border-action"><a href="<?php echo $this->request->base?>/admin/forum/forums/moveup/<?php echo $forum['Forum']['id'];?>/<?php echo $forum['Forum']['parent_id'];?>/<?php echo $forum['Forum']['category_id'];?>/<?php echo $forum['Forum']['order'];?>" ><?php echo __d('forum','Move up'); ?></a></li>
                                <?php else:?>
                                <li class="col-sm-4"></li>
                                <?php endif;?>
                                <li class="col-sm-4 text-center forum-border-action"><a data-toggle="modal" data-target="#ajax"  href="<?php echo $this->request->base?>/admin/forum/forums/create/<?php echo $category['ForumCategory']['id'];?>/<?php echo $forum['Forum']['id'];?>" ><?php echo __d('forum','Edit'); ?></a></li>
                                <li class="col-sm-4 text-center"><a style="cursor:pointer;" class="deleteForum" style="cursor:pointer;" data-id="<?php echo $forum['Forum']['id'];?>"  ><?php echo __d('forum','Delete'); ?></a></li>
                            </ul>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach;
        else: ?>
            <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-customize">
                    <tbody>
                    <tr>
                        <td>
                            <p><?php echo __d('forum','No forums found'); ?></p>
                        </td>
                        <td>

                        </td>
                        <td>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <?php endif;?>

    </div>
</div>

<?php endforeach; //unset($m_forum);?>

<style>
    .list-sub-forum i{
        font-size: 15px;
    }
</style>

