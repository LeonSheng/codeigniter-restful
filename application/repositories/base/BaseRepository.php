<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class BaseRepository extends EntityRepository
{
    protected $_alias = 'o';

    /**
     * Find top one entity by a set of criteria (default to use 'eq' search for string field)
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param bool $likeSearch
     * @return object|null
     */
    public function findOneBy(array $criteria, array $orderBy = null, bool $likeSearch = false)
    {
        $resources = $this->findAllBy($criteria, $orderBy, $likeSearch);
        $list = $resources->getList();
        if (count($list) > 0) {
            return $list[0];
        }
        return null;
    }

    /**
     * Find all entities by a set of criteria (default to use 'like' search for string field)
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param bool $likeSearch
     * @return Resources
     */
    public function findAllBy(array $criteria, array $orderBy = null, bool $likeSearch = true): Resources
    {
        $builder = $this->_em->createQueryBuilder();
        $listBuilder = $builder
            ->select($this->_alias)
            ->from("\\$this->_entityName", $this->_alias)
            ->where('1 = 1');
        $this->appendCriteria($listBuilder, $criteria, $orderBy, $likeSearch);
        $list = $listBuilder->getQuery()->getResult();
        $resources = new Resources();
        $resources->setList($list);
        return $resources;
    }

    /**
     * Find paged entities by a set of criteria (default to use 'like' search for string field)
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param bool $likeSearch
     * @param int|null $pageSize
     * @param int|null $pageIndex
     * @return Resources
     * @throws
     */
    public function findPagedAllBy(array $criteria,
                                   array $orderBy = null,
                                   bool $likeSearch = true,
                                   int $pageSize = null,
                                   int $pageIndex = null): Resources
    {
        $limit = $pageSize !== null && $pageSize > 0 ? (int)$pageSize : 20;
        $pageIndex = $pageIndex !== null && $pageIndex >= 0 ? (int)$pageIndex : 0;
        $offset =  $limit * $pageIndex;

        //total records count
        $builder = $this->_em->createQueryBuilder();
        $countBuilder = $builder
            ->select($builder->expr()->count($this->_alias))
            ->from("\\$this->_entityName", $this->_alias)
            ->where('1 = 1');
        $this->appendCriteria($countBuilder, $criteria, null, $likeSearch);
        $totalElements = (int)$countBuilder->getQuery()->getSingleScalarResult();

        //pagination
        $builder = $this->_em->createQueryBuilder();
        $listBuilder = $builder
            ->select($this->_alias)
            ->from("\\$this->_entityName", $this->_alias)
            ->where('1 = 1');
        $this->appendCriteria($listBuilder, $criteria, $orderBy, $likeSearch, $limit, $offset);
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
     * @param array|null $orderBy
     * @param bool $likeSearch
     * @param int|null $limit
     * @param int|null $offset
     * @throws
     */
    private function appendCriteria(QueryBuilder &$qb,
                                    array $criteria,
                                    array $orderBy = null,
                                    bool $likeSearch = true,
                                    int $limit = null,
                                    int $offset = null)
    {
        $expr = $this->_em->getExpressionBuilder();
        if (!key_exists('isDeleted', $criteria)) {
            $qb = $qb->andWhere($expr->eq("$this->_alias.isDeleted", 'false'));
        }
        foreach ($criteria as $key => $value) {
            $fieldType = $this->_em->getClassMetadata($this->_entityName)->getFieldMapping($key)['type'];
            switch ($fieldType) {
                case 'string':
                    if ($likeSearch && !StringUtils::endWith($key, 'Id')) {
                        $qb = $qb->andWhere($expr->like("$this->_alias.${key}", $qb->expr()->literal("%$value%")));
                    } else {
                        $qb = $qb->andWhere($expr->eq("$this->_alias.${key}", $qb->expr()->literal("$value")));
                    }
                    break;
                default:
                    $qb = $qb->andWhere($expr->eq("$this->_alias.${key}", $value));
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
