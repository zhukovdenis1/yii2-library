<?php

namespace app\modules\library\models\forms;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use app\modules\library\models\Book;

class BookForm extends Model
{
    public $id;
    public $title;
    public $year;
    public $isbn;
    public $description;
    public $author_ids = [];
    public $imageFile;
    public $cover_image;

    private $_book;
    private $_scenario;

    public function __construct($scenario = 'create', $book = null, $config = [])
    {
        $this->_scenario = $scenario;
        $this->_book = $book;

        if ($scenario === 'update' && !$book) {
            throw new \InvalidArgumentException('Book instance is required for update scenario');
        }

        parent::__construct($config);

        if ($book) {
            $this->id = $book->id;
            $this->title = $book->title;
            $this->year = $book->year;
            $this->isbn = $book->isbn;
            $this->description = $book->description;
            $this->cover_image = $book->cover_image;
        }

        if ($book && $book->authors) {
            $this->author_ids = ArrayHelper::getColumn($book->authors, 'id');
        }
    }

    public function rules()
    {
        $rules = [
            [['title', 'year', 'isbn', 'author_ids'], 'required'],
            ['title', 'string', 'max' => 255],
            ['year', 'integer', 'min' => 1000, 'max' => date('Y') + 10],
            ['description', 'string'],
            ['isbn', 'string', 'max' => 20],
            ['isbn', 'match', 'pattern' => '/^[\d\-]+$/', 'message' => 'ISBN can only contain numbers and dashes.'],
            ['isbn', 'unique', 'targetClass' => Book::class,
                'when' => function($model) {
                    // Проверяем уникальность только для новых записей или при изменении ISBN
                    return $model->isNewRecord ||
                        ($model->_book && $model->isbn !== $model->_book->isbn);
                }
            ],
            ['author_ids', 'each', 'rule' => ['integer']],
            ['imageFile', 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif', 'maxSize' => 1024 * 1024 * 5],
        ];

        return $rules;
    }

    public function attributeLabels()
    {
        return [
            'title' => Yii::t('app', 'Title'),
            'year' => Yii::t('app', 'Year'),
            'isbn' => Yii::t('app', 'ISBN'),
            'description' => Yii::t('app', 'Description'),
            'author_ids' => Yii::t('app', 'Authors'),
            'imageFile' => Yii::t('app', 'Cover Image'),
        ];
    }

    /**
     * Get book instance (for update scenario)
     */
    public function getBook()
    {
        return $this->_book;
    }

    /**
     * Get scenario
     */
    public function getScenarioName()
    {
        return $this->_scenario;
    }
}