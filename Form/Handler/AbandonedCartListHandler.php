<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Form\Handler;

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
     * @var FormInterface
     */
    protected $form;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

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
     */
    public function __construct(
        FormInterface $form,
        Request $request,
        RegistryInterface $doctrine,
        ValidatorInterface $validator,
        TranslatorInterface $translator,
        CampaignFactory $campaignFactory,
        CampaignAbandonedCartRelationFactory $campaignAbandonedCartRelationFactory
    ) {
        $this->form = $form;
        $this->request = $request;
        $this->manager = $doctrine->getManager();
        $this->validator = $validator;
        $this->translator = $translator;
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

        $campaignAbandonedCartRelationFactory = $this->campaignAbandonedCartRelationFactory
            ->create($campaign, $marketingList);
        $this->manager->persist($campaignAbandonedCartRelationFactory);
        $this->manager->flush();
    }
}
