<?php

declare(strict_types=1);

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Подписка на автора';
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Html::encode($model->name), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="author-subscribe">
    <h1><?php echo Html::encode($this->title); ?></h1>

    <p>Подпишитесь на уведомления о новых книгах автора <strong><?php echo Html::encode($model->name); ?></strong>.</p>

    <?php $form = ActiveForm::begin(['id' => 'subscribe-form']); ?>

    <div class="mb-3">
        <label class="form-label" for="phone">Номер телефона</label>
        <input type="text" id="phone" name="phone" class="form-control" placeholder="+7 999 123-45-67">
    </div>

    <div class="form-group">
        <?php echo Html::submitButton('Подписаться', ['class' => 'btn btn-primary']); ?>
        <?php echo Html::a('Отмена', ['view', 'id' => $model->id], ['class' => 'btn btn-outline-secondary ms-2']); ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
