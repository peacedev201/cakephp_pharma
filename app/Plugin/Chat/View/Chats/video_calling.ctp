<?php
$this->addPhraseJs(array(
        'uploadPhoto' => __("Upload Photos"),
    )
);

?>
<?php $this->start('mooVideoContent'); ?>
    <div id="root" 
         user_id="<?php echo $user_id;?>" 
         caller_id="<?php echo $caller_id;?>" 
         receiver_id="<?php echo $receiver_id;?>"
         room_id="<?php echo $room_id;?>"
         members="<?php echo $members;?>"
         token="<?php echo $token;?>"></div>
<?php $this->end(); ?>

<?php

$this->ChatGzip->script(array('zip'=>'mooChat-videoCalling.js.gz','unzip'=>'Chat.client/mooChat-videoCalling'));

?>