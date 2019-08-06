<?php

App::import('Cron.Task', 'CronTaskAbstract');

class FriendInviterTaskInvite extends CronTaskAbstract {

    public function execute() {
        $invite_model = MooCore::getInstance()->getModel('FriendInviter.Invite');
        $items = $invite_model->find('all', array(
            'conditions' => array(
                'Invite.cron_send' => 1,
            ),
            'limit' => 20
                )
        );
        if ($items) {
            foreach ($items as $item) {
                $ssl_mode = Configure::read('core.ssl_mode');
                $http = (!empty($ssl_mode)) ? 'https' :  'http';
                $userModel = MooCore::getInstance()->getModel('User');
        	$cuser = $userModel->findById($item['Invite']['user_id']);				
                if($cuser){
                    $cuser = $cuser['User'];
                    $email = $item['Invite']['recipient'];
                    $cuser['moo_href'] = $userModel->getHref($cuser);
                    $mailComponent = MooCore::getInstance()->getComponent('Mail.MooMail');
                    try{
                        $mailComponent->send(trim($email),'site_invite',
                                                            array(
                                                                    'email' => trim($email),
                                                                    'sender_title' => $cuser['moo_title'],
                                                                    'sender_link' => $http.'://'.$_SERVER['HTTP_HOST'].$cuser['moo_href'],
                                                                    'message' => $item['Invite']['message'],
                                                                    'signup_link' => $http.'://'.$_SERVER['HTTP_HOST'].$this->request->base.'/ref/' . $item['Invite']['code'],
                                                                    'site_name' => Configure::read('core.site_name')
                                                            )
                        );
                    } catch (Exception $ex){}
                }
                $invite_model->updateAll(array('Invite.cron_send' => 0), array('Invite.id' => $item['Invite']['id']));
            }         
        }                     
    }

}
