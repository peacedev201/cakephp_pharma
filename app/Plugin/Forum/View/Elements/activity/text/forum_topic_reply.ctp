<?php
if(!empty($object['User']['id'])){
    $username = $this->Moo->getName($object['User']);
}else{
    $username = '<a class="username-deleted">'. __d('forum','Deleted Account').'</a>';
}
echo __d('forum',"just replied on %s's topic: %s",$username,'<a href="'.$object['ForumTopic']['moo_href'].'/reply_id:'.$activity['Activity']['target_id'].'">'.$object['ForumTopic']['moo_title'].'</a>');
?>