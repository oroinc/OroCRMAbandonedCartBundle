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
        if (!array_key_exists('total', $result) || !array_key_exists('qty', $result)) {
            throw new \InvalidArgumentException('Total and Qty should not be empty');
        }
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
