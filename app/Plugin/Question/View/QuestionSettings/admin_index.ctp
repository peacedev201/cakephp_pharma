<?php
__d('question','Create/Edit Question');
__d('question','View Question');
__d('question','Enable question');
__d('question','Limit number of tags per question');
__d('question','File type allowed');
__d('question','Item per page');
__d('question','Enable hashtag for question');
__d('question','Auto Approval When A Question Added');
__d('question','Limit number of answers that a user can post per question');
__d('question','Number point receive when user vote for an answer');
__d('question','Number point receive when user vote for a question');
__d('question','Number point receive when their answer has been marked as best answer');
__d('question','Number point receive when user create a new question');
__d('question','Number point receive when user create a new answer');
__d('question','By pass force login');

echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb(__d('question','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('question', 'Question'), '/admin/question/questions');
$this->Html->addCrumb(__d('question','Question Settings'), array('controller' => 'question_settings', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Question"));
$this->end();
?>

<div class="portlet-body form">
    <div class=" portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <?php echo $this->Moo->renderMenu('Question', __d('question','Settings'));?>
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-12">
                    <div class="tab-content">
                        <div class="tab-pane active" id="portlet_tab1">
                            <?php echo $this->element('admin/setting');?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>