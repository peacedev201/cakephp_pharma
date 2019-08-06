<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooCredit'), 'object' => array('$', 'mooCredit'))); ?>

<?php $this->Html->scriptEnd(); ?>
<?php $this->setNotEmpty('west'); ?>
<?php $this->start('west'); ?>

<div class="box2 filter_block">
    <h3 class="visible-xs visible-sm"><?php echo __d('credit', 'Browse') ?></h3>
    <div class="box_content">
        <ul class="list2 menu-list">
            <li class="<?php echo (isset($active_menu_top_members)) ? $active_menu_top_members : ""; ?> "
                id="browse_all"><a
                    href="<?php echo $this->request->base ?>/credits"><?php echo __d('credit', 'Top Members') ?></a>
            </li>
            <?php if (!empty($uid)): ?>
                <li class="<?php echo (isset($active_menu_my_credits)) ? $active_menu_my_credits : ""; ?> "><a
                        href="<?php echo $this->request->base ?>/credits/index/my_credits"><?php echo __d('credit', 'My Transactions') ?></a>
                </li>
                <?php
                    if(ENABLE_WITHDRAW == true){
                ?>
                <li class="<?php echo (isset($active_menu_my_withdraw_request)) ? $active_menu_my_withdraw_request : ""; ?> "><a
                        href="<?php echo $this->request->base ?>/credits/index/my_withdraw_request"><?php echo __d('credit', 'My withdraw request') ?></a>
                </li>
                <?php } ?>
            <?php endif; ?>
            <li class="<?php echo (isset($active_menu_rank)) ? $active_menu_rank : ""; ?> "><a href="<?php echo $this->request->base ?>/credits/index/rank"><?php echo __d('credit', 'Credits Rank') ?></a>
            </li>
            <li class="<?php echo (isset($active_menu_faqs)) ? $active_menu_faqs : ""; ?>"><a
                    href="<?php echo $this->request->base ?>/credits/index/faqs"><?php echo __d('credit', 'FAQs') ?></a>
            </li>
            <li class="<?php echo (isset($active_menu_action_type)) ? $active_menu_action_type : ""; ?>"><a
                    href="<?php echo $this->request->base ?>/credits/index/action"><?php echo __d('credit', 'Action types and credits') ?></a>
            </li>
        </ul>
    </div>
</div>

<?php $this->end(); ?>
<?php $this->setNotEmpty('east'); ?>

<div class="bar-content">
    <div class="content_center">
        <div <?php if($type != "faqs"):?>id="list-content"<?php endif;?>>
            <div class="mo_breadcrumb">
                <h1><?php echo $title; ?></h1>
            </div>
            <?php
            switch ($type){
                case 'my_credits':
                    echo $this->element('list/my_credits', array('items' => $items, 'more_url' => '/credits/more_my_credits/page:2'));
                    break;
                case 'faqs':
                    echo $this->element('list/faqs');
                    break;
                case 'rank':
                    echo $this->element( 'list/credit_ranks', array( 'more_url' => '/credit/ranks/browse/page:2' ) );
                    break;
                case 'action':
                    echo $this->element( 'list/credit_actions');
                    break;
                case 'withdraw_request':
                    echo $this->element( 'withdraw_request');
                    break;
                case 'my_withdraw_request':
                    echo $this->element( 'list/my_withdraw_request');
                    break;
                default:
                    echo $this->element('list/top_members', array('more_url' => '/credits/browse/page:2/count:' . $count));
            }
            ?>
        </div>
    </div>
</div>

