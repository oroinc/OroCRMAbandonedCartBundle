<?php

namespace OroCRM\Bundle\AbandonedCartBundle\ImportExport\Strategy;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ImportExportBundle\Event\StrategyEvent;
use OroCRM\Bundle\AbandonedCartBundle\Model\ExtendedMergeVar\CartItemsMergeVarProvider;
use OroCRM\Bundle\MagentoBundle\Entity\Cart;
use OroCRM\Bundle\MailChimpBundle\Entity\ExtendedMergeVar;
use OroCRM\Bundle\MailChimpBundle\Entity\MemberExtendedMergeVar;

class CartItemsMergeVarStrategyListener
{
    /**
     * @var DoctrineHelper
     */
    protected $doctrineHelper;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var string
     */
    protected $cartItemTemplate;

    /**
     * @param DoctrineHelper $doctrineHelper
     * @param \Twig_Environment $twig
     * @param string $cartItemTemplate
     */
    public function __construct(DoctrineHelper $doctrineHelper, \Twig_Environment $twig, $cartItemTemplate)
    {
        if (false === is_string($cartItemTemplate) || empty($cartItemTemplate)) {
            throw new \InvalidArgumentException('Cart item template for Extended Merge Var must be provided.');
        }
        $this->doctrineHelper = $doctrineHelper;
        $this->twig = $twig;
        $this->cartItemTemplate = $cartItemTemplate;
    }

    /**
     * @param StrategyEvent $event
     * @return void
     */
    public function onProcessAfter(StrategyEvent $event)
    {
        $entity = $event->getEntity();

        if (!$entity instanceof MemberExtendedMergeVar) {
            return;
        }

        $staticSegment = $entity->getStaticSegment();

        if (!$staticSegment) {
            return;
        }

        $extendedMergeVars = $staticSegment->getSyncedExtendedMergeVars();

        $cartItemsMergeVars = $extendedMergeVars
            ->filter(
                function(ExtendedMergeVar $extendedMergeVar) {
                    return false !== strpos($extendedMergeVar->getName(), CartItemsMergeVarProvider::NAME_PREFIX);
                }
            );

        if ($cartItemsMergeVars->isEmpty()) {
            return;
        }

        $context = $entity->getMergeVarValuesContext();

        if (!isset($context['entity_id']) || empty($context['entity_id'])) {
            return;
        }

        $cartEntityClass = $staticSegment
            ->getMarketingList()
            ->getSegment()
            ->getEntity();

        $cart = $this->loadCart($cartEntityClass, $context['entity_id']);

        if (is_null($cart) || $cart->getCartItems()->isEmpty()) {
            return null;
        }

        $result = [];
        foreach ($cartItemsMergeVars as $cartItemMergeVar) {
            $cartItemMergeVarValue = $this->prepareCartItemMergeVarValue($cartItemMergeVar, $cart);
            if (is_null($cartItemMergeVarValue)) {
                continue;
            }
            $result[$cartItemMergeVar->getTag()] = $cartItemMergeVarValue;
        }

        $mergeVarValues = $entity->getMergeVarValues();
        $mergeVarValues = array_merge($mergeVarValues, $result);

        $entity->setMergeVarValues($mergeVarValues);
    }

    /**
     * @param $cartEntityClass
     * @param $cartId
     * @return null|Cart
     */
    protected function loadCart($cartEntityClass, $cartId)
    {
        $cart = $this->doctrineHelper
            ->getEntityRepository($cartEntityClass)
            ->find($cartId);

        return $cart;
    }

    /**
     * @param ExtendedMergeVar $cartItemMergeVar
     * @param Cart $cart
     * @return null|string
     */
    protected function prepareCartItemMergeVarValue(ExtendedMergeVar $cartItemMergeVar, Cart $cart)
    {
        $cartItems = $cart->getCartItems();

        if ($cartItems->isEmpty()) {
            return null;
        }

        $cartItemIndex = $this->extractCartItemIndex($cartItemMergeVar);

        $cartItem = $cartItems->get($cartItemIndex);

        if (is_null($cartItem)) {
            return null;
        }

        $value = $this->twig
            ->render(
                $this->cartItemTemplate,
                ['item' => $cartItem, 'index' => $cartItemIndex]
            );

        return $value;
    }

    /**
     * @param ExtendedMergeVar $cartItemMergeVar
     * @return int
     */
    protected function extractCartItemIndex(ExtendedMergeVar $cartItemMergeVar)
    {
        $name = $cartItemMergeVar->getName();
        $index = str_replace('item_', '', $name);
        $index = abs(intval($index));
        if ($index > 0) {
            $index--;
        }
        return $index;
    }
}
