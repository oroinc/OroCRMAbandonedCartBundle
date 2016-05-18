<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Acl\Voter;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use OroCRM\Bundle\AbandonedCartBundle\Acl\Voter\AbandonedCartVoter;
use OroCRM\Bundle\MagentoBundle\Provider\ChannelType;

class AbandonedCartVoterTest extends \PHPUnit_Framework_TestCase
{
    const INTEGRATION_CHANNEL_CLASS_NAME = 'ChannelClassName';

    /**
     * @var AbandonedCartVoter
     */
    protected $voter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|DoctrineHelper
     */
    protected $doctrineHelper;

    protected function setUp()
    {
        $this->doctrineHelper = $this->getMockBuilder('Oro\Bundle\EntityBundle\ORM\DoctrineHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->voter = new AbandonedCartVoter($this->doctrineHelper, self::INTEGRATION_CHANNEL_CLASS_NAME);
    }

    protected function tearDown()
    {
        unset($this->voter, $this->doctrineHelper);
    }

    /**
     * @param object $object
     * @param string $className
     * @param array $attributes
     * @param bool $enabled
     * @param bool $expected
     *
     * @dataProvider attributesDataProvider
     */
    public function testVote($object, $className, $attributes, $enabled, $expected)
    {
        $this->doctrineHelper->expects($this->any())
            ->method('getEntityClass')
            ->with($object)
            ->will($this->returnValue($className));

        $this->voter->setClassName('OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartVoter');

        $this->doctrineHelper->expects($this->any())
            ->method('getSingleEntityIdentifier')
            ->with($object, false)
            ->will($this->returnValue(1));

        /**
         * @var \PHPUnit_Framework_MockObject_MockObject|EntityRepository $repository
         */
        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->doctrineHelper->expects($this->any())
            ->method('getEntityRepository')
            ->with(self::INTEGRATION_CHANNEL_CLASS_NAME)
            ->will($this->returnValue($repository));

        $repository
            ->expects($this->once())->method('findOneBy')
            ->with(['type' => ChannelType::TYPE, 'enabled' => true])
            ->will($this->returnValue($enabled));

        /**
         * @var TokenInterface $token
         */
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');

        $this->assertEquals(
            $expected,
            $this->voter->vote($token, $object, $attributes)
        );
    }

    /**
     * @return array
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function attributesDataProvider()
    {
        $objectIdentityClass = 'Symfony\Component\Security\Acl\Model\ObjectIdentityInterface';

        return [
            // has active Magento channels
            [
                $this->getObjectIdentityInterfaceMock($objectIdentityClass),
                $objectIdentityClass,
                ['VIEW'],
                true,
                AbandonedCartVoter::ACCESS_ABSTAIN
            ],
            [
                $this->getObjectIdentityInterfaceMock($objectIdentityClass),
                $objectIdentityClass,
                ['CREATE'],
                true,
                AbandonedCartVoter::ACCESS_ABSTAIN
            ],
            [
                $this->getObjectIdentityInterfaceMock($objectIdentityClass),
                $objectIdentityClass,
                ['EDIT'],
                true,
                AbandonedCartVoter::ACCESS_ABSTAIN
            ],
            [
                $this->getObjectIdentityInterfaceMock($objectIdentityClass),
                $objectIdentityClass,
                ['DELETE'],
                true,
                AbandonedCartVoter::ACCESS_ABSTAIN
            ],
            // has not active Magento channels
            [
                $this->getObjectIdentityInterfaceMock($objectIdentityClass),
                $objectIdentityClass,
                ['VIEW'],
                false,
                AbandonedCartVoter::ACCESS_DENIED
            ],
            [
                $this->getObjectIdentityInterfaceMock($objectIdentityClass),
                $objectIdentityClass,
                ['CREATE'],
                false,
                AbandonedCartVoter::ACCESS_DENIED
            ],
            [
                $this->getObjectIdentityInterfaceMock($objectIdentityClass),
                $objectIdentityClass,
                ['EDIT'],
                false,
                AbandonedCartVoter::ACCESS_DENIED
            ],
            [
                $this->getObjectIdentityInterfaceMock($objectIdentityClass),
                $objectIdentityClass,
                ['DELETE'],
                false,
                AbandonedCartVoter::ACCESS_DENIED
            ]
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ObjectIdentityInterface
     */
    protected function getObjectIdentityInterfaceMock($objectIdentityClass)
    {
        $objectIdentity = $this->getMock($objectIdentityClass);
        $objectIdentity->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartVoter'));

        return $objectIdentity;
    }
}
