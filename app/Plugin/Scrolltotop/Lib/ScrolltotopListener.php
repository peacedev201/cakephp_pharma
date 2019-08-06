<?php
App::uses('CakeEventListener', 'Event');

class ScrolltotopListener implements CakeEventListener {

    public function implementedEvents() {
        return array(            
            'MooView.beforeRender' => 'beforeRender',
        );
    }
    
    public function beforeRender($event)
    {
    	$view = $event->subject();
    	if (Configure::read('Scrolltotop.scrolltotop_enable'))
    	{
    		$helper = MooCore::getInstance()->getHelper('Scrolltotop_Scrolltotop');
    		if ($view instanceof MooView) {    			
	    		if (isset($view->request->params['admin']) && $view->request->params['admin']) {
	    			
	    			$view->Helpers->Html->_View->append('css', '<style>'.$helper->getCss().'</style>');
	    			$view->Helpers->Html->scriptBlock(($helper->getJavascript()));
	    		} else {	    			
		        	$view->addInitJs($helper->getJavascript());
		        	$view->Helpers->Html->_View->append('css', '<style>'.$helper->getCss().'</style>');	        	
		        }
    		}
    	}
    }
}
