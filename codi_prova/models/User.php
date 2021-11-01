<?php

namespace app\models;

use yii\db\ActiveRecord;

class User  extends ActiveRecord implements \yii\web\IdentityInterface
{


  public static function tableName()
  {
    return '{{user}}';
  }
  /**
   * {@inheritdoc}
   */
  public static function findIdentity($id)
  {
    return static::findOne($id);
  }

  /**
   * {@inheritdoc}
   */
  public static function findIdentityByAccessToken($token, $type = null)
  {
    throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
  }

  /**
   * Finds user by username
   *
   * @param string $username
   * @return static|null
   */
  public static function findByUsername($username)
  {

    return User::findOne(['username' => $username]);
  }

  /**
   * {@inheritdoc}
   */
  public function getId()
  {
    return $this->getPrimaryKey();
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthKey()
  {
    return $this->authKey;
  }

  /**
   * {@inheritdoc}
   */
  public function validateAuthKey($authKey)
  {
    return $this->authKey === $authKey;
  }

  /**
   * Validates password
   *
   * @param string $password password to validate
   * @return bool if password provided is valid for current user
   */
  public function validatePassword($password)
  {
    return \Yii::$app->getSecurity()->validatePassword($password, $this->passwordHash);
  }


  public function beforeSave($insert)
  {
    if (parent::beforeSave($insert)) {
      if ($this->isNewRecord) {
        $this->authKey = \Yii::$app->security->generateRandomString();
      }
      return true;
    }
    return false;
  }

  public function setPassword($password)
  {
    $this->passwordHash = \Yii::$app->security->generatePasswordHash($password);
  }

  /**
   * Generates "remember me" authentication key
   */
  public function generateAuthKey()
  {
    $this->authKey = \Yii::$app->security->generateRandomString();
  }
}
