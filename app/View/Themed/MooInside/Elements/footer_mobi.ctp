<?php

if (!$this->isEmpty('east') && $this->isActive('east')) :  ?>
<div class="visible-xs visible-sm">
    <div class="mobile-footer">

        <?php if( !$this->isEmpty('east') && $this->isActive('east') ): ?>
        <a href="#" data-toggle="modal" data-target="#right" >
            <i class="material-icons">view_headline</i>
            <span><?php echo __('More') ?></span>
        </a>
        <?php endif; ?>

    </div>
</div>
<?php endif; ?>