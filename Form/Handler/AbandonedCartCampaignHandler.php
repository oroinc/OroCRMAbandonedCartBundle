<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ValidatorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\MarketingListBundle\Form\Handler\MarketingListHandler;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\AbandonedCartCampaignFactory;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;

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
     * @param Request $request
     * @param RegistryInterface $doctrine
     * @param ValidatorInterface $validator
     * @param TranslatorInterface $translator
     * @param AbandonedCartCampaignFactory $campaignAbandonedCartRelationFactory
     * @param AbandonedCartCampaignProviderInterface $abandonedCartCampaignProvider
     */
    public function __construct(
        FormInterface $form,
        Request $request,
        RegistryInterface $doctrine,
        ValidatorInterface $validator,
        TranslatorInterface $translator,
        AbandonedCartCampaignFactory $campaignAbandonedCartRelationFactory,
        AbandonedCartCampaignProviderInterface $abandonedCartCampaignProvider
    ) {
        parent::__construct($form, $request, $doctrine, $validator, $translator);
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
