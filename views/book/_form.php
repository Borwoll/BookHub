<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

?>

<div class="book-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-md-8">
            <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]); ?>

            <?php echo $form->field($model, 'year')->input('number', [
                'min' => 1000,
                'max' => date('Y') + 10,
            ]); ?>

            <?php echo $form->field($model, 'description')->textarea(['rows' => 6]); ?>

            <?php echo $form->field($model, 'isbn')->textInput(['maxlength' => true]); ?>

            <div class="form-group">
                <label class="form-label">Авторы *</label>
                <?php foreach ($authors as $author) { ?>
                    <div class="form-check">
                        <?php echo Html::checkbox(
                            'Book[author_ids][]',
                            in_array($author->id, $selectedAuthors),
                            [
                                'value' => $author->id,
                                'class' => 'form-check-input',
                                'id' => 'author-' . $author->id,
                            ],
                        ); ?>
                         <label class="form-check-label" for="author-<?php echo $author->id; ?>">
                            <?php echo Html::encode($author->full_name); ?>
                        </label>
                    </div>
                <?php } ?>
                <?php if (empty($authors)) { ?>
                    <p class="text-muted">
                        Нет доступных авторов.
                        <?php echo Html::a('Добавить автора', ['/author/create'], ['class' => 'btn btn-outline-primary btn-sm']); ?>
                    </p>
                <?php } ?>
            </div>
        </div>

        <div class="col-md-4">
            <?php echo $form->field($model, 'photo_file')->fileInput([
                            'accept' => 'image/*',
                        ])->label('Обложка книги'); ?>

            <?php if ($model->cover_photo) { ?>
                <div class="current-photo mb-3">
                    <label class="form-label">Текущая обложка:</label><br>
                    <?php echo Html::img('@web/' . $model->cover_photo, [
                                    'class' => 'img-fluid',
                                    'style' => 'max-width: 200px;',
                                ]); ?>
                </div>
            <?php } ?>

            <div class="alert alert-info">
                <small>
                    <strong>Требования к изображению:</strong><br>
                    • Форматы: PNG, JPG, JPEG, GIF<br>
                    • Максимальный размер: 2 МБ<br>
                    • Рекомендуемые размеры: 300x400 пикселей
                </small>
            </div>
        </div>
    </div>

    <div class="form-group mt-3">
        <?php echo Html::submitButton('Сохранить', ['class' => 'btn btn-success']); ?>
        <?php echo Html::a('Отмена', ['index'], ['class' => 'btn btn-secondary']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const authorCheckboxes = document.querySelectorAll('input[name="Book[author_ids][]"]');

    form.addEventListener('submit', function(e) {
        const checkedAuthors = document.querySelectorAll('input[name="Book[author_ids][]"]:checked');
        if (checkedAuthors.length === 0) {
            e.preventDefault();
            alert('Пожалуйста, выберите хотя бы одного автора для книги.');
        }
    });
});
</script>
