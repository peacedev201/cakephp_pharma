<?php
$param = unserialize($notification['Notification']['params']);
if($param['reaction'] == REACTION_LIKE){
    echo __( 'likes your photo' );
}else{
    echo __d('reaction', 'reacted your photo');
}
?>