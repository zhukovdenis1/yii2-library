<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var bool $canCreateAuthor */
/** @var bool $isGuest */

$this->title = Yii::t('app', 'Authors');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-index">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php if ($canCreateAuthor): ?>
            <?= Html::a(Yii::t('app', 'Create Author'), ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'full_name',
                'label' => Yii::t('app', 'Full Name'),
                'value' => function($model) {
                    return $model->getFullName();
                },
            ],
            [
                'label' => Yii::t('app', 'Total books'),
                'attribute' => 'book_count',
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'visible' => !$isGuest,
            ],
        ],
    ]); ?>

</div>
