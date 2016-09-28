<?php

namespace Oro\Bundle\AbandonedCartBundle\Tests\Unit\Model\AbandonedCartList;

use Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartList\ConversionFactory;
use Oro\Bundle\MarketingListBundle\Entity\MarketingList;

class ConversionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConversionFactory
     */
    protected $factory;

    protected function setUp()
    {
        $this->factory = new ConversionFactory();
    }

    public function testCreate()
    {
        $marketingList = new MarketingList();
        $conversion = $this->factory->create($marketingList);

        $this->assertInstanceOf('Oro\Bundle\AbandonedCartBundle\Entity\AbandonedCartConversion', $conversion);
    }
}
