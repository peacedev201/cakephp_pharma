<?php 
/*if($item_object != null)
{
    $sender = $this->Gift->getUserById($item_object["GiftSents"]["sender_id"]);
    $receiver = $this->Gift->getUserById($item_object["GiftSents"]["receiver_id"]);
    $sender_text = $receiver_text = "";
    if($sender != null)
    {
        $sender_text = '<a href="'.$sender['User']['moo_href'].'">'.$sender['User']['moo_title'].'</a>';
    }
    if($receiver != null)
    {
        $receiver_text = '<a href="'.$receiver['User']['moo_href'].'">'.$receiver['User']['moo_title'].'</a>';
    }
    echo sprintf(__d('gift','%s sent gift to %s'), $sender_text, $receiver_text);
}
else 
{
    echo '<span class="notice_red">' . __d('gift', 'deleted') . '</span>';
}*/
echo __d('gift','sent gift to friend');
?>
