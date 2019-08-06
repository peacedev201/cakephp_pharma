<?php
$faqHelper = MooCore::getInstance()->getHelper('Faq_Faq');
?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooFaq'), 'object' => array('$', 'mooFaq'))); ?>
mooFaq.initCreateFaq();
<?php $this->Html->scriptEnd(); ?>

<div id="list-content">
    <div class="bar-content full_content">
        <div class="content_center">
            <ul class="faq-breadcrumb">
                <li style="list-style: none outside none;">
                    <?php $count_breadcrumb = 1 ?>
                    <?php foreach ($breadcrum as $brc): ?>
                        <a href="<?php echo($this->request->base . $brc['link']); ?>"><?php echo $brc['name']; ?></a>
                        <?php if ($count_breadcrumb != count($breadcrum)): ?>
                            <i class="glyphicon glyphicon-chevron-right" style="color: rgba(0, 0, 0, 0.54)"></i>
                        <?php endif; ?>
                        <?php $count_breadcrumb++; ?>
                    <?php endforeach; ?>
                </li>
            </ul>
            <div class="post_body">	
                <ul class="faq-view-detail">
                    <h1 class="faq-detail-title"><?php echo h($faq['Faq']['title']) ?></h1>
                    <!--<li class="full_content faq_list" style="list-style: none outside none;">-->
                    <div class="full_content p_m_10" id="faq-content">
                        <?php echo $faq['Faq']['body']; ?>		
                    </div>

                    <a href="javascript:void(0);" share-url="<?php
                    echo $this->Html->url(array(
                        'plugin' => FALSE,
                        'controller' => 'share',
                        'action' => 'ajax_share',
                        'Faq_Faq',
                        'id' => $faq['Faq']['id'],
                        'type' => 'faq_item_detail'
                            ), true);
                    ?>" class="shareFeedBtn"><i class="icon-share"></i> <?php echo __d('faq', 'Share'); ?></a>


                    <div class="faq-userfull-content">
                        <h5><?php echo intval($faq['Faq']['per_usefull']); ?><?php echo __d('faq', '% users marked this FAQ as helpful. Is this FAQ helpful?') ?></h5>
                        <br>
                        <?php if ($total_vote > 0): ?>
                            <h5> <?php echo __d('faq', 'Last update: %s - Total %s vote', $last_update, $total_vote) ?></h5>
                        <?php endif; ?>
                        <?php if ($uid): ?>
                            <a <?php if ($choice == 1) echo 'id="btn-choice"' ?> class="btn btn-action"href="<?php echo $this->request->base ?>/faq/faq_helpfulreports/answer/<?php echo $faq['Faq']['id'] ?>"><?php echo __d('faq', 'Yes') ?></a>
                            <a <?php if ($choice == 0) echo 'id="btn-choice"' ?> href="javascript:void(0)" data-id="<?php echo $faq['Faq']['id'] ?>" class="btn btn-action js_drop_down_helpful"><?php echo __d('faq', 'No') ?></a>
                        <?php else: ?>
                            <a class="btn btn-action shareFeedBtn"><?php echo __d('faq', 'Yes') ?></a>
                            <a href="javascript:void(0)" class="btn btn-action shareFeedBtn"><?php echo __d('faq', 'No') ?></a>
                        <?php endif; ?>
                        <?php if ($is_submit): ?>
                            <div id="flashMessage" class="Metronic-alerts alert alert-success fade in"><?php echo __d('faq', 'Thanks for your feedback!') ?></div>
                        <?php endif; ?>
                        <div class="link_helpful_<?php echo $faq['Faq']['id'] ?>" style="display: none;">
                            <form id="answernoForm" method="POST">
                                <li class="list2 menu-list" id="browse">
                                    <h5 class="whyNot-faq"><?php echo __d('faq', 'Why not?') ?></h5>
                                    <ul class="sub-menu" >
                                        <?php echo $this->Form->hidden('faq_id', array('value' => $faq['Faq']['id'])); ?>
                                        <li style="list-style: none outside none;">
                                            <input type="radio" name="faqhelpful" value="<?php echo FAQ_REASON_1 ?>" <?php if (FAQ_REASON_1 == $choice_id) echo 'checked="checked"'; ?>> <?php echo __d('faq', 'The answer is incorrect') ?><br>
                                        </li>
                                        <li style="list-style: none outside none;">
                                            <input type="radio" name="faqhelpful" value="<?php echo FAQ_REASON_2 ?>" <?php if (FAQ_REASON_2 == $choice_id) echo 'checked="checked"'; ?>> <?php echo __d('faq', 'The answer is confusing') ?><br>
                                        </li>
                                        <li style="list-style: none outside none;">
                                            <input type="radio" name="faqhelpful" value="<?php echo FAQ_REASON_3 ?>" <?php if (FAQ_REASON_3 == $choice_id) echo 'checked="checked"'; ?>> <?php echo __d('faq', 'I don\'t like how this works') ?><br>
                                        </li>
                                        <li style="list-style: none outside none;">
                                            <input type="radio" name="faqhelpful" value="<?php echo FAQ_REASON_4 ?>" <?php if (FAQ_REASON_4 == $choice_id) echo 'checked="checked"'; ?>> <?php echo __d('faq', 'Other') ?><br>
                                        </li>
                                        <div class="error-message" style="display:none;"></div>
                                    </ul>
                                </li>  
                                <a href="javascript:void(0)" class="btn btn-action answerno"><?php echo __d('faq', 'Submit') ?></a>
                            </form>
                        </div>
                    </div>
                    <!--</li>-->
                </ul>
            </div>
        </div>
    </div>
    <?php if ($faq['Faq']['alow_comment']) : ?>
        <div class="bar-content full_content faq-comment">
            <?php echo $this->renderComment(); ?>
        </div>
    <?php endif; ?>
</div>