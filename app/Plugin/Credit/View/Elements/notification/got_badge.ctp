<?php

$params = json_decode($notification['Notification']['params']);
if(isset($params->name))
{
    echo __d('credit','Congratulations! You\'ve got %s badge.', $params->name);
}

?>