<?php
    require_once(APP . DS . 'Plugin' . DS . 'Business' . DS .'Config' . DS . 'constants.php');
	App::uses('BusinessListener','Business.Lib');
	CakeEventManager::instance()->attach(new BusinessListener());
    
    MooSeo::getInstance()->addSitemapEntity("Business", array(
    	'business'	
    ));
    
    function linkUrl( $string, $limit = 70 ) 
    {    
        $string = Inflector::slug( strtolower($string), '_' );

        if ( strlen($string) > $limit ) {
            $string = substr($string, 0, $limit);
        }

        return $string;
    }
    
    function unLinkUrl( $string, $limit = 70 ) 
    {    
        $string = Inflector::slug( strtolower($string), ' ' );

        if ( strlen($string) > $limit ) {
            $string = substr($string, 0, $limit);
        }

        return $string;
    }
    
    function getIdFromUrl($param)
    {
        $temp = explode('-', $param);
        $ids = $temp[count($temp) - 1];
        $ids = explode('_', $ids);
        $business_id = $ids[0];
        $item_id = isset($ids[1]) ? $ids[1] : '';
        
        unset($temp[count($temp) - 1]);
        $seoname = implode('-', $temp);
        return array($business_id, $item_id, $seoname);
    }
    
    function getTabFromUrl($param)
    {
        $temp = explode('/', $param);
        return $temp[0];
    }
?>