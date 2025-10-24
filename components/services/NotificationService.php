<?php

namespace app\components\services;

use Yii;
use yii\base\Component;
use app\modules\library\models\Subscription;
use app\components\queue\jobs\SendSmsJob;

/**
 * Notification Service
 * Manages sending notifications via queue
 */
class NotificationService extends Component
{
    /**
     * Notify subscribers about new book
     *
     * @param \app\modules\library\models\Book $book
     * @param array $authorIds Author IDs
     * @return array Array of job IDs
     */
    public function notifyNewBook($book, $authorIds)
    {
        if (empty($authorIds)) {
            return [];
        }

        // Find all active subscriptions for these authors
        $subscriptions = Subscription::find()
            ->where(['author_id' => $authorIds, 'status' => Subscription::STATUS_ACTIVE])
            ->with('author')
            ->all();

        if (empty($subscriptions)) {
            Yii::info("No active subscriptions found for authors: " . implode(', ', $authorIds), 'sms.notification');
            return [];
        }

        $jobIds = [];

        // Group subscriptions by phone to avoid duplicate SMS
        $sentPhones = [];

        foreach ($subscriptions as $subscription) {
            // Skip if already sent to this phone (book has multiple authors)
            if (in_array($subscription->phone, $sentPhones)) {
                continue;
            }

            $author = $subscription->author;

            // Prepare message
            $message = Yii::t('app', 'New book "{title}" by {author} is now available!', [
                'title' => $book->title,
                'author' => $author->getFullName(),
            ]);

            // Push job to queue
            $jobId = Yii::$app->queue->push(new SendSmsJob([
                'phone' => $subscription->phone,
                'message' => $message,
                'bookId' => $book->id,
                'authorId' => $author->id,
            ]));

            $jobIds[] = $jobId;
            $sentPhones[] = $subscription->phone;

            Yii::info("SMS job #{$jobId} queued for {$subscription->phone} (Author: {$author->getFullName()}, Book: {$book->title})", 'sms.notification');
        }

        Yii::info("Total SMS jobs queued: " . count($jobIds), 'sms.notification');

        return $jobIds;
    }

    /**
     * Send test SMS (for debugging)
     *
     * @param string $phone
     * @param string $message
     * @return int Job ID
     */
    public function sendTestSms($phone, $message)
    {
        return Yii::$app->queue->push(new SendSmsJob([
            'phone' => $phone,
            'message' => $message,
            'bookId' => null,
            'authorId' => null,
        ]));
    }
}
