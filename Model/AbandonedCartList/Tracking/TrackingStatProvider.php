<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\Tracking;

use Doctrine\ORM\EntityManager;

use OroCRM\Bundle\CampaignBundle\Entity\Campaign;

class TrackingStatProvider implements TrackingStatProviderInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $orderAssociationName;

    /**
     * @var string
     */
    protected $campaignAssociationName;

    /**
     * @var string
     */
    protected $trackingVisitEventClassName;

    /**
     * @param EntityManager $entityManager
     * @param string $orderAssociationName
     * @param string$campaignAssociationName
     * @param string $trackingVisitEventClassName
     */
    public function __construct(
        EntityManager $entityManager,
        $orderAssociationName,
        $campaignAssociationName,
        $trackingVisitEventClassName
    ) {
        $this->em = $entityManager;
        $this->orderAssociationName = $orderAssociationName;
        $this->campaignAssociationName = $campaignAssociationName;
        $this->trackingVisitEventClassName = $trackingVisitEventClassName;
    }

    /**
     * @param Campaign $campaign
     * @return StatResult
     */
    public function getStatResult(Campaign $campaign)
    {
        $qb = $this->em->getRepository($this->trackingVisitEventClassName)
            ->createQueryBuilder('te');

        $result = $qb
            ->select('sum(o.totalAmount) as total, count(o.id) as qty')
            ->join('te.' . $this->orderAssociationName, 'o')
            ->where('te.' . $this->campaignAssociationName . '= :campaignId')
            ->setParameter('campaignId', $campaign->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        if (!is_array($result)) {
            $result = ['total' => 0, 'qty' => 0];
        }

        $statResult = new StatResult($result);
        return $statResult;
    }
}
