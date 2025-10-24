<?php

namespace app\modules\library\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Subscription model
 *
 * @property int $id
 * @property int $author_id
 * @property string $phone
 * @property int $status
 * @property int $created_at
 *
 * @property Author $author
 */
class Subscription extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%subscription}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false, // No updated_at field
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id', 'phone'], 'required'],
            ['author_id', 'integer'],
            ['author_id', 'exist', 'targetClass' => Author::class, 'targetAttribute' => 'id'],
            ['phone', 'string', 'max' => 20],
            ['phone', 'match', 'pattern' => '/^\+7\d{10}$/', 'message' => 'Phone must be in format +79991234567'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            // Unique constraint
            ['phone', 'unique', 'targetAttribute' => ['author_id', 'phone'],
                'message' => 'This phone is already subscribed to this author.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'author_id' => Yii::t('app', 'Author'),
            'phone' => Yii::t('app', 'Phone Number'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }

    /**
     * Check if subscription is active
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Activate subscription
     *
     * @return bool
     */
    public function activate()
    {
        $this->status = self::STATUS_ACTIVE;
        return $this->save(false, ['status']);
    }

    /**
     * Deactivate subscription
     *
     * @return bool
     */
    public function deactivate()
    {
        $this->status = self::STATUS_INACTIVE;
        return $this->save(false, ['status']);
    }
}
