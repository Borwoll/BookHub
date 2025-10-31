<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

?>

<div class="author-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-8">
            <?php echo $form->field($model, 'full_name')->textInput(['maxlength' => true, 'placeholder' => 'Фамилия Имя Отчество']); ?>

            <div class="alert alert-info">
                <small>
                    <strong>Рекомендации по заполнению:</strong><br>
                    • Указывайте полное ФИО автора<br>
                    • Используйте правильный регистр (Толстой Лев Николаевич)<br>
                    • Проверьте правильность написания перед сохранением
                </small>
            </div>
        </div>

        <div class="col-md-4">
            <?php if ($model->isNewRecord === false) { ?>
                <div class="card">
                    <div class="card-header">
                        <h6>Статистика автора</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Книг в каталоге:</strong> <?php echo count($model->books ?? []); ?></p>
                        <p><strong>Подписчиков:</strong> <?php echo count($model->activeSubscriptions ?? []); ?></p>
                        <p><strong>Дата добавления:</strong> <?php echo date('d.m.Y', $model->created_at); ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="form-group mt-3">
        <?php echo Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => 'btn btn-success']); ?>
        <?php echo Html::a('Отмена', ['index'], ['class' => 'btn btn-secondary']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
