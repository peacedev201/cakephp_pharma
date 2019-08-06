<script>
  jQuery('textarea:not(.no-grow)').autogrow();
</script>
<form id="frmUsernote">
<div class="modal-header" style="font-size: 14px;">
    <?php echo __d('usernotes','Notes') ?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
</div>
    <input type="hidden" id="target_id" name="target_id" value="<?php echo $target_id; ?>">
    <input type="hidden" name="id" value="<?php echo $note_id; ?>">
<div class="modal-body">
    <textarea name="content" class="form-control" id="usernotesContent"><?php echo $content; ?></textarea> 
</div>
<div class="modal-footer">
    <a href="javascript:void(0);" id="btnUsernoteSave" class="btn btn-action"><?php echo __d('usernotes','Save') ?></a>
        <div class="error-message" id="unote-error-message" style="display: none;margin-top: 5px"></div>

</div>
</form>