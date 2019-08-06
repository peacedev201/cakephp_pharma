<?php
class ForumFile extends ForumAppModel{

    public $actsAs = array(
        'Storage.Storage' => array(
            'type'=>array(
                'forum_files'=>'file_name',
            ),
        ),
    );

    public function getFiles($id){
       $results = $this->find('all', array(
            'conditions' => array(
                'ForumFile.target_id' => $id
            )
        ) );
       return $results;
    }

    public function deleteFile( $file )
    {
        if ( file_exists(WWW_ROOT . 'uploads' . DS . 'forums'. DS. 'files' . DS . $file['ForumFile']['file_name']) )
            unlink(WWW_ROOT . 'uploads' . DS . 'forums'. DS. 'files' . DS . $file['ForumFile']['file_name']);

        $this->delete( $file['ForumFile']['id'] );
    }
}