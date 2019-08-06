<?php
echo $this->Html->script(array('admin/layout/scripts/compare.js?' . Configure::read('core.version')), array('inline' => false));
echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb(__d('contest', 'Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('contest', 'Contests Manager'), '/admin/contest/contests');

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Contests"));
$this->end();

$this->Paginator->options(array('url' => $passedArgs));
$helper = MooCore::getInstance()->getHelper('Contest_Contest');
?>
<?php echo $this->Moo->renderMenu('Contest', __d('contest', 'Contests Manager')); ?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).ready(function () {
$('.footable').footable();
});
function changeContestStatus(item, id)
{
if (confirm('<?php echo __d('contest', 'Are you sure?'); ?>')){
window.location = mooConfig.url.base + '/admin/contest/contests/contest_status/' + jQuery(item).val() + '/' + id;
}else{
    jQuery(item).val(jQuery(item).data('contest_status'));
}
}
function changeContestCategory(item, id)
{
if (confirm('<?php echo __d('contest', 'Are you sure?'); ?>')){
window.location = mooConfig.url.base + '/admin/contest/contests/contest_category/' + jQuery(item).val() + '/' + id;
}else{
jQuery(item).val(jQuery(item).data('category_id'));
}
}
function changeApproveStatus(item, id)
{
if (confirm('<?php echo __d('contest', 'Are you sure?'); ?>')){
window.location = mooConfig.url.base + '/admin/contest/contests/approve_status/' + jQuery(item).val() + '/' + id;
}else{
jQuery(item).val(jQuery(item).data('approve_status'));
}
}
<?php $this->Html->scriptEnd(); ?>
<div id="center">
    <form style="padding: 14px;"  method="post" action="<?php echo $this->base . '/admin/contest/contests'; ?>" class="form-inline">
        <div class="form-group">
            <label><?php echo __d('contest', 'Title'); ?></label>
            <input class="form-control input-medium input-inline" value="<?php if (isset($name)) echo $name; ?>" type="text" name="name">
        </div>
        <div class="form-group">
            <label><?php echo __d('contest', 'Category'); ?></label>
            <select class="form-control input-medium input-inline" name="category_id">
                <option></option>
                <?php foreach ($categories as $id => $category): ?>
                    <option <?php if (isset($category_id) && $category_id == $id) echo 'selected="selected"'; ?> value="<?php echo $id; ?>"><?php echo $category; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label ><?php echo __d('contest', 'Featured'); ?></label>
            <?php $array_feature = array(1 => __d('contest', 'yes'), 0 => __d('contest', 'no')) ?>
            <select class="form-control input-medium input-inline" name="featured">
                <option></option>
                <?php foreach ($array_feature as $id => $feature_name): ?>
                    <option <?php if (isset($feature) && $feature == $id) echo 'selected="selected"'; ?> value="<?php echo $id; ?>"><?php echo $feature_name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="btn btn-gray" id="sample_editable_1_new" type="submit">
            <?php echo __d('contest', 'Search'); ?>
        </button>
    </form>

    <?php if (count($contests)): ?>
    <table class="table table-striped table-bordered table-hover" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th ><?php echo $this->Paginator->sort('id', __d('contest', 'ID')); ?></th>
                <th ><?php echo $this->Paginator->sort('type', __d('contest', 'Type')); ?></th>
                <th style="width:500px"><?php echo $this->Paginator->sort('Contest.name', __d('contest', 'Name')); ?></th>
                <th ><?php echo $this->Paginator->sort('Contest.approve_status', __d('contest', 'Approve Status')); ?></th>
                <th ><?php echo $this->Paginator->sort('Contest.contest_status', __d('contest', 'Contest Status')); ?></th>				
                <th><?php echo $this->Paginator->sort('User.name', __d('contest', 'Owner')); ?></th>
                <th><?php echo $this->Paginator->sort('Category.name', __d('contest', 'Category')); ?></th>
                <th style="width:50px"><?php echo $this->Paginator->sort('Contest.auto_approve', __d('contest', 'Auto Approve Entry')); ?></th>
                <th style="width:50px"><?php echo $this->Paginator->sort('Contest.featured', __d('contest', 'Feature')); ?></th>
                <th ><?php echo __d('contest', 'Created date'); ?></th>
                <th><?php echo __d('contest', 'Action'); ?></th>
            </tr>
        </thead>
        <tbody>
                <?php foreach ($contests as $contest): ?>
                    <tr>
                        <td>
                            <?php echo $contest['Contest']['id']; ?>
                        </td>
                        <td>
                            <?php echo ucwords($contest['Contest']['type']); ?>
                        </td>
                        <td>
                            <a href="<?php echo $contest['Contest']['moo_href']; ?>"><?php echo $contest['Contest']['moo_title']; ?></a>
                        </td>
                        <td>
                            <?php
                            $approve_status = array('approved' => __d('contest', 'Approved'),
                                'pending' => __d('contest', 'Pending'),
                                'denied' => __d('contest', 'Denied'));
                            echo $this->Form->select('approve_status', $approve_status, array(
                                'empty' => false,
                                'class' => 'form-control',
                                'data-approve_status' => $contest['Contest']['approve_status'],
                                'value' => $contest['Contest']['approve_status'],
                                'onchange' => 'changeApproveStatus(this, ' . $contest['Contest']['id'] . ')'
                            ));
                            ?>
                        </td>
                        <td>
                            <?php
                            if($contest['Contest']['contest_status'] == 'closed') {
                                
                                $contest_status = array(
                                    'closed' => __d('contest', 'Closed'));
                            }else{
                                
                                $contest_status = array('draft' => __d('contest', 'Draft'),
                                    'published' => __d('contest', 'Published'),
                                    'closed' => __d('contest', 'Closed'));
                            }
                            echo $this->Form->select('contest_status', $contest_status, array(
                                'empty' => false,
                                'class' => 'form-control',
                                'data-contest_status' => $contest['Contest']['contest_status'],
                                'value' => $contest['Contest']['contest_status'],
                                'onchange' => 'changeContestStatus(this, ' . $contest['Contest']['id'] . ')'
                            ));
                            ?>
                        </td>
                        <td>
                            <a href="<?php echo $contest['User']['moo_href']; ?>"><?php echo $contest['User']['name']; ?></a>
                        </td>

                        <td>
                            <?php
                            echo $this->Form->select('category_id', $categories, array(
                                'empty' => false,
                                'class' => 'form-control',
                                'data-category_id' => $contest['Contest']['category_id'],
                                'value' => $contest['Contest']['category_id'],
                                'onchange' => 'changeContestCategory(this, ' . $contest['Contest']['id'] . ')'
                            ));
                            ?>
                        </td>
                        <td>
                            <?php echo (!empty($contest['Contest']['auto_approve'])) ? __d('contest', 'Yes') : __d('contest', 'No'); ?>
                        </td>
                        <td style="width:50px">
                            <?php if ($contest['Contest']['featured']) : ?>
                                <a style="cursor:pointer;" onclick="mooConfirm('<?php echo __d('contest', 'Are you sure you want to un-feature this contest?'); ?>', '<?php echo $this->request->base; ?>/admin/contest/contests/contest_feature/<?php echo $contest["Contest"]["id"] ?>')" ><i class="fa fa-check-square-o " title="<?php echo __d('contest', 'Un-feature') ?>"></i></a>
                            <?php else: ?>
                                <a style="cursor:pointer;" onclick="mooConfirm('<?php echo __d('contest', 'Are you sure you want to set feature this contest?'); ?>', '<?php echo $this->request->base; ?>/admin/contest/contests/contest_feature/<?php echo $contest["Contest"]["id"] ?>')"  ><i class="fa fa-times-circle" title="<?php echo __d('contest', 'Feature') ?>"></i></a>
                            <?php endif; ?>
                        </td>

                        <td><?php echo $this->Moo->getTime($contest['Contest']['created']); ?></td>
                        <td>
                            <a href="javascript:void(0)" class="tip" title="<?php echo __d('contest', 'Delete'); ?>" onclick="mooConfirm('<?php echo __d('contest', 'Are you sure you want to delete this contest?'); ?>', '<?php echo $this->request->base; ?>/admin/contest/contests/delete/<?php echo $contest["Contest"]["id"] ?>')"><i class="icon-trash icon-small"></i></a>				
                        </td>
                    </tr>
                    <?php endforeach ?>
        </tbody>
    </table>
    <div class="pagination">
        <?php echo $this->Paginator->first('First'); ?>&nbsp;
        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('contest', 'Prev')) : ''; ?>&nbsp;
        <?php echo $this->Paginator->numbers(); ?>&nbsp;
        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('contest', 'Next')) : ''; ?>&nbsp;
        <?php echo $this->Paginator->last('Last'); ?>
    </div>
    <?php else: ?>
        <p><?php echo __d('contest', 'No contest found'); ?></p>
    <?php endif; ?>
    
</div>