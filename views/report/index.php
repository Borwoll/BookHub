<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'ТОП 10 авторов по году';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-index">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Выберите год для отчета</h5>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin([
                        'method' => 'get',
                        'options' => ['class' => 'row g-3 align-items-end'],
                    ]); ?>

                    <div class="col-md-4">
                        <label for="year" class="form-label">Год</label>
                        <?php echo Html::dropDownList(
                            'year',
                            $year,
                            array_combine($availableYears, $availableYears),
                            [
                                'class' => 'form-select',
                                'prompt' => 'Выберите год...',
                            ],
                        ); ?>
                    </div>

                    <div class="col-md-3">
                        <?php echo Html::submitButton('Показать отчет', ['class' => 'btn btn-primary']); ?>
                    </div>

                    <div class="col-md-3">
                        <?php echo Html::a('JSON', ['json', 'year' => $year], [
                                'class' => 'btn btn-outline-info',
                                'target' => '_blank',
                                'title' => 'Получить данные в формате JSON',
                            ]); ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <?php if ($authors !== []) { ?>
                <div class="mt-4">
                    <h3>ТОП 10 авторов за <?php echo Html::encode($year); ?> год</h3>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Место</th>
                                    <th>Автор</th>
                                    <th>Количество книг</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($authors as $index => $author) { ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-<?php echo $index < 3 ? 'warning' : 'secondary'; ?> fs-6">
                                                #<?php echo $index + 1; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo Html::a(
                                                Html::encode($author['full_name']),
                                                ['/author/view', 'id' => $author['id']],
                                                ['class' => 'fw-bold text-decoration-none'],
                                            ); ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?php
                                                                $count = (int) $author['books_count'];
                                    $mod10 = $count % 10;
                                    $mod100 = $count % 100;

                                    if ($mod100 >= 11 && $mod100 <= 19) {
                                        $word = 'книг';
                                    } elseif ($mod10 === 1) {
                                        $word = 'книга';
                                    } elseif ($mod10 >= 2 && $mod10 <= 4) {
                                        $word = 'книги';
                                    } else {
                                        $word = 'книг';
                                    }
                                    echo $count . ' ' . $word;
                                    ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo Html::a('Подробнее', ['author', 'id' => $author['id'], 'year' => $year], [
                                    'class' => 'btn btn-outline-primary btn-sm',
                                            ]); ?>
                                            <?php echo Html::a('Все книги', ['/author/view', 'id' => $author['id']], [
                                    'class' => 'btn btn-outline-secondary btn-sm',
                                            ]); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php } elseif ($year && ! empty($availableYears)) { ?>
                <div class="alert alert-warning mt-4">
                    <h5>Нет данных за <?php echo Html::encode($year); ?> год</h5>
                    <p>За указанный год не было выпущено книг или данные отсутствуют в каталоге.</p>
                </div>

            <?php } elseif (empty($availableYears)) { ?>
                <div class="alert alert-info mt-4">
                    <h5>Нет данных для отчета</h5>
                    <p>В каталоге пока нет книг. Добавьте книги для формирования отчета.</p>
                    <?php echo Html::a('Добавить книгу', ['/book/create'], ['class' => 'btn btn-success']); ?>
                </div>
            <?php } ?>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6>О отчете</h6>
                </div>
                <div class="card-body">
                    <p>
                        Данный отчет показывает топ-10 авторов, выпустивших наибольшее количество книг
                        в выбранном году.
                    </p>

                    <h6>Доступные форматы:</h6>
                    <ul class="list-unstyled">
                        <li>📊 Веб-интерфейс (текущая страница)</li>
                        <li>📄 JSON для API интеграций</li>
                    </ul>

                    <?php if ($availableYears !== []) { ?>
                        <div class="mt-3">
                            <h6>Доступные года:</h6>
                            <div class="d-flex flex-wrap gap-1">
                                <?php foreach ($availableYears as $availableYear) { ?>
                                    <?php echo Html::a($availableYear, ['index', 'year' => $availableYear], [
                                        'class' => 'btn btn-sm ' . ($availableYear === $year ? 'btn-primary' : 'btn-outline-secondary'),
                                    ]); ?>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <?php if ($authors !== []) { ?>
                <div class="card mt-3">
                    <div class="card-header">
                        <h6>Статистика за <?php echo Html::encode($year); ?> год</h6>
                    </div>
                    <div class="card-body">
                        <?php
                                            $totalBooks = array_sum(array_column($authors, 'books_count'));
                $avgBooks = count($authors) > 0 ? round($totalBooks / count($authors), 1) : 0;
                ?>

                        <ul class="list-unstyled">
                            <li><strong>Авторов в топе:</strong> <?php echo count($authors); ?></li>
                            <li><strong>Всего книг:</strong> <?php echo $totalBooks; ?></li>
                            <li><strong>Среднее количество:</strong> <?php echo $avgBooks; ?> книг/автор</li>
                        </ul>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

</div>
