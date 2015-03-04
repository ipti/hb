<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\data\ActiveDataProvider;

use kartik\grid\GridView;
use kartik\grid\CheckboxColumn;

use app\models\student;

/* @var $this yii\web\View */
/* @var $model app\models\Campaign */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="campaign-form form">
   <?php $form = ActiveForm::begin([
        'id' => $model->formName(),
    ]); ?>
    
    
    <?php
    //beforeSubmit
    $js = "
        $('form#".$model->formName()."').on('beforeSubmit', function(e){
            var \$form = $(this);
            submitCampaignForm(\$form);
        }).on('submit', function(e){
            e.preventDefault();
        });";
    $this->registerJs($js);
    ?>

    <?= Html::activeHiddenInput($model, 'coordinator') ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => 20]) ?>
    <?= $form->field($model, 'begin')->input("date") ?>
    <?= $form->field($model, 'end')->input("date") ?>
    <?= $form->field($model, 'end')->input("date") ?>
    <?php
        $data = \yii\helpers\ArrayHelper::map(app\models\school::find()->all(), 'id', 'name');
        echo Html::label(yii::t('app','Schools'));
        echo \kartik\select2\Select2::widget([
            'name' => 'schools', 
            'data' => array_merge(["" => ""], $data),
            'options' => [
                'placeholder' => yii::t('app', 'Select School'),
                'multiple' => true,
                ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    
    ?>
    
    
    
    <?php 
        echo $model->isNewRecord ? 
         GridView::widget([
            'tableOptions'=>[ 
                    'id' => 'tableStudents',
                    'class'=>'header-fixed',
                ],
            //'filterModel' => new app\models\StudentSearch(),
            'dataProvider' => new ActiveDataProvider([
                'query' => student::find()->asArray(),
                'pagination' => [
                    'pageSize' => 0,
                ],
            ]),
            'columns' => [
                'name',
                'responsible',
                ['class' => CheckboxColumn::className(),
                    'header' => yii::t('app', 'Select')
                ],
            ],
            'pjax'=>true,
            'pjaxSettings'=>[
                'neverTimeout'=>true,
                'options'=>[
                    'id'=>'pjaxStudents'
                ],
            ],
            'hover'=>true,
        ])
            : ""; 
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
