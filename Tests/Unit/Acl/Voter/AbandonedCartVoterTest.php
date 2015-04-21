<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Acl\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use OroCRM\Bundle\AbandonedCartBundle\Acl\Voter\AbandonedCartVoter;
use OroCRM\Bundle\MagentoBundle\Provider\ChannelType;

class AbandonedCartVoterTest extends \PHPUnit_Framework_TestCase
{
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

        $this->voter = new AbandonedCartVoter($this->doctrineHelper);
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

        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->doctrineHelper->expects($this->any())
            ->method('getEntityRepository')
            ->with('OroIntegrationBundle:Channel')
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
        $className = 'OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartVoter';
        $objectIdentityClass = 'Symfony\Component\Security\Acl\Model\ObjectIdentityInterface';
        $objectIdentity = $this->getMock($objectIdentityClass);
        $objectIdentity->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($className));

        return [
            // has active Magento channels
            [$objectIdentity, $objectIdentityClass, ['VIEW'], true, AbandonedCartVoter::ACCESS_ABSTAIN],
            [$objectIdentity, $objectIdentityClass, ['CREATE'], true, AbandonedCartVoter::ACCESS_ABSTAIN],
            [$objectIdentity, $objectIdentityClass, ['EDIT'], true, AbandonedCartVoter::ACCESS_ABSTAIN],
            // has not active Magento channels
            [$objectIdentity, $objectIdentityClass, ['VIEW'], false, AbandonedCartVoter::ACCESS_DENIED],
            [$objectIdentity, $objectIdentityClass, ['CREATE'], false, AbandonedCartVoter::ACCESS_DENIED],
            [$objectIdentity, $objectIdentityClass, ['EDIT'], false, AbandonedCartVoter::ACCESS_DENIED]
        ];
    }
}
