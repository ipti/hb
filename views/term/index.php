<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $campaign integer */

$this->assetBundles['Term'] = new app\assets\AppAsset();
$this->assetBundles['Term']->js = [
    'scripts/TermView/Functions.js',
    'scripts/TermView/Click.js'
];

$this->title = Yii::t('app', 'Terms');
$this->params['breadcrumbs'][] = $this->title;
$this->params['button'] = 
        Html::a(Yii::t('app', 'Create Term'), ['create', 'c' => $campaign], ['class' => 'btn btn-success navbar-btn']);
?>
<div class="term-index">


    <?=
    GridView::widget([
        'id' => 'termsGridView',
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class'=> kartik\grid\DataColumn::className(),
                'attribute' => 'enrollment',
                'label' => yii::t('app','Student'),
                'content' => function ($model, $key, $index, $column){
                    return $model->students->name;
                }
            ],
            ['class' => '\kartik\grid\BooleanColumn',
                'contentOptions' => ['class' => 'agreedClick cursor-pointer'],
                'attribute' => 'agreed',
                'vAlign' => 'middle',
            ],
        ],
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => [
                'id' => 'pjaxTerm'
            ],
        ],
    ]);
    ?>
</div>

<?php
    Modal::begin([
        'size'=>Modal::SIZE_SMALL,
        'id'=>'updateModal',
        'closeButton'=>false
    ]);
    
    echo "<p style='text-align:center; font-size:16px'>Tem certeza de que deseja atualizar?</p>";
    echo "<br>";
    echo "<div>";
    echo Html::button(Yii::t('app', 'Cancel'), ['data-dismiss'=>'modal', 'class'=>'btn btn-danger pull-left'])
        .Html::button(Yii::t('app', 'Confirm'), ['id'=>'updateModal-confirm', 'class'=>'btn btn-success pull-right']);
    echo "</div>";
    echo "<br>";
    echo "<br>";
    Modal::end();
?>
