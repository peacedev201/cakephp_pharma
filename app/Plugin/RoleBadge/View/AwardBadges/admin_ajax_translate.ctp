<script type="text/javascript">
    $(document).ready(function () {
        $('#btnSaveT').click(function () {
            disableButton('btnSaveT');
            $.post("<?php echo $this->Html->url(array('plugin' => 'role_badge', 'controller' => 'award_badges', 'action' => 'admin_ajax_translate_save'));?>", $("#createFormT").serialize(), function (data) {
                enableButton('btnSaveT');
                var json = $.parseJSON(data);
                if (json.result === 1) {
                    window.location.reload();
                } else {
                    $("#error-message-t").show();
                    $("#error-message-t").html(json.message);
                }
            });

            return false;
        });
    });
</script>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo __d('role_badge', 'Translation'); ?></h4>
</div>
<div class="modal-body">
    <form id="createFormT" class="form-horizontal" role="form">
        <?php echo $this->Form->hidden('id', array('value' => $aAwardBadge['AwardBadge']['id'])); ?>
        <?php echo $this->Form->hidden('field', array('value' => $field)); ?>
        <div class="form-body">
            <?php foreach ($languages as $sKey => $sLanguage) : ?>
                <?php $sValue = ""; ?>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo $sLanguage; ?></label>
                    <div class="col-md-9">
                        <?php foreach ($aAwardBadge[$field . 'Translation'] as $aTranslation) : ?>
                            <?php if ($aTranslation['locale'] == $sKey): ?>
                                <?php $sValue = $aTranslation['content']; break; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        
                        <?php if($field == 'name'): ?>
                            <?php echo $this->Form->text('name.' . $sKey, array('placeholder' => __d('role_badge', 'Enter text'), 'class' => 'form-control', 'value' => $sValue)); ?>
                        <?php else: ?>
                            <?php echo $this->Form->textarea('description.' . $sKey, array('class' => 'form-control', 'value' => $sValue)); ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="form-group">
                <label class="col-md-3 control-label">&nbsp;</label>
                <div class="col-md-9">
                    <div id="error-message-t" class="alert alert-danger error-message" style="display: none; margin-top: 10px;"></div>
                </div>
            </div>
        </div>
    </form>

    <div class="alert alert-danger error-message" style="display:none;margin-top:10px;"></div>
</div>
<div class="modal-footer">
    <a href="javascript:void(0)" id="btnSaveT" class="btn btn-action"><?php echo __d('role_badge', 'Save Change'); ?></a>
    <button type="button" class="btn default" data-dismiss="modal"><?php echo __d('role_badge', 'Close') ?></button>
</div>