<?php

$this->Html->addCrumb(__d('usernotes', 'Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('usernotes', 'Note Manager'), array('controller' => 'usernotess'));
echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable', 'Usernotes.admin','vendor/jquery.autogrow-textarea.min'), array('inline' => false));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Usernotes'));
$this->end();
?>

<?php echo$this->Moo->renderMenu('Usernotes', __d('usernotes', 'Notes manager')); ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
    jQuery(document).ready(function(){
    jQuery('body').on('click', '#btnUsernoteSave', function(){
    jQuery.admin.saveAdminNote();
    });
    });
    function noteconfirmSubmitForm (msg, form_id){
    jQuery.fn.SimpleModal({
    btn_ok : "<?php echo __d('usernotes','Ok') ?>",
            btn_cancel: "<?php echo __d('usernotes','Cancel') ?>",
            model: 'confirm',
            callback: function(){
            document.getElementById(form_id).submit();
            },
            title: "<?php echo __d('usernotes','Please Confirm') ?>",
            contents: msg,
            hideFooter: false,
            closeButton: false
    }).showModal();
    };
<?php echo $this->Html->scriptEnd(); ?>
    <style type = "text/css" >
                .pagination > li.current.paginate_button,
                .pagination > li.disabled {
                position: relative;
                float: left;
                padding: 6px 12px;
                margin-left: -1px;
                line-height: 1.42857143;
                color: #428bca;
                text-decoration: none;
                background-color: #eee;
                border: 1px solid #ddd;
                }
            </style>

<?php
        __d('usernotes','Save Settings');
        __d('usernotes','Home');
        __d('usernotes','Can write a note');
        __d('usernotes','Usernotes');
?>
<div class="portlet-body">
    <div class="table-toolbar">
        <form method="post" action="<?php echo $this->request->base ?>/admin/usernotes/usernotess">
            <div class="row">
                <div class="col-md-6">
                    <div class="btn-group">
                        <button type="button"  onclick="confirmSubmitForm('<?php echo __d('usernotes', 'Are you sure you want to delete?'); ?>', 'adminForm')" id="sample_editable_1_new" class="btn btn-gray">
<?php echo __d('usernotes', 'Delete'); ?>
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="sample_1_filter" class="dataTables_filter">
                        <label>
                            <?php echo $this->Form->text('keyword', array('class' => 'form-control', 'value' => $keyword, 'placeholder' => __d('usernotes', 'Search by keyword'))); ?>
                        </label>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <form class="form-horizontal" id="adminForm" method="post" action="<?php echo $this->request->base ?>/admin/usernotes/usernotess/delete_note">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="sample_1">
                <thead>
                <th style="width: 7px">
                    <?php
                    echo $this->Form->checkbox('', array(
                        'hiddenField' => false,
                        'div' => false,
                        'label' => false,
                        'onclick' => 'toggleCheckboxes2(this)'
                    ));
                    ?>
                </th>
                <th>
                    <?php echo $this->Paginator->sort('id', __d('usernotes', 'ID')); ?>
                </th>
                <th>
                    <?php echo $this->Paginator->sort('name', __d('usernotes', 'Name')); ?>
                </th>
                <th>
                    <?php echo $this->Paginator->sort('email', __d('usernotes', 'Email')); ?>
                </th>
                <th>
                    <?php echo __d('usernotes', 'Note'); ?>
                </th>
                <th >
                    <?php echo $this->Paginator->sort('email', __d('usernotes', 'Created Date')); ?>
                </th>
                <th>
                <?php echo __d('usernotes', 'Options') ?>
                </th>
                </thead>
                <?php if (!empty($aUsers)): ?>
                    <?php $count = 0; ?>
                    <?php
                    foreach ($aUsers as $user):
                        $note = $user['Usernote'];
                        $user = $user['User'];
                        ?>
                <tr  class="gradeX <?php ( ++$count % 2 ? "odd" : "even") ?>">
                    <td style="text-align: center">
                        <input type="checkbox" value="<?php echo $note['id'] ?>" class="check" id="cb<?php echo $note['id'] ?>" name="data[cid][]">
                    </td>
                    <td><?php echo $user['id']; ?></td>
                    <td><a target="_blank" href="<?php echo $this->request->base .'/users/view/'.$user['id']; ?>"><?php echo $user['name']; ?></a></td>
                    <td><?php echo $user['email']; ?></td>
                    <td style="width: 45%;"><div style="word-break: break-all;max-height: 300px;overflow-y: auto;width: 100%;" ><?php echo str_replace("\n", "<br>", $note['content']); ?></div></td>
                    <td><?php echo $note['created_date']; ?></td>
                    <td><a href="#" onclick="jQuery.admin.leaveNote(<?php echo!empty($user['id']) ? $user['id'] : 0; ?>); return false;" ><?php echo __d('usernotes', 'Leave note') ?></a>  
                        <!--<a href="#" onclick="jQuery.admin.deleteNote(<?php echo!empty($user['id']) ? $user['id'] : 0; ?>);return false;" ><?php echo __d('usernotes', 'Delete') ?></a></td>-->
                </tr>        
    <?php endforeach; ?>
<?php endif; ?>
            </table>
        </div>
    </form>
</div>

<div class="row">

    <div class="col-sm-12 text-right">
	<div class="pagination">
        <?php echo $this->Paginator->prev('« '.__d('usernotes','Previous'), null, null, array('class' => 'disabled')); ?>
		<?php echo $this->Paginator->numbers(); ?>
		<?php echo $this->Paginator->next(__d('usernotes','Next').' »', null, null, array('class' => 'disabled')); ?> 
    </div>
    </div>
</div>
