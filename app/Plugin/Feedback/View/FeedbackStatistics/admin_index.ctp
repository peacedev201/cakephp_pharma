<?php
    $this->Html->addCrumb(__d('feedback', 'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('feedback', 'Statuses'), array('controller' => 'feedback_statuses'));
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Feedback'));
    $this->end();
?>
<?php echo $this->Moo->renderMenu('Feedback', __d('feedback', 'Statistics'));?>

<div class="portlet-body">
    <?php echo __d('feedback', 'Below are some valuable statistics for the Feedback submitted on this site');?>
    <form style="width:50%;" class="global_form">
        <div>
            <table width="100%" class="table table-striped table-bordered">
                <tbody>
                <tr>
                    <td width="50%" class="admin_table_bold"><?php echo __d('feedback', 'Total Feedback');?> :</td>
                    <td class="admin_table_bold"><?php echo $totalFeedback;?></td>
                </tr>
                <tr>
                    <td class="admin_table_bold"><?php echo __d('feedback', 'Total Member Feedback');?> :</td>
                    <td class="admin_table_bold"><?php echo $totalMemberFeedback;?></td>
                </tr>
                <tr>
                    <td class="admin_table_bold"><?php echo __d('feedback', 'Total Anonymous Feedback');?> :</td>
                    <td style="font-weight:bold;"><?php echo $totalAnonymous;?></td>
                </tr>
                <tr>
                    <td class="admin_table_bold"><?php echo __d('feedback', 'Total Comments');?> :</td>
                    <td class="admin_table_bold"><?php echo $sumComment;?></td>
                </tr>
                <tr>
                    <td class="admin_table_bold"><?php echo __d('feedback', 'Total Votes');?> :</td>
                    <td class="admin_table_bold"><?php echo $sumVote;?></td>
                </tr>
                </tbody>
            </table>

        </div>
    </form>
</div>
