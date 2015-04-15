<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Entity;

use Symfony\Component\PropertyAccess\PropertyAccess;
use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartConversion;

class AbandonedCartConversionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbandonedCartConversion
     */
    protected $entity;

    protected function setUp()
    {
        $this->entity = new AbandonedCartConversion();
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
     * @dataProvider settersAndGettersDataProvider
     * @param string $property
     * @param mixed $value
     * @param mixed $default
     */
    public function testSettersAndGetters($property, $value, $default = null)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $this->assertEquals(
            $default,
            $propertyAccessor->getValue($this->entity, $property)
        );

        $propertyAccessor->setValue($this->entity, $property, $value);

        $this->assertEquals(
            $value,
            $propertyAccessor->getValue($this->entity, $property)
        );
    }

    /**
     * @return array
     */
    public function settersAndGettersDataProvider()
    {
        return [
            ['marketingList', $this->getMock('OroCRM\Bundle\MarketingListBundle\Entity\MarketingList')]
        ];
    }
}
