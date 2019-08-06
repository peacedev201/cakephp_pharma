<?php
define('PRODUCT_PHOTO_TINY_WIDTH', 83);
define('PRODUCT_PHOTO_TINY_HEIGHT', 83);
define('PRODUCT_PHOTO_THUMB_WIDTH', 460);
define('PRODUCT_PHOTO_THUMB_HEIGHT', 460);
define('PRODUCT_PHOTO_LARGE_WIDTH', 800);
define('PRODUCT_PHOTO_LARGE_HEIGHT', 800);
define('PRODUCT_UPLOAD_MIN_WIDTH', 600);
define('PRODUCT_MAX_RATING', 5);
define('ORDER_CODE_PREFIX', 'OR-');
define('PRODUCT_UPLOAD_DIR', 'uploads/products/');
define('PRODUCT_UPLOAD_URL', '/'.PRODUCT_UPLOAD_DIR);

define('ORDER_GATEWAY_CHEQUE', 'cheque');
define('ORDER_GATEWAY_CHEQUE_STORE', 'cheque_store');
define('ORDER_GATEWAY_PAYPAL', 'paypal');
define('ORDER_GATEWAY_PAYPAL_STORE', 'paypal_store');
define('ORDER_GATEWAY_CREDIT', 'credits');

define('ORDER_STATUS_NEW', 'NEW');
define('ORDER_STATUS_PROCESSING', 'PROCESSING');
define('ORDER_STATUS_PENDING', 'PENDING');
define('ORDER_STATUS_CANCELLED', 'CANCELLED');
define('ORDER_STATUS_REFUNDED', 'REFUNDED');
define('ORDER_STATUS_COMPLETED', 'COMPLETED');

define('PRODUCT_SORT_MOST_RECENT', 'most_recent');
define('PRODUCT_SORT_NAME_ASC', 'name_asc');
define('PRODUCT_SORT_NAME_DESC', 'name_desc');
define('PRODUCT_SORT_PRICE_ASC', 'price_asc');
define('PRODUCT_SORT_PRICE_DESC', 'price_desc');
define('PRODUCT_SORT_RATING_ASC', 'rating_asc');
define('PRODUCT_SORT_RATING_DESC', 'rating_desc');

define('CURRENCY_POSITION_LEFT', 'left');
define('CURRENCY_POSITION_RIGHT', 'right');

define('STORE_PERMISSION_CREATE_STORE', 'store_create_store');
define('STORE_PERMISSION_VIEW_PRODUCT_DETAIL', 'store_view_product_detail');
define('STORE_PERMISSION_BUY_PRODUCT', 'store_buy_product');
define('STORE_PERMISSION_CREDIT', 'credit_use');

define('STORE_PHOTO_THUMB_WIDTH', 300);
define('STORE_PHOTO_THUMB_HEIGHT', 300);
define('STORE_PHOTO_TINY_WIDTH', 83);
define('STORE_PHOTO_TINY_HEIGHT', 83);
define('STORE_UPLOAD_DIR', 'uploads/stores');
define('STORE_UPLOAD_URL', STORE_UPLOAD_DIR);

define('TRANSACTION_STATUS_INITIAL', 'initial');
define('TRANSACTION_STATUS_COMPLETED', 'completed');
define('TRANSACTION_STATUS_PENDING', 'pending');
define('TRANSACTION_STATUS_EXPIRED', 'expired');
define('TRANSACTION_STATUS_REFUNDED', 'refuned');
define('TRANSACTION_STATUS_FAILED', 'failed');
define('TRANSACTION_STATUS_CANCEL', 'cancel');
define('TRANSACTION_STATUS_INACTIVE', 'inactive');

define('STORE_SHIPPING_FREE', 'free_shipping');
define('STORE_SHIPPING_PER_ITEM', 'per_item_shipping');
define('STORE_SHIPPING_PICKUP', 'pickup_from_store');
define('STORE_SHIPPING_FLAT', 'flat_shipping_rate');
define('STORE_SHIPPING_WEIGHT', 'weight_based_shipping');

define('STORE_PACKAGE_FEATURED_PRODUCT_ID', 1);
define('STORE_PACKAGE_FEATURED_STORE_ID', 2);

define('STORE_PRODUCT_TYPE_REGULAR', 'regular');
define('STORE_PRODUCT_TYPE_DIGITAL', 'digital');
define('STORE_PRODUCT_TYPE_LINK', 'link');

define('STORE_DIGITAL_PRODUCT_UPLOAD_DIR', 'uploads/products/files/');
define('STORE_DIGITAL_PRODUCT_UPLOAD_URL', '/'.STORE_DIGITAL_PRODUCT_UPLOAD_DIR);

define('STORE_VIDEO_UPLOAD_DIR', 'uploads/products/videos/');
define('STORE_VIDEO_UPLOAD_URL', '/'.STORE_VIDEO_UPLOAD_DIR);
define('STORE_ALLOW_VIDEO_EXTENSION', 'mp4');

//for credit
define('STORE_SHOW_MONEY_TYPE_NORMAL', 1);
define('STORE_SHOW_MONEY_TYPE_CREDIT', 2);
define('STORE_SHOW_MONEY_TYPE_ALL', 3);

define('STORE_PRODUCT_ACTIVITY_TYPE', 'Store_Store_Product');
define('STORE_PRODUCT_ACTIVITY_REVIEW_ITEM', 'Store_Store_Product');
define('STORE_PRODUCT_ACTIVITY_REVIEW_ACTION', 'product_review');

//for paypal
define('STORE_PAYPAL_TYPE_ADAPTIVE', 1);
define('STORE_PAYPAL_TYPE_EXPRESS', 2);