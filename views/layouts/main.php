<?php

declare(strict_types=1);

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language; ?>" class="h-100">
<head>
    <title><?php echo Html::encode($this->title); ?></title>
    <?php $this->head(); ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody(); ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top'],
    ]);
echo Nav::widget([
    'options' => ['class' => 'navbar-nav'],
    'items' => [
        ['label' => 'Книги', 'url' => ['/book/index']],
        ['label' => 'Авторы', 'url' => ['/author/index']],
        ['label' => 'Подписка', 'url' => ['/subscription/index']],
        ['label' => 'ТОП авторов', 'url' => ['/report/index']],
        Yii::$app->user->isGuest
            ? ['label' => 'Вход', 'url' => ['/site/login']]
            : '<li class="nav-item">'
                . Html::beginForm(['/site/logout'])
                . Html::submitButton(
                    'Выход (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'nav-link btn btn-link logout'],
                )
                . Html::endForm()
                . '</li>',
    ],
]);
NavBar::end();
?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (($this->params['breadcrumbs'] ?? []) !== []) { ?>
            <?php echo Breadcrumbs::widget([
                'links' => $this->params['breadcrumbs'],
                'homeLink' => ['label' => 'Главная', 'url' => Yii::$app->homeUrl],
            ]); ?>
        <?php } ?>
        <?php echo Alert::widget(); ?>
        <?php echo $content; ?>
    </div>
</main>

<!-- footer removed -->

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
