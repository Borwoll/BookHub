<?php

use yii\bootstrap5\Html;

$this->title = 'Добавить книгу';
$this->params['breadcrumbs'][] = ['label' => 'Каталог книг', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-create">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <?php echo $this->render('_form', [
        'model' => $model,
        'authors' => $authors,
        'selectedAuthors' => [],
    ]); ?>

</div>
