<?php 
class StoreProductReportsController extends StoreAppController
{	
    public $components = array('Paginator');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->admin_url = $this->request->base.'/admin/store/store_product_reports/';
        $this->set('admin_url', $this->admin_url);
        $this->loadModel('Store.StoreProduct');
        $this->loadModel('Store.StoreProductReport');
        $this->loadModel('Store.Store');
    }
    ////////////////////////////////////////////////////////admin////////////////////////////////////////////////////////
    public function admin_index()
    {
        //load products
        $reports = $this->StoreProductReport->loadManagerPaging($this);

        $this->set(array(
            'title_for_layout' => __d('store', 'Product Reports'),
            'reports' => $reports,
        ));
    }
    
    public function admin_approve()
    {
        $this->admin_active($this->request->data, 1, 'approve');
    }
    
    public function admin_disapprove()
    {
        $this->admin_active($this->request->data, 0, 'approve');
    }
    
    private function admin_active($data, $value = 1, $task)
    {
        $count = 0;
        if(!empty($data['cid']))
        {
            foreach($data['cid'] as $id)
            {
                $report = $this->StoreProductReport->findById($id);
                if(!empty($report['StoreProductReport']['product_id']))
                {
                    $product_id = $report['StoreProductReport']['product_id'];
                    $this->StoreProduct->activeField($product_id, $task, $value, true);
                    if($task == 'approve')
                    {
                        switch($value)
                        {
                            case 0:
                                $this->StoreProduct->changeProductActivityPrivacy($product_id, PRIVACY_ME);
                                break;
                            case 1:
                                $this->StoreProduct->changeProductActivityPrivacy($product_id, PRIVACY_PUBLIC);
                                break;
                        }
                    }
                }
            }
        }
        $this->_redirectSuccess(__d('store', 'Successfully updated'), $this->referer());
    }
    
    public function admin_delete()
    {
        $data = $this->request->data;
        if(!empty($data['cid']))
        {
            foreach($data['cid'] as $id)
            {
                $this->StoreProductReport->delete($id);
            }
        }
        $this->_redirectSuccess(__d('store', 'Successfully deleted'), '/admin/store/product_reports/');
    }
    
    ////////////////////////////////////////////////////////frontend/////////////////////////////////////////////////////
    public function report_dialog()
    {
        if(Configure::read('store.uid') == 0)
        {
            $this->_jsonError(__d('store', "Please login to continue", "Store"));
        }
        else 
        {
            $this->set(array(
                'product_id' => $this->request->data['product_id'],
            ));
            $this->render('Store.Elements/report_dialog');
        }
    }
    
    public function report_product()
    {
        $data = $this->request->data;
        if(!$this->StoreProduct->checkProductExist($data['product_id']))
        {
            $this->_jsonError(__d('store', 'Product not found'));
        }
        else if($this->StoreProductReport->checkReportExist($data['product_id']))
        {
            $this->_jsonError(__d('store', 'You had already reported this product.'));
        }
        else if(empty($data['content']))
        {
            $this->_jsonError(__d('store', 'Reason can not be empty'));
        }
        else if($this->StoreProductReport->saveReport($data))
        {
            $adminUser = $this->Store->getAdminUser();
            $this->Store->sendNotification($adminUser['User']['id'], MooCore::getInstance()->getViewer(true), 'report_product', '/admin/store/product_reports/', '', 'Store');
            $this->_jsonSuccess(__d('store', 'Successfully reported.'));
        }
        $this->_jsonError(__d('store', 'Something went wrong, please try again.'));
    }
    
    public function email_friend_dialog()
    {
        if(Configure::read('store.uid') == 0)
        {
            $this->_jsonError(__d('store', "Please login to continue", "Store"));
        }
        else 
        {
            $this->set(array(
                'product_id' => $this->request->data['product_id'],
            ));
            $this->render('Store.Elements/email_friend_dialog');
        }
    }
    
    public function email_friend()
    {
        $data = $this->request->data;
        $recipients = !empty($data['recipients']) ? explode(",", $data['recipients']) : null;
        if(!$this->StoreProduct->checkProductExist($data['product_id']))
        {
            $this->_jsonError(__d('store', 'Product not found'));
        }
        else if($recipients == null)
        {
            $this->_jsonError(__d('store', 'Recipients is required'));
        }
        else if(count($recipients) > 10)
        {
            $this->_jsonError(sprintf(__d('store', 'You are only able to send to %s recipients per request'), 10));
        }
        
        foreach($recipients as $recipient)
        {
            $email = trim($recipient);
            if(!$this->validEmail($email))
            {
                $this->_jsonError(__d('store', 'Invalid email'));
            }
        }
        $cUser = $this->_getUser();
        if(empty($data['message']))
        {
            $this->_jsonError(__d('store', 'Message is required'));
        }
        else if($this->StoreProductReport->sendEmailToFriends($data['product_id'], $recipients, $data['message'], $cUser['email'], $cUser['name']))
        {
            $this->_jsonSuccess(__d('store', 'Your email has been sent'));
        }
        $this->_jsonError(__d('store', 'Something went wrong, please try again.'));
    }
}