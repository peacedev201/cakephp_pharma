<?php
App::uses('Widget','Controller/Widgets');

class winnerContestWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$contest = MooCore::getInstance()->getSubject();
    	$entryModel = MooCore::getInstance()->getModel('Contest.ContestEntry');
        $uid = MooCore::getInstance()->getViewer(true);
        $win_entries = $entryModel->getWinningEntries($contest);
        $this->setData('win_entries', $win_entries);
        $this->setData('contest', $contest);
    }
}