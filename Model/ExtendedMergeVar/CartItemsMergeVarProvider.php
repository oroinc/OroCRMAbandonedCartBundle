<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\ExtendedMergeVar;

use Symfony\Component\Translation\TranslatorInterface;

use OroCRM\Bundle\MailChimpBundle\Model\ExtendedMergeVar\ProviderInterface;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;

class CartItemsMergeVarProvider implements ProviderInterface
{
    const CART_ITEMS_LIMIT = 3;
    const NAME_PREFIX = 'item';
    const CART_ITEM_NAME = '%s_%d';

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
     * @param MarketingList $marketingList
     * @return array
     */
    public function provideExtendedMergeVars(MarketingList $marketingList)
    {
        $entity = $this->abandonedCartCampaignProvider
            ->getAbandonedCartCampaign($marketingList);

        if (is_null($entity)) {
            return [];
        }

        $mergeVars = [];
        for ($i = 1; $i <= self::CART_ITEMS_LIMIT; $i++) {

            $label = $this->translator
                ->trans(
                    'orocrm.abandonedcart.cart_item_mergevar.label',
                    ['%index%' => $i]
                );

            $mergeVars[] = [
                'name' => sprintf(self::CART_ITEM_NAME, self::NAME_PREFIX, $i),
                'label' => $label
            ];
        }

        return $mergeVars;
    }
}
