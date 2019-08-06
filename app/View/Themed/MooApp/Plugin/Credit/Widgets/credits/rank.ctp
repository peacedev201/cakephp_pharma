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
                <?php echo (isset($item['CreditBalances']['current_credit'])) ? $item['CreditBalances']['current_credit'] : "0";?>
        </div>
        <div class="rank-info">
            <div>
                <i class="current-balance"></i>
                <p>   <?php echo __d('credit','Current Balance');?> <em><?php echo (isset($item['CreditBalances']['current_credit'])) ? $item['CreditBalances']['current_credit'] : "0";?></em>  </p>
            </div>
            <div>
                <i class="total-earn-credit"></i>
                <p><?php echo __d('credit','Total Earned Credits');?> <em><?php echo (isset($item['CreditBalances']['earned_credit'])) ? $item['CreditBalances']['earned_credit'] : "0";?></em></p>
            </div>
            <div>
                <i class="total-spent-credit"></i>
                <p><?php echo __d('credit','Total Spent Credits');?> <em><?php echo (isset($item['CreditBalances']['spent_credit'])) ? $item['CreditBalances']['spent_credit'] : "0";?></em></p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
