<div class="bar-content">
    <div class="content_center">
        <div class="mo_breadcrumb">
            <h3><?php echo __d('forum','Edit Signature');?></h3>
        </div>
        <div id="msg_success" style="display: none" class="Metronic-alerts alert alert-success fade in"><?php echo __d('forum','Your changes have been saved');?></div>

        <div class="create_form">
            <div class="content_center">
                <div class="box3">
                    <form action="<?php echo  $this->request->base; ?>/forums/topic/signature" id="formSignature" method="post">
                        <div class="full_content p_m_10">
                            <div class="form_content">
                                <ul>
                                    <li>
                                        <div class="col-md-12">
                                            <p><?php echo __d('forum', 'Edit your signature using the editor below, and click on "Save Signature" to save changes.');?></p>
                                            <?php echo $this->Form->checkbox('show_signature', array('checked' => $cuser['show_signature'])); ?>
                                            <label for="show_signature"><?php echo __d('forum','Auto insert into my reply');?></label>
                                        </div>
                                        <div class="clear"></div>
                                    </li>
                                    <li>
                                        <div class="col-md-12">
                                            <?php echo $this->Form->tinyMCE('signature', array('value' => $cuser['signature'], 'class' => 'forum-textarea')); ?>
                                        </div>
                                        <div class="clear"></div>
                                    </li>
                                    <li>
                                        <div class="col-md-12">
                                            <a id="btn_signature" class='mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1 btn-signature'><?php echo __d('forum' ,'Save Signature')?></a>
                                        </div>
                                        <div class="clear"></div>
                                    </li>
                                    <li>
                                        <div class="error-message" id="errorMessage" style="display:none"></div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooViewForum"], function($,mooViewForum) {
        mooViewForum.initSignature();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooViewForum'), 'object' => array('$', 'mooViewForum'))); ?>
    mooViewForum.initSignature();
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>