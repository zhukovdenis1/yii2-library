<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\modules\library\models\Book $model */
/** @var bool $canUpdateBook */
/** @var bool $canDeleteBook */
/** @var bool $isGuest */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Books'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-view">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <div>
            <?php if ($canUpdateBook): ?>
                <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php endif; ?>
            <?php if ($canDeleteBook): ?>
                <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this book?'),
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?php if ($model->cover_image): ?>
                <img src="<?= Html::encode($model->getCoverImageUrl()) ?>" class="img-fluid rounded" alt="<?= Html::encode($model->title) ?>">
            <?php else: ?>
                <div class="bg-secondary d-flex align-items-center justify-content-center rounded" style="height: 400px;">
                    <i class="bi bi-book" style="font-size: 6rem; color: #fff;"></i>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-8">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'title',
                    [
                        'attribute' => 'author_ids',
                        'label' => Yii::t('app', 'Authors'),
                        'value' => function($model) use ($isGuest) {
                            $authors = $model->authors;
                            $links = [];
                            $controller = $isGuest ? '/library/guest/author' : '/library/user/author';
                            foreach ($authors as $author) {
                                $links[] = Html::a(Html::encode($author->getFullName()), [$controller . '/view', 'id' => $author->id]);
                            }
                            return implode(', ', $links);
                        },
                        'format' => 'raw',
                    ],
                    'year',
                    'isbn',
                    'description:ntext',
                    'created_at:datetime',
                    'updated_at:datetime',
                ],
            ]) ?>
        </div>
    </div>

</div>
