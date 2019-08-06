<?php if (isset($item) && !empty($item)):
    $creditHelper = MooCore::getInstance()->getHelper('Credit_Credit');
?>
<?php if(Configure::read("Credit.member_can_send_credit") == 'send_friend_only'): ?>

    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('mooCredit'), 'object' => array('mooCredit'))); ?>
        mooCredit.initCreditSendToFriend();
    <?php $this->Html->scriptEnd(); ?>

<?php else: ?>

    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('mooCredit'), 'object' => array('mooCredit'))); ?>
        mooCredit.initCreditSendToMember();
    <?php $this->Html->scriptEnd(); ?>

<?php endif; ?>

    <?php
    if(empty($title)) $title = __d('credit', 'Credit Rank');
    if(isset($title_enable)&&($title_enable)=== "") $title_enable = false; else $title_enable = true;
    ?>
    <div class="box2 send_credit">
        <?php if($title_enable): ?>
            <h3><?php echo $title; ?></h3>
        <?php endif; ?>
        <div class="box_content credit_badge" style="width: 100%;display: inline-block;">
            <div class="ranks-image">
                <img width="60" src="<?php echo $creditHelper->getImageRank($now_rank, array('prefix' => '150_square'))?>" id="item-avatar" class="img_wrapper">
            </div>
            <div class="badge-info">
                <div class="badge-rank">
                    <div class="badge-rank-now" style="width: <?php echo $width_rank;?>%;">
                        <?php echo round($item['CreditBalances']['current_credit'],2);?>
                    </div>
                </div>
                <?php if(!empty($next_rank)):?>
                <div class="badge-rank-next">
                        <?php echo round($next_rank['CreditRanks']['credit'],2);?>
                </div>
                <?php endif;?>
            </div>
            <?php if(!empty($next_rank)):?>
            <div class="badge-info-next-rank">
                <?php echo __d('credit','Next Rank');?> :
                <a class="json-view" data-url="<?php echo $this->request->base?>/credits/index/rank" href="<?php echo $this->request->base?>/credits/index/rank"><?php echo htmlspecialchars($next_rank['CreditRanks']['name']);?></a>
            </div>
            <?php endif;?>
        </div>
    </div>
<?php endif; ?>
