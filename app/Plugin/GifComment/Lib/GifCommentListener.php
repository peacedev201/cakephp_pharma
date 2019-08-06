<?php

App::uses('CakeEventListener', 'Event');

class GifCommentListener implements CakeEventListener {

    public function implementedEvents() {
        return array(
            'MooView.beforeRender' => 'beforeRender',
            'Element.activities.afterRenderCommentForm' => 'renderGif',
            'View.Elements.activityForm.afterRenderItems' => 'renderGifPostFeed',
        );
    }

    public function beforeRender($e) {
        if (Configure::read('GifComment.gif_comment_enabled')) {
            $v = $e->subject();
			
			if ($v->request->is('androidApp') || $v->request->is('iosApp')) return;
			
            $v->Helpers->Html->css(array(
                'GifComment.gifcm'
                    ), array('block' => 'css')
            );

            if (Configure::read('debug') == 0) {
                $min = "min.";
            } else {
                $min = "";
            }
            $v->Helpers->MooRequirejs->addPath(array(
                "mooGifComment" => $v->Helpers->MooRequirejs->assetUrlJS("GifComment.js/gifcm.{$min}js"),
                "mooGifPostFeed" => $v->Helpers->MooRequirejs->assetUrlJS("GifComment.js/gifpf.{$min}js"),
            ));

            $v->addPhraseJs(array(
                //'delete_question_confirm' => __d('question', 'Are you sure you want to delete this question?')
            ));

        }
    }

    public function renderGif($e) {
        $v = $e->subject();
		if ($v->request->is('androidApp') || $v->request->is('iosApp')) return;
        echo $v->element('GifComment.load_gif', array('gId' => $e->data['id'],'typeId' => $e->data['type']));
    }
    public function renderGifPostFeed($e) {
        $v = $e->subject();
		if ($v->request->is('androidApp') || $v->request->is('iosApp')) return;
        echo $v->element('GifComment.post_feed', array());
    }


}
