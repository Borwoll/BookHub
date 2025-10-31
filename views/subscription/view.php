<?php

use yii\bootstrap5\Html;

$this->title = 'Мои подписки';
$this->params['breadcrumbs'][] = ['label' => 'Подписка на авторов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subscription-view">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <?php if ($phone) { ?>
        <div class="alert alert-info">
            <strong>Номер телефона:</strong> <?php echo Html::encode($phone); ?>
        </div>

        <?php if ($subscriptions !== []) { ?>
            <h3>Активные подписки (<?php echo count($subscriptions); ?>)</h3>

            <div class="row">
                <?php foreach ($subscriptions as $subscription) { ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <?php echo Html::a(
                                        Html::encode($subscription->author->full_name),
                                        ['/author/view', 'id' => $subscription->author->id],
                                    ); ?>
                                </h5>

                                <p class="card-text">
                                    <small class="text-muted">
                                        Книг в каталоге: <?php echo count($subscription->author->books ?? []); ?><br>
                                        Подписка создана: <?php echo date('d.m.Y', $subscription->created_at); ?>
                                    </small>
                                </p>

                                <div class="d-grid gap-2">
                                    <?php echo Html::a('Просмотр автора', ['/author/view', 'id' => $subscription->author->id], [
                                                                            'class' => 'btn btn-outline-primary btn-sm',
                                                                        ]); ?>

                                    <?php echo Html::a('Отписаться', ['unsubscribe', 'id' => $subscription->id], [
                                                                            'class' => 'btn btn-outline-danger btn-sm',
                                                                            'data' => [
                                                                                'confirm' => 'Вы уверены, что хотите отписаться от ' . $subscription->author->full_name . '?',
                                                                                'method' => 'post',
                                                                            ],
                                                                        ]); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

        <?php } else { ?>
            <div class="alert alert-warning">
                <h5>У вас нет активных подписок</h5>
                <p>Подпишитесь на интересных авторов, чтобы получать уведомления о новых книгах!</p>
                <?php echo Html::a('Найти авторов', ['index'], ['class' => 'btn btn-success']); ?>
            </div>
        <?php } ?>

        <div class="mt-4">
            <div class="card">
                <div class="card-header">
                    <h6>Управление подписками</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo Html::a('Подписаться на новых авторов', ['create'], [
                                                                    'class' => 'btn btn-success',
                                                                ]); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo Html::a('Все авторы', ['/author/index'], [
                                                                    'class' => 'btn btn-outline-primary',
                                                                ]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php } else { ?>
        <div class="alert alert-info">
            <h5>Введите номер телефона для просмотра подписок</h5>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?php $form = yii\bootstrap5\ActiveForm::begin([
                                                        'action' => ['view'],
                                                        'method' => 'get',
                                                    ]); ?>

                <div class="mb-3">
                    <label for="phone" class="form-label">Номер телефона</label>
                    <?php echo Html::textInput('phone', '', [
                                                            'class' => 'form-control',
                                                            'placeholder' => '+79161234567',
                                                            'pattern' => '\+?[1-9]\d{1,14}',
                                                            'title' => 'Введите номер в международном формате',
                                                            'required' => true,
                                                        ]); ?>
                </div>

                <div class="mb-3">
                    <?php echo Html::submitButton('Проверить подписки', ['class' => 'btn btn-primary']); ?>
                </div>

                <?php yii\bootstrap5\ActiveForm::end(); ?>
            </div>

            <div class="col-md-6">
                <div class="alert alert-info">
                    <small>
                        <strong>Как проверить подписки:</strong><br>
                        • Введите номер телефона в международном формате<br>
                        • Нажмите "Проверить подписки"<br>
                        • Вы увидите список всех активных подписок<br>
                        • Можете отписаться от любого автора
                    </small>
                </div>
            </div>
        </div>
    <?php } ?>

</div>
