<div class="title-modal">
    <?php echo __('Upload Video') ?>
    <button type="button" class="close"  data-dismiss="modal"><span  aria-hidden="true">&times;</span></button>
</div>

<div class="modal-body">
    <div class="create_form">
        <div class="bar-content full_content p_m_10">
            <div class="create_form">
                <?php 
                if ($bSubscribe) :
                    echo __d('upload_video', 'You have reached video upload limitation, please upgrade your membership to continue!');
                else:
                    echo __d('upload_video', 'You have reached video upload limitation!');
                endif;
                ?>
            </div>
        </div>
    </div>
</div>

<?php if ($bSubscribe) : ?>
<div class="modal-footer">
    <div class="col-sm-12">
        <a href="<?php echo $this->request->base . '/subscription/subscribes/upgrade'; ?>" class="btn btn-action"><?php echo __d('upload_video', 'Upgrade now'); ?></a>
        <a href="javascript:void(0);" class="button button-action" data-dismiss="modal"><?php echo __d('upload_video', 'No, Thanks'); ?></a>
    </div>
    <div class="clear"></div>
</div>
<?php endif; ?>
