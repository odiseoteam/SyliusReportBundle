<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\DataFetcher;

use Sylius\Bundle\ReportBundle\DataFetcher\TimePeriod;
use Sylius\Bundle\ReportBundle\Form\Type\DataFetcher\SalesTotalType;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Report\DataFetcher\DefaultDataFetchers;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class SalesTotalDataFetcher extends TimePeriod
{
    /**
     * {@inheritdoc}
     */
    protected function getData(array $configuration = [])
    {
        $groupBy = $this->getGroupBy($configuration);

        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('DATE(o.checkout_completed_at) as date', 'COUNT(o.id) as "Number of orders"')
            ->from($this->entityManager->getClassMetadata(OrderInterface::class)->getTableName(), 'o')
            ->where($queryBuilder->expr()->gte('o.checkout_completed_at', ':from'))
            ->andWhere($queryBuilder->expr()->lte('o.checkout_completed_at', ':to'))
            ->setParameter('from', $configuration['start']->format('Y-m-d H:i:s'))
            ->setParameter('to', $configuration['end']->format('Y-m-d H:i:s'))
            ->groupBy($groupBy)
            ->orderBy($groupBy)
        ;

        $baseCurrencyCode = $configuration['baseCurrency'] ? 'in '.$configuration['baseCurrency'] : '';
        $queryBuilder
            ->select('DATE(o.checkout_completed_at) as date', 'TRUNCATE(SUM(o.total)/ 100,2) as "total sum '.$baseCurrencyCode.'"')
        ;

        return $queryBuilder
            ->execute()
            ->fetchAll()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return SalesTotalType::class;
    }
}
