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

    const URL_MERGE_VAR   = 'url';
    const NAME_MERGE_VAR  = 'name';
    const QTY_MERGE_VAR   = 'qty';
    const PRICE_MERGE_VAR = 'price';
    const TOTAL_MERGE_VAR = 'total';

    /**
     * @var AbandonedCartCampaignProviderInterface
     */
    protected $abandonedCartCampaignProvider;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var array
     */
    protected $cartItemFields = [
        self::URL_MERGE_VAR,
        self::NAME_MERGE_VAR,
        self::QTY_MERGE_VAR,
        self::PRICE_MERGE_VAR,
        self::TOTAL_MERGE_VAR
    ];

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

        $mergeVars = [];
        for ($i = 1; $i <= self::CART_ITEMS_LIMIT; $i++) {
            foreach ($this->cartItemFields as $field) {
                $mergeVars[] = [
                    'name' => sprintf('%s_%d_%s', self::NAME_PREFIX, $i, $field),
                    'label' => $this->translateLabel($field, $i)
                ];
            }
        }

        return $mergeVars;
    }

    /**
     * @param string $field
     * @param int $index
     * @return string
     */
    protected function translateLabel($field, $index)
    {
        return $this->translator->trans(
            sprintf('orocrm.abandonedcart.mergevar.cart_item.%s.label', $field),
            ['%index%' => $index]
        );
    }
}
