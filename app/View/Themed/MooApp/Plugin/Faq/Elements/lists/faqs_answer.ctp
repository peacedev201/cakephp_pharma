<?php $uid = MooCore::getInstance()->getViewer(true); ?>
<?php $faqHelper = MooCore::getInstance()->getHelper('Faq_Faq'); ?>
<?php $uid = MooCore::getInstance()->getViewer(true); ?>
<?php $faqHelper = MooCore::getInstance()->getHelper('Faq_Faq'); ?>
<?php if ($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery", "mooFaq"], function($, mooFaq) {
            mooFaq.initCreateFaq(<?php echo $settings ?>);
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooFaq'), 'object' => array('$', 'mooFaq'))); ?>
    mooFaq.initCreateFaq(<?php echo $settings ?>);
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
    <?php if(!isset($response)) $response = true; ?>
<h5><?php echo intval($faq['Faq']['per_usefull']); ?><?php echo __d('faq', '% users marked this FAQ as helpful. Is this FAQ helpful?') ?></h5>
<br>
<?php $total_vote = (intval($faq['Faq']['total_yes']) + intval($faq['Faq']['total_no'])); ?>
<?php if ($total_vote > 0): ?>
    <?php $last_update = $faqHelper->getLastupdateFaq($faq['Faq']['id']); ?>
    <h5> <?php echo __d('faq', 'Last update: %s - Total %s vote', $last_update, $total_vote) ?></h5>
<?php endif; ?>
<?php $choice = $faqHelper->getResultSubmitFaq($faq['Faq']['id']); ?>
<?php if ($uid): ?>
    <a <?php if ($choice['choice'] == 1) echo 'id="btn-choice"' ?> class="btn btn-default anser_faq_helpfull submit_answer_yes" data-id="<?php echo $faq['Faq']['id'] ?>" href="javascript:void(0)"><?php echo __d('faq', 'Yes') ?></a>
    <a <?php if ($choice['choice'] == 0) echo 'id="btn-choice"' ?> href="javascript:void(0)" data-id="<?php echo $faq['Faq']['id'] ?>" class="btn btn-default anser_faq_helpfull js_drop_down_helpful"><?php echo __d('faq', 'No') ?></a>
<?php else: ?>
    <a class="btn btn-action shareFeedBtn"><?php echo __d('faq', 'Yes') ?></a>
    <a href="javascript:void(0)" class="btn btn-action shareFeedBtn"><?php echo __d('faq', 'No') ?></a>
<?php endif; ?>
<?php if ($settings): ?>
    <?php if (isset($response) && $response): ?>
        <div id="flashMessage" class="Metronic-alerts alert alert-success fade in"><?php echo $message ?></div>
    <?php endif; ?>
<?php endif; ?>
        <div class="link_helpful_<?php echo $faq['Faq']['id'] ?>" <?php if (isset($response) && $response): ?> style="display: none;" <?php endif; ?>>
    <form id="answernoForm<?php echo $faq['Faq']['id'] ?>" method="POST">
        <li class="list2 menu-list" id="browse">
            <h5 class="whyNot-faq"><?php echo __d('faq', 'Why not?') ?></h5>
            <ul class="faq-sub-menu" >
                <?php echo $this->Form->hidden('faq_id', array('value' => $faq['Faq']['id'])); ?>
                <li style="list-style: none outside none;">
                    <input type="radio" name="faqhelpful" id="faqhelpful" value="<?php echo FAQ_REASON_1 ?>" <?php if (FAQ_REASON_1 == $choice['choice_id']) echo 'checked="checked"'; ?>> <?php echo __d('faq', 'The answer is incorrect') ?><br>
                </li>
                <li style="list-style: none outside none;">
                    <input type="radio" name="faqhelpful" id="faqhelpful" value="<?php echo FAQ_REASON_2 ?>" <?php if (FAQ_REASON_2 == $choice['choice_id']) echo 'checked="checked"'; ?>> <?php echo __d('faq', 'The answer is confusing') ?><br>
                </li>
                <li style="list-style: none outside none;">
                    <input type="radio" name="faqhelpful" id="faqhelpful" value="<?php echo FAQ_REASON_3 ?>" <?php if (FAQ_REASON_3 == $choice['choice_id']) echo 'checked="checked"'; ?>> <?php echo __d('faq', 'I don\'t like how this works') ?><br>
                </li>
                <li style="list-style: none outside none;">
                    <input type="radio" name="faqhelpful" id="faqhelpful" value="<?php echo FAQ_REASON_4 ?>" <?php if (FAQ_REASON_4 == $choice['choice_id']) echo 'checked="checked"'; ?>> <?php echo __d('faq', 'Other') ?><br>
                </li>
                <?php if ($settings): ?>
                    <?php if (!$response): ?>
                        <div class="error-message error-message-<?php echo $faq['Faq']['id'] ?>"><?php echo $message ?></div>
                    <?php else: ?>
                        <div class="error-message error-message-<?php echo $faq['Faq']['id'] ?>" style="display:none;"></div>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </li>  
        <a href="javascript:void(0)" data-id="<?php echo $faq['Faq']['id'] ?>" class="btn btn-default anser_faq_helpfull answerno-faq-list"><?php echo __d('faq', 'Submit') ?></a>

    </form>

</div>
