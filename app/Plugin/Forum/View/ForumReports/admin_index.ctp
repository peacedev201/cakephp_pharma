<?php
    echo $this->Html->css(array(
        'jquery-ui', 
        'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array(
        'jquery-ui', 
        'footable'));
    $this->Html->addCrumb(__d('forum',  'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('forum',  'Reports Manager'), array(
        'controller' => 'forum_reports',
        'action' => 'admin_index'));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Stores'));
    $this->end();
?>
<?php echo$this->Moo->renderMenu('Forum', __d('forum', 'Reports'));?>
<div id="page-wrapper">
	<div class="panel panel-default">
        <div class="panel-heading">
            <?php echo __d('forum', "Manage Reports");?>
            <div class="pull-right">
                <div class="btn-group">
                    <button aria-expanded="false" type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown">
                        <?php echo __d('forum', "Actions");?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li>
                            <a title="<?php echo __d('forum', "Delete");?>" href="javascript:void(0)" onclick="deleteAll()">
                                <?php echo __d('forum', "Delete");?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="panel-body">
            <?php if(!empty($reports)):?>
                <form class="form-horizontal" id="adminForm" method="post" action="<?php echo $this->request->base?>/admin/forum/forum_reports/delete">
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="sample_1">
                        <thead>
                            <tr>
                                <th style="width: 7px">
                                    <?php echo $this->Form->checkbox('', array(
                                        'hiddenField' => false,
                                        'div' => false,
                                        'label' => false,
                                        'onclick' => 'toggleCheckboxes(this)'
                                    ));?>
                                </th>
                                <th>
                                    <?php echo $this->Paginator->sort('name', __d('forum',  'Name')); ?>
                                </th>
                                <th style="width: 25%">
                                    <?php echo __d('forum',  'Reason'); ?>
                                </th>
                                <th style="width: 12%">
                                    <?php echo $this->Paginator->sort('User.name', __d('forum',  'Reporter')); ?>
                                </th>
                                <th style="width: 12%">
                                    <?php echo $this->Paginator->sort('created', __d('forum',  'Create Date')); ?>
                                </th>
                                <th style="width: 12%"></th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php
                            $m_topic = MooCore::getInstance()->getModel('Forum.ForumTopic');
                            $count = 0;
                            foreach ($reports as $report):
                                $user = $report['User'];
                                $topic = $report['ForumTopic'];
                                $report = $report['ForumReport'];
                                if($topic['parent_id']){
                                    $parent = $m_topic->findById($topic['parent_id']);
                                }else{
                                    $parent = array();
                                }
                        ?>
                            <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                                <td style="text-align: center">
                                    <input type="checkbox" value="<?php echo $report['id']?>" class="multi_cb" id="cb<?php echo $report['id']?>" name="reports[]">
                                </td>
                                <td>
                                    <?php if(!empty($parent)):?>
                                        <?php echo __d('forum', 'In reply to').': '. $this->Text->truncate($parent['ForumTopic']['moo_title'], 100, array('eclipse' => '...')) ;?>
                                    <?php else:?>
                                        <?php echo __d('forum', 'Topic').': '. $this->Text->truncate($topic['moo_title'], 100, array('eclipse' => '...')) ;?>
                                    <?php endif;?>
                                </td>
                                <td>
                                    <?php echo $report['reason']?>
                                </td>
                                <td>
                                    <a href="<?php echo $this->request->base?>/admin/users/edit/<?php echo $user['id']?>">
                                        <?php echo h($user['name'])?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo $this->Time->niceShort($report['created'])?>
                                </td>
                                <td>
                                    <?php if(!empty($parent)):?>
                                        <a href="<?php echo $parent['ForumTopic']['moo_href'].'/reply_id:'.$topic['id'];?>" target="_blank"><?php echo __d('forum', "View");?></a>
                                    <?php else:?>
                                        <a href="<?php echo $topic['moo_href'];?>" target="_blank"><?php echo __d('forum', "View");?></a>
                                    <?php endif;?>
                                    &#124;
                                    <a href="javascript:void(0)" onclick="deleteReport('<?php echo $report['id'];?>')">
                                        <?php echo __d('forum', "Delete");?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach ?>

                        </tbody>
                    </table>
                    </div>
                </form>
				<div class="row">
                    <div class="col-sm-12">
                        <div id="dataTables-example_paginate" class="dataTables_paginate paging_simple_numbers">
                            <ul class="pagination">
                                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('forum', 'First'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('forum', 'Previous'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                                <?php echo $this->Paginator->numbers(array('class' => 'paginate_button', 'tag' => 'li', 'separator' => '')); ?>
                                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('forum', 'Next'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('forum', 'Last'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                            </ul>
                        </div>
                    </div>
                </div>
				
            <?php else:?>
                <?php echo __d('forum', "No Reports");?>
            <?php endif;?>
        </div>
    </div>
</div>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
    function toggleCheckboxes (obj){
        if ($(obj).is(':checked'))
        {
            $('.multi_cb').attr('checked', 'checked');
            $('.multi_cb').parent('span').addClass('checked');
        }
        else
        {
            $('.multi_cb').attr('checked', false);
            $('.multi_cb').parent('span').removeClass('checked');
        }
    }

    function deleteAll()
    {
        if($('input.multi_cb:checked').length < 1)
        {
            mooAlert(mooPhrase.__('you_must_select_at_least_an_item'));
        }
        else if(confirm(mooPhrase.__('are_you_sure_you_want_to_delete')))
        {
            $('#adminForm').submit();
        }
    }

    function deleteReport(id){
        if(confirm(mooPhrase.__('are_you_sure_you_want_to_delete'))){
            $('#cb'+id).attr('checked', 'checked');
            $('#cb'+id).parent('span').addClass('checked');
            $('#adminForm').submit();
        }
    }
<?php $this->Html->scriptEnd(); ?>
