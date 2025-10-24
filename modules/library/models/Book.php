<?php

namespace app\modules\library\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * Book model
 *
 * @property int $id
 * @property string $title
 * @property int $year
 * @property string $description
 * @property string $isbn
 * @property string $cover_image
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Author[] $authors
 * @property UploadedFile $imageFile
 */
class Book extends ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * @var array Author IDs for the form
     */
    public $author_ids = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%book}}';
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
            [['title', 'year', 'isbn'], 'required'],
            ['title', 'string', 'max' => 255],
            ['year', 'integer', 'min' => 1000, 'max' => date('Y') + 10],
            ['description', 'string'],
            ['isbn', 'string', 'max' => 20],
            ['isbn', 'unique'],
            ['isbn', 'match', 'pattern' => '/^[\d\-]+$/', 'message' => 'ISBN can only contain numbers and dashes.'],
            ['cover_image', 'string', 'max' => 255],
            ['imageFile', 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif', 'maxSize' => 1024 * 1024 * 5, 'checkExtensionByMimeType' => false], // 5MB
            ['author_ids', 'required', 'message' => 'Please select at least one author.'],
            ['author_ids', 'each', 'rule' => ['integer']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'year' => Yii::t('app', 'Year'),
            'description' => Yii::t('app', 'Description'),
            'isbn' => Yii::t('app', 'ISBN'),
            'cover_image' => Yii::t('app', 'Cover Image'),
            'imageFile' => Yii::t('app', 'Cover Image'),
            'author_ids' => Yii::t('app', 'Authors'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Authors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthors()
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('{{%book_author}}', ['book_id' => 'id']);
    }

    /**
     * Get authors as a comma-separated string
     *
     * @return string
     */
    public function getAuthorsString()
    {
        $authors = $this->authors;
        $names = array_map(function ($author) {
            return $author->getFullName();
        }, $authors);

        return implode(', ', $names);
    }

    /**
     * Get cover image URL
     *
     * @return string|null
     */
    public function getCoverImageUrl()
    {
        if ($this->cover_image) {
            return Yii::getAlias('@web/uploads/books/' . $this->cover_image);
        }

        return null;
    }

    /**
     * Get cover image path
     *
     * @return string|null
     */
    public function getCoverImagePath()
    {
        if ($this->cover_image) {
            return Yii::getAlias('@webroot/uploads/books/' . $this->cover_image);
        }

        return null;
    }

    /**
     * Delete old cover image file
     *
     * @return bool
     */
    public function deleteOldImage()
    {
        if ($this->cover_image) {
            $filePath = $this->getCoverImagePath();
            if (file_exists($filePath)) {
                return unlink($filePath);
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        parent::afterFind();
        // Load author IDs for the form
        //$this->author_ids = $this->getAuthors()->select('id')->column();
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        // Delete cover image
        $this->deleteOldImage();

        // Delete relations
        Yii::$app->db->createCommand()
            ->delete('{{%book_author}}', ['book_id' => $this->id])
            ->execute();

        return true;
    }
}
