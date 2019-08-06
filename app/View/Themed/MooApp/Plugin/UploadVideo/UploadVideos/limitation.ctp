<div class="create_form">
    <div class="bar-content full_content p_m_10">
        <div class="content_center">
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

                <?php if ($bSubscribe) : ?>
                <li>
                    <div class="col-sm-12">
                        <a href="<?php echo $this->request->base . '/subscription/subscribes/upgrade'; ?>" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored"><?php echo __d('upload_video', 'Upgrade now'); ?></a>
                    </div>
                    <div class="clear"></div>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
