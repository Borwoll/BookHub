<?php

use yii\bootstrap5\Html;

$this->title = 'Каталог книг';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-index">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <?php if (isset($message)) { ?>
        <div class="alert alert-info">
            <?php echo Html::encode($message); ?>
        </div>
    <?php } ?>

    <div class="mb-3">
        <?php if (Yii::$app->user->isGuest === false) { ?>
            <?php echo Html::a('Добавить книгу', ['create'], ['class' => 'btn btn-success']); ?>
        <?php } ?>
    </div>

    <?php if (empty($books)) { ?>
        <div class="alert alert-warning">
            <h4>Нет данных</h4>
            <p>Книги не найдены. <?php echo Html::a('Добавить первую книгу', ['create'], ['class' => 'btn btn-primary btn-sm']); ?></p>
        </div>
    <?php } else { ?>
        <div class="row">
            <?php foreach ($books as $book) { ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <?php if (!empty($book['cover_photo'])) { ?>
                            <img src="<?php echo Html::encode($book['cover_photo']); ?>" class="card-img-top" alt="Обложка" style="height: 200px; object-fit: cover;">
                        <?php } else { ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-book fa-3x text-muted"></i>
                            </div>
                        <?php } ?>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo Html::encode($book['title'] ?? 'Без названия'); ?></h5>
                            <p class="card-text text-muted small">
                                Год: <?php echo Html::encode($book['year'] ?? 'Не указан'); ?>
                            </p>
                            <p class="card-text text-muted small">
                                Авторы: <?php echo Html::encode($book['authors'] ?? 'Не указаны'); ?>
                            </p>
                            <?php if (!empty($book['description'])) { ?>
                                <p class="card-text flex-grow-1">
                                    <?php echo Html::encode(mb_substr($book['description'], 0, 150)); ?>
                                    <?php if (mb_strlen($book['description']) > 150) { ?>...<?php } ?>
                                </p>
                            <?php } ?>

                            <div class="mt-auto">
                                <?php echo Html::a('Подробнее', ['view', 'id' => $book['id']], ['class' => 'btn btn-outline-primary btn-sm']); ?>

                                <?php if (Yii::$app->user->isGuest === false) { ?>
                                    <?php echo Html::a('Изменить', ['update', 'id' => $book['id']], ['class' => 'btn btn-outline-secondary btn-sm']); ?>
                                    <?php echo Html::a('Удалить', ['delete', 'id' => $book['id']], [
                                        'class' => 'btn btn-outline-danger btn-sm',
                                        'data' => [
                                            'confirm' => 'Вы уверены, что хотите удалить эту книгу?',
                                            'method' => 'post',
                                        ],
                                    ]); ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

    <div class="mt-4">
        <small class="text-muted">
            <i class="fas fa-info-circle"></i>
            Каталог книг
        </small>
    </div>

</div>
