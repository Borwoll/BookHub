<?php

declare(strict_types=1);

use app\models\User as AppUser;
use yii\rbac\DbManager;
use yii\web\User;

class Yii
{
    public static $app;
}

/**
 * @property DbManager $authManager
 * @property __WebUser|User $user
 */
class __Application {}

/**
 * @property AppUser $identity
 */
class __WebUser {}
