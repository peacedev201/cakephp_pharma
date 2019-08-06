<?php 
switch ($contest['Contest']['type']) {
    case 'photo':
        echo $this->element('ajax/submit_entry_photo');
        break;
    case 'music':
        echo $this->element('ajax/submit_entry_music');
        break;
    case 'video':
        echo $this->element('ajax/submit_entry_video');
        break;
    default:
        break;
}
