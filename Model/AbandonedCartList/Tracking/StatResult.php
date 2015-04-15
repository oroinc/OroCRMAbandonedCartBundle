<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\Tracking;

class StatResult implements StatResultInterface
{
    /**
     * @var string
     */
    protected $total;

    /**
     * @var string
     */
    protected $qty;

    /**
     * @param array $result
     */
    public function __construct(array $result)
    {
        $this->total = $result['total'];
        $this->qty = $result['qty'];
    }

    /**
     * @return string
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return string
     */
    public function getQty()
    {
        return $this->qty;
    }
}
