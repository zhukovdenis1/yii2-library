<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\library\models\Book[] $recentBooks */

$this->title = Yii::t('app', 'Library Catalog');
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4"><?= Yii::t('app', 'Welcome to Library Catalog') ?></h1>

        <p class="lead"><?= Yii::t('app', 'Discover books, follow authors, and stay updated with new releases') ?></p>

        <p>
            <?= Html::a(Yii::t('app', 'Browse Books'), ['/library/guest/book/index'], ['class' => 'btn btn-lg btn-primary me-2']) ?>
            <?= Html::a(Yii::t('app', 'Browse Authors'), ['/library/guest/author/index'], ['class' => 'btn btn-lg btn-outline-primary']) ?>
        </p>
    </div>

    <div class="body-content">

        <?php if (!empty($recentBooks)): ?>
            <h2 class="mb-4"><?= Yii::t('app', 'Recent Books') ?></h2>
            <div class="row">
                <?php foreach ($recentBooks as $book): ?>
                    <div class="col-md-4 mb-4">
                        <?= $this->render('@app/modules/library/views/book/_book_item', ['model' => $book]) ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <?= Html::a(Yii::t('app', 'View All Books') . ' &raquo;', ['/library/guest/book/index'], ['class' => 'btn btn-primary']) ?>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-4 mb-3">
                    <h2><?= Yii::t('app', 'Books') ?></h2>
                    <p><?= Yii::t('app', 'Browse our collection of books. Filter by author, year, or search by title.') ?></p>
                    <p><?= Html::a(Yii::t('app', 'Browse Books') . ' &raquo;', ['/library/guest/book/index'], ['class' => 'btn btn-outline-primary']) ?></p>
                </div>
                <div class="col-lg-4 mb-3">
                    <h2><?= Yii::t('app', 'Authors') ?></h2>
                    <p><?= Yii::t('app', 'Explore authors and subscribe to get notifications about their new books via SMS.') ?></p>
                    <p><?= Html::a(Yii::t('app', 'Browse Authors') . ' &raquo;', ['/library/guest/author/index'], ['class' => 'btn btn-outline-primary']) ?></p>
                </div>
                <div class="col-lg-4">
                    <h2><?= Yii::t('app', 'Reports') ?></h2>
                    <p><?= Yii::t('app', 'View statistics and reports, including top authors by year.') ?></p>
                    <p><?= Html::a(Yii::t('app', 'View Reports') . ' &raquo;', ['/library/guest/report/top-authors'], ['class' => 'btn btn-outline-primary']) ?></p>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>
