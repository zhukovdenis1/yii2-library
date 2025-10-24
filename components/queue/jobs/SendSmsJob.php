<?php

namespace app\components\queue\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\RetryableJobInterface;

/**
 * SMS sending job for queue
 */
class SendSmsJob extends BaseObject implements JobInterface, RetryableJobInterface
{
    /**
     * @var string Phone number
     */
    public $phone;

    /**
     * @var string SMS message text
     */
    public $message;

    /**
     * @var int Book ID (optional, for logging)
     */
    public $bookId;

    /**
     * @var int Author ID (optional, for logging)
     */
    public $authorId;

    /**
     * Execute job
     *
     * @param \yii\queue\Queue $queue
     * @return void
     * @throws \Exception
     */
    public function execute($queue)
    {
        $smsService = Yii::$app->smsService;

        try {
            // Send SMS
            $result = $smsService->send($this->phone, $this->message);

            // Log successful sending
            Yii::info([
                'phone' => $this->phone,
                'book_id' => $this->bookId,
                'author_id' => $this->authorId,
                'result' => $result,
                'status' => 'success',
            ], 'sms.queue');

        } catch (\Exception $e) {
            // Log error
            Yii::error([
                'phone' => $this->phone,
                'book_id' => $this->bookId,
                'author_id' => $this->authorId,
                'error' => $e->getMessage(),
                'status' => 'failed',
            ], 'sms.queue');

            // Re-throw exception to trigger retry
            throw $e;
        }
    }

    /**
     * Time to reserve (TTR) in seconds
     * Maximum time for job execution
     *
     * @return int
     */
    public function getTtr()
    {
        return 60; // 60 seconds
    }

    /**
     * Number of retry attempts
     *
     * @param int $attempt Current attempt number
     * @param \Exception $error Exception from previous attempt
     * @return bool Whether to retry
     */
    public function canRetry($attempt, $error)
    {
        // Retry up to 3 times
        return $attempt < 3;
    }
}
