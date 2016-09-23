<?php

namespace Oro\Bundle\AbandonedCartBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\AbandonedCartBundle\Entity\AbandonedCartConversion;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

/**
 * @Route("/abandoned-cart-conversion")
 */
class AbandonedCartConversionController extends Controller
{
    /**
     * @Route(
     *      "/manage-workflow/{id}",
     *      name="oro_abandoned_cart_manage_workflow",
     *      requirements={"id"="\d+"}
     * )
     * @AclAncestor("oro_abandonedcart")
     *
     * @Template
     * @param MarketingList $marketingList
     * @return array
     */
    public function manageWorkflowAction(MarketingList $marketingList)
    {
        $conversion = $this->getConversionByMarketingList($marketingList);

        /** @var Form $form */
        $form = $this->get('oro_abandonedcart.form.conversion');

        // TODO remove after Fix/bap 7098 #3509 will merged
        $handler = $this->get('oro_abandonedcart.form.handler.conversion_form');

        $result = ['entity' => $conversion];
        if ($handler->process($conversion)) {
            $result['savedId'] = $conversion->getId();
        }

        $result['form'] = $form->createView();

        return $result;
    }

    /**
     * @param MarketingList $marketingList
     * @return AbandonedCartConversion
     */
    protected function getConversionByMarketingList(MarketingList $marketingList)
    {
        $conversion = $this->get('oro_abandonedcart.conversion_manager')
            ->findConversionByMarketingList($marketingList);

        if (!$conversion) {
            $conversion = $this->get('oro_abandonedcart.conversion_factory')->create($marketingList);
        }

        return $conversion;
    }
}
