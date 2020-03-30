<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
     * @throws
     */
    public function index_post()
    {
        $entity = ObjectUtils::fromArray($this->requestBody, $this->className);
        RepositoryUtils::initializeForCreate($entity);
        $this->beforeCreate($entity);
        $this->doctrine->em->persist($entity);
        $this->doctrine->em->flush();
        $this->afterCreate($entity);
        $this->response(ObjectUtils::toArray($entity), HTTP_CREATED);
    }

    /**
     * Update one by id
     *
     * @param string $id required
     * @throws
     */
    public function one_patch(string $id)
    {
        /** @var BaseEntity $entity */
        $entity = ObjectUtils::fromArray($this->requestBody, $this->className);
        $entity->setIdNull();
        $loadedEntity = $this->repository->find($id);
        if ($loadedEntity === null)
            show_404();
        $this->beforeUpdate($loadedEntity, $entity);
        RepositoryUtils::setUpdate($entity, $loadedEntity);
        $this->doctrine->em->persist($loadedEntity);
        $this->doctrine->em->flush();
        $this->afterUpdate($loadedEntity);
        $this->response(ObjectUtils::toArray($loadedEntity), HTTP_OK);
    }

    /**
     * Delete one by id
     *
     * @param string $id required
     * @param bool $physical Whether to delete it physically
     * @throws
     */
    public function one_delete(string $id, bool $physical)
    {
        /** @var BaseEntity $entity */
        $entity = $this->repository->find($id);
        if ($entity === null)
            show_404();
        $this->beforeDelete($entity);
        if ($physical) {
            $this->doctrine->em->remove($entity);
        } else {
            $entity->setIsDeleted(true);
        }
        $this->doctrine->em->flush();
        $this->afterDelete($entity);
        $this->response(null, HTTP_NO_CONTENT);
    }

    /**
     * Delete multiple by ids
     *
     * @param bool $physical Whether to delete them physically
     * @throws
     */
    public function index_delete(bool $physical)
    {
        $ids = $this->requestBody;
        if (is_array($ids) && count($ids) > 0) {
            $this->doctrine->em->beginTransaction();
            foreach ($ids as $id) {
                $entity = $this->repository->find($id);
                if ($entity === null)
                    show_404();
                $this->beforeDelete($entity);
                if ($physical) {
                    $this->doctrine->em->remove($entity);
                } else {
                    $entity->setIsDeleted(true);
                }
                $this->afterDelete($entity);
            }
            $this->doctrine->em->flush();
            $this->doctrine->em->commit();
        } else {
            show_json_error(STATUS_TEXT[HTTP_BAD_REQUEST], HTTP_BAD_REQUEST);
        }
        $this->response(null, HTTP_NO_CONTENT);
    }

    /**
     * Find paged all by example entity (that is, criteria in sql)
     *
     * @param string|null $orderBy Usage example: orderBy=id,desc
     * @param bool $likeSearch eq or like criteria search
     * @param int|null $pageSize Page size
     * @param int|null $pageIndex Page index
     */
    public function index_get(string $orderBy = null, bool $likeSearch = true, int $pageSize = null, int $pageIndex = null)
    {
        $args = $this->requestParams;
        $exampleEntity = ObjectUtils::fromArray($args, $this->className);
        $exampleArray = ObjectUtils::toArray($exampleEntity);
        $orderBy = $this->parseOrderBy($orderBy, $exampleArray);
        $criteria = array_filter($exampleArray, function ($value) {
            return $value !== null;
        });
        $resources = $this->repository->findPagedAllBy($criteria, $orderBy, $likeSearch, $pageSize, $pageIndex);
        $data = [
            'list' => ObjectUtils::toArray($resources->getList()),
            'page' => [
                'totalElements' => $resources->getPage()->getTotalElements(),
                'totalPages' => $resources->getPage()->getTotalPages(),
                'pageSize' => $resources->getPage()->getPageSize(),
                'pageIndex' => $resources->getPage()->getPageIndex(),
            ]
        ];
        $this->response($data, HTTP_OK);
    }

    /**
     * Find one by id
     *
     * @param string $id required
     */
    public function one_get(string $id)
    {
        $one = $this->repository->find($id);
        if ($one === null)
            show_404();
        $this->response(ObjectUtils::toArray($one), HTTP_OK);
    }

    /**
     * @param string|null $orderBy
     * @param array $exampleArray
     * @return array|null
     */
    public function parseOrderBy($orderBy, $exampleArray)
    {
        $orderBy = is_string($orderBy) ? explode(',', $orderBy) : null;
        if ($orderBy !== null && is_array($orderBy) && count($orderBy) === 2) {
            $key = $orderBy[0];
            $value = strtolower($orderBy[1]);
            if (array_key_exists($key, $exampleArray) && ($value === 'asc' || $value === 'desc')) {
                $orderBy = [$key => $value];
            } else {
                $orderBy = null;
            }
        } else {
            $orderBy = null;
        }
        return $orderBy;
    }

    protected function beforeCreate($entity) {}
    protected function beforeUpdate($loadedEntity, $patch) {}
    protected function beforeDelete($entity) {}
    protected function afterCreate($entity) {}
    protected function afterUpdate($loadedEntity) {}
    protected function afterDelete($entity) {}
}
