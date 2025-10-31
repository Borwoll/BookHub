<?php

use yii\bootstrap5\Html;

$this->title = 'Авторы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-index">

    <h1><?php echo Html::encode($this->title); ?></h1>


    <?php if (isset($error)) { ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> <?php echo Html::encode($error); ?>
        </div>
    <?php } ?>

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <div>
            <?php if (Yii::$app->user->isGuest === false) { ?>
                <?php echo Html::a('Добавить автора', ['create'], ['class' => 'btn btn-success']); ?>
            <?php } ?>
        </div>

        <div>
            <?php echo Html::beginForm(['index'], 'GET', ['class' => 'd-flex']); ?>
            <?php echo Html::textInput('search', $searchQuery ?? '', [
                'class' => 'form-control me-2',
                'placeholder' => 'Поиск авторов...',
                'style' => 'width: 250px;',
            ]); ?>
            <?php echo Html::submitButton('Найти', ['class' => 'btn btn-outline-primary']); ?>
            <?php if ($searchQuery !== '') { ?>
                <?php echo Html::a('Сбросить', ['index'], ['class' => 'btn btn-outline-secondary ms-1']); ?>
            <?php } ?>
            <?php echo Html::endForm(); ?>
        </div>
    </div>

    <?php if ($searchQuery !== '') { ?>
        <div class="alert alert-info">
            Результаты поиска для: <strong><?php echo Html::encode($searchQuery); ?></strong>
        </div>
    <?php } ?>

    <?php if (empty($authors)) { ?>
        <div class="alert alert-warning">
            <h4>Нет данных</h4>
            <p>
                <?php if ($searchQuery !== '') { ?>
                    По вашему запросу авторы не найдены. <?php echo Html::a('Показать всех', ['index'], ['class' => 'btn btn-primary btn-sm']); ?>
                <?php } else { ?>
                    Авторы не найдены. <?php echo Html::a('Добавить первого автора', ['create'], ['class' => 'btn btn-primary btn-sm']); ?>
                <?php } ?>
            </p>
        </div>
    <?php } else { ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">ФИО автора</th>
                        <th scope="col">Количество книг</th>
                        <th scope="col">Активные подписки</th>
                        <th scope="col" class="text-center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($authors as $index => $author) { ?>
                        <tr>
                            <th scope="row"><?php echo $index + 1; ?></th>
                            <td>
                                <strong><?php echo Html::encode($author->fullName ?? $author['full_name'] ?? 'Неизвестен'); ?></strong>
                            </td>
                            <td>
                                <span class="badge bg-primary"><?php echo (int) ($author->booksCount ?? $author['books_count'] ?? 0); ?></span>
                            </td>
                            <td>
                                <span class="badge bg-success"><?php echo (int) ($author->activeSubscriptionsCount ?? $author['active_subscriptions_count'] ?? 0); ?></span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <?php echo Html::a(
                                        '<i class="fas fa-eye"></i>',
                                        ['view', 'id' => $author->id ?? $author['id']],
                                        [
                                            'class' => 'btn btn-outline-primary',
                                            'title' => 'Просмотр',
                                        ],
                                    ); ?>

                                    <?php echo Html::a(
                                        '<i class="fas fa-bell"></i>',
                                        ['subscribe', 'id' => $author->id ?? $author['id']],
                                        [
                                            'class' => 'btn btn-outline-info',
                                            'title' => 'Подписаться на уведомления',
                                        ],
                                    ); ?>

                                    <?php if (Yii::$app->user->isGuest === false) { ?>
                                        <?php echo Html::a(
                                            '<i class="fas fa-edit"></i>',
                                            ['update', 'id' => $author->id ?? $author['id']],
                                            [
                                                'class' => 'btn btn-outline-secondary',
                                                'title' => 'Редактировать',
                                            ],
                                        ); ?>

                                        <?php echo Html::a(
                                            '<i class="fas fa-trash"></i>',
                                            ['delete', 'id' => $author->id ?? $author['id']],
                                            [
                                                'class' => 'btn btn-outline-danger',
                                                'title' => 'Удалить',
                                                'data' => [
                                                    'confirm' => 'Вы уверены, что хотите удалить этого автора?',
                                                    'method' => 'post',
                                                ],
                                            ],
                                        ); ?>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <small class="text-muted">
                Найдено авторов: <strong><?php echo count($authors); ?></strong>
            </small>
        </div>
    <?php } ?>

    <div class="mt-4">
        <small class="text-muted">
            <i class="fas fa-info-circle"></i>
            Каталог авторов
        </small>
    </div>

</div>
