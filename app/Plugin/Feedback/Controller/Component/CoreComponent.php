<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('Component', 'Controller');

class CoreComponent extends Component {

    public function initialize(Controller $controller) {
        // Load model
    }

    public function is234(){
        $setting_model = MooCore::getInstance()->getModel('Setting');
        
        $version = $setting_model->findByName( 'version' );
        $ver = $version['Setting']['value'];
       
        if(!empty($ver)){
            list($id1,$id2,$id3) = explode('.', $ver);
           
            if(!empty($id1)){
                if($id1 > 2){
                   return true;
                }

                if(!empty($id2) && $id2 > 3){
                    return true;
                }
                
                if(!empty($id3) && $id3 >= 2){
                    return true;
                }
            }
        }
        return false;
    }
}
