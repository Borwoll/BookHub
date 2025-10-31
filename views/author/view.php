<?php

use yii\bootstrap5\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;

$this->title = $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-view">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <div class="mb-3">
        <?php if (Yii::$app->user->isGuest === false && Yii::$app->user->identity->canManageBooks()) { ?>
            <?php echo Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
            <?php echo Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены, что хотите удалить этого автора?',
                    'method' => 'post',
                ],
            ]); ?>
        <?php } ?>

        <?php echo Html::a('Подписаться на автора', ['subscription/create', 'author_id' => $model->id], [
            'class' => 'btn btn-success',
        ]); ?>

        <?php echo Html::a('Назад к списку', ['index'], ['class' => 'btn btn-outline-secondary']); ?>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?php echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'full_name:text:ФИО автора',
                    [
                        'label' => 'Общее количество книг',
                        'value' => count($model->books ?? []),
                    ],
                    [
                        'label' => 'Количество подписчиков',
                        'value' => count($model->activeSubscriptions ?? []),
                    ],
                    [
                        'attribute' => 'created_at',
                        'label' => 'Дата добавления',
                        'value' => date('d.m.Y H:i', $model->created_at),
                    ],
                    [
                        'attribute' => 'updated_at',
                        'label' => 'Последнее обновление',
                        'value' => date('d.m.Y H:i', $model->updated_at),
                    ],
                ],
            ]); ?>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Подписка на уведомления</h5>
                </div>
                <div class="card-body">
                    <p>Подпишитесь на уведомления о новых книгах этого автора и получайте SMS на свой телефон!</p>
                    <?php echo Html::a('Подписаться', ['subscription/create', 'author_id' => $model->id], [
                        'class' => 'btn btn-success btn-block',
                    ]); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <h3>Книги автора</h3>

        <?php if (isset($booksDataProvider) === false) { ?>
            <div class="alert alert-warning">
                <p>Ошибка загрузки списка книг.</p>
            </div>
        <?php } elseif ($booksDataProvider->count > 0) { ?>
            <?php echo GridView::widget([
                'dataProvider' => $booksDataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute' => 'title',
                        'label' => 'Название',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::a(Html::encode($model->title), ['/book/view', 'id' => $model->id]);
                        },
                    ],

                    [
                        'attribute' => 'year',
                        'label' => 'Год',
                    ],

                    [
                        'attribute' => 'isbn',
                        'label' => 'ISBN',
                    ],

                    [
                        'label' => 'Соавторы',
                        'value' => function ($bookModel) use ($model) {
                            $authors = $bookModel->authors;
                            $coAuthors = [];
                            foreach ($authors as $author) {
                                if ($author->id !== $model->id) {
                                    $coAuthors[] = $author->full_name;
                                }
                            }

                            return ! empty($coAuthors) ? implode(', ', $coAuthors) : 'Нет';
                        },
                    ],

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            return ['/book/' . $action, 'id' => $model->id];
                        },
                    ],
                ],
            ]); ?>
        <?php } else { ?>
            <div class="alert alert-info">
                <p>У этого автора пока нет книг в каталоге.</p>
                <?php if (Yii::$app->user->isGuest === false && Yii::$app->user->identity->canManageBooks()) { ?>
                    <?php echo Html::a('Добавить книгу', ['/book/create'], ['class' => 'btn btn-primary']); ?>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

</div>
