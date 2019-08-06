
<?php
    //echo $north . "<div>$center$east</div>";
?>
<?php if( !$this->isEmpty('north') ): ?>
    <?php echo $north ;?>
<?php endif; ?>

    <?php if (!empty($is_profile_page)): ?>
       
        <!--Add cover here-->
         <?php echo $this->element('user/header_profile'); ?>
        
       
     <?php endif; ?>
      <div class="main_content">
        <?php if( !$this->isEmpty('east') ): ?>
        <div id="right"  class="sl-rsp-modal col-md-4 pull-right">
        <?php echo $east; ?>
        </div>
        <?php endif; ?>
        <div id="center" <?php if( !$this->isEmpty('east')): echo 'class="col-md-8"'; 
                               endif; ?>>
        <?php echo $center; ?>
        </div>
        </div>
   


