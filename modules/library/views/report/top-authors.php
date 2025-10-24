<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */
/** @var int $year */
/** @var array $availableYears */
/** @var bool $isGuest */

$this->title = Yii::t('app', 'Top 10 Authors by Year');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-top-authors">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-auto">
                    <label for="year" class="col-form-label"><?= Yii::t('app', 'Select Year') ?>:</label>
                </div>
                <div class="col-auto">
                    <select name="year" id="year" class="form-select">
                        <?php if (empty($availableYears)): ?>
                            <option value="<?= date('Y') ?>"><?= date('Y') ?></option>
                        <?php else: ?>
                            <?php foreach ($availableYears as $y): ?>
                                <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary"><?= Yii::t('app', 'Show') ?></button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($dataProvider->getTotalCount() > 0): ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'label' => Yii::t('app', 'Rank'),
                    'value' => function($model, $key, $index) {
                        return $index + 1;
                    },
                ],
                [
                    'label' => Yii::t('app', 'Author'),
                    'value' => function($model) use ($isGuest) {
                        $controller = $isGuest ? '/library/guest/author' : '/library/user/author';
                        return Html::a(Html::encode($model['full_name']), [$controller . '/view', 'id' => $model['id']]);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'book_count',
                    'label' => Yii::t('app', 'Book Count'),
                ],
            ],
        ]); ?>
    <?php else: ?>
        <div class="alert alert-info">
            <?= Yii::t('app', 'No data available for the selected year.') ?>
        </div>
    <?php endif; ?>

</div>
