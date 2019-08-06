<?php if($gift != null): 
    $gift = $gift['Gift'];
?>
<div class="box2 filter_block">
    <h3><?php echo __d('gift', 'Statistic') ?></h3>
    <div class="box_content">
        <ul class="list2 menu-list">
            <li>
                <?php echo $gift['view_count'];?> <?php echo $gift['view_count'] > 1 ? __d('gift', 'Views') : __d('gift', 'View')?>
            </li>
            <li>
                <?php echo $gift['send_count'];?> <?php echo $gift['send_count'] > 1 ? __d('gift', 'Sents') : __d('gift', 'Sent')?>
            </li>
        </ul>
    </div>
</div>
<?php endif;?>