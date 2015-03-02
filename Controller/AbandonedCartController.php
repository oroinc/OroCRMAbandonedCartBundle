<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/abandoned-cart")
 */
class AbandonedCartController extends Controller
{
    /**
     * @Route("/list", name="orocrm_abandoned_cart_list")
     */
    public function listAction()
    {
        return array();
    }
}
