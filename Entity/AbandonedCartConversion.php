<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use OroCRM\Bundle\MailChimpBundle\Entity\Campaign;
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
     * @var Campaign[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="OroCRM\Bundle\MailChimpBundle\Entity\Campaign")
     * @ORM\JoinTable(name="orocrm_abandcart_conv_camps",
     *      joinColumns={@ORM\JoinColumn(name="conversion_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="mailchimp_campaign_id", referencedColumnName="id",
     *      onDelete="CASCADE")}
     * )
     */
    protected $campaigns;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->campaigns = new ArrayCollection();
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
     * @return AbandonedCartConversion
     */
    public function setMarketingList(MarketingList $marketingList)
    {
        $this->marketingList = $marketingList;

        return $this;
    }

    /**
     * @return ArrayCollection|Campaign[]
     */
    public function getCampaigns()
    {
        return $this->campaigns;
    }

    /**
     * @param Campaign $campaign
     */
    public function removeCampaign(Campaign $campaign)
    {
        if ($this->getCampaigns()->contains($campaign)) {
            $this->getCampaigns()->removeElement($campaign);
        }
    }

    /**
     * @param Campaign $campaign
     */
    public function addCampaign(Campaign $campaign)
    {
        if (!$this->getCampaigns()->contains($campaign)) {
            $this->getCampaigns()->add($campaign);
        }
    }
}
