<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use OroCRM\Bundle\AbandonedCartBundle\DependencyInjection\OroCRMAbandonedCartExtension;

class OroCRMAbandonedCartExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $configuration = new ContainerBuilder();
        $loader = new OroCRMAbandonedCartExtension();
        $loader->load([], $configuration);
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerBuilder', $configuration);
    }
}
