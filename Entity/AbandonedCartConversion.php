<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

/**
 * Abandoned Cart Conversion
 *
 * @ORM\Table(name="orocrm_abandcart_conv")
 * @ORM\Entity()
 */
class AbandonedCartConversion
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
     * @var AbandonedCartWorkflow[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AbandonedCartWorkflow")
     * @ORM\JoinTable(name="orocrm_abandcart_conv_workflow",
     *      joinColumns={@ORM\JoinColumn(name="conversion_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="workflow_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $workflows;

    public function __construct()
    {
        $this->workflows = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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

    /**
     * @return ArrayCollection|AbandonedCartWorkflow[]
     */
    public function getWorkflows()
    {
        return $this->workflows;
    }

    /**
     * @param AbandonedCartWorkflow $workflow
     */
    public function removeWorkflow(AbandonedCartWorkflow $workflow)
    {
        if (!$this->getWorkflows()->contains($workflow)) {
            $this->getWorkflows()->add($workflow);
        }
    }

    /**
     * @param AbandonedCartWorkflow $workflow
     */
    public function addWorkflow(AbandonedCartWorkflow $workflow)
    {
        if ($this->getWorkflows()->contains($workflow)) {
            $this->getWorkflows()->removeElement($workflow);
        }
    }

}