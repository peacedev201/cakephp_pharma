
<?php if (!empty($entries)): ?><div class="box2">   
    <?php if (isset($title_enable) && $title_enable): ?>       
            <h3><?php echo $title ?></h3>
        <?php endif; ?>

        <div class="box_content">
            <?php echo $this->element('lists/entries_list_m', array('entries' => $entries),  array('plugin' => 'Contest')); ?>
        </div>
    </div> 
<?php endif; ?>