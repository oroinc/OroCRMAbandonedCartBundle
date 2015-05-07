<?php

namespace OroCRM\Bundle\AbandonedCartBundle\EventListener\ImportExport;

use Doctrine\Common\Collections\Collection;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ImportExportBundle\Event\StrategyEvent;
use Oro\Bundle\LocaleBundle\Formatter\NumberFormatter;
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
     * @var NumberFormatter
     */
    protected $numberFormatter;

    /**
     * @param DoctrineHelper $doctrineHelper
     * @param NumberFormatter $numberFormatter
     */
    public function __construct(DoctrineHelper $doctrineHelper, NumberFormatter $numberFormatter)
    {
        $this->doctrineHelper = $doctrineHelper;
        $this->numberFormatter = $numberFormatter;
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

        foreach ($cartItemsMergeVars as $cartItemMergeVar) {
            $cartItemMergeVarValue = $this->prepareCartItemMergeVarValue($cartItemMergeVar, $cart);
            if (empty($cartItemMergeVarValue)) {
                continue;
            }
            $entity->addMergeVarValue($cartItemMergeVar->getTag(), (string) $cartItemMergeVarValue);
        }
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
                function (ExtendedMergeVar $extendedMergeVar) {
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

        preg_match($this->getCartItemRegExp(), $cartItemMergeVar->getName(), $matches);

        if (empty($matches[2])) {
            return null;
        }

        $value = null;
        switch ($matches[2]) {
            case CartItemsMergeVarProvider::URL_MERGE_VAR:
                $value = $cartItem->getProductUrl();
                break;
            case CartItemsMergeVarProvider::NAME_MERGE_VAR:
                $value = $cartItem->getName();
                break;
            case CartItemsMergeVarProvider::QTY_MERGE_VAR:
                $value = $this->numberFormatter->formatDecimal($cartItem->getQty());
                break;
            case CartItemsMergeVarProvider::PRICE_MERGE_VAR:
                $value = $this->numberFormatter->formatCurrency($cartItem->getPrice());
                break;
            case CartItemsMergeVarProvider::TOTAL_MERGE_VAR:
                $value = $this->numberFormatter->formatCurrency($cartItem->getRowTotal());
                break;
        }

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
        return sprintf('/%s_([0-9]+)_([a-z]+)/', CartItemsMergeVarProvider::NAME_PREFIX);
    }
}
