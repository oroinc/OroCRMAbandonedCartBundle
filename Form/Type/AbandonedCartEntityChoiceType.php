<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Form\Type;

use Oro\Bundle\EntityBundle\Form\Type\EntityChoiceType;
use Oro\Bundle\FormBundle\Form\Type\ChoiceListItem;

class AbandonedCartEntityChoiceType extends EntityChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'orocrm_abandonedcart_list_entity_choice';
    }

    /**
     * @param bool $showPlural
     * @return array
     */
    protected function getChoices($showPlural)
    {
        $choices = [];

        $entity = $this->provider->getEntity('OroCRM\Bundle\MagentoBundle\Entity\Cart');

        $attributes = [];
        foreach ($entity as $key => $val) {
            if (!in_array($key, ['name'])) {
                $attributes['data-' . $key] = $val;
            }
        }
        $choices[$entity['name']] = new ChoiceListItem(
            $showPlural ? $entity['plural_label'] : $entity['label'],
            $attributes
        );


        return $choices;
    }
}
