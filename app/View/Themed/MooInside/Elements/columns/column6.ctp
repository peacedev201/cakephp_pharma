<?php
   // echo $north . "<div>$west$center</div>";
?>
<?php if( !$this->isEmpty('north') ): ?>
    <?php echo $north ;?>
<?php endif; ?>

    <?php if (!empty($is_profile_page)): ?>
        <?php echo $this->element('user/header_profile'); ?>
    <?php endif; ?>
     <div class="main_content">
        <?php if( !$this->isEmpty('west') ): ?>
            <div id="right" class="sl-rsp-modal col-md-4">
                <div class="visible-xs visible-sm closeButton">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span> <span class="sr-only">Close</span></button>
                </div>
                <?php echo $west; ?>
            </div>
        <?php endif; ?>
        <div id="center" <?php if( !$this->isEmpty('west') ): echo 'class="col-md-8"'; 
                               endif; ?>>
        <?php echo $center; ?>
        </div>
        </div>
    





