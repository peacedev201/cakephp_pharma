<?php
$params = json_decode($notification['Notification']['params']);

if(isset($params->credit))
{
    $text_credit = __n("credit","credits",$params->credit);
    echo __d('credit',' sent %s %s to you', $params->credit,$text_credit);
}

