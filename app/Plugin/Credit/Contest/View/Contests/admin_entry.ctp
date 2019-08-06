<?php
echo $this->Html->script(array('admin/layout/scripts/compare.js?' . Configure::read('core.version')), array('inline' => false));
echo $this->Html->css(array('jquery-ui', 'Contest.admin', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb(__d('contest', 'Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('contest', 'Entries Manager'), '/admin/contest/contests/entry');

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Contests"));
$this->end();

$this->Paginator->options(array('url' => $passedArgs));
$helper = MooCore::getInstance()->getHelper('Contest_Contest');
?>
<?php echo $this->Moo->renderMenu('Contest', __d('contest', 'Entries Manager')); ?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).ready(function () {
$('.footable').footable();
});
function changeEntryStatus(item, id)
{
if (confirm('<?php echo __d('contest', 'Are you sure?'); ?>')){
window.location = mooConfig.url.base + '/admin/contest/contests/contest_entry_status/' + jQuery(item).val() + '/' + id;
}else{
    jQuery(item).val(jQuery(item).data('entry_status'));
}
}
<?php $this->Html->scriptEnd(); ?>
<div id="center" class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-3">
                <div class="btn-group">
                    <button class="btn btn-gray" id="sample_editable_1_new" onclick="confirmSubmitForm('<?php echo __d('contest', 'Are you sure you want to delete selected entries') ?>', 'deleteForm')">
                        <?php echo __('Delete'); ?>
                    </button>
                </div>
            </div>
            <form style="padding: 14px;"  method="post" action="<?php echo $this->base . '/admin/contest/contests/entry'; ?>" class="form-inline">
                <div class="form-group">
                    <label><?php echo __d('contest', 'Contest'); ?></label>
                    <input class="form-control input-medium input-inline" value="<?php if (isset($name)) echo $name; ?>" type="text" name="name">
                </div>
                <div class="form-group">
                    <label><?php echo __d('contest', 'Status'); ?></label>
                    <select class="form-control input-medium input-inline" name="entry_status">
                        <option></option>
                        <?php foreach ($entry_status_select as $id => $tmp): ?>
                            <option <?php if (isset($entry_status) && $entry_status == $id) echo 'selected="selected"'; ?> value="<?php echo $id; ?>"><?php echo $tmp; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-gray" id="sample_editable_1_new" type="submit">
                    <?php echo __d('contest', 'Search'); ?>
                </button>
            </form>
        </div>
    </div>
    <form method="post" action="<?php echo $this->request->base ?>/admin/contest/contests/mul_delete" id="deleteForm">
        <table class="table table-striped table-bordered table-hover" cellpadding="0" cellspacing="0">
            <thead>
                <tr><?php if ($cuser['Role']['is_super']): ?>
                        <th width="30"><input type="checkbox" onclick="toggleCheckboxes2(this)"></th>
                    <?php endif; ?>
                    <th><?php echo $this->Paginator->sort('ContestEntry.id', __d('contest', 'ID')); ?></th>
                    <th style="width:75px"><?php echo __d('contest', 'Entry'); ?></th>				
                    <th><?php echo $this->Paginator->sort('Contest.name', __d('contest', 'Owner')); ?></th>
                    <th style="width:500px;"><?php echo $this->Paginator->sort('User.name', __d('contest', 'Contest Name')); ?></th>
                    <th><?php echo $this->Paginator->sort('ContestEntry.entry_status', __d('contest', 'Status')); ?></th>
                    <th><?php echo $this->Paginator->sort('ContestEntry.contest_vote_count', __d('contest', 'Vote Count')); ?></th>
                    <th ><?php echo __d('contest', 'Created date'); ?></th>
                    <th><?php echo __d('contest', 'Action'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($entries)): ?>
                    <?php $count = 0;
                    foreach ($entries as $entry):
                        ?>
                        <tr class="gradeX <?php ( ++$count % 2 ? "odd" : "even") ?>">
                            <?php if ($cuser['Role']['is_super']): ?>
                                <td><input type="checkbox" name="contest_entries[]" value="<?php echo $entry['ContestEntry']['id'] ?>" class="check"></td>
        <?php endif; ?>
                            <td><?php echo $entry['ContestEntry']['id'] ?></td>
                            <td>
                                <a class="entry_image entry_item_admin"  data-file="<?php echo $helper->getEntryImage($entry); ?>" style="background-image: url(<?php echo $helper->getEntryImage($entry, array('prefix' => '75_square')); ?>)">
                                </a>
                            </td>					
                            <td>
                                <a href="<?php echo $entry['User']['moo_href']; ?>"><?php echo $entry['User']['name']; ?></a>
                            </td>
                            <td>
                                <a href="<?php echo $entry['Contest']['moo_href']; ?>"><?php echo $entry['Contest']['name']; ?></a>
                            </td>
                            <td>
                                <?php if ($entry['Contest']['contest_status'] == 'closed'): ?>
                                     <?php
                                        echo $this->Form->select('entry_status', $entry_status_select, array(
                                            'empty' => false,
                                            'disabled' => 'disabled',
                                            'class' => 'form-control',
                                            'value' => $entry['ContestEntry']['entry_status'],
                                            'onchange' => 'changeEntryStatus(this, ' . $entry['ContestEntry']['id'] . ')'
                                        ));
                                    ?>
                                <?php else: ?>
                                    <?php
                                        $status = $entry_status_select;
                                        if($entry['ContestEntry']['entry_status'] == 'win') {
                                            unset($status['pending']);
                                            unset($status['published']);
                                        }
                                        if($entry['ContestEntry']['entry_status'] == 'published') {
                                            unset($status['pending']);
                                        }
                                        if($entry['ContestEntry']['entry_status'] == 'pending') {
                                            unset($status['win']);
                                        }
                                    ?>
                                    <?php
                                    echo $this->Form->select('entry_status', $status, array(
                                        'empty' => false,
                                        'class' => 'form-control',
                                        'data-entry_status' => $entry['ContestEntry']['entry_status'],
                                        'value' => $entry['ContestEntry']['entry_status'],
                                        'onchange' => 'changeEntryStatus(this, ' . $entry['ContestEntry']['id'] . ')'
                                    ));
                                    ?>
        <?php endif; ?>
                            </td>
                            <td><?php echo $entry['ContestEntry']['contest_vote_count']; ?></td>
                            <td><?php echo $this->Moo->getTime($entry['ContestEntry']['created']); ?></td>
                            <td>	
                                <a href="javascript:void(0)" class="tip" title="<?php echo __d('contest', 'Delete'); ?>" onclick="mooConfirm('<?php echo __d('contest', 'Are you sure you want to delete this entry?'); ?>', '<?php echo $this->request->base; ?>/admin/contest/contests/delete_entry/<?php echo $entry["ContestEntry"]["id"] ?>')"><i class="icon-trash icon-small"></i></a>				
                            </td>
                        </tr>
                    <?php endforeach ?>
<?php else: ?>
                    <tr>
                        <td colspan="9">
    <?php echo __d('contest', 'No entries found'); ?>
                        </td>
                    </tr>
<?php endif; ?>
            </tbody>
        </table>
    </form>
    <div class="pagination">
        <?php echo $this->Paginator->first('First'); ?>&nbsp;
        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('contest', 'Prev')) : ''; ?>&nbsp;
        <?php echo $this->Paginator->numbers(); ?>&nbsp;
        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('contest', 'Next')) : ''; ?>&nbsp;
<?php echo $this->Paginator->last('Last'); ?>
    </div>
</div>