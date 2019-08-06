<?php if($best_angel || !empty($angel_list)): ?>
    <?php
        if (isset($title_enable) && ($title_enable) === "") $title_enable = false; else $title_enable = true;
    ?>

    <?php if ( !empty( $angel_list ) ): ?>
<div class="box2 box-friend">
    <?php if($title_enable): ?>
    <h3><?php echo  __('Inviter Angels') ?></h3>
    <?php endif; ?>
    <div class="box_content">
        <ul class="list_block">
            <?php
            foreach ($angel_list as $user): ?>
                <li><?php echo $this->Moo->getItemPhoto(array('User' => $user['User']),array('prefix' => '50_square'),array('class' => 'user_avatar_small tip'))?></li>
            <?php
            endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>        
<?php endif; ?>
