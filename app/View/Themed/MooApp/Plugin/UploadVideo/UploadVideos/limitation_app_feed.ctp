<div class="create_form">
    <div class="full_content p_m_10">
        <ul class="list6">
            <li>    
                <div class="col-sm-12">
                    <?php 
                    if ($bSubscribe) :
                        echo __d('upload_video', 'You have reached video upload limitation, please upgrade your membership to continue!');
                    else:
                        echo __d('upload_video', 'You have reached video upload limitation!');
                    endif;
                    ?>
                </div>
                <div class="clear"></div>
            </li>
            
            <li>
                <div class="col-sm-12">
                    <?php if ($bSubscribe) : ?>
                    <a href="<?php echo $this->request->base . '/subscription/subscribes/upgrade'; ?>" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1"><?php echo __d('upload_video', 'Upgrade now'); ?></a>
                    <a href="javascript:void(0);" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored" data-dismiss="modal"><?php echo __d('upload_video', 'No, Thanks'); ?></a>
                    <?php else: ?>
                    <a href="javascript:void(0);" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored" data-dismiss="modal"><?php echo __('Ok'); ?></a>
                    <?php endif; ?>
                </div>
                <div class="clear"></div>
            </li>
        </ul>
    </div>
</div>
