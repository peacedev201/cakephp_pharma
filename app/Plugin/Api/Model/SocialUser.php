<?php

App::uses('ApiAppModel', 'Api.Model');

/**
 * Social Model
 *
 * @property User $User
 */
class SocialUser extends ApiAppModel
{
    public function getSocialUser($params = array())
    {
        $cond = array();
        if (isset($params['provider']))
        {
            $cond['SocialUser.provider'] = $params['provider'];
        }
        if (isset($params['provider_uid']))
        {
            $cond['SocialUser.provider_uid'] = $params['provider_uid'];
        }
        if (isset($params['user_id']))
        {
            $cond['SocialUser.user_id'] = $params['user_id'];
        }
        if ($cond == null)
        {
            return array();
        }
        return $this->find('first', array(
                    'conditions' => $cond
        ));
    }

    public function generatePassword($length = 8)
    {
        // inicializa variables
        $password = "";
        $i = 0;
        $possible = "0123456789bcdfghjkmnpqrstvwxyz";

        // agrega random
        while ($i < $length)
        {
            $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);

            if (!strstr($password, $char))
            {
                $password .= $char;
                $i++;
            }
        }
        return $password;
    }
}
