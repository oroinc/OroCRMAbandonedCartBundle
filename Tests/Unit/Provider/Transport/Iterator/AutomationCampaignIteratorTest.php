<?php

namespace Oro\Bundle\AbandonedCartBundle\Tests\Unit\Provider\Transport\Iterator;

use Oro\Bundle\AbandonedCartBundle\Provider\Transport\Iterator\AutomationCampaignIterator;
use Oro\Bundle\MailChimpBundle\Provider\Transport\Iterator\CampaignIterator;
use Oro\Bundle\MailChimpBundle\Provider\Transport\MailChimpClient;

class AutomationCampaignIteratorTest extends \PHPUnit\Framework\TestCase
{
    const TEST_BATCH_SIZE = 2;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|MailChimpClient
     */
    protected $client;

    /**
     * @var CampaignIterator
     */
    protected $campaignIterator;

    protected function setUp()
    {
        $this->client = $this
            ->getMockBuilder(MailChimpClient::class)
            ->setMethods(['getCampaigns', 'getCampaignReport'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->campaignIterator = new CampaignIterator($this->client, [], self::TEST_BATCH_SIZE);
    }

    protected function tearDown()
    {
        unset($this->client, $this->campaignIterator);
    }

    /**
     * @dataProvider iteratorDataProvider
     * @param array $campaignValueMap
     * @param array $expected
     */
    public function testIteratorWorks(array $campaignValueMap, array $expected)
    {
        $automationCampaignIterator = new AutomationCampaignIterator($this->campaignIterator);

        $this->client
            ->expects($this->exactly(count($campaignValueMap)))
            ->method('getCampaigns')
            ->will($this->returnValueMap($campaignValueMap));

        $this->client
            ->expects($this->any())
            ->method('getCampaignReport')
            ->willReturn([]);

        $actual = [];
        foreach ($automationCampaignIterator as $key => $value) {
            $actual[$key] = $value;
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function iteratorDataProvider()
    {
        return [
            'two pages with filters' => [
                'listValueMap' => [
                    [
                        ['offset' => 0, 'count' => 2, 'uses_segment' => true, 'type' => 'automation'],
                        [
                            'total_items' => 3,
                            'campaigns' => [
                                ['id' => '3d21b11eb1', 'name' => 'Automation Campaign 1', 'report' => []],
                                ['id' => '3d21b11eb2', 'name' => 'Automation Campaign 2', 'report' => []],
                            ]
                        ]
                    ],
                    [
                        ['offset' => 1, 'count' => 2, 'uses_segment' => true, 'type' => 'automation'],
                        [
                            'total_items' => 3,
                            'campaigns' => [
                                ['id' => '3d21b11eb3', 'name' => 'Automation Campaign 3', 'report' => []],
                            ]
                        ]

                    ]
                ],
                'expected' => [
                    ['id' => '3d21b11eb1', 'name' => 'Automation Campaign 1', 'report' => []],
                    ['id' => '3d21b11eb2', 'name' => 'Automation Campaign 2', 'report' => []],
                    ['id' => '3d21b11eb3', 'name' => 'Automation Campaign 3', 'report' => []]
                ]
            ],
            'empty' => [
                'listValueMap' => [
                    [
                        ['offset' => 0, 'count' => 2, 'uses_segment' => true, 'type' => 'automation'],
                        [
                            'total_items' => 0,
                            'campaigns' => []
                        ]
                    ]
                ],
                'expected' => []
            ],
        ];
    }
}
