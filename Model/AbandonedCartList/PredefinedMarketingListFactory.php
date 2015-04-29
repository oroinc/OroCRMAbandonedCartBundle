<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList;

use Doctrine\Common\Persistence\ObjectManager;

use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingListType;

class PredefinedMarketingListFactory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $cartClassName;

    /**
     * @var string
     */
    protected $marketingListTypeClassName;

    /**
     * @param ObjectManager $objectManager
     * @param $cartClassName
     * @param $marketingListTypeClassName
     */
    public function __construct(ObjectManager $objectManager, $cartClassName, $marketingListTypeClassName)
    {
        if (!is_string($cartClassName) || empty($cartClassName)) {
            throw new \InvalidArgumentException('Cart class name must be provided.');
        }

        $this->objectManager = $objectManager;
        $this->cartClassName = $cartClassName;
        $this->marketingListTypeClassName = $marketingListTypeClassName;
    }

    /**
     * @return MarketingList
     */
    public function create()
    {
        $marketingList = new MarketingList();
        $marketingList->setEntity($this->cartClassName);

        /** @var MarketingListType $type */
        $type = $this->objectManager
            ->find(
                $this->marketingListTypeClassName,
                MarketingListType::TYPE_DYNAMIC
            );

        $marketingList->setType($type);

        return $marketingList;
    }
}
