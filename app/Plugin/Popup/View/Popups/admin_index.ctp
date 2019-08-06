<?php
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->Html->addCrumb(__('Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('popup','Popups Manager'), array('controller' => 'popups', 'action' => 'admin_index'));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Popup'));
    $this->end();
?>
<?php echo$this->Moo->renderMenu('Popup', __('General'));?>
<div class="portlet-body">
    <div class="table-toolbar">

        <div class="row">
            <div class="col-md-6">
                <div class="btn-group">
                    <button class="btn btn-gray" id="sample_editable_1_new" onclick="confirmSubmitForm('<?php echo __d('popup','Are you sure you want to delete these popups')?>', 'deleteForm')">
                        <?php echo  __d('popup','Delete');?>
                    </button>
                </div>
                <div class="btn-group">
                    <a class="btn btn-gray"  href="<?php echo $this->request->base?>/admin/popups_for_page/popups/create">
                        <?php echo __d('popup','Create New Popup')?>
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div id="sample_1_filter" class="dataTables_filter"><label>
                        <form method="post" action="<?php echo $this->request->base?>/admin/popups_for_page/popups">
                            <?php echo $this->Form->text('keyword', array('class' => 'form-control input-medium input-inline', 'placeholder' => __d('popup','Search by title')));?>
                            <?php echo $this->Form->submit('', array( 'style' => 'display:none' ));?>
                        </form>
                    </label></div>
            </div>
        </div>
    </div>
    <form method="post" action="<?php echo $this->request->base?>/admin/popups_for_page/popups/delete" id="deleteForm">
        <table class="table table-striped table-bordered table-hover" id="sample_1">
            <thead>
            <tr class="tbl_head">
                <?php if ($cuser['Role']['is_super']): ?>
                    <th width="30"><input type="checkbox" onclick="toggleCheckboxes2(this)"></th>
                <?php endif; ?>
                <th width="50px"><?php echo $this->Paginator->sort('Popup.id', __('ID')); ?></th>
                <th><?php echo $this->Paginator->sort('Popup.title', __d('popup','Title')); ?></th>
                <th><?php echo $this->Paginator->sort('Page.title', __d('popup','Appear At')); ?></th>
                <th><?php echo $this->Paginator->sort('Popup.enable', __d('popup','Enable')); ?></th>
            </tr>
            </thead>
            <tbody>

            <?php $count = 0;
            foreach ($popups as $popup): ?>
                <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>" id="<?php echo $popup['Popup']['id']?>">
                    <?php if ( $cuser['Role']['is_super'] ): ?>
                        <td><input type="checkbox" name="popups[]" value="<?php echo $popup['Popup']['id']?>" class="check"></td>
                    <?php endif; ?>
                    <td width="50px"><?php echo $popup['Popup']['id']?></td>
                    <td class="reorder"><a href="<?php echo $this->request->base?>/admin/popups_for_page/popups/create/<?php echo $popup['Popup']['id']?>"><?php echo $popup['Popup']['title']?></a></td>
                    <td class="reorder"><?php if($popup['Popup']['page_id'] == 0) echo "All Page"; else echo $popup['Page']['title']?></td>
                    <td class="reorder">
                        <?php
                        if($popup['Popup']['enable'] == '1') {
                            echo __d('popup','Yes');
                        }
                        else {
                            echo __d('popup','No');
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach ?>

            </tbody>
        </table>
    </form>
    <div class="pagination pull-right">
        <?php echo $this->Paginator->prev('« '.__d('popup','Previous'), null, null, array('class' => 'disabled')); ?>
        <?php echo $this->Paginator->numbers(); ?>
        <?php echo $this->Paginator->next(__d('popup','Next').' »', null, null, array('class' => 'disabled')); ?>
    </div>
</div>
