<?php

Router::connect("/profile_completions/:action/*",array('plugin'=>'profile_completion','controller'=>'profile_completions'));
Router::connect("/profile_completions/*",array('plugin'=>'profile_completion','controller'=>'profile_completions','action'=>'index'));

?>
