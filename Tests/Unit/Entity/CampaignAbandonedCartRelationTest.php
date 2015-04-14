<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Entity;

use Symfony\Component\PropertyAccess\PropertyAccess;

use OroCRM\Bundle\AbandonedCartBundle\Entity\CampaignAbandonedCartRelation;

class CampaignAbandonedCartRelationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CampaignAbandonedCartRelation
     */
    protected $entity;

    protected function setUp()
    {
        $this->entity = new CampaignAbandonedCartRelation();
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

    public function propertiesDataProvider()
    {
        return array(
            array('marketingList', $this->getMock('OroCRM\Bundle\MarketingListBundle\Entity\MarketingList')),
            array('campaign', $this->getMock('OroCRM\Bundle\CampaignBundle\Entity\Campaign')),
        );
    }
}
