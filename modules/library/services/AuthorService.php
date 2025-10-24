<?php

namespace app\modules\library\services;

use app\modules\library\models\Author;
use app\modules\library\models\forms\SubscriptionForm;
use app\modules\library\models\Subscription;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * Author Service
 * Contains all business logic for authors
 */
class AuthorService
{
    /**
     * Get authors data provider
     *
     * @param int $pageSize
     * @return ActiveDataProvider
     */
    public function getDataProvider(int $pageSize = 20)
    {
        $query = Author::find()
            ->select([
                '{{%author}}.*',
                'book_count' => 'COUNT({{%book_author}}.book_id)',
            ])
            ->leftJoin('{{%book_author}}', '{{%book_author}}.author_id = {{%author}}.id')
            ->groupBy('{{%author}}.id')
            ->orderBy(['last_name' => SORT_ASC, 'first_name' => SORT_ASC]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'db' => Yii::$app->db
        ]);
    }


    /**
     * Get author's books data provider
     *
     * @param Author $author
     * @param int $pageSize
     * @return ActiveDataProvider
     */
    public function getAuthorBooksDataProvider(Author $author, int $pageSize = 12)
    {
        return new ActiveDataProvider([
            'query' => $author->getBooks()->with('authors')->orderBy(['year' => SORT_DESC, 'title' => SORT_ASC]),
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);
    }

    /**
     * Get all authors for dropdown
     *
     * @return Author[]
     */
    public function getAllAuthors()
    {
        return Author::find()->orderBy(['last_name' => SORT_ASC])->all();
    }

    /**
     * Find author by ID
     *
     * @param int $id
     * @return Author|null
     */
    public function findById($id)
    {
        return Author::find()
            ->where(['id' => $id])
            ->with('books')
            ->one();
    }

    /**
     * Create new author
     *
     * @param array $data
     * @return Author
     */
    public function create(array $data)
    {
        $author = new Author();
        $author->load($data);

        if ($author->save()) {
            return $author;
        }

        return $author; // Return with errors
    }

    /**
     * Update author
     *
     * @param Author $author
     * @param array $data
     * @return bool
     */
    public function update(Author $author, array $data)
    {
        $author->load($data);
        return $author->save();
    }

    /**
     * Delete author
     *
     * @param Author $author
     * @return bool
     * @throws \Exception
     */
    public function delete(Author $author)
    {
        if ($author->getBooks()->exists()) {
            throw new \Exception(Yii::t('app', 'Cannot delete author with books. Please delete books first.'));
        }

        return $author->delete() !== false;
    }

    /**
     * Subscribe to author
     *
     * @param int $authorId
     * @param string $phone
     * @return Subscription|null
     * @throws \Exception
     */
    public function subscribe(int $authorId, string $phone)
    {
        $form = new SubscriptionForm();
        $form->author_id = $authorId;
        $form->phone = $phone;

        return $form->subscribe();
    }

    /**
     * Check if author can be deleted
     *
     * @param Author $author
     * @return bool
     */
    public function canDelete(Author $author)
    {
        return !$author->getBooks()->exists();
    }
}
