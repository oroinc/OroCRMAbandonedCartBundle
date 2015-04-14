<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Form\Handler;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ObjectRepository;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ValidatorInterface;
use Symfony\Component\Translation\TranslatorInterface;

use Oro\Bundle\SegmentBundle\Entity\SegmentType;

use OroCRM\Bundle\MarketingListBundle\Model\MarketingListSourceInterface;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignFactory;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignAbandonedCartRelationFactory;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingListType;
use OroCRM\Bundle\AbandonedCartBundle\Entity\CampaignAbandonedCartRelation;
use OroCRM\Bundle\AbandonedCartBundle\Form\Handler\AbandonedCartListHandler;
use OroCRM\Bundle\CampaignBundle\Entity\Campaign;

class AbandonedCartListHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbandonedCartListHandler
     */
    protected $handler;

    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var MarketingListSourceInterface
     */
    protected $marketingListSource;

    /**
     * @var CampaignFactory
     */
    protected $campaignFactory;

    /**
     * @var CampaignAbandonedCartRelationFactory
     */
    protected $campaignAbandonedCartRelationFactory;

    /**
     * @var MarketingList
     */
    protected $marketingList;

    /**
     * @var Campaign
     */
    protected $campaign;

    /**
     * @var CampaignAbandonedCartRelation
     */
    protected $campaignAbandonedCartRelation;

    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @var ObjectRepository
     */
    protected $repository;

    protected function setUp()
    {
        $registry = $this->getMockForAbstractClass('Symfony\Bridge\Doctrine\RegistryInterface');

        $this->manager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $registry->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($this->manager));

        $this->form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = new Request();

        $this->validator = $this->getMock('Symfony\Component\Validator\ValidatorInterface');
        $this->translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $this->marketingListSource = $this
            ->getMock('OroCRM\Bundle\MarketingListBundle\Model\MarketingListSourceInterface');

        $this->campaignFactory = $this
            ->getMock('OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignFactory');
        $this->campaignAbandonedCartRelationFactory = $this
            ->getMock('OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignAbandonedCartRelationFactory');

        $this->marketingList = new MarketingList();
        $this->campaign = new Campaign();
        $this->campaignAbandonedCartRelation = new CampaignAbandonedCartRelation();

        $this->handler = new AbandonedCartListHandler(
            $this->form,
            $this->request,
            $registry,
            $this->validator,
            $this->translator,
            $this->marketingListSource,
            $this->campaignFactory,
            $this->campaignAbandonedCartRelationFactory
        );

        $this->repository = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectRepository')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testProcessWhenCampaignRelationDoesNotExist()
    {
        $this->request->setMethod('POST');
        $this->assertProcessSegment();

        $this->form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->manager->expects($this->once())
            ->method('getRepository')
            ->with('OroCRMAbandonedCartBundle:CampaignAbandonedCartRelation')
            ->will($this->returnValue($this->repository));

        $this->repository
            ->expects($this->once())->method('findOneBy')
            ->will($this->returnValue(null));

        $this->campaignFactory->expects($this->once())
            ->method('create')
            ->with($this->marketingList)
            ->will($this->returnValue($this->campaign));

        $this->manager->expects($this->atLeastOnce())
            ->method('persist');

        $this->manager->expects($this->atLeastOnce())
            ->method('flush');

        $this->campaignAbandonedCartRelationFactory->expects($this->once())
            ->method('create')
            ->with($this->campaign, $this->marketingList)
            ->will($this->returnValue($this->campaignAbandonedCartRelation));

        $this->handler->process($this->marketingList);
    }

    public function testProcessWhenCampaignRelationExists()
    {
        $this->request->setMethod('POST');
        $this->assertProcessSegment();

        $this->form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->manager->expects($this->once())
            ->method('getRepository')
            ->with('OroCRMAbandonedCartBundle:CampaignAbandonedCartRelation')
            ->will($this->returnValue($this->repository));

        $this->repository
            ->expects($this->once())->method('findOneBy')
            ->will($this->returnValue($this->campaignAbandonedCartRelation));

        $this->campaignFactory->expects($this->never())
            ->method('create');

        $this->handler->process($this->marketingList);
    }

    protected function assertProcessSegment()
    {
        $formData = [
            'definition' => 'test'
        ];

        $this->form->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('test_form'));

        $this->request->request->set('test_form', $formData);
        $businessUnit = $this->getMockBuilder('Oro\Bundle\OrganizationBundle\Entity\BusinessUnit')
            ->disableOriginalConstructor()
            ->getMock();

        $owner = $this->getMockBuilder('Oro\Bundle\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $owner->expects($this->atLeastOnce())
            ->method('getOwner')
            ->will($this->returnValue($businessUnit));

        $this->marketingList->setName('test')
            ->setDescription('description')
            ->setType(new MarketingListType(MarketingListType::TYPE_DYNAMIC))
            ->setOwner($owner);

        $segmentType = new SegmentType(SegmentType::TYPE_DYNAMIC);
        $this->manager->expects($this->once())
            ->method('find')
            ->with('OroSegmentBundle:SegmentType', MarketingListType::TYPE_DYNAMIC)
            ->will($this->returnValue($segmentType));
    }

    public function testProcessWrongRequest()
    {
        $this->request->setMethod('GET');
        $this->assertFalse($this->handler->process(new MarketingList()));
    }
}
