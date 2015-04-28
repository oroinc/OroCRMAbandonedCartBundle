<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Datagrid;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

use Oro\Bundle\DataGridBundle\Datagrid\DatagridInterface;
use Oro\Bundle\DataGridBundle\Datasource\Orm\OrmDatasource;
use Oro\Bundle\DataGridBundle\Event\BuildAfter;
use OroCRM\Bundle\AbandonedCartBundle\Datagrid\MarketingListListener;

class MarketingListListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MarketingListListener
     */
    protected $listener;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|DatagridInterface
     */
    protected $datagrid;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|OrmDatasource
     */
    protected $ormDataSource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|QueryBuilder
     */
    protected $qb;

    /**
     * @var string
     */
    protected $gridName;

    /**
     * @var string
     */
    protected $abandonedCartCampaignClass;

    protected function setUp()
    {
        $this->datagrid = $this->getMock('Oro\Bundle\DataGridBundle\Datagrid\DatagridInterface');
        $this->ormDataSource = $this->getMockBuilder('Oro\Bundle\DataGridBundle\Datasource\Orm\OrmDatasource')
            ->disableOriginalConstructor()
            ->getMock();
        $this->qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->gridName = 'dummy_grid_name';
        $this->abandonedCartCampaignClass = 'className';
        $this->listener = new MarketingListListener($this->gridName, $this->abandonedCartCampaignClass);
    }

    public function testOnBuildAfterWhenIsNotApplicable()
    {
        $event = new BuildAfter($this->datagrid);

        $this->datagrid->expects($this->once())->method('getName')->will($this->returnValue('another_grid_name'));

        $this->datagrid->expects($this->never())->method('getDatasource');

        $this->listener->onBuildAfter($event);
    }

    public function testOnBuildAfterDatasourceIsNotValid()
    {
        $event = new BuildAfter($this->datagrid);

        $this->datagrid->expects($this->once())->method('getName')->will($this->returnValue($this->gridName));

        $notOrmDatasource = $this->getMock('Oro\Bundle\DataGridBundle\Datasource\DatasourceInterface');

        $this->datagrid->expects($this->once())->method('getDatasource')
            ->will($this->returnValue($notOrmDatasource));

        $this->ormDataSource->expects($this->never())->method('getQueryBuilder');

        $this->listener->onBuildAfter($event);
    }

    public function testOnBuildAfter()
    {
        $event = new BuildAfter($this->datagrid);

        $this->datagrid->expects($this->once())->method('getName')->will($this->returnValue($this->gridName));

        $this->datagrid->expects($this->once())->method('getDatasource')
            ->will($this->returnValue($this->ormDataSource));

        $this->ormDataSource->expects($this->once())->method('getQueryBuilder')
            ->will($this->returnValue($this->qb));

        $this->qb->expects($this->once())->method('getRootAliases')
            ->will($this->returnValue(['rootAlias']));

        $this->qb->expects($this->once())->method('leftJoin')
            ->with(
                $this->abandonedCartCampaignClass,
                'acc',
                Join::WITH,
                'acc.marketingList = rootAlias.id'
            )
            ->will($this->returnSelf());

        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $this->qb->expects($this->any())->method('expr')->will($this->returnValue($expr));

        $andX = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();

        $expr->expects($this->any())->method('andX')->will($this->returnValue($andX));

        $expr
            ->expects($this->any())
            ->method('isNull')
            ->with('acc.marketingList')
            ->will($this->returnValue('acc.marketingList IS NULL'));

        $this->qb
            ->expects($this->once())
            ->method('andWhere')
            ->with($expr)
            ->will($this->returnSelf());

        $this->listener->onBuildAfter($event);
    }
}
