<?php
App::uses('CakeSession', 'Model/Datasource');
class BusinessLocation extends BusinessAppModel 
{   
    public $validationDomain = 'business';
    public $order = 'BusinessLocation.ordering ASC';
    public $actsAs = array('Tree' => 'nested');
    public $mooFields = array('title','href','plugin','type','url', 'thumb');
    public $validate = array(
        'name' => array(
            array(
                'rule' => 'notBlank',
                'message' => 'Name is required'
            ),
            array(   
                'rule' => array('checkDuplicateName'),
                'message' => 'This name is already exist'
            ),	
        ),
    );
    
    function checkDuplicateName()
    {
        $cond = array(
            'BusinessLocation.name' => $this->data['BusinessLocation']['name'],
            'BusinessLocation.parent_id' => $this->data['BusinessLocation']['parent_id']
        );
        if(isset($this->data['BusinessLocation']['id']) && $this->data['BusinessLocation']['id'] > 0)
        {
            $cond[] = 'BusinessLocation.id != '.$this->data['BusinessLocation']['id'];
        }
        if($this->hasAny($cond))
        {
            return false;
        }
        return true;
    }
  
    public function getHref($row) 
    {
        $request = Router::getRequest();
        if(isset($row['name']) && isset($row['id']))
        {
            return $request->base.'/business_search/in_'.linkUrl($row['name']).'/'.$row['id'];
        }
        return '';
    }
    
    public function autoAddBusinessLocation($country, $region)
    {
        if($country != null && $region != null)
        {
            $countryValue = $this->findByNameAndParentId($country, 0);
            if($countryValue == null)
            {
                $this->create();
                $this->save(array(
                    'parent_id' => 0,
                    'name' => $country,
                    'enabled' => 1
                ));
                $country_id = $this->id;
            }
            else
            {
                $country_id = $countryValue['BusinessLocation']['id'];
            }

            $regionValue = $this->findByNameAndParentId($region, $country_id);
            if($regionValue == null && $country_id > 0)
            {
                $this->create();
                $this->save(array(
                    'parent_id' => $country_id,
                    'name' => $region,
                    'enabled' => 1
                ));
                $region_id = $this->id;
            }
            else
            {
                $region_id = $regionValue['BusinessLocation']['id'];
            }
            return $region_id;
        }
        return '';
    }
    
    public function getPopularLocations()
    {
        //find enable parent
        $parent = $this->find('list', array(
            'conditions' => array(
                'BusinessLocation.parent_id' => 0,
                'BusinessLocation.enabled' => 1
            ),
            'fields' => array('BusinessLocation.id', 'BusinessLocation.id')
        ));
        if($parent == null)
        {
            return array();
        }
        
        return $this->find('all', array(
            'conditions' => array(
                /*'OR' => array(
                    'BusinessLocation.parent_id' => $parent,
                    'BusinessLocation.id' => $parent,
                ),*/
                'BusinessLocation.parent_id' => $parent,
                'BusinessLocation.enabled' => 1,
            ),
            'order' => array('BusinessLocation.business_count' => 'DESC'),
            'limit' => Configure::read('Business.business_num_of_popular_location')
        ));
    }
    
    public function getDefaultLocation()
    {
        $parent_ids = $this->find('list', array(
            'conditions' => array(
                'BusinessLocation.parent_id' => 0,
                'BusinessLocation.enabled' => 1
            ),
            'fields' => array('BusinessLocation.id', 'BusinessLocation.id')
        ));
        return $this->find('first', array(
            'conditions' => array(
                'BusinessLocation.is_default' => 1,
                'BusinessLocation.parent_id' => $parent_ids
            )
        ));
    }
    
