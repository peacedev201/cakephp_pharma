<?php
App::uses('CakeEventListener', 'Event');

class SpotlightListener implements CakeEventListener
{
    public function implementedEvents()
    {
        return array(
            'MooView.beforeRender' => 'beforeRender',
            'UserController.deleteUserContent' => 'deleteUserContent',
        );
    }

    public function deleteUserContent($event) {

        App::import('Spotlight.Model', 'SpotlightTransaction');
        $this->SpotlightTransaction = new SpotlightTransaction();
        $items_trans = $this->SpotlightTransaction->findAllByUserId($event->data['aUser']['User']['id']);
        foreach ($items_trans as $item_tran) {
            $this->SpotlightTransaction->delete($item_tran['SpotlightTransaction']['id']);
        }

        App::import('Spotlight.Model', 'SpotlightUser');
        $this->SpotlightUser = new SpotlightUser();
        $items = $this->SpotlightUser->findAllByUserId($event->data['aUser']['User']['id']);
        foreach ($items as $item) {
            $this->SpotlightUser->delete($item['SpotlightUser']['id']);
        }

    }

    public function beforeRender($event)
    {
        if(Configure::read('Spotlight.spotlight_enabled')){
            $e = $event->subject();

            $e->Helpers->Html->css(array(
                'Spotlight.js_carousel',
                'Spotlight.main',
            ),
                array('block' => 'css')
            );

            $min = '';
            if (Configure::read('debug') == 0){
                $min="min.";
            }

            $e->Helpers->MooRequirejs->addPath(array(
                'mooSpotlight' => $e->Helpers->MooRequirejs->assetUrlJS("Spotlight.js/main.{$min}js"),
                'mooCarousel' => $e->Helpers->MooRequirejs->assetUrlJS("Spotlight.js/js_carousel.js"),
            ));

            $e->Helpers->MooRequirejs->addShim(array(
                'mooCarousel'=>array("deps" =>array('jquery')),
            ));

            $e->Helpers->MooPopup->register('themeModal');
        }
    }
}

    