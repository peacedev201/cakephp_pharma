<?php
App::uses('StickersController','Sticker.Controller');
App::uses('StickerAppController','Sticker.Controller');
class ApiStickersController extends StickersController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->OAuth2 = $this->Components->load('OAuth2');
        $this->OAuth2->verifyResourceRequest(array('token'));
    }
    
    function browse()
    {
        parent::sticker_modal();
    }
    
    function images()
    {
        if(empty($this->request->params['id']))
        {
            throw new ApiBadRequestException(__d('sticker', 'Sticker id is required'));
        }
        $sticker_id = $this->request->params['id'];
        parent::sticker_modal_images($sticker_id, false);
    }
    
    function recent()
    {
        parent::sticker_modal_recent(false);
    }
    
    function apisearch()
    {
        if(empty($this->request->data['keyword']))
        {
            throw new ApiBadRequestException(__d('sticker', 'Keyword is required'));
        }
        $this->request->query['keyword'] = $this->request->data['keyword'];
        parent::search(false);
    }
}
