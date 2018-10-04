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
use Oro\Bundle\SegmentBundle\Entity\SegmentType;
use Oro\Bundle\UserBundle\Entity\User;
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
     * @var \PHPUnit\Framework\MockObject\MockObject|Form
     */
    protected $form;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ValidatorInterface
     */
    protected $validator;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|TranslatorInterface
     */
    protected $translator;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|AbandonedCartCampaignFactory
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
     * @var \PHPUnit\Framework\MockObject\MockObject|EntityManager
     */
    protected $manager;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ObjectRepository
     */
    protected $repository;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|AbandonedCartCampaignProviderInterface
     */
    protected $abandonedCartCampaignProvider;

    protected function setUp()
    {
        $registry = $this->getMockForAbstractClass('Symfony\Bridge\Doctrine\RegistryInterface');

        $this->manager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        /**
         * @var \PHPUnit\Framework\MockObject\MockObject|RegistryInterface $registry
         */
        $registry->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($this->manager));

        $this->form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = new Request();
        $requestStack = new RequestStack();
        $requestStack->push($this->request);

        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');

        $this->abandonedCartCampaignFactory = $this
            ->getMockBuilder(
                'Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartList\AbandonedCartCampaignFactory'
            )
            ->disableOriginalConstructor()
            ->getMock();

        $this->abandonedCartCampaignProvider = $this
            ->createMock('Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface');

        $this->marketingList = new MarketingList();
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

        $this->repository = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectRepository')
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

        $this->abandonedCartCampaignProvider->expects($this->once())
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
        $businessUnit = $this->getMockBuilder('Oro\Bundle\OrganizationBundle\Entity\BusinessUnit')
            ->disableOriginalConstructor()
            ->getMock();

        /**
         * @var \PHPUnit\Framework\MockObject\MockObject|User $owner
         */
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
