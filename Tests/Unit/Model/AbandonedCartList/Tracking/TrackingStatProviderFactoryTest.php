<?php

namespace Oro\Bundle\AbandonedCartBundle\Tests\Unit\Model\AbandonedCartList\Tracking;

use Doctrine\ORM\EntityManager;
use Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartList\Tracking\TrackingStatProviderFactory;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TrackingStatProviderFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var TrackingStatProviderFactory
     */
    protected $factory;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|RegistryInterface $registry
     */
    protected $registry;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|EntityManager $em
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
            'Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartList\Tracking\TrackingStatProviderInterface',
            $trackingStatProvider
        );
    }
}
