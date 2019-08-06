<?php if( !$this->isEmpty('north') ): ?>
<?php echo $north; ?>
<?php endif; ?>
    
    <!--Check profile page-->
    <?php if (!empty($is_profile_page)): ?>
    <!--Add cover here-->
    <?php echo $this->element('user/header_profile'); ?>
    <?php endif; ?>
     <div class="main_content">
    <div id="center">
        <?php echo $center; ?>
    </div>
</div>


