<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\modules\library\models\Book $model */
/** @var bool $canUpdateBook */
?>

<div class="card h-100">
    <?php if ($model->cover_image): ?>
        <img src="<?= Html::encode($model->getCoverImageUrl()) ?>" class="card-img-top" alt="<?= Html::encode($model->title) ?>" style="height: 300px; object-fit: cover;">
    <?php else: ?>
        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 300px;">
            <i class="bi bi-book" style="font-size: 4rem; color: #fff;"></i>
        </div>
    <?php endif; ?>

    <div class="card-body d-flex flex-column">
        <h5 class="card-title"><?= Html::encode($model->title) ?></h5>
        <p class="card-text text-muted mb-2">
            <small><?= Html::encode($model->getAuthorsString()) ?></small>
        </p>
        <p class="card-text">
            <small class="text-muted"><?= Yii::t('app', 'Year') ?>: <?= Html::encode($model->year) ?></small>
        </p>
        <?php if ($model->description): ?>
            <p class="card-text"><?= Html::encode(mb_substr($model->description, 0, 100)) ?>...</p>
        <?php endif; ?>

        <div class="mt-auto">
            <?= Html::a(Yii::t('app', 'View'), ['view', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
            <?php if ($canUpdateBook ?? false): ?>
                <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
            <?php endif; ?>
        </div>
    </div>
</div>
