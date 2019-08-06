<?php

App::uses('AppController', 'Controller');

class StickerAppController extends AppController {

    protected function _redirectError($msg, $url)
    {
        if ($msg != null)
        {
            $this->Session->setFlash($msg, 'default', array(
                'class' => 'Metronic-alerts alert alert-danger fade in'
            ));
        }
        $this->redirect($url);
    }

    protected function _redirectSuccess($msg, $url)
    {
        if ($msg != null)
        {
            $this->Session->setFlash($msg, 'default', array(
                'class' => 'Metronic-alerts alert alert-success fade in'
            ));
        }
        $this->redirect($url);
    }

    protected function _jsonSuccess($msg, $flashMsg = false, $params = null)
    {
        $this->autoRender = false;
        if ($flashMsg)
        {
            $this->Session->setFlash($msg, 'default', array(
                'class' => 'Metronic-alerts alert alert-success fade in'
            ));
        }
        $data = array(
            'result' => 1,
            'message' => $msg
        );
        if ($params != null)
        {
            $data = array_merge($data, $params);
        }
        echo json_encode($data);
        exit;
    }

    protected function _jsonError($msg, $flashMsg = false, $params = null)
    {
        $this->autoRender = false;
        if ($flashMsg)
        {
            $this->Session->setFlash($msg, 'default', array(
                'class' => 'Metronic-alerts alert alert-danger fade in'
            ));
        }
        $data = array(
            'result' => 0,
            'message' => $msg
        );
        if ($params != null)
        {
            $data = array_merge($data, $params);
        }

        echo json_encode($data);
        exit;
        ;
    }

    protected function generateOrdering($model_name)
    {
        $this->loadModel($model_name);
        $cond = array();
        $item = $this->$model_name->find('first', array(
            'conditions' => $cond,
            'order' => array($model_name . '.ordering DESC')
        ));
        if ($item != null)
        {
            return $item[$model_name]['ordering'] + 1;
        }
        return 1;
    }

    protected function _prepareDir($path)
    {
        $path = WWW_ROOT . $path;

        if (!file_exists($path))
        {
            mkdir($path, 0777, true);
            file_put_contents($path . DS . 'index.html', '');
        }
    }

}
