<?php
$param = unserialize($notification['Notification']['params']);
if($param['reaction'] == REACTION_LIKE){
    echo __('liked a status you are tagged in');
}else{
    echo __d('reaction', 'reacted a status you are tagged in');
}
?>