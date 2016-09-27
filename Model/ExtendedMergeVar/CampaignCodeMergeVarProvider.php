<?php

namespace Oro\Bundle\AbandonedCartBundle\Model\ExtendedMergeVar;

use Symfony\Component\Translation\TranslatorInterface;

use Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use Oro\Bundle\MailChimpBundle\Model\ExtendedMergeVar\ProviderInterface;

class CampaignCodeMergeVarProvider implements ProviderInterface
{
    const CAMPAIGN_CODE_NAME = 'campaign_code';

    /**
     * @var AbandonedCartCampaignProviderInterface
     */
    protected $abandonedCartCampaignProvider;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param AbandonedCartCampaignProviderInterface $abandonedCartCampaignProvider
     * @param TranslatorInterface $translator
     */
    public function __construct(
        AbandonedCartCampaignProviderInterface $abandonedCartCampaignProvider,
        TranslatorInterface $translator
    ) {
        $this->abandonedCartCampaignProvider = $abandonedCartCampaignProvider;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(MarketingList $marketingList)
    {
        return (bool)$this->abandonedCartCampaignProvider->getAbandonedCartCampaign($marketingList);
    }

    /**
     * {@inheritdoc}
     */
    public function provideExtendedMergeVars(MarketingList $marketingList)
    {
        if (!$this->isApplicable($marketingList)) {
            return [];
        }

        return [
            [
                'name' => self::CAMPAIGN_CODE_NAME,
                'label' => $this->translator->trans('oro.abandonedcart.campaign_code_mergevar.label')
            ]
        ];
    }
}
