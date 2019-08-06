<?php
$helper = MooCore::getInstance()->getHelper('Contest_Contest');
$win_credit = $helper->getCreditToWin($contest);
echo __d('contest', 'Join to win %s credits', $win_credit);