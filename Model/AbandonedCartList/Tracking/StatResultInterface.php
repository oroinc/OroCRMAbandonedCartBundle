<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\Tracking;

interface StatResultInterface
{
    /**
     * @return string
     */
    public function getQty();

    /**
     * @return string
     */
    public function getTotal();
}
