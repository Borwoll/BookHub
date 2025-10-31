<?php

use yii\bootstrap5\Html;

?>

<div class="card mb-3">
    <div class="row g-0">
        <div class="col-md-2">
            <?php if ($model->cover_photo) { ?>
                <?php echo Html::img('@web/' . $model->cover_photo, [
                    'class' => 'img-fluid rounded-start',
                    'style' => 'max-height: 150px; object-fit: cover;',
                ]); ?>
            <?php } else { ?>
                <div class="bg-light d-flex align-items-center justify-content-center rounded-start" style="height: 150px;">
                    <span class="text-muted">Нет обложки</span>
                </div>
            <?php } ?>
        </div>
        <div class="col-md-8">
            <div class="card-body">
                <h5 class="card-title">
                    <?php echo Html::a(Html::encode($model->title), ['view', 'id' => $model->id]); ?>
                </h5>
                <p class="card-text">
                    <strong>Авторы:</strong> <?php echo Html::encode($model->getAuthorsNames()); ?><br>
                    <strong>Год:</strong> <?php echo Html::encode($model->year); ?><br>
                    <?php if ($model->isbn) { ?>
                        <strong>ISBN:</strong> <?php echo Html::encode($model->isbn); ?><br>
                    <?php } ?>
                </p>
                <?php if ($model->description) { ?>
                    <p class="card-text">
                        <?php echo Html::encode(mb_substr($model->description, 0, 200)); ?>
                        <?php if (mb_strlen($model->description) > 200) { ?>...<?php } ?>
                    </p>
                <?php } ?>
                <p class="card-text">
                    <small class="text-muted">
                        Добавлено: <?php echo date('d.m.Y', $model->created_at); ?>
                    </small>
                </p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card-body">
                <?php echo Html::a('Подробнее', ['view', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']); ?>
                <?php if (Yii::$app->user->isGuest === false && Yii::$app->user->identity->canManageBooks()) { ?>
                    <div class="mt-2">
                        <?php echo Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-secondary btn-sm']); ?>
                    </div>
                    <div class="mt-1">
                        <?php echo Html::a('Удалить', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-outline-danger btn-sm',
                            'data' => [
                                'confirm' => 'Вы уверены, что хотите удалить эту книгу?',
                                'method' => 'post',
                            ],
                        ]); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
