<?php

namespace OroCRM\Bundle\AbandonedCartBundle\EventListener\ImportExport;

use Doctrine\Common\Collections\Collection;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ImportExportBundle\Event\StrategyEvent;
use OroCRM\Bundle\MagentoBundle\Entity\Cart;
use OroCRM\Bundle\MagentoBundle\Entity\CartItem;
use OroCRM\Bundle\MailChimpBundle\Entity\ExtendedMergeVar;
use OroCRM\Bundle\MailChimpBundle\Entity\MemberExtendedMergeVar;
use OroCRM\Bundle\MailChimpBundle\Entity\StaticSegment;
use OroCRM\Bundle\AbandonedCartBundle\Model\ExtendedMergeVar\CartItemsMergeVarProvider;

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
        if (!is_string($cartItemTemplate) || empty($cartItemTemplate)) {
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

        $cartItemsMergeVars = $this->receiveExtendedMergeVars($staticSegment);

        if ($cartItemsMergeVars->isEmpty()) {
            return;
        }

        $cart = $this->loadCart($entity);

        if (is_null($cart) || $cart->getCartItems()->isEmpty()) {
            return;
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
     * @param StaticSegment $staticSegment
     * @return Collection|ExtendedMergeVar[]
     */
    protected function receiveExtendedMergeVars(StaticSegment $staticSegment)
    {
        $extendedMergeVars = $staticSegment->getSyncedExtendedMergeVars();
        $extendedMergeVars = $extendedMergeVars
            ->filter(
                function(ExtendedMergeVar $extendedMergeVar) {
                    return preg_match($this->getCartItemRegExp(), $extendedMergeVar->getName());
                }
            );

        return $extendedMergeVars;
    }

    /**
     * @param MemberExtendedMergeVar $entity
     * @return null|Cart
     */
    protected function loadCart(MemberExtendedMergeVar $entity)
    {
        $context = $entity->getMergeVarValuesContext();

        if (empty($context['entity_id'])) {
            return null;
        }

        $cartEntityClass = $entity->getStaticSegment()
            ->getMarketingList()
            ->getSegment()
            ->getEntity();

        $cart = $this->doctrineHelper
            ->getEntityRepository($cartEntityClass)
            ->find($context['entity_id']);

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

        if (is_null($cartItem) || !$cartItem instanceof CartItem) {
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
        preg_match($this->getCartItemRegExp(), $cartItemMergeVar->getName(), $matches);
        if (empty($matches[1])) {
            return 0;
        }

        $index = abs((int) $matches[1]);
        if ($index > 0) {
            $index--;
        }

        return $index;
    }

    /**
     * @return string
     */
    protected function getCartItemRegExp()
    {
        return sprintf('/%s_([0-9]+)/', CartItemsMergeVarProvider::NAME_PREFIX);
    }
}
