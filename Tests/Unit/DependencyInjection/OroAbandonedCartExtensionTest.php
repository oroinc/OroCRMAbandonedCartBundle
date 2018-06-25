<?php

namespace Oro\Bundle\AbandonedCartBundle\Tests\Unit\DependencyInjection;

use Oro\Bundle\AbandonedCartBundle\DependencyInjection\OroAbandonedCartExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OroAbandonedCartExtensionTest extends \PHPUnit\Framework\TestCase
{
    public function testLoad()
    {
        $configuration = new ContainerBuilder();
        $loader = new OroAbandonedCartExtension();
        $loader->load([], $configuration);
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerBuilder', $configuration);
    }
}
