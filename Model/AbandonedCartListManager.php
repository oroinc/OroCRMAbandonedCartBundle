<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model;

use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartList;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\SegmentBuilder;
use Symfony\Component\Translation\TranslatorInterface;

class AbandonedCartListManager
{
    /**
     * @var SegmentBuilder
     */
    protected $segmentBuilder;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(SegmentBuilder $segmentBuilder, TranslatorInterface $translator)
    {
        $this->segmentBuilder = $segmentBuilder;
        $this->translator = $translator;
    }

    /**
     * Update AbandonedCartList Segment.
     * If segment does not exist yet, it will be created
     *
     * @param AbandonedCartList $abandonedCartList
     * @param $definition
     * @return void
     */
    public function updateSegment(AbandonedCartList $abandonedCartList, $definition)
    {
        $this->ensureAbandonedCartListHasSegment($abandonedCartList);
        $abandonedCartList
            ->updateSegment(
                $this->generateSegmentName($abandonedCartList), $definition
            );
    }

    /**
     * @param AbandonedCartList $abandonedCartList
     * @return void
     */
    protected function ensureAbandonedCartListHasSegment(AbandonedCartList $abandonedCartList)
    {
        $segment = $abandonedCartList->getSegment();
        if (is_null($segment)) {
            $segment = $this->segmentBuilder->build();
            $abandonedCartList->setSegment($segment);
        }
    }

    /**
     * @param AbandonedCartList $abandonedCartList
     * @return string
     */
    protected function generateSegmentName(AbandonedCartList $abandonedCartList)
    {
        return $this->translator
            ->trans(
                'orocrm.abandonedcart.segment_name_pattern',
                array('%name%' => $abandonedCartList->getName())
            );
    }
}
