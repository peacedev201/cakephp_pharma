<?php if (!empty($t_contests)): ?>
    <div class="box2">   
        <?php if (isset($title_enable) && $title_enable): ?>       
            <h3><?php echo $title ?></h3>
        <?php endif; ?>
        <div class="box_content">
            <?php echo $this->element('lists/contest_list_m', array('contests' => $t_contests),  array('plugin' => 'Contest')); ?>
        </div>
    </div>
<?php endif; ?>
