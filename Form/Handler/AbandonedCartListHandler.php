<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Form\Handler;

use OroCRM\Bundle\MarketingListBundle\Model\MarketingListSourceInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\ValidatorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

use OroCRM\Bundle\MarketingListBundle\Form\Handler\MarketingListHandler;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignFactory;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignAbandonedCartRelationFactory;

class AbandonedCartListHandler extends MarketingListHandler
{
    /**
     * @var CampaignFactory
     */
    protected $campaignFactory;

    /**
     * @var CampaignAbandonedCartRelationFactory
     */
    protected $campaignAbandonedCartRelationFactory;

    /**
     * @param FormInterface $form
     * @param Request $request
     * @param RegistryInterface $doctrine
     * @param ValidatorInterface $validator
     * @param TranslatorInterface $translator
     * @param CampaignFactory $campaignFactory
     * @param CampaignAbandonedCartRelationFactory $campaignAbandonedCartRelationFactory
     * @param MarketingListSourceInterface $suitableSource
     */
    public function __construct(
        FormInterface $form,
        Request $request,
        RegistryInterface $doctrine,
        ValidatorInterface $validator,
        TranslatorInterface $translator,
        MarketingListSourceInterface $suitableSource,
        CampaignFactory $campaignFactory,
        CampaignAbandonedCartRelationFactory $campaignAbandonedCartRelationFactory
    ) {
        parent::__construct($form, $request, $doctrine, $validator, $translator, $suitableSource);
        $this->campaignFactory = $campaignFactory;
        $this->campaignAbandonedCartRelationFactory = $campaignAbandonedCartRelationFactory;
    }

    /**
     * @param MarketingList $marketingList
     * @return bool
     */
    public function process(MarketingList $marketingList)
    {
        if (parent::process($marketingList)) {
            $this->processCampaign($marketingList);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param MarketingList $marketingList
     */
    protected function processCampaign(MarketingList $marketingList)
    {
        $campaignRelation = $this->manager->getRepository('OroCRMAbandonedCartBundle:CampaignAbandonedCartRelation')
            ->findOneBy(array('marketingList' => $marketingList->getId()));

        if ($campaignRelation) {
            return;
        }

        $campaign = $this->campaignFactory->create($marketingList);
        $this->manager->persist($campaign);
        $this->manager->flush();

        $campaignAbandonedCartRelation = $this->campaignAbandonedCartRelationFactory
            ->create($campaign, $marketingList);
        $this->manager->persist($campaignAbandonedCartRelation);
        $this->manager->flush();
    }
}
