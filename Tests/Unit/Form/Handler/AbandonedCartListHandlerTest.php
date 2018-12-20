<?php

namespace Oro\Bundle\AbandonedCartBundle\Tests\Unit\Form\Handler;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Oro\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign;
use Oro\Bundle\AbandonedCartBundle\Form\Handler\AbandonedCartCampaignHandler;
use Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;
use Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartList\AbandonedCartCampaignFactory;
use Oro\Bundle\MarketingListBundle\Entity\MarketingList;
use Oro\Bundle\MarketingListBundle\Entity\MarketingListType;
use Oro\Bundle\OrganizationBundle\Entity\BusinessUnit;
use Oro\Bundle\SegmentBundle\Entity\SegmentType;
use Oro\Bundle\UserBundle\Entity\User;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbandonedCartListHandlerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AbandonedCartCampaignHandler
     */
    protected $handler;

    /**
     * @var MockObject|Form
     */
    protected $form;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var MockObject|ValidatorInterface
     */
    protected $validator;

    /**
     * @var MockObject|TranslatorInterface
     */
    protected $translator;

    /**
     * @var MockObject|AbandonedCartCampaignFactory
     */
    protected $abandonedCartCampaignFactory;

    /**
     * @var MarketingList
     */
    protected $marketingList;

    /**
     * @var AbandonedCartCampaign
     */
    protected $abandonedCartCampaign;

    /**
     * @var MockObject|EntityManager
     */
    protected $manager;

    /**
     * @var MockObject|ObjectRepository
     */
    protected $repository;

    /**
     * @var MockObject|AbandonedCartCampaignProviderInterface
     */
    protected $abandonedCartCampaignProvider;

    protected function setUp()
    {
        $registry = $this->getMockForAbstractClass(RegistryInterface::class);

        $this->manager = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        /**
         * @var MockObject|RegistryInterface $registry
         */
        $registry->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($this->manager));
        $this->form = $this->getMockBuilder(Form::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = new Request();
        $requestStack = new RequestStack();
        $requestStack->push($this->request);

        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->validator->expects($this->any())->method('validate')->willReturn([]);
        $this->translator = $this->createMock(TranslatorInterface::class);

        $this->abandonedCartCampaignFactory = $this
            ->getMockBuilder(AbandonedCartCampaignFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->abandonedCartCampaignProvider = $this
            ->createMock(AbandonedCartCampaignProviderInterface::class);

        $this->marketingList = new MarketingList();
        $this->marketingList->setType(new MarketingListType(MarketingListType::TYPE_MANUAL));
        $this->abandonedCartCampaign = new AbandonedCartCampaign();

        $this->handler = new AbandonedCartCampaignHandler(
            $this->form,
            $requestStack,
            $registry,
            $this->validator,
            $this->translator,
            $this->abandonedCartCampaignFactory,
            $this->abandonedCartCampaignProvider
        );

        $this->repository = $this->getMockBuilder(ObjectRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testProcessWhenCampaignRelationDoesNotExist()
    {
        $this->request->setMethod('POST');
        $this->assertProcessSegment();

        $this->form->expects($this->exactly(2))
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->abandonedCartCampaignProvider->expects($this->once())
            ->method('getAbandonedCartCampaign')
            ->with($this->marketingList)
            ->will($this->returnValue(null));

        $this->abandonedCartCampaignFactory->expects($this->once())
            ->method('create')
            ->with($this->marketingList)
            ->will($this->returnValue($this->abandonedCartCampaign));

        $this->handler->process($this->marketingList);
    }

    public function testProcessWhenCampaignRelationExists()
    {
        $this->request->setMethod('POST');
        $this->assertProcessSegment();

        $this->form->expects($this->exactly(2))
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->abandonedCartCampaignProvider
            ->expects($this->once())
            ->method('getAbandonedCartCampaign')
            ->with($this->marketingList)
            ->will($this->returnValue($this->abandonedCartCampaign));

        $this->abandonedCartCampaignFactory->expects($this->never())->method('create');

        $this->handler->process($this->marketingList);
    }

    protected function assertProcessSegment()
    {
        $formData = [
            'definition' => 'test'
        ];

        $this->form->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('test_form'));

        $this->request->request->set('test_form', $formData);
        $businessUnit = $this->getMockBuilder(BusinessUnit::class)
            ->disableOriginalConstructor()
            ->getMock();

        /**
         * @var MockObject|User $owner
         */
        $owner = $this->getMockBuilder(User::class)
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
