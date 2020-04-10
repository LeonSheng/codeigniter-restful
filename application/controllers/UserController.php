<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserController extends BaseController
{
    function __construct()
    {
        parent::__construct(User::class);
    }

    /**
     * @param User $entity
     */
    protected function beforeCreate(&$entity)
    {
        parent::beforeCreate($entity);
        if (StringUtils::isBlank($entity->getUsername())) {
            $this->response(ERROR_USER_USERNAME_REQUIRED, HTTP_BAD_REQUEST);
        }
        if (StringUtils::isBlank($entity->getPassword())) {
            $this->response(ERROR_USER_PASSWORD_REQUIRED, HTTP_BAD_REQUEST);
        }
        $loaded = $this->repository->findOneBy(['username' => $entity->getUsername()]);
        if ($loaded !== null) {
            $this->response(ERROR_USER_USERNAME_EXISTS, HTTP_BAD_REQUEST);
        }
    }
}
