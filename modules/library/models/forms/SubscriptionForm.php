<?php

namespace app\modules\library\models\forms;

use app\modules\library\models\Author;
use app\modules\library\models\Subscription;
use Yii;
use yii\base\Model;

/**
 * Subscription form for guests (unauthenticated users)
 */
class SubscriptionForm extends Model
{
    public $phone;
    public $author_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone', 'author_id'], 'required'],
            ['phone', 'string', 'max' => 20],
            ['phone', 'match', 'pattern' => '/^\+7\d{10}$/',
                'message' => 'Phone must be in format +79991234567 (e.g., +79161234567)'],
            ['author_id', 'integer'],
            ['author_id', 'exist', 'targetClass' => Author::class, 'targetAttribute' => 'id'],
            ['phone', 'validateUniqueSubscription'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'phone' => Yii::t('app', 'Phone Number'),
            'author_id' => Yii::t('app', 'Author'),
        ];
    }

    /**
     * Validate unique subscription
     */
    public function validateUniqueSubscription($attribute)
    {
        $exists = Subscription::find()
            ->where([
                'author_id' => $this->author_id,
                'phone' => $this->phone,
                'status' => Subscription::STATUS_ACTIVE,
            ])
            ->exists();

        if ($exists) {
            $this->addError($attribute, Yii::t('app', 'You are already subscribed to this author.'));
        }
    }

    /**
     * Subscribe to author
     *
     * @return Subscription|null
     */
    public function subscribe()
    {
        if (!$this->validate()) {
            return null;
        }

        $subscription = new Subscription();
        $subscription->author_id = $this->author_id;
        $subscription->phone = $this->phone;
        $subscription->status = Subscription::STATUS_ACTIVE;

        if ($subscription->save()) {
            return $subscription;
        }

        return null;
    }
}
