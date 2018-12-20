<?php

namespace Oro\Bundle\AbandonedCartBundle\Tests\Unit\Entity;

use Oro\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign;
use Symfony\Component\PropertyAccess\PropertyAccess;

class AbandonedCartCampaignTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AbandonedCartCampaign
     */
    protected $entity;

    protected function setUp()
    {
        $this->entity = new AbandonedCartCampaign();
    }

    protected function tearDown()
    {
        unset($this->entity);
    }

    public function testGetId()
    {
        $this->assertNull($this->entity->getId());

        $value = 42;
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
    public function testSettersAndGetters($property, $value)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $accessor->setValue($this->entity, $property, $value);
        $this->assertEquals($value, $accessor->getValue($this->entity, $property));
    }

    /**
     * @return array
     */
    public function propertiesDataProvider()
    {
        return [
            ['marketingList', $this->createMock('Oro\Bundle\MarketingListBundle\Entity\MarketingList')],
            ['campaign', $this->createMock('Oro\Bundle\CampaignBundle\Entity\Campaign')],
        ];
    }
}
