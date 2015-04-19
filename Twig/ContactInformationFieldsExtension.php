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
     * @param ContactInformationFieldHelper $helper
     */
    public function __construct(ContactInformationFieldHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'get_cart_contact_information_fields_info',
                [$this, 'getCartContactInformationFieldsInfo']
            )
        ];
    }

    /**
     * @return array
     */
    public function getCartContactInformationFieldsInfo()
    {
        return $this->helper->getEntityContactInformationColumnsInfo('OroCRM\Bundle\MagentoBundle\Entity\Cart');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}
