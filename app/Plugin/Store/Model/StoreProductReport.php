<?php 
App::uses('StoreAppModel', 'Store.Model');
class StoreProductReport extends StoreAppModel{
    public $recursive = 1; 
    public $belongsTo = array(
        'StoreProduct'=> array(
            'className' => 'Store.StoreProduct',
            'foreignKey' => 'product_id',
            'dependent' => true),
        'User'=> array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'dependent' => true)
    );
    
    public function beforeSave($options = array()) 
    {
        parent::beforeSave($options);
        $this->data['StoreProductReport']['user_id'] = MooCore::getInstance()->getViewer(true);
    }
    
    public function checkReportExist($product_id)
    {
        return $this->hasAny(array(
            'StoreProductReport.user_id' => MooCore::getInstance()->getViewer(true),
            'StoreProductReport.product_id' => $product_id
        ));
    }
    
    public function saveReport($data)
    {
        return $this->save($data);
    }
    
    function loadManagerPaging($obj, $limit = 20)
    {
        //load data
        $obj->Paginator->settings=array(
            'order' => array('StoreProductReport.id' => 'DESC'),
            'limit' => $limit,
        );
        $data = $obj->paginate('StoreProductReport');
        return $data;
    }
    
    public function sendEmailToFriends($product_id, $recipients, $message, $from_email, $from_name)
    {
        $mProduct =  MooCore::getInstance()->getModel("Store.StoreProduct");
        $product = $mProduct->loadOnlyProduct($product_id);
        //send email
        $link = Router::url('/', true).substr($product['StoreProduct']['moo_url'], 1);
        $message = '<a href="'.$link.'">'.$link.'</a><br/>'.$message;
        if($recipients != null)
        {
            $mail = $temp_mail = Configure::read('Mail');
            foreach($recipients as $recipient)
            {
                $recipient = trim($recipient);
                $temp_mail['mail_name'] = $from_name;
                $temp_mail['mail_from'] = $from_email;
                Configure::write('Mail', $temp_mail);
                $cMooMail = MooCore::getInstance()->getComponent('MooMail');
                $cMooMail->send($recipient, 'email_to_friend', array( 
                    'product_name' => $product['StoreProduct']['name'],
                    'content' => $message
                ));
            }
            Configure::write('Mail', $mail);
        }
        return true;
    }
}