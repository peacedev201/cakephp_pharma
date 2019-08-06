<?php
class FeedbackImage extends FeedbackAppModel 
{
	public $actsAs = array(
		'Storage.Storage' => array(
            'type'=>array('feedbacks'=>'image_url'),
		)
	);
	
    public function clearImageByFeedbackId($feedback_id)
    {
        return $this->updateAll(array(
            'feedback_id' => 0
        ), array(
            'feedback_id' => $feedback_id
        ));
    }
    
    public function updateImageFeedbackId($feedback_id, $feedback_image_ids)
    {
        return $this->updateAll(array(
            'feedback_id' => $feedback_id
        ), array(
            "id IN(".$feedback_image_ids.")"
        ));
    }
}