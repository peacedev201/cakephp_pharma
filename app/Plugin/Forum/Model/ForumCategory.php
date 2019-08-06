<?php
/**
 * Created by PhpStorm.
 * User: Social
 * Date: 12/14/2017
 * Time: 3:14 PM
 */
class ForumCategory extends ForumAppModel
{
    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->locale = Configure::read('Config.language');
    }
    public $actsAs = array(
        'Translate' => array('name' => 'nameTranslation'),
        'MooUpload.Upload' => array(
            'thumb' => array(
                'path' => '{ROOT}webroot{DS}uploads{DS}forums{DS}category_icons{DS}{field}{DS}',
                'thumbnailSizes' => array(
                    'size' => array()
                )
            ),

        ),
        'Storage.Storage' => array(
            'type'=>array('forum_category_thumb'=>'thumb'),
        ),
    );
    public $validationDomain = 'forum';
    public $mooFields = array('name','thumb','order');
    public $validate = array(
        'name' => array(
            'rule' => 'notBlank',
            'message' => 'Category\'s name is required',
        ),
    );
    public $recursive = 2;
    private $_default_locale = 'eng' ;
    public function setLanguage($locale) {
        $this->locale = $locale;
    }
    public function getForumCategoryById($id) {
        $forum_cat = $this->findById($id);
        if (empty($forum_cat)) {
            $this->locale = $this->_default_locale;
            $forum_cat = $this->findById($id);
        }
        return $forum_cat ;
    }
}