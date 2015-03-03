<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartList;

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
            'entity_class' => $this->container->getParameter('orocrm_abandonedcart_list.entity.class')
        ];
    }

    /**
     * @Route("/view/{id}", name="orocrm_abandoned_cart_list_view", requirements={"id"="\d+"}, defaults={"id"=0})
     * @Acl(
     *      id="orocrm_abandoned_cart_list_view",
     *      type="entity",
     *      permission="VIEW",
     *      class="OroCRMAbandonedCartBundle:AbandonedCartList"
     * )
     * @Template
     *
     * @param AbandonedCartList $entity
     *
     * @return array
     */
    public function viewAction(AbandonedCartList $entity)
    {
        return [
            'entity'   => $entity
        ];
    }

    /**
     * @Route("/create", name="orocrm_abandoned_cart_list_create")
     * @Template("OroCRMAbandonedCartBundle:AbandonedCart:update.html.twig")
     * @Acl(
     *      id="orocrm_abandoned_cart_list_create",
     *      type="entity",
     *      permission="CREATE",
     *      class="OroCRMAbandonedCartBundle:AbandonedCartList"
     * )
     */
    public function createAction()
    {
        return $this->update(new AbandonedCartList());
    }

    /**
     * @Route("/update/{id}", name="orocrm_abandoned_cart_list_update", requirements={"id"="\d+"}, defaults={"id"=0})
     *
     * @Template
     * @Acl(
     *      id="orocrm_abandoned_cart_list_update",
     *      type="entity",
     *      permission="EDIT",
     *      class="OroCRMAbandonedCartBundle:AbandonedCartList"
     * )
     *
     * @param AbandonedCartList $entity
     *
     * @return array
     */
    public function updateAction(AbandonedCartList $entity)
    {
        return $this->update($entity);
    }

    /**
     * @Route("/delete/{id}", name="orocrm_abandoned_cart_list_delete", requirements={"id"="\d+"})
     * @Acl(
     *      id="orocrm_abandoned_cart_list_delete",
     *      type="entity",
     *      permission="DELETE",
     *      class="OroCRMAbandonedCartBundle:AbandonedCartList"
     * )
     */
    public function deleteAction(AbandonedCartList $abandonedCartList)
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');

        $em->remove($abandonedCartList);
        $em->flush();

        return new JsonResponse('', Codes::HTTP_OK);
    }

    /**
     * @param AbandonedCartList $entity
     *
     * @return array
     */
    protected function update(AbandonedCartList $entity)
    {
        $response = $this->get('oro_form.model.update_handler')->handleUpdate(
            $entity,
            $this->get('orocrm_abandonedcart_list.form.abandonedcart_list'),
            function (AbandonedCartList $entity) {
                return array(
                    'route'      => 'orocrm_abandoned_cart_list_update',
                    'parameters' => array('id' => $entity->getId())
                );
            },
            function (AbandonedCartList $entity) {
                return array(
                    'route'      => 'orocrm_abandoned_cart_list_view',
                    'parameters' => array('id' => $entity->getId())
                );
            },
            $this->get('translator')->trans('orocrm.abandonedcartlist.entity.saved'),
            $this->get('orocrm_abandonedcart_list.form.handler.abandoned_cart_list')
        );

        if (is_array($response)) {
            return array_merge(
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
