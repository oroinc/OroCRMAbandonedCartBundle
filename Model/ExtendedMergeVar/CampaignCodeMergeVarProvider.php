<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\ExtendedMergeVar;

use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;
use OroCRM\Bundle\MailChimpBundle\Model\ExtendedMergeVar\ProviderInterface;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

class CampaignCodeMergeVarProvider implements ProviderInterface
{
    const CAMPAIGN_CODE_NAME = 'campaign_code';
    const CAMPAIGN_CODE_LABEL = 'Campaign Code';

    /**
     * @var AbandonedCartCampaignProviderInterface
     */
    protected $abandonedCartCampaignProvider;

    /**
     * @param AbandonedCartCampaignProviderInterface $abandonedCartCampaignProvider
     */
    public function __construct(AbandonedCartCampaignProviderInterface $abandonedCartCampaignProvider)
    {
        $this->abandonedCartCampaignProvider = $abandonedCartCampaignProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function provideExtendedMergeVars(MarketingList $marketingList)
    {
        $entity = $this->abandonedCartCampaignProvider
            ->getAbandonedCartCampaign($marketingList);

        if (is_null($entity)) {
            return [];
        }

        return [
            [
                'name' => self::CAMPAIGN_CODE_NAME,
                'label' => self::CAMPAIGN_CODE_LABEL
            ]
        ];
    }
}
