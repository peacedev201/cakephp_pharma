<ul class="credit-content-list">
<?php
$creditHelper = MooCore::getInstance()->getHelper('Credit_Credit');
if (!empty($ranks) && count($ranks) > 0)
{
    foreach ($ranks as $rank):
        ?>
        <li class="full_content p_m_10">
            <div class="ranks-image">
                <img width="100" src="<?php echo $creditHelper->getImageRank($rank, array('prefix' => '150_square'))?>" id="item-avatar" class="img_wrapper">
            </div>
            <div class="col-md-9">
                <h3><?php echo htmlspecialchars($rank['CreditRanks']['name']);?></h3>
                <div><strong><?php echo __d('credit','Credit');?>: <?php echo round($rank['CreditRanks']['credit'],2);?></strong></div>
                <div><?php echo __d('credit','Number of members at this rank');?>: <?php echo $creditHelper->memberOfRank($rank['CreditRanks']['credit']);?></div>
                <div><?php echo __d('credit','Description');?>: <?php echo h($rank['CreditRanks']['description']);?></div>
            </div>
        </li>
    <?php endforeach;?>
<?php }else {
    echo '<div class="clear" align="center">' . __d('credit', 'No more results found') . '</div>';
}
?>
<?php if (isset($more_url) && !empty($more_result)): ?>
    <?php echo $this->Html->viewMore($more_url) ?>
<?php endif; ?>
</ul>
