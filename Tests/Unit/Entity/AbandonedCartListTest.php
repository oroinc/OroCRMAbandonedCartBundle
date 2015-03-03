<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Entity;

use Oro\Bundle\OrganizationBundle\Entity\BusinessUnit;
use Oro\Bundle\SegmentBundle\Entity\Segment;
use Oro\Bundle\UserBundle\Entity\User;
use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartList;
use Symfony\Component\PropertyAccess\PropertyAccess;

class AbandonedCartListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbandonedCartList
     */
    private $entity;

    protected function setUp()
    {
        $this->entity = new AbandonedCartList();
    }

    public function testGetId()
    {
        $this->assertNull($this->entity->getId());

        $value = 8;
        $idReflection = new \ReflectionProperty(get_class($this->entity), 'id');
        $idReflection->setAccessible(true);
        $idReflection->setValue($this->entity, $value);
        $this->assertEquals($value, $this->entity->getId());
    }

    /**
     * @dataProvider propertiesDataProvider
     * @param string $property
     * @param mixed $value
     */
    public function testGetSetAccessorMethods($property, $value)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $accessor->setValue($this->entity, $property, $value);
        $this->assertEquals($value, $accessor->getValue($this->entity, $property));
    }

    public function testGetEntity()
    {
        $this->assertEquals(AbandonedCartList::ENTITY_FULL_NAME, $this->entity->getEntity());
    }

    public function testDefinitionAccessorMethodsWhenSegmentExists()
    {
        $definition = 'DEFINITION';
        $this->entity->setSegment(new Segment());
        $this->entity->setDefinition($definition);
        $this->assertEquals($definition, $this->entity->getDefinition());
    }

    public function testDefinitionAccessorMethodsWhenSegmentDoesNotExist()
    {
        $definition = 'DEFINITION';
        $this->entity->setDefinition($definition);
        $this->assertNull($this->entity->getDefinition());
    }

    public function testUpdateSegment()
    {
        $name = 'NEW NAME';
        $definition = 'DEFINITION';

        $owner = new User();
        $ownerBusinessUnit = new BusinessUnit();
        $owner->setOwner($ownerBusinessUnit);
        $this->entity->setOwner($owner);
        $this->entity->setSegment(new Segment());

        $this->entity->updateSegment($name, $definition);

        $this->assertEquals($name, $this->entity->getSegment()->getName());
        $this->assertEquals($definition, $this->entity->getSegment()->getDefinition());
        $this->assertEquals($ownerBusinessUnit, $this->entity->getSegment()->getOwner());
    }

    public function testGetOwnerBusinessUnit()
    {
        $owner = new User();
        $ownerBusinessUnit = new BusinessUnit();
        $owner->setOwner($ownerBusinessUnit);
        $this->entity->setOwner($owner);

        $this->assertEquals($ownerBusinessUnit, $this->entity->getOwnerBusinessUnit());
    }

    public function testGetCreatedAt()
    {
        $this->assertGreaterThanOrEqual(new \DateTime('now', new \DateTimeZone('UTC')), $this->entity->getCreatedAt());
    }

    public function testGetUpdatedAt()
    {
        $this->assertGreaterThanOrEqual(new \DateTime('now', new \DateTimeZone('UTC')), $this->entity->getUpdatedAt());
    }

    public function propertiesDataProvider()
    {
        return array(
            array('name', 'test'),
            array('description', 'test'),
            array('segment', $this->getMock('Oro\Bundle\SegmentBundle\Entity\Segment')),
            array('owner', $this->getMock('Oro\Bundle\UserBundle\Entity\User')),
            array('organization', $this->getMock('Oro\Bundle\OrganizationBundle\Entity\Organization'))
        );
    }
}
