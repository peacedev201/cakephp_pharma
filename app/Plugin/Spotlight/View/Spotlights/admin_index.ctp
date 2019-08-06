<?php
    $this->Html->addCrumb(__('Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('spotlight','Spotlight Users'), array('controller' => 'spotlights', 'action' => 'admin_index'));
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Spotlight'));
    $this->end();
    $this->Paginator->options(array('url' => $this->passedArgs));
?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
function save_order()
{
    var list={};
    $('input[name="data[ordering]"]').each(function(index,value){
        list[$(value).data('id')] = $(value).val();
    })
    jQuery.post("<?php echo $this->request->base?>/admin/spotlight/spotlights/save_order/",{spotlights:list},function(data){
        window.location = data;
    });
}
<?php $this->Html->scriptEnd(); ?>
   
<?php echo  $this->Moo->renderMenu('Spotlight', __d('spotlight','Spotlight Users')); ?>
<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
            	<?php if ($cuser['Role']['is_super']): ?>   
            	<div class="btn-group">
                    <button class="btn btn-gray" id="sample_editable_1_new" onclick="confirmSubmitForm('<?php echo __d('spotlight','Are you sure you want to remove these users from Spotlight.')?>', 'deleteForm')">
                        <?php echo  __d('spotlight','Remove');?>
                    </button>
                </div>             
                <div class="btn-group">
                    <a onclick="save_order()" class="btn btn-gray" >
                        <?php echo __d('spotlight','Save order');?>
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <div id="sample_1_filter" class="dataTables_filter">
                    <label>
                    <form method="post" action="<?php echo  $this->request->base ?>/admin/spotlight/spotlights">
                        <?php echo $this->Form->text('keyword', array('class' => 'form-control input-medium input-inline', 'placeholder' => __d('spotlight','Search by name or email'))); ?>
                        <?php echo $this->Form->submit('', array('style' => 'display:none')); ?>
                    </form>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <form method="post" action="<?php echo  $this->request->base ?>/admin/spotlight/spotlights/remove_multiuser" id="deleteForm">
        <?php echo $this->Form->input('type', array('type' => 'hidden', 'value' => '')) ?>
    <table class="table table-striped table-bordered table-hover" id="sample_1">
        <thead>
        <tr>
        	<?php if ($cuser['Role']['is_super']): ?>
                <th width="30"><input type="checkbox" onclick="toggleCheckboxes2(this)"></th>
            <?php endif; ?>
            <th>
                <?php echo $this->Paginator->sort('id', __d('spotlight','ID')); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('User.name', __d('spotlight','Name')); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('User.email', __d('spotlight','Email')); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('Role.name', __d('spotlight','Role')); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('created', __d('spotlight','Registry Time')); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('created', __d('spotlight','End Time')); ?>
            </th>
            <?php if($order_setting == 0):?>
            <th width="50">
                <?php echo $this->Paginator->sort('ordering', __d('spotlight','Order')); ?>
            </th>
            <?php endif;?>
            <th class="text-center" width="80">
                <?php echo $this->Paginator->sort('active', __d('spotlight','Active')); ?>
            </th>
            <th class="text-center" width="80">
                <?php echo __d('spotlight','Action')?>
            </th>
        </tr>
        </thead>
        <tbody>
            <?php $count = 0;
            foreach ($users as $user):?>
                <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                	<?php if ( $cuser['Role']['is_super'] ): ?>
                        <td><input type="checkbox" name="spotlights[]" value="<?php echo $user['SpotlightUser']['id']?>" class="check"></td>
                    <?php endif; ?>
                    <td><?php echo  $user['SpotlightUser']['id'] ?></td>
                    <td class="reorder">
                        <a href="<?php echo  $this->request->base ?>/admin/users/edit/<?php echo  $user['User']['id'] ?>"><?php echo  h($user['User']['name']) ?></a>
                    </td>
                    <td class="reorder"><?php echo  $user['User']['email'] ?></td>
                    <td><?php echo  $user['Role']['name'] ?></td>
                    <td class="reorder"><?php echo  date('m/d/Y H:i:s', strtotime($user['SpotlightUser']['created'])); ?></td>
                    <td class="reorder"><?php echo  date('m/d/Y H:i:s', strtotime($user['SpotlightUser']['end_date'])); ?></td>
                    <?php if($order_setting == 0):?>
                    <td width="50px" class="reorder"><input data-id="<?php echo $user['SpotlightUser']['id']?>" style="width:50px" type="text" name="data[ordering]" value="<?php echo $user['SpotlightUser']['ordering']?>" /> </td>
                    <?php endif;?>
                    <td class="reorder text-center">
                        <?php if ( $user['SpotlightUser']['active'] ): ?>
                            <a href="<?php echo $this->request->base.'/admin/spotlight/spotlights/do_unactive/'.$user['SpotlightUser']['id']?>" onclick="return confirm('<?php echo __d('spotlight','You are sure inactive user %s ?', $user['User']['name'])?>')"><i class="fa fa-check-square-o " title="<?php echo __d('spotlight','Disable');?>"></i></a>&nbsp;
                        <?php else: ?>
                            <a href="<?php echo $this->request->base.'/admin/spotlight/spotlights/do_active/'.$user['SpotlightUser']['id']?>" onclick="return confirm('<?php echo __d('spotlight','You are sure active user %s ?', $user['User']['name'])?>')"><i class="fa fa-times-circle" title="<?php echo __d('spotlight','Enable');?>"></i></a>&nbsp;
                        <?php endif; ?>
                    </td>
                    <td class="reorder text-center"><a href="<?php echo $this->request->base?>/admin/spotlight/spotlights/remove/<?php echo $user['SpotlightUser']['id']?>" onclick="return confirm('<?php echo __d('spotlight','You are sure remove user %s from Spotlight?', $user['User']['name'])?>')"  title="<?php echo __d('spotlight','Remove');?>"><i class="icon-trash icon-small"></i></a></td>
                </tr>
            <?php endforeach ?>

        </tbody>
    </table>
    </form>
    <div class="pagination">
        <?php echo $this->Paginator->prev('« '.__('Previous'), null, null, array('class' => 'disabled')); ?>
		<?php echo $this->Paginator->numbers(); ?>
		<?php echo $this->Paginator->next(__('Next').' »', null, null, array('class' => 'disabled')); ?>
    </div>
</div>
