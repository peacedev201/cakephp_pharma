<?php
App::uses('AppHelper', 'View/Helper');
class ActivitylogHelper extends AppHelper {

	public function getEnable()
	{
		return Configure::read('Activitylog.activitylog_enabled');
	}

    function possession( $actor, $owner = null, $is_web = false )
    {
        if ( empty( $owner ) || $actor['id'] == $owner['id'] ){
            $genderTxt = "Unknown";
            if ($actor['gender'] == 'Male'){
                $genderTxt = __d('activitylog','his');
            }else if ($actor['gender'] == 'Female'){
                $genderTxt = __d('activitylog','her');
            }else if($actor['gender'] == 'Unknown'){
                $genderTxt = __d('activitylog','their');
            }
            return $genderTxt;
        }

        if ( Configure::read('Config.language') != 'eng' ){
            if (!$is_web)
                return h($owner['name']);
            else {
                $mooHelper = MooCore::getInstance()->getHelper('Core_Moo');
                return $mooHelper->getName($owner);
            }
        }else {
            if (!$is_web)
                return h($owner['name']) . '\'s';
            else {
                $mooHelper = MooCore::getInstance()->getHelper('Core_Moo');
                return $mooHelper->getName($owner) . '\'s';
            }
        }
    }

}
