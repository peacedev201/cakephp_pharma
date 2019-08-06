<?php
$params = json_decode($notification['Notification']['params']);

if(isset($params->period))
{
    $text_credit = __n("credit","credits",$params->period);
    echo __d('spotlight'," congrats, now you're in the spotlight in %s day(s)", $params->period);
}
