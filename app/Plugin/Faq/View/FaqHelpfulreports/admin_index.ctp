<?php
$faqHelper = MooCore::getInstance()->getHelper('Faq_Faq');
?>
<?php
echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb(__d('faq', 'Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('faq', 'FAQ'), '/admin/faq/faqs');
$this->Html->addCrumb(__d('faq', 'F.A.Q Reports'), array('controller' => 'faq_helpfulreports', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'FAQ'));
$this->Paginator->options(array('url' => $data_search));
$this->end();
?>
<?php echo $this->Moo->renderMenu('Faq', __d('faq', 'Reports')); ?>

<div id ="select-helpfull-faq-form">
    <div class="form-group">
        <?php $array_helpful = array(1 => __d('faq', 'Helpful'), 0 => __d('faq', 'Not Helpful')) ?>
        <select class="form-control input-medium input-inline" name="vote_id" id ="selectHelpful">
            <?php foreach ($array_helpful as $id => $helpful_name): ?>
            <option <?php if ($id == $type) echo 'selected="selected"'; ?> value="<?php echo $id; ?>"><?php echo $helpful_name; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <form method="post" action="<?php echo $this->base . '/admin/faq/faqs'; ?>">
            <?php echo $this->Form->text('title', array('class' => 'form-control input-medium input-inline', 'placeholder' => __d('faq', 'Search FAQ'))); ?>
            <?php echo $this->Form->submit('', array('style' => 'display:none')); ?>
        </form>
    </div>
</div>

<div id="centerTable">
    <table class="table table-striped table-bordered table-hover" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?php echo __d('faq', 'ID'); ?></th>		
                <th><?php echo __d('faq', 'Name'); ?></th>				
                <th><?php echo __d('faq', 'Questions'); ?></th>
                    <?php if ($type == "0"): ?>
                <th><?php echo __d('faq', 'Reason'); ?></th>
                    <?php endif; ?>
                <th><?php echo __d('faq', 'Date'); ?></th>
                <th><?php echo __d('faq', 'Action'); ?></th>
            </tr>
        </thead>
        <tbody>
                <?php if(!empty($faqsResult)): ?>
                <?php foreach ($faqsResult as $faqRp):?>
            <tr>
                <td>
                    <p><?php echo $faqRp['FaqResult']['id']; ?></p>
                </td>
                <td>
                    <a href="<?php echo $faqRp['User']['moo_href']; ?>"><?php echo $faqRp['User']['name']; ?></a>
                </td>
                <td>
                    <?php $faq = $faqHelper->getFaqById($faqRp['FaqResult']['faq_id'],Configure::read('Config.language')); ?>
                    <a href="<?php echo $this->request->base ?>/admin/faq/faqs/create/<?php echo $faq['Faq']['id'] ?>"><?php echo $faq['Faq']['title']; ?></a>
                </td>
                <?php if ($type == "0"): ?>
                        <td>
                            <?php if($faqRp['FaqResult']['helpfull_id'] == FAQ_REASON_1) echo __d('faq','The answer is incorrect') ?>
                            <?php if($faqRp['FaqResult']['helpfull_id'] == FAQ_REASON_2) echo __d('faq','The answer is confusing') ?>
                            <?php if($faqRp['FaqResult']['helpfull_id'] == FAQ_REASON_3) echo __d('faq','I don\'t like how this works') ?>
                            <?php if($faqRp['FaqResult']['helpfull_id'] == FAQ_REASON_4) echo __d('faq','Other') ?>
                        </td>
                        <?php endif; ?>
                <td><?php echo $this->Moo->getTime($faqRp['FaqResult']['created']); ?></td>
                <td>
                    <a href="<?php echo $this->request->base ?>/admin/faq/faqs/create/<?php echo $faq['Faq']['id'] ?>"><?php echo __d('faq','Edit FAQ') ?></a>
                </td>
            </tr>
                <?php endforeach ?>
                    <?php else: ?>
            <tr>
                <td colspan="<?php echo (6 - intval($type)); ?>"><?php echo __d('faq', 'No report found'); ?></td>
            </tr>
                    <?php endif; ?>
        </tbody>
    </table>


    <div class="pagination">
        <?php echo $this->Paginator->first(__d('faq', 'First')); ?>&nbsp;
        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('faq', 'Prev')) : ''; ?>&nbsp;
        <?php echo $this->Paginator->numbers(); ?>&nbsp;
        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('faq', 'Next')) : ''; ?>&nbsp;
        <?php echo $this->Paginator->last(__d('faq', 'Last')); ?>
    </div>
</div>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).ready(function() {
$('#selectHelpful').on('change', function() {
window.location.href = "<?php echo $this->request->base; ?>/admin/faq/faq_helpfulreports/index/type:"+ this.value;
});
});
<?php $this->Html->scriptEnd(); ?>
