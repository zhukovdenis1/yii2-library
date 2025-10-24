<?php

namespace app\components\services;

use Yii;
use yii\base\Component;
use yii\httpclient\Client;

/**
 * SMS Service for sending SMS via SmsPilot API
 */
class SmsService extends Component
{
    /**
     * @var string SmsPilot API key
     */
    public $apiKey;

    /**
     * @var string SmsPilot API URL
     */
    public $apiUrl = 'https://smspilot.ru/api.php';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (!$this->apiKey) {
            throw new \yii\base\InvalidConfigException('SMS Service API key must be set.');
        }
    }

    /**
     * Send SMS message
     *
     * @param string $phone Phone number in format +79991234567
     * @param string $message SMS text message
     * @return array Response from SmsPilot API
     * @throws \Exception
     */
    public function send($phone, $message)
    {
        // Format phone number (remove non-digits)
        $phone = $this->formatPhone($phone);

        // Validate phone
        if (!$this->validatePhone($phone)) {
            throw new \Exception("Invalid phone number format: {$phone}");
        }

        try {
            // Create HTTP client
            $client = new Client();

            $response = $client->createRequest()
                ->setMethod('POST')
                ->setUrl($this->apiUrl)
                ->setData([
                    'send' => $message,
                    'to' => $phone,
                    'apikey' => $this->apiKey,
                    'format' => 'json',
                ])
                ->send();

            if (!$response->isOk) {
                throw new \Exception("SMS API HTTP error: " . $response->statusCode);
            }

            $result = $response->data;

            // Check for API errors
            if (isset($result['error'])) {
                $errorMsg = isset($result['error']['description'])
                    ? $result['error']['description']
                    : 'Unknown API error';
                throw new \Exception("SMS API error: " . $errorMsg);
            }

            Yii::info([
                'phone' => $phone,
                'message' => $message,
                'result' => $result,
            ], 'sms.success');

            return $result;

        } catch (\Exception $e) {
            Yii::error([
                'phone' => $phone,
                'message' => $message,
                'error' => $e->getMessage(),
            ], 'sms.error');

            throw $e;
        }
    }

    /**
     * Format phone number (remove all non-digits)
     *
     * @param string $phone
     * @return string
     */
    protected function formatPhone($phone)
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    /**
     * Validate phone number format
     *
     * @param string $phone Phone without formatting (digits only)
     * @return bool
     */
    protected function validatePhone($phone)
    {
        // Russian phone: 11 digits starting with 7
        return preg_match('/^7\d{10}$/', $phone);
    }
}
