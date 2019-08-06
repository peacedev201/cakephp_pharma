
<?php if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>
<?php if(!empty($is_social)){
    echo $this->element('signin_facebook' );
}else{
    echo $this->element('signin' );
} ?>
