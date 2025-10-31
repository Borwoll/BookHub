<?php

use yii\bootstrap5\Html;

$this->title = 'Редактировать книгу: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Каталог книг', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="book-update">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <?php echo $this->render('_form', [
        'model' => $model,
        'authors' => $authors,
        'selectedAuthors' => $selectedAuthors,
    ]); ?>

</div>
