<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;

/**
 * Класс модели менеджера.
 *
 * @property int    $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $name
 * @property int    $is_works
 */
class Manager extends ActiveRecord
{
    public static function tableName()
    {
        return 'managers';
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
            [['name', 'is_works'], 'required'],
            ['name', 'string', 'max' => 255],
            ['is_works', 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'created_at' => 'Добавлен',
            'updated_at' => 'Изменен',
            'name'       => 'ФИО',
            'is_works'   => 'Сейчас работает',
        ];
    }

    /**
     * Метод получения списка менеджеров.
     *
     * @return array
     */
    public static function getList() : array
    {
        return array_column(
            self::find()->orderBy('name ASC')->asArray()->all(),
            'name',
            'id'
        );
    }

    /**
     * Метод получения идентификатора "рандомного" менеджера.
     *
     * @return mixed|null
     */
    public static function getRandomManagerId()
    {
        $subQuery = (new Query())
            ->select(['count(*)'])
            ->from('requests')
            ->where('manager_id = managers.id');

        $result = (new Query())
            ->select([
                'id',
                'count_request' => $subQuery,
            ])
            ->from('managers')
            ->where(['is_works' => 1])
            ->orderBy(['count_request' => SORT_ASC])
            ->one();

        return $result ? $result['id'] : null;
    }
}
