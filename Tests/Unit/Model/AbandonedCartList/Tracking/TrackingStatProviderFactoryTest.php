<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model\AbandonedCartList\Tracking;

use Doctrine\ORM\EntityManager;

use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\Tracking\TrackingStatProviderFactory;

class TrackingStatProviderFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TrackingStatProviderFactory
     */
    protected $factory;

    protected function setUp()
    {
        /* @var \PHPUnit_Framework_MockObject_MockObject|EntityManager $em */
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->factory = new TrackingStatProviderFactory($em);
    }

    public function testCreate()
    {
        $trackingStatProvider = $this->factory->create('orderAssociationName', 'campaignAssociationName');

        $this->assertInstanceOf(
            'OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\Tracking\TrackingStatProviderInterface',
            $trackingStatProvider
        );
    }
}
