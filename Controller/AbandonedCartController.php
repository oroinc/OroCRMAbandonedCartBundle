<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use FOS\RestBundle\Util\Codes;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use OroCRM\Bundle\MarketingListBundle\Datagrid\ConfigurationProvider;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

/**
 * @Route("/abandoned-cart")
 */
class AbandonedCartController extends Controller
{
    /**
     * @Route("/list", name="orocrm_abandoned_cart_list")
     * @AclAncestor("orocrm_abandoned_cart_list_view")
     * @Template
     */
    public function listAction()
    {
        return [
            'entity_class' => $this->container->getParameter('orocrm_marketing_list.entity.class')
        ];
    }

    /**
     * @Route("/view/{id}", name="orocrm_abandoned_cart_list_view", requirements={"id"="\d+"}, defaults={"id"=0})
     * @Acl(
     *      id="orocrm_abandoned_cart_list_view",
     *      type="entity",
     *      permission="VIEW",
     *      class="OroCRMAbandonedCartBundle:AbandonedCartCampaign"
     * )
     * @Template
     *
     * @param MarketingList $entity
     *
     * @return array
     */
    public function viewAction(MarketingList $entity)
    {
        $entityConfig = $this->get('orocrm_marketing_list.entity_provider')->getEntity($entity->getEntity());
        $campaign = $this->get('orocrm_abandonedcart.abandoned_cart_list.campaign_manager')
            ->getCampaignByMarketingList($entity);

        return [
            'entity'   => $entity,
            'config'   => $entityConfig,
            'gridName' => ConfigurationProvider::GRID_PREFIX . $entity->getId(),
            'campaign' => $campaign,
        ];
    }

    /**
     * @Route("/create", name="orocrm_abandoned_cart_list_create")
     * @Acl(
     *      id="orocrm_abandoned_cart_list_create",
     *      type="entity",
     *      permission="CREATE",
     *      class="OroCRMAbandonedCartBundle:AbandonedCartCampaign"
     * )
     * @Template("OroCRMAbandonedCartBundle:AbandonedCart:update.html.twig")
     */
    public function createAction()
    {
        $marketingList = $this->get('orocrm_abandonedcart.predefined_marketing_list_factory')->create();

        return $this->update($marketingList);
    }

    /**
     * @Route("/update/{id}", name="orocrm_abandoned_cart_list_update", requirements={"id"="\d+"}, defaults={"id"=0})
     *
     * @Template
     * @Acl(
     *      id="orocrm_abandoned_cart_list_update",
     *      type="entity",
     *      permission="EDIT",
     *      class="OroCRMAbandonedCartBundle:AbandonedCartCampaign"
     * )
     *
     * @param MarketingList $entity
     *
     * @return array
     */
    public function updateAction(MarketingList $entity)
    {
        return $this->update($entity);
    }

    /**
     * @Route("/delete/{id}", name="orocrm_abandoned_cart_list_delete", requirements={"id"="\d+"})
     * @Acl(
     *      id="orocrm_abandoned_cart_list_delete",
     *      type="entity",
     *      permission="DELETE",
     *      class="OroCRMAbandonedCartBundle:AbandonedCartCampaign"
     * )
     *
     * @param MarketingList $marketingList
     * @return JsonResponse
     */
    public function deleteAction(MarketingList $marketingList)
    {
        $class = $this->container->getParameter('orocrm_marketing_list.entity.class');
        $em = $this->getDoctrine()->getManagerForClass($class);

        $em->remove($marketingList);
        $em->flush($marketingList);

        return new JsonResponse('', Codes::HTTP_OK);
    }

    /**
     * @param MarketingList $entity
     * @return array
     */
    protected function update(MarketingList $entity)
    {
        $response = $this->get('oro_form.model.update_handler')->handleUpdate(
            $entity,
            $this->get('orocrm_abandonedcart_list.form.abandonedcart_list'),
            function (MarketingList $entity) {
                return [
                    'route'      => 'orocrm_abandoned_cart_list_update',
                    'parameters' => ['id' => $entity->getId()]
                ];
            },
            function (MarketingList $entity) {
                return [
                    'route'      => 'orocrm_abandoned_cart_list_view',
                    'parameters' => ['id' => $entity->getId()]
                ];
            },
            $this->get('translator')->trans('orocrm.abandonedcart.entity.saved'),
            $this->get('orocrm_abandonedcart.form.handler.abandonedcart_campaign')
        );

        if (is_array($response)) {
            $response = array_merge(
                $response,
                [
                    'entities' => $this->get('oro_entity.entity_provider')->getEntities(),
                    'metadata' => $this->get('oro_query_designer.query_designer.manager')->getMetadata('segment')
                ]
            );
        }

        return $response;
    }
}
