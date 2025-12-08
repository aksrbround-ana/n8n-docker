<?php

namespace app\services;

use stdClass;
use Yii;
use app\models\Accountant;

class AuthService
{

    public const ACCESS_TOKEN_NAME = 'access_token';
    /**
     *
     * @return string
     */
    public static function encodePassword($password): string
    {
        return md5($password);
    }

    public static function getPermissions(Accountant $user)
    {
        return Yii::$app->authManager->getPermissionsByRole($user->rule);
    }

    public static function generateAccessToken()
    {
        return Yii::$app->security->generateRandomString(32);
    }
}
