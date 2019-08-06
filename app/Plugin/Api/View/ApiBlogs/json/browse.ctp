<?php
$blogHelper = MooCore::getInstance()->getHelper('Blog_Blog');
$photoSizes = explode('|', Configure::read('core.photo_image_sizes'));

if (empty($blogs)) {
    throw new ApiNotFoundException(__d('api', 'Blog not found'));
}
else {
    foreach ($blogs as $blog):
        $imageArray = array();
        foreach ($photoSizes as $size) {
            $imageArray[$size] = $blogHelper->getImage($blog, array('prefix' => $size));
        }
        $blogArrayTmp = array(
            'id' => $blog['Blog']['id'],
            'title' => h($blog['Blog']['title']),
            'body' => $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $blog['Blog']['body'])), 200, array('eclipse' => '')), Configure::read('Blog.blog_hashtag_enabled')),
            'thumbnail' => $imageArray,
            'privacy' => $blog['Blog']['privacy'],
            'categoryId' => $blog['Blog']['category_id'],
            'commentCount' => $blog['Blog']['comment_count'],
            'shareCount' => $blog['Blog']['share_count'],
            'likeCount' => $blog['Blog']['like_count'],
            'dislikeCount' => $blog['Blog']['dislike_count'],
            'userId' => $blog['Blog']['user_id'],
            'userName' => $blog['User']['name'],
            'userUrl' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($blog['User']['moo_href'], 'UTF-8', 'UTF-8')) ,
            'type' => $blog['Blog']['moo_type'],
            'url' => FULL_BASE_URL .  str_replace('?','',mb_convert_encoding($blog['Blog']['moo_href'], 'UTF-8', 'UTF-8')),
            'published' => h($blog['Blog']['created']),
            'publishedTranslated' => $this->Moo->getTime($blog['Blog']['created'], Configure::read('core.date_format'), $utz),
        );

        $afterRenderApiBlog = new CakeEvent("Api.View.ApiBlog.afterRenderApiBlogList", $this, array(
            'item_id' => $blog['Blog']['id'],
            'item_type' => $blog['Blog']['moo_type'],
            'data' => $blog,
            'data_json' => $blogArrayTmp,
        ));

        $this->getEventManager()->dispatch($afterRenderApiBlog);
        if(!empty($afterRenderApiBlog->result['result'])) {
            $result = $afterRenderApiBlog->result['result'];
            $blogArrayTmp = array_merge($blogArrayTmp, $result);
        }
        $blogArray[] = $blogArrayTmp;
    endforeach;
    echo json_encode($blogArray);
}