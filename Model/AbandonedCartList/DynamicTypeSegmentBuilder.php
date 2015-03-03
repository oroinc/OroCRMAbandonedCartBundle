<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList;

use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\SegmentBundle\Entity\Segment;
use Oro\Bundle\SegmentBundle\Entity\SegmentType;
use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartList;

class DynamicTypeSegmentBuilder implements SegmentBuilder
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function build()
    {
        $segment = new Segment();
        $segment->setEntity(AbandonedCartList::ENTITY_FULL_NAME);
        $segment->setType($this->createSegmentType());
        return $segment;
    }

    /**
     * @return SegmentType
     */
    protected function createSegmentType()
    {
        return $this->objectManager->find('OroSegmentBundle:SegmentType', SegmentType::TYPE_DYNAMIC);
    }
}
