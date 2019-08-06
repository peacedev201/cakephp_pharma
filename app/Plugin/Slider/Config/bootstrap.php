<?php
App::uses('SliderListener', 'Slider.Lib');
CakeEventManager::instance()->attach(new SliderListener());
?>