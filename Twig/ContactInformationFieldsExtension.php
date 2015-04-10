<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Twig;

use OroCRM\Bundle\MarketingListBundle\Model\ContactInformationFieldHelper;

class ContactInformationFieldsExtension extends \Twig_Extension
{
    const NAME = 'orocrm_abandonedcart_list_contact_information_fields';

    /**
     * @var ContactInformationFieldHelper
     */
    protected $helper;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @param ContactInformationFieldHelper $helper
     */
    public function __construct(ContactInformationFieldHelper $helper)
    {
        $this->helper = $helper;
        $this->entity = 'OroCRM\Bundle\MagentoBundle\Entity\Cart';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'get_contact_information_fields_info',
                [$this, 'getContactInformationFieldsInfo']
            )
        ];
    }

    /**
     * @return array
     */
    public function getContactInformationFieldsInfo()
    {
        return $this->helper->getEntityContactInformationColumnsInfo($this->entity);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}
