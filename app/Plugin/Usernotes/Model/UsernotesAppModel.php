<?php 
App::uses('AppModel', 'Model');
class UsernotesAppModel extends AppModel{
    function getLastQuery()
{
    $dbo = $this->getDatasource();
    $logs = $dbo->getLog();
    $lastLog = end($logs['log']);
    return $lastLog['query'];
}
}