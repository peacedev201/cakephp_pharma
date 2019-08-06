<?php
if (!empty($tags))
{
    echo '<ul class="tags">';
	foreach ($tags as $tag){
		if(is_array($tag)){
			$tag = $tag['Tag']['tag'];
		}
		echo '<li><a href="' . $this->request->webroot . 'forums/topic/search/'.h($tag).'/hashtag'.'">'.h($tag).'</a></li>';
	}
    echo '</ul>';
}
else
	echo __d('forum','Nothing found');
?>