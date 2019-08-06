<?php
Router::connect('/reactions/:action/*', array(
    'plugin' => 'Reaction',
    'controller' => 'reactions'
));

Router::connect('/reactions/*', array(
    'plugin' => 'Reaction',
    'controller' => 'reactions',
    'action' => 'index'
));

// --- /:objectType/reaction/:item_id/:reaction_type
Router::connect('/api/:objectType/reaction/:id/:reactionType', array(
    'plugin' => 'Reaction',
    'controller' => 'api_reactions',
    '[method]' => 'POST',
    'action' => 'add',
    'ext' => 'json',
));


// --- /:objectType/reaction/delete/:item_id/:reaction_type
Router::connect('/api/:objectType/reaction/delete/:id/:reactionType', array(
    'plugin' => 'Reaction',
    'controller' => 'api_reactions',
    '[method]' => 'POST',
    'action' => 'delete',
    'ext' => 'json',
));

// --- /:objectType/reaction/view/:item_id/:reaction_type
Router::connect('/api/:objectType/reaction/view/:id/:reactionType', array(
    'plugin' => 'Reaction',
    'controller' => 'api_reactions',
    '[method]' => 'GET',
    'action' => 'view',
    //'ext' => 'json',
),
    array(
        'id' => '[0-9]+',
    )
);

// --- /:objectType/reaction/view/:item_id/:reaction_type
Router::connect('/api/:objectType/reaction/view_more/:id/:reactionType', array(
    'plugin' => 'Reaction',
    'controller' => 'api_reactions',
    '[method]' => 'GET',
    'action' => 'view_more',
    //'ext' => 'json',
),
    array(
        'id' => '[0-9]+',
    )
);
// --- END LIKES
