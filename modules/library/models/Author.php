<?php

namespace app\modules\library\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Author model
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Book[] $books
 * @property Subscription[] $subscriptions
 */
class Author extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%author}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name'], 'required'],
            [['first_name', 'last_name', 'middle_name'], 'string', 'max' => 100],
            [['first_name', 'last_name', 'middle_name'], 'trim'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'middle_name' => Yii::t('app', 'Middle Name'),
            'full_name' => Yii::t('app', 'Full Name'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Books]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBooks()
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])
            ->viaTable('{{%book_author}}', ['author_id' => 'id']);
    }

    /**
     * Gets query for [[Subscriptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriptions()
    {
        return $this->hasMany(Subscription::class, ['author_id' => 'id']);
    }

    /**
     * Get full name of author
     *
     * @return string
     */
    public function getFullName()
    {
        $parts = array_filter([
            $this->last_name,
            $this->first_name,
            $this->middle_name,
        ]);

        return implode(' ', $parts);
    }

    /**
     * Get short name (Last Name with initials)
     *
     * @return string
     */
    public function getShortName()
    {
        $firstName = $this->first_name ? mb_substr($this->first_name, 0, 1) . '.' : '';
        $middleName = $this->middle_name ? mb_substr($this->middle_name, 0, 1) . '.' : '';

        return trim($this->last_name . ' ' . $firstName . ' ' . $middleName);
    }

    /**
     * Count books by this author for a specific year
     *
     * @param int $year
     * @return int
     */
    public function getBookCountByYear($year)
    {
        return $this->getBooks()->where(['year' => $year])->count();
    }

    /**
     * Get total book count for this author
     *
     * @return int
     */
    public function getBookCount()
    {
        if ($this->isRelationPopulated('books')) {
            return count($this->books);
        }
        return (int)$this->getBooks()->count();
    }
}
