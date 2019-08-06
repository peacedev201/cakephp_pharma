<?php
    $param = unserialize($notification['Notification']['params']);
    if($param['reaction'] == REACTION_LIKE){
        echo __( 'likes your status' );
    }else{
        echo __d('reaction', 'reacted your status' );
    }
?>