<?php
$param = unserialize($notification['Notification']['params']);
if($param['reaction'] == REACTION_LIKE){
    echo __( 'likes "%s"', $param['text']);
}else{
    echo __d('reaction', 'reacted "%s"', $param['text']);
}
?>