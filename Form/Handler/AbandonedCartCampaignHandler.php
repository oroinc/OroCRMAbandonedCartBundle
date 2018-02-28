<?php

namespace Oro\Bundle\AbandonedCartBundle\Form\Handler;

use Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;
use Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartList\AbandonedCartCampaignFactory;
use Oro\Bundle\MarketingListBundle\Entity\MarketingList;
use Oro\Bundle\MarketingListBundle\Form\Handler\MarketingListHandler;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ValidatorInterface;

class AbandonedCartCampaignHandler extends MarketingListHandler
{
    /**
     * @var AbandonedCartCampaignFactory
     */
    protected $abandonedCartCampaignFactory;

    /**
     * @var AbandonedCartCampaignProviderInterface
     */
    protected $abandonedCartCampaignProvider;

    /**
     * @param FormInterface $form
     * @param RequestStack $requestStack
     * @param RegistryInterface $doctrine
     * @param ValidatorInterface $validator
     * @param TranslatorInterface $translator
     * @param AbandonedCartCampaignFactory $campaignAbandonedCartRelationFactory
     * @param AbandonedCartCampaignProviderInterface $abandonedCartCampaignProvider
     */
    public function __construct(
        FormInterface $form,
        RequestStack $requestStack,
        RegistryInterface $doctrine,
        ValidatorInterface $validator,
        TranslatorInterface $translator,
        AbandonedCartCampaignFactory $campaignAbandonedCartRelationFactory,
        AbandonedCartCampaignProviderInterface $abandonedCartCampaignProvider
    ) {
        parent::__construct($form, $requestStack, $doctrine, $validator, $translator);
        $this->abandonedCartCampaignFactory = $campaignAbandonedCartRelationFactory;
        $this->abandonedCartCampaignProvider = $abandonedCartCampaignProvider;
    }

    /**
     * @param MarketingList $marketingList
     * @return bool
     */
    public function process(MarketingList $marketingList)
    {
        if (parent::process($marketingList)) {
            $this->processAbandonedCartCampaign($marketingList);
            return true;
        }

        return false;
    }

    /**
     * @param MarketingList $marketingList
     */
    protected function processAbandonedCartCampaign(MarketingList $marketingList)
    {
        $abandonedCartCampaign = $this->abandonedCartCampaignProvider
            ->getAbandonedCartCampaign($marketingList);

        if (is_null($abandonedCartCampaign)) {
            $abandonedCartCampaign = $this->abandonedCartCampaignFactory
                ->create($marketingList);
        }

        $this->manager->persist($abandonedCartCampaign);
        $this->manager->flush($abandonedCartCampaign);
    }
}
