<?php
class FeedbackBlockuser extends FeedbackAppModel 
{
    public function isIdExist($id)
    {
        return $this->hasAny(array('id' => $id));
    }
}