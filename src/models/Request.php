<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\Query;

/**
 * @property int          $id
 * @property string       $created_at
 * @property string       $updated_at
 * @property string       $email
 * @property string       $phone
 * @property string|null  $text
 * @property int|null     $manager_id
 * @property int|null     $previous_request_id
 *
 * @property Manager|null $manager
 */
class Request extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'requests';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['email', 'phone'], 'required'],
            ['email', 'email'],
            ['manager_id', 'integer'],
            ['manager_id', 'exist', 'targetClass' => Manager::class, 'targetAttribute' => 'id'],
            ['previous_request_id', 'integer'],
            ['previous_request_id', 'exist', 'targetClass' => Request::class, 'targetAttribute' => 'id'],
            [['email', 'phone'], 'string', 'max' => 255],
            ['text', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'                  => 'ID',
            'created_at'          => 'Добавлен',
            'updated_at'          => 'Изменен',
            'email'               => 'Email',
            'phone'               => 'Номер телефона',
            'manager_id'          => 'Ответственный менеджер',
            'text'                => 'Текст заявки',
            'previous_request_id' => 'Предыдущая заявка',
        ];
    }

    public function getManager()
    {
        return $this->hasOne(Manager::class, ['id' => 'manager_id']);
    }

    public function getDuplicateRequestId()
    {
        $query = (new Query())
            ->select('id')
            ->from('requests')
            ->andFilterWhere(['or', ['like', 'email', $this->email], ['like', 'phone', $this->phone]])
            ->andFilterWhere(['<>', 'id', $this->id])
            ->orderBy(['created_at' => SORT_DESC]);

        if ($this->id) {
            $dayDiff = "TIMESTAMPDIFF(DAY, '" . $this->created_at . "', created_at)";
            $secondDiff = "TIMESTAMPDIFF(SECOND, '" . $this->created_at . "', created_at)";
            $query->andFilterWhere(['<', $secondDiff, 0]);
        } else {
            $dayDiff = "TIMESTAMPDIFF(DAY, NOW(), created_at)";
        }

        $query->andFilterWhere(['<', $dayDiff, 30]);
        $result = $query->one();

        return $result ? $result['id'] : null;
    }
}
