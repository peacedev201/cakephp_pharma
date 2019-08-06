<?php if ($is_enable): ?>
    
    <?php 

        $this->addPhraseJs(array(
            'confirm'=>__('Please Confirm'),
            'save_note'=>__('Save'),
            'ok'=>__('Ok'),
            'cancel'=>__('Cancel'),
            'please_confirm'=>__('Please Confirm'),
            'are_you_sure_you_want_to_remove_note'=>__d('usernotes','Are you sure you want to remove this note?'),
        ));


     ?>
    

    <div class="box2 usernote-widget" >
        <input type="hidden" class="usernote-target-id" value="<?php echo $target_id; ?>">
        <textarea style="display: none" class="usernote-text-hidden no-grow"><?php echo $this->Moo->cleanHtml($this->Text->convert_clickable_links_for_hashtags( $note_content )); ?></textarea>
        <input type="hidden" class="unote-id" value="<?php echo $note_id; ?>">
        <h3><?php echo $note_title; ?>
             <a style="margin-right: 10px;" class="usernote-leave-note" href="javascript:void(0);" ><i class="material-icons">edit</i></a> 
             <a class="usernote-remove" href="javascript:void(0);" style="float:right;<?php echo empty($note_id)?"display:none" :""; ?>"><i class="material-icons">delete_forever</i></a>
        </h3>
        <div class="testspin"></div>
        <div class="usernotes" >
            <div class="row">
                    <div class="col-md-12">
                        <textarea readonly="true" class="form-control usernote-content no-grow" rows="10" style="overflow: auto; background-color:white;cursor: default; max-height: 250px;overflow-y: auto; pointer-events: none;"><?php echo h($this->Moo->cleanHtml($this->Text->convert_clickable_links_for_hashtags( $note_content ))); ?></textarea>
                    </div>
            </div>
            <div class="error-message unoteErrorMessage"  style="display:none"></div>
            <div class="row usernote-action" style="padding:5px;display: none">
                <div class="col-md-6 text-right col-xs-6">
                    <a   class="btn btn-clean usernote-save"><?php echo __d('usernotes','Save'); ?></a>
                </div>
                <div class="col-md-6 col-xs-6">
                    <a  class="btn btn-clean text-left usernote-cancel"><?php echo __d('usernotes','Cancel'); ?></a>
                </div>
            </div>
             <div class="usernote-spinner"></div>
        </div>
    </div>


    <?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery","mooUsernotes"], function($, mooUsernotes) {
            mooUsernotes.initOnUserList();
        });
    </script>
    <?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooUsernotes'), 'object' => array('$', 'mooUsernotes'))); ?>
    mooUsernotes.initOnUserList();
    <?php $this->Html->scriptEnd(); ?>
    <?php endif; ?>



<?php endif; ?>

