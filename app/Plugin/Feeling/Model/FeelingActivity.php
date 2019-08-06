<?php
App::uses('FeelingAppModel', 'Feeling.Model');

class FeelingActivity extends FeelingAppModel
{
    /*public function beforeDelete($cascade = true){
        parent::beforeDelete($cascade);
    }*/

    public function get_felling($activity){
        $feeling_activity = $this->find('first', array(
            'conditions' => array('activity_id' => $activity['id'])
        ));

        if(!empty($feeling_activity)){
            $feelingModel = MooCore::getInstance()->getModel('Feeling.Feeling');
            $feeling = $feelingModel->findById($feeling_activity['FeelingActivity']['feeling_id']);
            return $feeling;
        }
        return null;
    }
}