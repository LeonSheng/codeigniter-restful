<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class BaseRepository extends EntityRepository
{
    protected $_alias = 'o';

    /**
     * Find all entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param bool $isExact
     * @return Resources
     */
    public function findAllBy($criteria, $orderBy = null, bool $isExact = false): Resources
    {
        $builder = $this->_em->createQueryBuilder();
        $listBuilder = $builder
            ->select($this->_alias)
            ->from($this->_entityName, $this->_alias)
            ->where('1 = 1');
        $this->appendCriteria($listBuilder, $criteria, $isExact, $orderBy);
        $list = $listBuilder->getQuery()->getResult();
        $resources = new Resources();
        $resources->setList($list);
        return $resources;
    }

    /**
     * Find paged entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $pageSize
     * @param int|null $pageIndex
     * @param bool $isExact
     * @return Resources
     * @throws
     */
    public function findPagedAllBy($criteria, $pageSize = null, $pageIndex = null, $orderBy = null, bool $isExact = false): Resources
    {
        $limit = $pageSize !== null && $pageSize > 0 ? (int)$pageSize : 20;
        $pageIndex = $pageIndex !== null && $pageIndex >= 0 ? (int)$pageIndex : 0;
        $offset =  $limit * $pageIndex;

        //total records count
        $builder = $this->_em->createQueryBuilder();
        $countBuilder = $builder
            ->select($builder->expr()->count($this->_alias))
            ->from($this->_entityName, $this->_alias)
            ->where('1 = 1');
        $this->appendCriteria($countBuilder, $criteria, $isExact);
        $totalElements = (int)$countBuilder->getQuery()->getSingleScalarResult();

        //pagination
        $builder = $this->_em->createQueryBuilder();
        $listBuilder = $builder
            ->select($this->_alias)
            ->from($this->_entityName, $this->_alias)
            ->where('1 = 1');
        $this->appendCriteria($listBuilder, $criteria, $isExact, $orderBy, $limit, $offset);
        $list = $listBuilder->getQuery()->getResult();

        $page = new Page();
        $page->setTotalElements($totalElements);
        $page->setTotalPages(ceil($totalElements / $limit));
        $page->setPageSize($limit);
        $page->setPageIndex($pageIndex);
        $resources = new Resources();
        $resources->setList($list);
        $resources->setPage($page);
        return $resources;
    }

    /**
     * @param QueryBuilder $qb
     * @param array $criteria
     * @param bool $isExact
     * @param null $orderBy
     * @param null $limit
     * @param null $offset
     * @throws
     */
    private function appendCriteria(QueryBuilder &$qb,
                                    array $criteria,
                                    $isExact = false,
                                    $orderBy = null,
                                    $limit = null,
                                    $offset = null)
    {
        $expr = $this->_em->getExpressionBuilder();
        if (!key_exists('isDeleted', $criteria)) {
            $qb = $qb->andWhere($expr->eq("{$this->_alias}.isDeleted", 'false'));
        }
        foreach ($criteria as $key => $value) {
            $fieldType = $this->_em->getClassMetadata($this->_entityName)->getFieldMapping($key)['type'];
            switch ($fieldType) {
                case 'string':
                    if ($isExact || StringUtils::endWith($key, 'Id')) {
                        $qb = $qb->andWhere($expr->eq("{$this->_alias}.${key}", $qb->expr()->literal("$value")));
                    } else {
                        $qb = $qb->andWhere($expr->like("{$this->_alias}.${key}", $qb->expr()->literal("%$value%")));
                    }
                    break;
                default:
                    $qb = $qb->andWhere($expr->eq("{$this->_alias}.${key}", $value));
                    break;
            }
        }
        if ($orderBy !== null) {
            foreach ($orderBy as $key => $order) {
                $qb = $qb->addOrderBy("{$this->_alias}.${key}", $order);
            }
        }
        if ($limit !== null) {
            $qb = $qb->setMaxResults($limit);
        }

        if ($offset !== null) {
            $qb = $qb->setFirstResult($offset);
        }
    }
}
