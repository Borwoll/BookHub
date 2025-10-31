<?php

use yii\bootstrap5\Html;
use yii\widgets\DetailView;

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Каталог книг', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-view">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <div class="mb-3">
        <?php if (Yii::$app->user->isGuest === false && Yii::$app->user->identity->canManageBooks()) { ?>
            <?php echo Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
            <?php echo Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены, что хотите удалить эту книгу?',
                    'method' => 'post',
                ],
            ]); ?>
        <?php } ?>
        <?php echo Html::a('Назад к списку', ['index'], ['class' => 'btn btn-outline-secondary']); ?>
    </div>

    <div class="row">
        <div class="col-md-3">
            <?php if ($model->cover_photo) { ?>
                <?php echo Html::img('@web/' . $model->cover_photo, [
                    'class' => 'img-fluid',
                    'alt' => Html::encode($model->title),
                ]); ?>
            <?php } else { ?>
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                    <span class="text-muted">Нет обложки</span>
                </div>
            <?php } ?>
        </div>
        <div class="col-md-9">
            <?php echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'Название',
                        'value' => $model->title,
                    ],
                    [
                        'label' => 'Авторы',
                        'value' => $model->getAuthorsNames() ?: 'Авторы не указаны',
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Год выпуска',
                        'value' => $model->year,
                    ],
                    [
                        'label' => 'ISBN',
                        'value' => $model->isbn ?: 'ISBN не указан',
                    ],
                    [
                        'label' => 'Описание',
                        'value' => $model->description ?: 'Описание не указано',
                        'format' => 'ntext',
                    ],
                    [
                        'label' => 'Дата добавления',
                        'value' => $model->createdAt ? date('d.m.Y H:i', $model->createdAt) : 'Не указана',
                    ],
                    [
                        'label' => 'Последнее обновление',
                        'value' => $model->updatedAt ? date('d.m.Y H:i', $model->updatedAt) : 'Не указана',
                    ],
                ],
            ]); ?>
        </div>
    </div>

    <?php if ($model->authors !== []) { ?>
        <div class="mt-4">
            <h4>Авторы книги</h4>
            <div class="row">
                <?php foreach ($model->authors as $author) { ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <?php echo Html::a(Html::encode($author->full_name), ['/author/view', 'id' => $author->id]); ?>
                                </h6>
                                <p class="card-text">
                                    <small class="text-muted">
                                        Книг в каталоге: <?php echo count($author->books ?? []); ?>
                                    </small>
                                </p>
                                <div class="mt-2">
                                    <?php echo Html::a('Подписаться на автора', ['/subscription/create', 'author_id' => $author->id], [
                                        'class' => 'btn btn-outline-primary btn-sm',
                                    ]); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

</div>
