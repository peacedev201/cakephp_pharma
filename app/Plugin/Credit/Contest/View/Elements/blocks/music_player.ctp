<?php

$helper = MooCore::getInstance()->getHelper('Contest_Contest'); 
if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery", "mooContest"], function ($, mooContest) {
    mooContest.initPlayer('<?php echo $helper->getSourceUrl($entry) ?>');
    });</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooContest'), 'object' => array('$', 'mooContest'))); ?>
mooContest.initPlayer('<?php echo $helper->getSourceUrl($entry) ?>');
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
<?php
    echo $this->Html->css(array('Contest.jplayer.blue.monday.min'), null, array('inline' => false));
?>
<div id="jquery_jplayer_1" class="jp-jplayer"></div>
    <div id="jp_container_1" class="jp-audio" role="application" aria-label="media player">
        <div class="jp-type-single">
            <div class="jp-gui jp-interface">
				<div class="jp-cover" style="background:url(<?php echo $helper->getEntryImage($entry, array('prefix' => '1500')); ?>)">
					<div class="jp-mask"></div>
				</div>
				<div class="jp-progress">
                    <div class="jp-seek-bar">
                        <div class="jp-play-bar"></div>
                    </div>
                </div>
				<div class="jp-details">
					<div class="jp-title" aria-label="title"><?php echo $entry['ContestEntry']['caption']; ?></div>
				</div>
				<div class="jp-controls-container">
                <div class="jp-controls">
                    <button class="jp-play" role="button" tabindex="0"><?php echo __d('contest', 'play') ?></button>
                </div>
                
                <div class="jp-volume-controls">
                    <button class="jp-mute" role="button" tabindex="0"><?php echo __d('contest', 'mute') ?></button>
                    <button class="jp-volume-max" role="button" tabindex="0"><?php echo __d('contest', 'max volume') ?></button>
                    <div class="jp-volume-bar">
                        <div class="jp-volume-bar-value"></div>
                    </div>
                </div>
                <div class="jp-time-holder">
                    <div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div><span>/</span>
                    <div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
                    
                </div>
				<div class="jp-toggles">
                        <button class="jp-repeat" role="button" tabindex="0"><?php echo __d('contest', 'repeat') ?></button>
                    </div>
				</div>
            </div>
    </div>
</div>
