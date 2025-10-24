<?php

namespace app\modules\library\services;

use Yii;
use app\modules\library\models\Book;
use app\modules\library\models\forms\BookForm;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;

class BookService
{
    public function getDataProvider($pageSize = 12)
    {
        //Более оптимально join (как в AuthorService). Но так не критично, и для демонстрации другого способа
        return new ActiveDataProvider([
            'query' => Book::find()->with('authors')->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);
    }

    public function getRecentBooks($limit = 6)
    {
        return Book::find()
            ->with('authors')
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    public function findById($id)
    {
        return Book::find()
            ->where(['id' => $id])
            ->with('authors')
            ->one();
    }

    public function createFromForm(BookForm $form)
    {
        $book = new Book();
        return $this->saveBookFromForm($book, $form);
    }

    public function updateFromForm(Book $book, BookForm $form)
    {
        return $this->saveBookFromForm($book, $form);
    }

    private function saveBookFromForm(Book $book, BookForm $form)
    {
        $book->title = $form->title;
        $book->year = $form->year;
        $book->isbn = $form->isbn;
        $book->description = $form->description;

        $isNewRecord = $book->isNewRecord;
        $oldImage = $book->cover_image;

        // Upload image
        if ($form->imageFile) {
            $fileName = Yii::$app->fileUploadService->upload($form->imageFile, $isNewRecord ? null : $oldImage);
            if ($fileName) {
                $book->cover_image = $fileName;
            }
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$book->save(false)) {
                throw new \Exception('Failed to save book.');
            }

            // Save book-author relations
            if (!$isNewRecord) {
                $this->deleteBookAuthors($book->id);
            }
            $this->saveBookAuthors($book->id, $form->author_ids);

            $transaction->commit();

            // Send notifications for new books
            if ($isNewRecord) {
                Yii::$app->notificationService->notifyNewBook($book, $form->author_ids);
            }

            return $book;

        } catch (\Exception $e) {
            $transaction->rollBack();

            // Delete uploaded image if creation failed
            if ($isNewRecord && isset($fileName)) {
                Yii::$app->fileUploadService->deleteFile($fileName);
            }

            Yii::error($e->getMessage(), 'book.' . ($isNewRecord ? 'create' : 'update'));
            throw $e;
        }
    }

    public function delete(Book $book)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Delete cover image
            if ($book->cover_image) {
                Yii::$app->fileUploadService->deleteFile($book->cover_image);
            }

            // Delete relations
            $this->deleteBookAuthors($book->id);

            if (!$book->delete()) {
                throw new \Exception('Failed to delete book.');
            }

            $transaction->commit();

            return true;

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), 'book.delete');
            throw $e;
        }
    }

    protected function saveBookAuthors($bookId, array $authorIds)
    {
        if (empty($authorIds)) {
            return;
        }

        foreach ($authorIds as $authorId) {
            Yii::$app->db->createCommand()->insert('{{%book_author}}', [
                'book_id' => $bookId,
                'author_id' => $authorId,
            ])->execute();
        }
    }

    protected function deleteBookAuthors($bookId)
    {
        Yii::$app->db->createCommand()
            ->delete('{{%book_author}}', ['book_id' => $bookId])
            ->execute();
    }
}