    public function getIdList($id)
    {
        $result = array($id);
        $data = $this->children($id);
        if($data != null)
        {
            foreach($data as $item)
            {
                $result[] = $item['BusinessLocation']['id'];
            }
        }
        return $result;
    }
    public function getItemById($id) {
         return $this->findById($id);
    }
    public function deleteBusinessLocation($id){
        $canDelete = true;
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $business = $mBusiness->findByBusinessLocationId($id);
        if(!empty($business)) {
            $canDelete = false;
        }
        if(!$canDelete) {
             return false;
        }
        if($this->delete($id)) {
            return true;
        }
    }
    public function setLocationDefault($id) {
        $location = $this->findById($id);
        if($location['BusinessLocation']['is_default'] == 1) {
            $this->updateAll(array('BusinessLocation.is_default' => 0), array('BusinessLocation.id <>' => $location['BusinessLocation']['id']));
        }
    }
    public function suggestGlobalLocation($keyword, $no_address = 0)
    {
        //search location
        $parent_ids = $this->find('list', array(
            'conditions' => array(
                'BusinessLocation.parent_id' => 0,
                'BusinessLocation.enabled' => 1
            ),
            'fields' => array('BusinessLocation.id', 'BusinessLocation.id')
        ));
        $data = $this->find('all', array(
            'conditions' => array(
                "BusinessLocation.name LIKE '%$keyword%'",
                'BusinessLocation.enabled' => 1,
                'BusinessLocation.parent_id' => $parent_ids
            )
        ));
        
        $result = null;
        if($data != null)
        {
            foreach($data as $item)
            {
                $result[] = array(
                    'value' => $item['BusinessLocation']['id'],
                    'label' => $item['BusinessLocation']['name']
                );
            }
        }

        //search address
        /*if(!$no_address && $data == null)
        {
            $mBusiness = MooCore::getInstance()->getModel('Business.Business');
            $data = $mBusiness->find('all', array(
                'conditions' => array(
                    "Business.address LIKE '%$keyword%'",
                    'Business.status' => BUSINESS_STATUS_APPROVED
                )
            ));
        
            if($data != null)
            {
                foreach($data as $item)
                {
                    $result[] = array(
                       // 'value' => $item['Business']['business_location_id'],
                        'value' => '',
                        'label' => $item['Business']['address']
                    );
                }
            }
        }*/
        return $result;
    }
    
    public function isLocationExist($id, $enable = null)
    {
        $cond = array(
            'BusinessLocation.id' => $id
        );
        if(is_bool($enable))
        {
            $cond['BusinessLocation.enabled'] = $enable; 
        }
        return $this->hasAny($cond);
    }
    
    public function updateBusinessCounter($location_id)
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $total = $mBusiness->find('count', array(
            'conditions' => array(
                'Business.business_location_id' => $location_id,
                'Business.status' => BUSINESS_STATUS_APPROVED
            )
        ));

