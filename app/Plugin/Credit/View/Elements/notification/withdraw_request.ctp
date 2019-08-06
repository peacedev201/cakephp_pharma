<?php
$params = json_decode($notification['Notification']['params']);
if(isset($params->status))
{
    if($params->status == CREDIT_STATUS_COMPLETED)
        echo __d('credit','%s completed your Withdrawal Request', $params->name);
    else
        echo __d('credit','%s cancelled your Withdrawal Request', $params->name);
}
