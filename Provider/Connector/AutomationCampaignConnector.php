<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Provider\Connector;

use Oro\Bundle\IntegrationBundle\Provider\ConnectorInterface;
use Oro\Bundle\MailChimpBundle\Entity\Campaign;
use Oro\Bundle\MailChimpBundle\Provider\Connector\AbstractMailChimpConnector;
use Oro\Bundle\MailChimpBundle\Provider\Transport\Iterator\CampaignIterator;
use OroCRM\Bundle\AbandonedCartBundle\Provider\Transport\Iterator\AutomationCampaignIterator;

class AutomationCampaignConnector extends AbstractMailChimpConnector implements ConnectorInterface
{
    const TYPE = 'automation_campaign';
    const JOB_IMPORT = 'abandonedcart_automation_campaign_import';

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'orocrm.abandonedcart.connector.automation_campaign.label';
    }

    /**
     * {@inheritdoc}
     */
    public function getImportEntityFQCN()
    {
        return $this->entityName;
    }

    /**
     * {@inheritdoc}
     */
    public function getImportJobName()
    {
        return self::JOB_IMPORT;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    protected function getConnectorSource()
    {
        /** @var CampaignIterator $campaignIterator */
        $campaignIterator = $this->transport->getCampaigns($this->getChannel(), Campaign::STATUS_SENDING);
        if ($campaignIterator instanceof \ArrayIterator) {
            return $campaignIterator;
        }
        return new AutomationCampaignIterator($campaignIterator);
    }
}