        $this->updateAll(array(
            'BusinessLocation.business_count' => $total
        ), array(
            'BusinessLocation.id' => $location_id
        ));
        return $total;
    }
    
    public function registerLocationKeyword($address, $lat = null, $lng = null, $postal_code = null, $country = null, $region = null, $city = null)
    {
        $mBusinessLocationSearch = MooCore::getInstance()->getModel('Business.BusinessLocationSearch');
        $alias = seoUrl($address);
        if(!$mBusinessLocationSearch->hasAny(array(
            'BusinessLocationSearch.alias' => $alias
        )))
        {
            $mBusinessLocationSearch->save(array(
                'address' => $address,
                'postal_code' => preg_replace('/\s+/', '', $postal_code),
                'alias' => $alias,
                'lat' => $lat,
                'lng' => $lng,
                'country' => $country,
                'region' => $region,
                'city' => $city,
            ));
        }
    }
    
    public function activeField($id, $task, $value)
    {
        $cond = array(
            'BusinessLocation.id' => $id,
        ); 
        $this->create();
        $this->updateAll(array(
            'BusinessLocation.'.$task => $value
        ), $cond);
    }
    
    public function getPopupLocation()
    {
        $this->recursive = -1;
        return $this->find('all', array(
            'conditions' => array(
                'BusinessLocation.on_popup' => 1
            ),
            'order' => array('BusinessLocation.is_default' => 'DESC')
        ));
    }
    
    public function setDefaultLocation($name = null, $id = null, $reset = false)
    {
        $default_location_name = CakeSession::read(BUSINESS_DEFAULT_LOCATION_NAME);
        if($default_location_name == null || $reset)
        {
            if(!empty($name))
            {
                $cat = $this->findByName($name);
            }
            else if(!empty($id))
            {
                $cat = $this->findById($id);
            }
            if(!empty($cat))
            {
                CakeSession::write(BUSINESS_DEFAULT_LOCATION_NAME, $cat['BusinessLocation']['name'], 9999999);
                CakeSession::write(BUSINESS_DEFAULT_LOCATION_ID, $cat['BusinessLocation']['id'], 9999999);
            }
            else 
            {
                CakeSession::write(BUSINESS_DEFAULT_LOCATION_NAME, $name, 9999999);
                CakeSession::write(BUSINESS_DEFAULT_LOCATION_ID, '', 9999999);
            }
        }
    }
    
    public function initDefaultLocation()
    {
        if($this->getDefaultLocationName() == null)
        {
            $this->recursive = -1;
            $location = $this->findByIsDefault(1);
            if($location != null)
            {
                CakeSession::write(BUSINESS_DEFAULT_LOCATION_NAME, $location['BusinessLocation']['name'], 9999999);
                CakeSession::write(BUSINESS_DEFAULT_LOCATION_ID, $location['BusinessLocation']['id'], 9999999);
            }
        }
    }
    
    public function getDefaultLocationName()
    {
        return CakeSession::read(BUSINESS_DEFAULT_LOCATION_NAME);;
    }
    
    public function getDefaultLocationId()
    {
        return CakeSession::read(BUSINESS_DEFAULT_LOCATION_ID);;
    }
    
    public function getDefalutLocationSeoName()
    {
        if(CakeSession::read(BUSINESS_DEFAULT_LOCATION_NAME) != '')
        {
            return seoUrl(CakeSession::read(BUSINESS_DEFAULT_LOCATION_NAME)).'-';
        }
        return null;
    }
    
    public function getCurrentLocationMap()
    {
        $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
        $location_name = CakeSession::read(BUSINESS_DEFAULT_LOCATION_NAME);
        $info = $businessHelper->getLngLatByAddress($location_name);
        $info['address'] = $location_name;
        return $info;
    }
    
    public function getLocationWeather($location)
    {
        $url = 'http://query.yahooapis.com/v1/public/yql?q=' . rawurlencode('select woeid from geo.places where text="' . $location . '"');
        $a = curl_init($url);
        curl_setopt($a, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($a, CURLOPT_SSL_VERIFYPEER, false);
        $b = curl_exec($a);
        $x = simplexml_load_string($b);
        curl_close($a);
        $woeid = (string) $x->results->place->woeid;
        
        $yql_weather = rawurlencode('select * from weather.forecast where woeid in(' . $woeid . ')');
        $url = 'http://query.yahooapis.com/v1/public/yql?q=' . $yql_weather . '&format=json';
        $weather = curl_init($url);
        curl_setopt($weather, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($weather, CURLOPT_SSL_VERIFYPEER, false);
        $jsonContent = curl_exec($weather);

        $jSonData = json_decode($jsonContent, true);
        $info = array(
            'location' => $location,
            'now' => 0,
            'high' => 0,
            'low' => 0,
            'text' => '',
            'image' => ''
        );
        
        //find current temp
        if(!empty($jSonData['query']['results']['channel']['item']['condition']['temp']))
        {
            $temp = $jSonData['query']['results']['channel']['item']['condition']['temp'];
            $info['now'] = $this->convertWeatherFtoC($temp);
        }
        
        //image
        if(!empty($jSonData['query']['results']['channel']['item']['condition']['code']))
        {
            $info['image'] = Router::url('/', true ).'business/images/weather/'.$jSonData['query']['results']['channel']['item']['condition']['code'].'.svg';
        }
        
        //find high low temp
        if(!empty($jSonData['query']['results']['channel']['item']['forecast']))
        {
            $curDate = strtotime(date('d M Y'));
            foreach($jSonData['query']['results']['channel']['item']['forecast'] as $item)
            {
                if(strtotime($item['date']) == strtotime(date('d M Y')))
                {
                    $info['high'] = $this->convertWeatherFtoC($item['high']);
                    $info['low'] = $this->convertWeatherFtoC($item['low']);
                    $info['text'] = $item['text'];
                    break;
                }
            }
        }
        return $info;
    }
    
    private function convertWeatherFtoC($value)
    {
        return round(($value - 32) / 1.8000);
    }
    
    public function getNearByCitys()
    {
        $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
        $mBusinessNearbyCity = MooCore::getInstance()->getModel('Business.BusinessNearbyCity');
        
        $location_name = $this->getDefaultLocationName();
        $data = $mBusinessNearbyCity->findByName($location_name);
        if($data != null)
        {
            $data = $data['BusinessNearbyCity'];
            return $businessHelper->findNearByCity($data['lat'], $data['lng']);
        }
        else
        {
            $data = $businessHelper->getLngLatByAddress($location_name);
            $result = $businessHelper->findNearByCity($data['lat'], $data['lng']);
            $mBusinessNearbyCity->save(array(
                'name' => $location_name,
                'lat' => $data['lat'],
                'lng' => $data['lng'],
                'content' => json_encode($result)
            ));
            return $result;
        }
    }
    
    public function isLocationNameExist($name)
    {
        return $this->hasAny(array(
            'LOWER(BusinessLocation.name)' => trim(strtolower($name))
        ));
    }
    
    public function isLocationSearchAddressExist($name)
    {
        $mBusinessLocationSearch = MooCore::getInstance()->getModel('Business.BusinessLocationSearch');
        return $mBusinessLocationSearch->hasAny(array(
            'LOWER(BusinessLocationSearch.address)' => trim(strtolower($name))
        ));
    }
    
    public function getLocaionSearchByPostalCode($postal_code)
    {
        $postal_code = trim($postal_code);
        $postal_code = preg_replace('/\s+/', '', $postal_code);
        $mBusinessLocationSearch = MooCore::getInstance()->getModel('Business.BusinessLocationSearch');
        return $mBusinessLocationSearch->findByPostalCode($postal_code);
    }
    
    public function getLocaionSearchByAddress($address)
    {
        $mBusinessLocationSearch = MooCore::getInstance()->getModel('Business.BusinessLocationSearch');
        return $mBusinessLocationSearch->find('first', array('conditions' => array('LOWER(BusinessLocationSearch.address)' => trim(strtolower($address)))));
    }
    public function getLocations($id = null, $parent_id = null, $is_highlight = null, $alphabet = null, $threaded = false)
    {
        $cond = array(
            'BusinessLocation.enabled' => 1
        );
        $order = array(
            'BusinessLocation.business_count' => 'DESC'
        );
        if($id > 0)
        {
            $cond['BusinessLocation.id'] = $id;
        }
        if(is_numeric($parent_id))
        {
            $cond['BusinessLocation.parent_id'] = $parent_id;
        }
        $find = 'all';
        if($threaded)
        {
            $find = 'threaded';
        }
        $data = $this->find($find, array(
            'conditions' => $cond,
            'order' => $order
        ));
        if($id > 0)
        {
            return $data[0];
        }
        return $data;
    }

    public function getMapLocations($parent_id = 0)
    {
        $parent = $this->find('all', array(
            'conditions' => array('BusinessLocation.parent_id' => $parent_id, 'BusinessLocation.enabled' => 1),
           // '''fields' => array('BusinessLocation.name'),
            'order' => array(
                'BusinessLocation.business_count' => 'DESC',
                'BusinessLocation.id' => 'ASC',
            )
        ));
        if($parent != null)
        {
            foreach($parent as $k => $item)
            {
                $parent[$k]['children'] = $this->find('all', array(
                    'conditions' => array('BusinessLocation.parent_id' => $item['BusinessLocation']['id'], 'BusinessLocation.enabled' => 1),
                    // 'fields' => array('BusinessLocation.name'),
                    'order' => array(
                        'BusinessLocation.id' => 'DESC',
                        'BusinessLocation.id' => 'ASC',
                    )
                ));
            }
        }
        return $parent;
    }
}