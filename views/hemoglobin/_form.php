<?php

use yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\select2\Select2;

use app\models\campaign;
use app\models\term;

/* @var $this yii\web\View */
/* @var $model app\models\hemoglobin */
/* @var $form yii\widgets\ActiveForm */
/* @var $campaign app\models\campaign */
?>

<div class="hemoglobin-form">

    <?php
    $form = ActiveForm::begin(['type' => ActiveForm::TYPE_VERTICAL]);

    if ($model->isNewRecord) {
        echo Form::widget([
            'model' => $model,
            'form' => $form,
            'columns' => 1,
            'attributes' => [
                'agreed_term' => [
                    'type' => Form::INPUT_WIDGET,
                    'widgetClass' => Select2::className(),
                    'options' => [
                        'data' => $sample == 1 ? (ArrayHelper::map($campaign->getTerms()->where('agreed = true')->all(), 'id', 'students.name')) : (ArrayHelper::map($campaign->getConsults()->where('attended = true')->all(), 'terms.id', 'terms.students.name')),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Select Student...'),
                             ""
                        ]
                    ],
                ],
                'rate' => [
                    'type' => Form::INPUT_TEXT,
                ],
                'sample' => [
                    'type' => Form::INPUT_RAW,
                    'value' => Html::activeHiddenInput($model, 'sample', ['value' => $sample])
                ]
            ]
        ]);
    } else {
        
        $term = term::find()->where('id = :sid', ['sid' => $model->agreed_term])->one();;
        $campaign = campaign::find()->where('id = :sid', ['sid' => $term->campaign])->one();
        echo Form::widget([
            'model' => $model,
            'form' => $form,
            'columns' => 1,
            'attributes' => [
                'agreed_term' => [
                    'type' => Form::INPUT_WIDGET,
                    'widgetClass' => Select2::className(),
                    'options' => [
                        'data' => $model->sample == 1 ? (ArrayHelper::map($campaign->getTerms()->where('agreed = true')->all(), 'id', 'students.name')) : (ArrayHelper::map($campaign->getConsults()->where('attended = true')->all(), 'terms.id', 'terms.students.name')),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Select Student...'),
                            "disabled" => "disabled"
                        ]
                    ],
                ],
                'rate' => [
                    'type' => Form::INPUT_TEXT,
                ],
                'sample' => [
                    'type' => Form::INPUT_RAW,
                    'value' => Html::activeHiddenInput($model, 'sample', ['value' => $model->sample])
                ]
            ]
        ]);
    }

    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ActiveForm::end();
    ?>
</div>
