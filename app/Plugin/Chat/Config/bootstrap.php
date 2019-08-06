<?php

if (Configure::read('Chat.chat_disable')==0) {
    App::uses('ChatListener', 'Chat.Lib');
    CakeEventManager::instance()->attach(new ChatListener());
}