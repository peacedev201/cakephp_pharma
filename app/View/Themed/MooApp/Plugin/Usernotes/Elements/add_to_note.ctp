<li class="mdl-menu__item"><a data-url="<?php echo $this->request->base?>/usernotess/profile_user_add_note/<?php echo $user['User']['id']?>" rel="profile-content" href="#">
    <?php echo __d('usernotes','Notes')?>
    </a>
</li>	

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