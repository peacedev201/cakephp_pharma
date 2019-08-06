<ul class="credit-content-list">
    <?php
    if (!empty($items) && count($items) > 0) {
        $i = $num_count;
        foreach ($items as $user):
            ?>
            <li class="full_content p_m_10">
                <div class="credit-race">
                    <?php echo $i; ?>
                </div>
                <?php echo $this->Moo->getItemPhoto(array('User' => $user['User']), array('prefix' => '100_square')) ?>
                <div class="credit-info">
                    <?php echo $this->Moo->getName($user['User'], true)?>
                    <!--<a class="title"
                       href="<?php echo $this->request->base ?>/<?php echo (!empty($user['User']['username'])) ? '-' . $user['User']['username'] : 'users/view/' . $user['User']['id'] ?>">
                        <?php echo h($user['User']['moo_title']) ?>
                    </a>-->
                    <div class="extra-info">
                        <div>
                            <strong><?php echo __d('credit', 'Current Balance'); ?>
                                : <?php echo round($user['CreditBalances']['current_credit'],2) ?></strong>
                        </div>
                        <div><?php echo __d('credit', 'Total Earned Credits'); ?>
                            : <?php echo round($user['CreditBalances']['earned_credit'],2) ?></div>
                        <div><?php echo __d('credit', 'Total Spent Credits'); ?>
                            : <?php echo round($user['CreditBalances']['spent_credit'],2) ?></div>
                    </div>
                </div>
            </li>
            <?php $i++;endforeach; ?>
    <?php } else {
        echo '<div class="clear" align="center">' . __d('credit', 'No more results found') . '</div>';
    }
    ?>
</ul>
<?php if (isset($more_url) && !empty($more_result)): ?>
    <?php echo $this->Html->viewMore($more_url) ?>
<?php endif; ?>
