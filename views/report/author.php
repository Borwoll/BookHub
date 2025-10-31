<?php

use yii\bootstrap5\Html;

$this->title = $author->full_name . ' - ' . $year . ' год';
$this->params['breadcrumbs'][] = ['label' => 'ТОП 10 авторов', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Отчет за ' . $year, 'url' => ['index', 'year' => $year]];
$this->params['breadcrumbs'][] = $author->full_name;
?>
<div class="report-author">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <div class="mb-3">
        <?php echo Html::a('← Назад к отчету', ['index', 'year' => $year], ['class' => 'btn btn-outline-secondary']); ?>
        <?php echo Html::a('Профиль автора', ['/author/view', 'id' => $author->id], ['class' => 'btn btn-primary']); ?>
        <?php echo Html::a('Подписаться', ['/subscription/create', 'author_id' => $author->id], ['class' => 'btn btn-success']); ?>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Книги за <?php echo $year; ?> год (<?php echo $booksCount; ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if ($books !== []) { ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>№</th>
                                        <th>Название</th>
                                        <th>ISBN</th>
                                        <th>Соавторы</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($books as $index => $book) { ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td>
                                                <strong><?php echo Html::encode($book->title); ?></strong>
                                                <?php if ($book->description) { ?>
                                                    <br><small class="text-muted">
                                                        <?php echo Html::encode(mb_substr($book->description, 0, 100)); ?>
                                                        <?php if (mb_strlen($book->description) > 100) { ?>...<?php } ?>
                                                    </small>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <code><?php echo Html::encode($book->isbn ?: 'Не указан'); ?></code>
                                            </td>
                                            <td>
                                                <?php
                                                $coAuthors = [];
                                        foreach ($book->authors as $bookAuthor) {
                                            if ($bookAuthor->id !== $author->id) {
                                                $coAuthors[] = Html::a(
                                                    Html::encode($bookAuthor->full_name),
                                                    ['/author/view', 'id' => $bookAuthor->id],
                                                );
                                            }
                                        }
                                        echo ! empty($coAuthors) ? implode(', ', $coAuthors) : '—';
                                        ?>
                                            </td>
                                            <td>
                                                <?php echo Html::a('Подробнее', ['/book/view', 'id' => $book->id], [
                                            'class' => 'btn btn-outline-primary btn-sm',
                                        ]); ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-info">
                            У автора нет книг за <?php echo $year; ?> год.
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6>Информация об авторе</h6>
                </div>
                <div class="card-body">
                    <p><strong>ФИО:</strong> <?php echo Html::encode($author->full_name); ?></p>
                    <p><strong>Всего книг в каталоге:</strong> <?php echo count($author->books ?? []); ?></p>
                    <p><strong>Книг за <?php echo $year; ?> год:</strong> <?php echo $booksCount; ?></p>
                    <p><strong>Активных подписчиков:</strong> <?php echo count($author->activeSubscriptions ?? []); ?></p>

                    <hr>

                    <div class="d-grid gap-2">
                        <?php echo Html::a('Все книги автора', ['/author/view', 'id' => $author->id], [
                            'class' => 'btn btn-outline-primary btn-sm',
                        ]); ?>

                        <?php echo Html::a('Подписаться на автора', ['/subscription/create', 'author_id' => $author->id], [
                            'class' => 'btn btn-success btn-sm',
                        ]); ?>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6>Статистика по годам</h6>
                </div>
                <div class="card-body">
                    <?php
                    $booksByYears = [];
foreach (($author->books ?? []) as $book) {
    $bookYear = $book->year;
    if (isset($booksByYears[$bookYear]) === false) {
        $booksByYears[$bookYear] = 0;
    }
    $booksByYears[$bookYear]++;
}
krsort($booksByYears);
?>

                    <?php if ($booksByYears !== []) { ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Год</th>
                                        <th>Книг</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($booksByYears as $bookYear => $count) { ?>
                                        <tr class="<?php echo $bookYear === $year ? 'table-warning' : ''; ?>">
                                            <td>
                                                <?php if ($bookYear === $year) { ?>
                                                    <strong><?php echo $bookYear; ?></strong>
                                                <?php } else { ?>
                                                    <?php echo Html::a($bookYear, ['index', 'year' => $bookYear]); ?>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $bookYear === $year ? 'warning' : 'secondary'; ?>">
                                                    <?php echo $count; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <p class="text-muted">Нет данных</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

</div>
