<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\modules\library\models\Book $model */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\modules\library\models\Author[] $authors */
?>

<div class="book-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'author_ids')->checkboxList(
        ArrayHelper::map($authors, 'id', 'fullName')
    ) ?>

    <?= $form->field($model, 'year')->textInput(['type' => 'number', 'min' => 1000, 'max' => date('Y') + 10]) ?>

    <?= $form->field($model, 'isbn')->textInput(['maxlength' => true, 'placeholder' => '978-3-16-148410-0']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'imageFile')->fileInput(['accept' => 'image/*']) ?>

    <?php if ($model->cover_image): ?>
        <div class="mb-3">
            <label class="form-label"><?= Yii::t('app', 'Current Image') ?></label>
            <div>
                <img src="<?= Html::encode($model->getCoverImageUrl()) ?>" alt="<?= Html::encode($model->title) ?>" style="max-width: 200px;" class="img-thumbnail">
            </div>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
