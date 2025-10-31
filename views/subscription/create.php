<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Подписаться на автора';
$this->params['breadcrumbs'][] = ['label' => 'Подписка на авторов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subscription-create">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <div class="row">
        <div class="col-md-6">
            <?php $form = ActiveForm::begin(); ?>

            <?php if ($model->author_id) { ?>
                <?php
                $selectedAuthor = app\models\Author::findOne($model->author_id);
                if ($selectedAuthor) { ?>
                    <div class="alert alert-info">
                        <strong>Выбранный автор:</strong> <?php echo Html::encode($selectedAuthor->full_name); ?>
                    </div>
                    <?php echo Html::hiddenInput('SubscriptionForm[author_id]', $model->author_id); ?>
                <?php } ?>
            <?php } else { ?>
                <?php echo $form->field($model, 'author_id')->dropDownList(
                    yii\helpers\ArrayHelper::map($authors, 'id', 'full_name'),
                    ['prompt' => 'Выберите автора...'],
                )->label('Автор'); ?>
            <?php } ?>

            <?php echo $form->field($model, 'phone')->textInput([
                'placeholder' => '+79161234567',
                'pattern' => '\+?[1-9]\d{1,14}',
                'title' => 'Введите номер в международном формате',
            ])->label('Номер телефона'); ?>

            <div class="form-group">
                <?php echo Html::submitButton('Подписаться', ['class' => 'btn btn-success']); ?>
                <?php echo Html::a('Отмена', ['index'], ['class' => 'btn btn-secondary']); ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>О подписке</h5>
                </div>
                <div class="card-body">
                    <p>
                        После подписки вы будете получать SMS уведомления на указанный номер телефона
                        каждый раз, когда в каталог будет добавлена новая книга выбранного автора.
                    </p>

                    <h6>Что вы получите:</h6>
                    <ul>
                        <li>Мгновенные уведомления о новых книгах</li>
                        <li>Название книги и автора</li>
                        <li>Ссылку для подробной информации</li>
                    </ul>

                    <div class="alert alert-warning">
                        <small>
                            <strong>Важно:</strong><br>
                            • Убедитесь, что номер телефона указан правильно<br>
                            • Используйте международный формат (+79161234567)<br>
                            • На один номер можно подписаться на несколько авторов<br>
                            • SMS отправляются бесплатно через тестовый сервис
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
