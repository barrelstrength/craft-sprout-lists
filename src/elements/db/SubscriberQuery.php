<?php
/**
 * @link https://sprout.barrelstrengthdesign.com
 * @copyright Copyright (c) Barrel Strength Design LLC
 * @license https://craftcms.github.io/license
 */

namespace barrelstrength\sproutlists\elements\db;

use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;

/**
 * Class SubscriberQuery
 *
 * @package barrelstrength\sproutlists\elements\db
 */
class SubscriberQuery extends ElementQuery
{
    /**
     * @var int
     */
    public $listId;

    /**
     * @var string
     */
    public $email;

    /**
     * @param $value
     *
     * @return static self reference
     */
    public function email($value): SubscriberQuery
    {
        $this->email = $value;

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function beforePrepare(): bool
    {
        $this->joinElementTable('sproutlists_subscribers');

        $this->query->select([
            'sproutlists_subscribers.userId',
            'sproutlists_subscribers.email',
            'sproutlists_subscribers.firstName',
            'sproutlists_subscribers.lastName',
            'sproutlists_subscribers.dateCreated',
            'sproutlists_subscribers.dateUpdated'
        ]);

        if ($this->listId) {

            $subscriberIds = (new Query())
                ->select(['itemId'])
                ->from(['{{%sproutlists_subscriptions}}'])
                ->where(['listId' => $this->listId])
                ->column();

            // Only return subscribers that match this query
            $this->subQuery->andWhere([
                'in',
                'sproutlists_subscribers.id',
                array_unique($subscriberIds, SORT_REGULAR)
            ]);
        }

        if ($this->email) {
            $this->subQuery->andWhere(Db::parseParam('sproutlists_subscribers.email', $this->email, '=', true));
        }

        return parent::beforePrepare();
    }
}
