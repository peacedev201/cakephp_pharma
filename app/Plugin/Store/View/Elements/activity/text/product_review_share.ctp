<?php
    $mActivity = MooCore::getInstance()->getModel('Activity');
    
    $parent_activity = $mActivity->findById($activity['Activity']['parent_id']);
    echo __d("store", "shared %s's %s", "<a href=".$parent_activity['User']['moo_href'].">".$parent_activity['User']['name']."</a>", "<a href=".$parent_activity['User']['moo_href']."/activity_id:".$parent_activity['Activity']['id'].">".__d('store', 'review')."</a>");
?>