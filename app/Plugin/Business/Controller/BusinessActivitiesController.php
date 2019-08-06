<?php 
class BusinessActivitiesController extends BusinessAppController
{    
    public function __construct($request = null, $response = null) 
    {
        parent::__construct($request, $response);
        $this->loadModel('Activity');
        $this->loadModel('Like');
        $this->loadModel('Business.Business');
        $this->loadModel('Business.BusinessFollow');
    }
    
    public function load_activities($task, $business_id = null)
    {
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $uid = $this->Auth->user('id');
        $activity_id = (!empty($this->request->named['activity_id'])) ? $this->request->named['activity_id'] : '';
        //$activities = $this->Business->loadActivities($task, $business_id, $page, $activity_id);
        $activities = $this->Activity->getActivities(BUSINESS_ACTIVITY_TYPE, $business_id, null, $page);
        $more_url = '/business_activities/load_activities/'.$task.'/page:'.($page + 1);
        if($business_id != null)
        {
            $more_url = '/business_activities/load_activities/'.$task.'/'.$business_id.'/page:'.($page + 1);
        }
        
        // get activity likes
        if (!empty($uid)) {
            $activity_likes = $this->Like->getActivityLikes($activities, $uid);
            $this->set('activity_likes', $activity_likes);
        }
        
        $this->set(array(
            'check_post_status' => true,
            'activities' => $activities,
            'bIsACtivityloadMore' => count($activities),
            'more_url' => $more_url
        ));
        $this->render('/Elements/activities');
    }
}