<?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery","store_manager"], function($,store_manager) {
            store_manager.initUploadVideo();
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array(
        'inline' => false, 
        'domReady' => true, 
        'requires' => array('jquery', 'store_manager'), 
        'object' => array('$', 'store_manager')
    ));?>
        store_manager.initUploadVideo();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<div class="title-modal">
    <?php echo __d('store', 'Upload Video')?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <div class="create_form">
        <form id="createForm">
            <div id="fetchForm">
                <?php echo __d('store', 'Copy and paste the video url in the text field below')?><br /><br />

                <?php 
                if ( !empty( $this->request->data['group_id'] ) )
                        echo $this->Form->hidden('group_id', array('value' => $this->request->data['group_id']));

                echo $this->Form->hidden('tags');
                ?>
                <ul class="list6 list6sm2">
                    <li>
                        <div class="col-md-2">
                        <label><?php echo __d('store', 'Source')?></label>
                        </div>
                        <div class="col-md-10">
                            <?php echo $this->Form->select( 'source', 
                                array( VIDEO_TYPE_YOUTUBE => 'YouTube', VIDEO_TYPE_VIMEO   => 'Vimeo'),
                                array( 'empty' => false )
                            );
                            ?>
                        </div>
                    </li>
                    <li>
                        <div class="col-md-2">
                        <label><?php echo __d('store', 'URL')?></label>
                        </div>
                        <div class="col-md-10">
                            <?php echo $this->Form->text('url'); ?>
                        </div>
                    </li>
                    <li>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                        </div>
                        <div class="col-md-10">
                            <a href="#" class="button button-action" id="fetchButton"><?php echo __d('store', 'Fetch Video')?></a>
                        </div>
                    </li>
                </ul>
                <div class="error-message" style="display:none;margin-top:10px;"></div>
            </div>
            <div id="videoForm"></div>
            <div class="clear"></div>
        </form>
        </div>
    </div>
</div>
</div>
