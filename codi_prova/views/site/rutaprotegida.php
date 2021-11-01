<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\captcha\Captcha;

$this->title = 'Ruta protegida';
?>
<div class="">
  <h1>Estas veient la ruta protegida</h1>
  <p> Hola, <?php echo Yii::$app->user->identity->username ?>!!</p>
</div>
