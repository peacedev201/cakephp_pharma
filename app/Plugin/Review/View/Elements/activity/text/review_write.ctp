<?php
$aReviewUser = $object;
$aReview = MooCore::getInstance()->getItemByType('Review.Review', $aReviewUser['ReviewUser']['review_id']);
$aReview['User']['moo_href'] = $aReview['User']['moo_href'] . '?tab=reviews';
?>

<span><?php echo __d('review', 'wrote a review'); ?></span>&nbsp;<?php echo $this->Moo->getName($aReview['User']); ?>