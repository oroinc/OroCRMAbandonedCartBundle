<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartWorkflow;

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
     * @var AbandonedCartWorkflow
     * @ORM\OneToOne(
     *      targetEntity="AbandonedCartWorkflow"
     * )
     * @ORM\JoinColumn(name="workflow_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $workflow;

    /**
     * @var int
     *
     * @ORM\Column(name="emails_sent", type="integer", nullable=true)
     */
    protected $emailsSent;

    /**
     * @var int
     *
     * @ORM\Column(name="opens", type="integer", nullable=true)
     */
    protected $opens;

    /**
     * @var int
     *
     * @ORM\Column(name="unique_opens", type="integer", nullable=true)
     */
    protected $uniqueOpens;

    /**
     * @var int
     *
     * @ORM\Column(name="clicks", type="integer", nullable=true)
     */
    protected $clicks;

    /**
     * @var int
     *
     * @ORM\Column(name="unique_clicks", type="integer", nullable=true)
     */
    protected $uniqueClicks;

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
    public function getEmailsSent()
    {
        return $this->emailsSent;
    }

    /**
     * @param $emailsSent
     * @return $this
     */
    public function setEmailsSent($emailsSent)
    {
        $this->emailsSent = $emailsSent;
        return $this;
    }

    /**
     * @return int
     */
    public function getOpens()
    {
        return $this->opens;
    }

    /**
     * @param $opens
     * @return $this
     */
    public function setOpens($opens)
    {
        $this->opens = $opens;
        return $this;
    }

    /**
     * @return int
     */
    public function getUniqueOpens()
    {
        return $this->uniqueOpens;
    }

    /**
     * @param $uniqueOpens
     * @return $this
     */
    public function setUniqueOpens($uniqueOpens)
    {
        $this->uniqueOpens = $uniqueOpens;
        return $this;
    }

    /**
     * @return int
     */
    public function getClicks()
    {
        return $this->clicks;
    }

    /**
     * @param $clicks
     * @return $this
     */
    public function setClicks($clicks)
    {
        $this->clicks = $clicks;
        return $this;
    }

    /**
     * @return int
     */
    public function getUniqueClicks()
    {
        return $this->uniqueClicks;
    }

    /**
     * @param $uniqueClicks
     * @return $this
     */
    public function setUniqueClicks($uniqueClicks)
    {
        $this->uniqueClicks = $uniqueClicks;
        return $this;
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
     * @return AbandonedCartWorkflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * @param AbandonedCartWorkflow $workflow
     * @return $this
     */
    public function setWorkflow(AbandonedCartWorkflow $workflow)
    {
        $this->workflow = $workflow;
        return $this;
    }
}