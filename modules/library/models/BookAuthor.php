<?php

namespace app\modules\library\models;

use yii\db\ActiveRecord;

/**
 * BookAuthor model for many-to-many relation
 *
 * @property int $book_id
 * @property int $author_id
 */
class BookAuthor extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%book_author}}';
    }

    public function rules()
    {
        return [
            [['book_id', 'author_id'], 'required'],
            [['book_id', 'author_id'], 'integer'],
        ];
    }
}