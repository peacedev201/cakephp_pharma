<?php

if ($uid): ?>
    <?php
    if(empty($title)) $title = __d('credit', 'Credit Statistics');
    if(isset($title_enable)&&($title_enable)=== "") $title_enable = false; else $title_enable = true;
    ?>
<div class="box2 send_credit">
        <?php if($title_enable): ?>
    <h3><?php echo $title; ?></h3>
        <?php endif; ?>
    <div class="your-rank-content box_content">
        <div class="ranking-content">
        </div>
        <div class="rank">
            <p style='font-size: 38px; text-align: center; font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;'>
                <b>
                    <?php echo (isset($item['CreditBalances']['current_credit'])) ? round($item['CreditBalances']['current_credit'],2) : "0";?>
                </b>
            </p>
        </div>
        <div class="rank-info">
            <div>
                <i class="current-balance"></i>
                <p>   <?php echo __d('credit','Current Balance');?> <em><?php echo (isset($item['CreditBalances']['current_credit'])) ? round($item['CreditBalances']['current_credit'],2) : "0";?></em>  </p>
            </div>
            <div>
                <i class="total-earn-credit"></i>
                <p><?php echo __d('credit','Total Earned Credits');?> <em><?php echo (isset($item['CreditBalances']['earned_credit'])) ? round($item['CreditBalances']['earned_credit'],2) : "0";?></em></p>
            </div>
            <div>
                <i class="total-spent-credit"></i>
                <p><?php echo __d('credit','Total Spent Credits');?> <em><?php echo (isset($item['CreditBalances']['spent_credit'])) ? round($item['CreditBalances']['spent_credit'],2) : "0";?></em></p>
            </div>
            <?php if ($subject_type != 'User' || $uid == MooCore::getInstance()->getViewer(true)): ?>
            <div style="text-align: right;width: 100%;padding-top: 25px;">
                <a href="<?php echo $this->request->base ?>/credits/index/my_credits" class="btn btn-action"><?php echo __d('credit', 'My Transactions') ?></a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>
