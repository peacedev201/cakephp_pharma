<?php
if(Configure::read('Feedback.feedback_enabled'))
{
    Router::connect('/feedbacks/popups/:action/*', array(
        'plugin' => 'Feedback',
        'controller' => 'feedbackPopups',
    ));
    
    Router::connect('/feedbacks/feedbacks/:action/*', array(
        'plugin' => 'Feedback',
        'controller' => 'feedbacks'
    ));

    Router::connect('/feedbacks/feedbacks/*', array(
        'plugin' => 'Feedback',
        'controller' => 'feedbacks',
        'action' => 'index'
    ));
    
    Router::connect('/feedbacks/feedbackvotes/:action/*', array(
        'plugin' => 'Feedback',
		'controller' => 'FeedbackVotes',
    ));

    Router::connect('/feedbacks/feedbackvotes/*', array(
        'plugin' => 'Feedback',
		'controller' => 'FeedbackVotes',
        'action' => 'index'
    ));
    
    Router::connect('/feedbacks/cat/*', array(
        'plugin' => 'Feedback',
        'controller' => 'feedbacks',
        'action' => 'index'
    ));
    
    Router::connect('/feedbacks/:action/*', array(
        'plugin' => 'Feedback',
        'controller' => 'feedbacks'
    ));

    Router::connect('/feedbacks/*', array(
        'plugin' => 'Feedback',
        'controller' => 'feedbacks',
        'action' => 'index'
    ));
}