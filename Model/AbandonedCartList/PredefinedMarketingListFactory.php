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
     * @param ObjectManager $objectManager
     * @param string $cartClassName
     */
    public function __construct(ObjectManager $objectManager, $cartClassName)
    {
        if (!is_string($cartClassName) || empty($cartClassName)) {
            throw new \InvalidArgumentException('Cart class name must be provided.');
        }

        $this->objectManager = $objectManager;
        $this->cartClassName = $cartClassName;
    }

    /**
     * @return MarketingList
     */
    public function create()
    {
        $marketingList = new MarketingList();
        $marketingList->setEntity($this->cartClassName);

        $type = $this->objectManager
            ->find(
                'OroCRMMarketingListBundle:MarketingListType',
                MarketingListType::TYPE_DYNAMIC
            );

        $marketingList->setType($type);

        return $marketingList;
    }
}
