<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model\MarketingList;

use OroCRM\Bundle\AbandonedCartBundle\Model\MarketingList\AbandonedCartSource;

class AbandonedCartSourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbandonedCartSource
     */
    protected $abandonedCartSource;

    protected function setUp()
    {
        $this->abandonedCartSource = new AbandonedCartSource();
    }

    public function testGetCode()
    {
        $this->assertEquals($this->abandonedCartSource->getCode(), AbandonedCartSource::SOURCE_CODE);
    }
}
