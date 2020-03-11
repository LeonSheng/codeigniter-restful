<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Provide useful RESTful CRUD request for each entity
 */
class BaseController extends RestController
{
    /** @var BaseRepository */
    protected $repository;
    protected $className;

    function __construct(string $className)
    {
        parent::__construct();
        $this->load->library('doctrine');
        $this->className = $className;
        $this->repository = $this->doctrine->em->getRepository($className);
    }

    /**
     * Create one
     *
     * @param string $id required
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ReflectionException
     */
    public function index_post(string $id)
    {
        if ($id != null) {
            show_json_error("${id} is forbidden in url", HTTP_BAD_REQUEST);
        }

        $entity = ObjectUtils::fromArray($this->requestBody, $this->className);
        RepositoryUtils::initializeForCreate($entity);
        $this->beforeCreate($entity);
        $this->doctrine->em->persist($entity);
        $this->doctrine->em->flush();
        $this->afterCreate($entity);
        $this->response(ObjectUtils::toArray($entity), HTTP_CREATED);
    }

    /**
     * Delete one by id
     *
     * @param string $id required
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function one_delete(string $id)
    {
        $entity = $this->repository->find($id);
        $this->beforeDelete($entity);
        $this->doctrine->em->remove($entity);
        $this->doctrine->em->flush();
        $this->afterDelete($entity);
        $this->response(null, HTTP_NO_CONTENT);
    }

    /**
     * Delete multiple by ids
     *
     * @param string $ids required
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function index_delete(string $ids)
    {
        $ids = explode(',', $ids);
        $this->doctrine->em->beginTransaction();
        foreach ($ids as $id) {
            $entity = $this->repository->find($id);
            $this->beforeDelete($entity);
            $this->doctrine->em->remove($entity);
            $this->afterDelete($entity);
        }
        $this->doctrine->em->flush();
        $this->doctrine->em->commit();
        $this->response(null, HTTP_NO_CONTENT);
    }

    /**
     * Update one by id
     *
     * @param string $id required
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ReflectionException
     */
    public function one_patch(string $id)
    {
        /** @var BaseEntity $entity */
        $entity = ObjectUtils::fromArray($this->requestBody, $this->className);
        $entity->setIdNull();
        /** @var BaseEntity $loadedEntity */
        $loadedEntity = $this->repository->find($id);
        $this->beforeUpdate($loadedEntity, $entity);
        RepositoryUtils::setUpdate($entity, $loadedEntity);
        $this->doctrine->em->persist($loadedEntity);
        $this->doctrine->em->flush();
        $this->afterUpdate($loadedEntity);
        $this->response(ObjectUtils::toArray($loadedEntity), HTTP_OK);
    }

    /**
     * Find paged all by example entity (that is, criteria in sql)
     *
     * @param int|null $pageSize Page size
     * @param int|null $pageIndex Page index
     * @param string|null $sort Used to query database with orderBy field. For example, $sort = 'id,desc'
     * @throws MappingException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws ReflectionException
     */
    public function index_get($pageSize, $pageIndex, $sort)
    {
        $args = $this->requestParams;
        $exampleEntity = ObjectUtils::fromArray($args, $this->className);
        $exampleArray = ObjectUtils::toArray($exampleEntity);
        $orderBy = $sort ? explode(',', $sort) : null;
        if ($orderBy != null && count($orderBy) === 2) {
            $key = $orderBy[0];
            $value = strtolower($orderBy[1]);
            if (array_key_exists($key, $exampleArray) && ($value === 'asc' || $value === 'desc')) {
                $orderBy = [$key => $value];
            } else {
                $orderBy = null;
            }
        }
        else {
            $orderBy = null;
        }
        $exampleArray = array_filter($exampleArray, function ($value) {
            return $value !== null;
        });
        $data = $this->repository->findPagedAll($exampleArray, $pageSize, $pageIndex, $orderBy);
        $this->response($data, HTTP_OK);
    }

    /**
     * Find one by id
     *
     * @param string $id required
     * @throws ReflectionException
     */
    public function one_get(string $id)
    {
        $one = $this->repository->find($id);
        if ($one === null)
            show_404();
        $this->response(ObjectUtils::toArray($one), HTTP_OK);
    }

    protected function beforeCreate($entity) {}
    protected function beforeDelete($entity) {}
    protected function beforeUpdate($loadedEntity, $patch) {}
    protected function afterCreate($entity) {}
    protected function afterDelete($entity) {}
    protected function afterUpdate($loadedEntity) {}
}
