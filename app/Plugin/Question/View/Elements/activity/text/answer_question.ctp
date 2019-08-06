<?php
	echo __d('question',"answered %s's question: %s",$this->Moo->getName($object['User']),'<a href="'.$object['Question']['moo_href'].'">'.$object['Question']['moo_title'].'</a>');
?>