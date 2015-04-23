<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model\AbandonedCartList\Tracking;

use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\RegistryInterface;

use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\Tracking\TrackingStatProviderFactory;

class TrackingStatProviderFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TrackingStatProviderFactory
     */
    protected $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RegistryInterface $registry
     */
    protected $registry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EntityManager $em
     */
    protected $em;

    protected function setUp()
    {
        $this->registry = $this->getMockBuilder('Symfony\Bridge\Doctrine\RegistryInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->registry
            ->expects($this->once())->method('getManager')
            ->will($this->returnValue($this->em));

        $this->factory = new TrackingStatProviderFactory($this->registry);
    }

    public function testCreate()
    {

        $trackingStatProvider = $this->factory->create(
            'orderAssociationName',
            'campaignAssociationName',
            'trackingVisitEventClassName'
        );

        $this->assertInstanceOf(
            'OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\Tracking\TrackingStatProviderInterface',
            $trackingStatProvider
        );
    }
}
