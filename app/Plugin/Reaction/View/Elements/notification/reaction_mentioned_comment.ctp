<?php
$param = unserialize($notification['Notification']['params']);
if($param['reaction'] == REACTION_LIKE){
    echo __('liked a comment you are mentioned in');
}else{
    echo __d('reaction', 'reacted a comment you are mentioned in');
}
?>