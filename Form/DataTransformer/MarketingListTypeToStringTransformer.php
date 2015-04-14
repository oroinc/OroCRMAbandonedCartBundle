<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ManagerRegistry;

use OroCRM\Bundle\MarketingListBundle\Entity\MarketingListType;

class MarketingListTypeToStringTransformer implements DataTransformerInterface
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function transform($value)
    {
        return MarketingListType::TYPE_DYNAMIC;
    }

    /**
     * @param string $value
     * @return MarketingListType
     */
    public function reverseTransform($value)
    {
        if (!$value) {
            return null;
        }

        $marketingListType = $this->managerRegistry
            ->getRepository('OroCRMMarketingListBundle:MarketingListType')
            ->findOneBy(array('name' => $value))
        ;

        if (null === $marketingListType) {
            throw new TransformationFailedException(sprintf(
                'An Marketing List Type "%s" does not exist!',
                $value
            ));
        }

        return $marketingListType;
    }
}
