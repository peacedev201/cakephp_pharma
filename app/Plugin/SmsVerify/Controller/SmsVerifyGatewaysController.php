<?php 
class SmsVerifyGatewaysController extends SmsVerifyAppController{
	
	public function admin_index($id = null)
    {
    	$this->set('title_for_layout', __d('sms_verify','Sms Verify Gateways'));
    	$this->loadModel("SmsVerify.SmsVerifyGateway");
    	$gateways = $this->SmsVerifyGateway->find('all');
    	
    	$this->set('gateways',$gateways);
    	
    }
    
    public function admin_active($id)
    {
    	$this->loadModel("SmsVerify.SmsVerifyGateway");
    	$this->SmsVerifyGateway->updateAll(array('SmsVerifyGateway.enable' => 1), array('SmsVerifyGateway.id ='=>$id));
    	$this->SmsVerifyGateway->updateAll(array('SmsVerifyGateway.enable' => 0), array('SmsVerifyGateway.id <>'=>$id));
    	
    	$this->Session->setFlash( __d('sms_verify','Gateway has been actived'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in') );
    	return $this->redirect('/admin/sms_verify/sms_verify_gateways');
    }
    
    public function admin_edit($id)
    {
    	$this->loadModel("SmsVerify.SmsVerifyGateway");
    	$gateway = $this->SmsVerifyGateway->findById($id);
    	
    	$this->set('gateway',$gateway);
    }
    
    public function admin_save($id)
    {
    	$this->loadModel("SmsVerify.SmsVerifyGateway");
    	$this->SmsVerifyGateway->id = $id;
    	$data = $this->request->data;
    	$this->SmsVerifyGateway->save(array('params'=>json_encode($data)));
    	
    	$this->Session->setFlash( __d('sms_verify','Gateway has been updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in') );
    	echo json_encode(array('result'=>1));
    	die();
    }
}