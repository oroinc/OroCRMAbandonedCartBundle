<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model;

use Oro\Bundle\OrganizationBundle\Entity\BusinessUnit;
use Oro\Bundle\SegmentBundle\Entity\Segment;
use Oro\Bundle\UserBundle\Entity\User;
use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartList;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\SegmentBuilder;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartListManager;
use Symfony\Component\Translation\TranslatorInterface;

class AbandonedCartListManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbandonedCartListManager
     */
    private $manager;

    /**
     * @var AbandonedCartList
     */
    private $abandonedCartList;

    /**
     * @var SegmentBuilder
     */
    protected $segmentBuilder;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    protected function setUp()
    {
        $this->segmentBuilder = $this
            ->getMockBuilder('OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\SegmentBuilder')->getMock();
        $this->translator = $this->getMockBuilder('Symfony\Component\Translation\TranslatorInterface')->getMock();
        $this->abandonedCartList = $this
            ->getMockBuilder('OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartList')->getMock();
        $this->manager = new AbandonedCartListManager($this->segmentBuilder, $this->translator);
    }

    public function testUpdateSegmentWhenAbandonedCartListHasNoSegment()
    {
        $definition = 'SEGMENT_DEFINITION';
        $entityName = 'Abandoned Cart List';
        $generatedName = 'Abandoned Cart List segment';
        $segment = new Segment();

        $this->abandonedCartList->expects($this->once())->method('getName')->will($this->returnValue($entityName));
        $this->segmentBuilder->expects($this->once())->method('build')->will($this->returnValue($segment));
        $this->abandonedCartList->expects($this->once())->method('setSegment')->with($segment);
        $this->translator->expects($this->once())->method('trans')
            ->with('orocrm.abandonedcart.segment_name_pattern', array('%name%' => $entityName))
            ->will($this->returnValue($generatedName));
        $this->abandonedCartList->expects($this->once())->method('updateSegment')
            ->with($generatedName, $definition);

        $this->manager->updateSegment($this->abandonedCartList, $definition);
    }

    public function testUpdateSegment()
    {
        $definition = 'SEGMENT_DEFINITION';
        $entityName = 'Abandoned Cart List';
        $generatedName = 'Abandoned Cart List segment';
        $segment = new Segment();

        $this->abandonedCartList->expects($this->any())->method('getSegment')->will($this->returnValue($segment));
        $this->abandonedCartList->expects($this->once())->method('getName')->will($this->returnValue($entityName));
        $this->segmentBuilder->expects($this->never())->method('build');
        $this->abandonedCartList->expects($this->never())->method('setSegment');
        $this->translator->expects($this->once())->method('trans')
            ->with('orocrm.abandonedcart.segment_name_pattern', array('%name%' => $entityName))
            ->will($this->returnValue($generatedName));
        $this->abandonedCartList->expects($this->once())->method('updateSegment')
            ->with($generatedName, $definition);

        $this->manager->updateSegment($this->abandonedCartList, $definition);
    }
}
