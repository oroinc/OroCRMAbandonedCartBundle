<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

/**
 * Abandoned Cart Statistics combined from MailChimp and Magento
 *
 * @ORM\Table(name="orocrm_abandcart_stats")
 * @ORM\Entity()
 */
class AbandonedCartStatistics
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var MarketingList
     *
     * @ORM\OneToOne(
     *      targetEntity="OroCRM\Bundle\MarketingListBundle\Entity\MarketingList"
     * )
     * @ORM\JoinColumn(name="marketing_list_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $marketingList;

    /**
     * @var int
     *
     * @ORM\Column(name="converted_to_orders", type="integer", nullable=true)
     */
    protected $convertedToOrders;

    /**
     * @var string
     *
     * @ORM\Column(name="total_sum", type="text", nullable=true)
     */
    protected $totalSum;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getConvertedToOrders()
    {
        return $this->convertedToOrders;
    }

    /**
     * @param $convertedToOrders
     * @return $this
     */
    public function setConvertedToOrders($convertedToOrders)
    {
        $this->convertedToOrders = $convertedToOrders;
        return $this;
    }

    /**
     * @return string
     */
    public function getTotalSum()
    {
        return $this->totalSum;
    }

    /**
     * @param $totalSum
     * @return $this
     */
    public function setTotalSum($totalSum)
    {
        $this->totalSum = $totalSum;
        return $this;
    }

    /**
     * @return MarketingList
     */
    public function getMarketingList()
    {
        return $this->marketingList;
    }

    /**
     * @param $marketingList
     * @return $this
     */
    public function setMarketingList(MarketingList $marketingList)
    {
        $this->marketingList = $marketingList;

        return $this;
    }
}
