<?php

use yii\bootstrap5\Html;

$this->title = 'Подписка на авторов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subscription-index">

    <h1><?php echo Html::encode($this->title); ?></h1>


    <div class="row">
        <div class="col-md-8">
            <p class="lead">
                Подпишитесь на уведомления о новых книгах ваших любимых авторов и получайте SMS на телефон!
            </p>

            <div class="mb-3">
                <?php echo Html::a('Подписаться на автора', ['create'], ['class' => 'btn btn-success']); ?>
                <?php echo Html::a('Проверить мои подписки', ['view'], ['class' => 'btn btn-outline-primary']); ?>
            </div>

            <?php if ($phone !== '') { ?>
                <h3>Подписки для номера <?php echo Html::encode($phone); ?></h3>

                <?php if (empty($subscriptions)) { ?>
                    <div class="alert alert-warning">
                        <h5>Подписок не найдено</h5>
                        <p>Для этого номера телефона подписки не найдены.</p>
                        <?php echo Html::a('Создать первую подписку', ['create'], ['class' => 'btn btn-primary']); ?>
                    </div>
                <?php } else { ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Автор</th>
                                    <th scope="col">Статус</th>
                                    <th scope="col">Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($subscriptions as $index => $subscription) { ?>
                                    <tr>
                                        <th scope="row"><?php echo $index + 1; ?></th>
                                        <td>
                                            <?php echo Html::a(
                                                Html::encode($subscription->authorName ?? $subscription['author_name'] ?? 'Неизвестен'),
                                                ['/author/view', 'id' => $subscription->authorId ?? $subscription['author_id']],
                                                ['class' => 'text-decoration-none'],
                                            ); ?>
                                        </td>
                                        <td>
                                            <?php if (($subscription->isActive ?? $subscription['is_active'] ?? false)) { ?>
                                                <span class="badge bg-success">Активна</span>
                                            <?php } else { ?>
                                                <span class="badge bg-secondary">Неактивна</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if (($subscription->isActive ?? $subscription['is_active'] ?? false)) { ?>
                                                <?php echo Html::a(
                                                    'Отписаться',
                                                    ['unsubscribe', 'id' => $subscription->id ?? $subscription['id']],
                                                    [
                                                        'class' => 'btn btn-outline-danger btn-sm',
                                                        'data' => [
                                                            'confirm' => 'Вы уверены, что хотите отписаться от уведомлений?',
                                                            'method' => 'post',
                                                        ],
                                                    ],
                                                ); ?>
                                            <?php } else { ?>
                                                <small class="text-muted">Подписка неактивна</small>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <h3>Популярные авторы</h3>
                <div class="alert alert-info">
                    <h5>Создайте подписку!</h5>
                    <p>Введите номер телефона справа, чтобы проверить существующие подписки, или нажмите "Подписаться на автора" чтобы создать новую.</p>
                </div>
            <?php } ?>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Как это работает?</h5>
                </div>
                <div class="card-body">
                    <ol>
                        <li>Выберите интересного автора</li>
                        <li>Укажите свой номер телефона</li>
                        <li>Получайте SMS при добавлении новых книг</li>
                    </ol>

                    <div class="alert alert-info mt-3">
                        <small>
                            <strong>Примечание:</strong><br>
                            SMS отправляются бесплатно через тестовый сервис SmsPilot.
                            Реальной отправки не происходит.
                        </small>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6>Проверить подписки</h6>
                </div>
                <div class="card-body">
                    <?php $form = yii\bootstrap5\ActiveForm::begin([
                        'action' => ['view'],
                        'method' => 'get',
                    ]); ?>

                    <div class="input-group">
                        <?php echo Html::textInput('phone', Yii::$app->request->get('phone'), [
                            'class' => 'form-control',
                            'placeholder' => '+79161234567',
                            'pattern' => '\+?[1-9]\d{1,14}',
                            'title' => 'Введите номер в международном формате',
                        ]); ?>
                        <button class="btn btn-outline-secondary" type="submit">
                            Проверить
                        </button>
                    </div>

                    <?php yii\bootstrap5\ActiveForm::end(); ?>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6>Подписки на уведомления</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Получайте SMS уведомления о новых книгах ваших любимых авторов.
                    </small>
                </div>
            </div>
        </div>
    </div>

</div>
