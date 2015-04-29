<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Form\Handler;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\ValidatorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

use Oro\Bundle\SegmentBundle\Entity\SegmentType;
use Oro\Bundle\UserBundle\Entity\User;

use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingListType;
use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign;
use OroCRM\Bundle\AbandonedCartBundle\Form\Handler\AbandonedCartCampaignHandler;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\AbandonedCartCampaignFactory;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;

class AbandonedCartListHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbandonedCartCampaignHandler
     */
    protected $handler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Form
     */
    protected $form;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ValidatorInterface
     */
    protected $validator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|TranslatorInterface
     */
    protected $translator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AbandonedCartCampaignFactory
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
     * @var \PHPUnit_Framework_MockObject_MockObject|EntityManager
     */
    protected $manager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ObjectRepository
     */
    protected $repository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AbandonedCartCampaignProviderInterface
     */
    protected $abandonedCartCampaignProvider;

    protected function setUp()
    {
        $registry = $this->getMockForAbstractClass('Symfony\Bridge\Doctrine\RegistryInterface');

        $this->manager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        /**
         * @var \PHPUnit_Framework_MockObject_MockObject|RegistryInterface $registry
         */
        $registry->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($this->manager));

        $this->form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = new Request();

        $this->validator = $this->getMock('Symfony\Component\Validator\ValidatorInterface');
        $this->translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');

        $this->abandonedCartCampaignFactory = $this
            ->getMockBuilder(
                'OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\AbandonedCartCampaignFactory'
            )
            ->disableOriginalConstructor()
            ->getMock();

        $this->abandonedCartCampaignProvider = $this
            ->getMock('OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface');

        $this->marketingList = new MarketingList();
        $this->abandonedCartCampaign = new AbandonedCartCampaign();

        $this->handler = new AbandonedCartCampaignHandler(
            $this->form,
            $this->request,
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

        $this->form->expects($this->once())
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

        $this->form->expects($this->once())
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

        $this->form->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('test_form'));

        $this->request->request->set('test_form', $formData);
        $businessUnit = $this->getMockBuilder('Oro\Bundle\OrganizationBundle\Entity\BusinessUnit')
            ->disableOriginalConstructor()
            ->getMock();

        /**
         * @var \PHPUnit_Framework_MockObject_MockObject|User $owner
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
