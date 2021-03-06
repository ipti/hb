<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "term".
 *
 * @property integer $id
 * @property integer $enrollment
 * @property integer $campaign
 * @property integer $agreed
 *
 * @property hemoglobin[] $hemoglobins
 * @property campaign $campaigns
 * @property enrollment $enrollments
 */
class term extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'term';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enrollment', 'agreed'], 'required'],
            [['enrollment', 'campaign', 'agreed'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'enrollment' => Yii::t('app', 'Enrollment'),
            'campaign' => Yii::t('app', 'Campaign'),
            'agreed' => Yii::t('app', 'Agreed'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHemoglobins()
    {
        return $this->hasMany(hemoglobin::className(), ['agreed_term' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCampaigns()
    {
        return $this->hasOne(campaign::className(), ['id' => 'campaign']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudents()
    {
        return $this->hasOne(student::className(), ['id' => 'student'])
            ->via('enrollments');
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEnrollments()
    {
        return $this->hasOne(enrollment::className(), ['id' => 'enrollment']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConsults()
    {
        return $this->hasOne(consultation::className(), ['term' => 'id']);
    }
}
