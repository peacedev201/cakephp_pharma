<?php
    $mActivity = MooCore::getInstance()->getModel('Activity');
    
    $parent_activity = $mActivity->findById($activity['Activity']['parent_id']);
    echo __d("business", "shared %s's %s", "<a href=".$parent_activity['User']['moo_href'].">".$parent_activity['User']['name']."</a>", "<a href=".$parent_activity['User']['moo_href']."/activity_id:".$parent_activity['Activity']['id'].">".__d('business', 'review')."</a>");
?>

<?php
    $subject = MooCore::getInstance()->getItemByType($activity['Activity']['type'], $activity['Activity']['target_id']);
    $show_subject = MooCore::getInstance()->checkShowSubjectActivity($subject);
    if($show_subject)
    {
        switch ($activity['Activity']['type'])
        {
            case "Group_Group":
                $mGroup = MooCore::getInstance()->getModel('Group.Group');
                $group = $mGroup->findById($activity['Activity']['target_id']);
                echo ' > <a href="'.$group['Group']['moo_href'].'">'.$group['Group']['name'].'</a>';
                break;
            case "User":
                if ($activity['Activity']['target_id'] > 0)
                {
                    $mUser = MooCore::getInstance()->getModel('User.User');
                    $user = $mUser->findById($activity['Activity']['target_id']);
                    echo ' > <a href="'.$user['User']['moo_href'].'">'.$user['User']['name'].'</a>';
                }
                break;
        }
    }
?>