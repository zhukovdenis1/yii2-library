<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;

/** @var yii\web\View $this */
/** @var app\modules\library\models\Author $model */
/** @var yii\data\ActiveDataProvider $booksDataProvider */
/** @var \app\modules\library\models\forms\SubscriptionForm $subscriptionForm */
/** @var bool $canUpdateAuthor */
/** @var bool $canDeleteAuthor */
/** @var bool $isGuest */

$this->title = $model->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Authors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-view">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php if ($canUpdateAuthor || $canDeleteAuthor): ?>
            <div>
                <?php if ($canUpdateAuthor): ?>
                    <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?php endif; ?>
                <?php if ($canDeleteAuthor): ?>
                    <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('app', 'Are you sure you want to delete this author?'),
                            'method' => 'post',
                        ],
                    ]) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="<?= ($isGuest && isset($subscriptionForm)) ? 'col-md-6' : 'col-md-12' ?>">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'first_name',
                    'last_name',
                    'middle_name',
                    [
                        'label' => Yii::t('app', 'Total books'),
                        'value' => $model->getBookCount(),
                    ],
                    'created_at:datetime',
                    'updated_at:datetime',
                ],
            ]) ?>
        </div>

        <?php if ($isGuest && isset($subscriptionForm)): ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= Yii::t('app', 'Subscribe to new books') ?></h5>
                        <p class="card-text text-muted"><?= Yii::t('app', 'Get SMS notifications when this author releases a new book.') ?></p>

                        <?php $form = ActiveForm::begin(['action' => ['subscribe', 'id' => $model->id]]); ?>

                        <?= $form->errorSummary($subscriptionForm, [
                            'class' => 'alert alert-danger',
                            'header' => ''
                        ]) ?>

                        <?= $form->field($subscriptionForm, 'phone')->textInput([
                            'placeholder' => '+79991234567',
                            'maxlength' => true,
                        ])->label(false) ?>

                        <div class="form-group">
                            <?= Html::submitButton(Yii::t('app', 'Subscribe'), ['class' => 'btn btn-primary']) ?>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <hr class="my-4">

    <h3><?= Yii::t('app', 'Books by this author') ?></h3>

    <?= ListView::widget([
        'dataProvider' => $booksDataProvider,
        'itemView' => '../book/_book_item',
        'layout' => "{items}\n{pager}",
        'itemOptions' => ['class' => 'col-md-4 mb-4'],
        'options' => ['class' => 'row'],
        'emptyText' => Yii::t('app', 'No books found.'),
    ]) ?>

</div>
