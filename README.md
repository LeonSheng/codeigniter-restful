# PHP RESTful API Server

#### Description
This is a lightweight, easy-to-deploy RESTful api server based on CodeIginter framework. It also integrates Doctrine as ORM database framework to improve development efficiency.

#### Start to run
```
composer install
php -S localhost:80 -t .
```

#### Basic Usage

**Database Config**

Set your database information(default to use MySQL).
```
notepad application/config/database.php
```
```
...
'username' => 'your database username',
'password' => 'your database password',
'database' => 'database name',
...
```


**Create Tables**

There are already two entities named 'User' and 'LoginTrace', use doctrine orm to create/update tables.
```
cd tools
php cli-doctrine.php orm:schema-tool:update --dump-sql --force
```


**RESTful CRUD**

Use [Curl](https://curl.haxx.se/) or [Postman](https://www.postman.com/) to do the tests.

* **POST:** Create a user
```
curl http://localhost/users -X POST -v -d "{\"username\":\"admin\",\"password\":\"admin\"}" -H "content-type: application/json"
```

* **GET:** Find all users by pagination or criteria

Default pagination: pageSize=20, pageIndex=0
```
curl http://localhost/users -X GET
```

Specify pagination: pageSize=30, pageIndex=1 (second page), sort=username,desc (order by username descent)
```
curl http://localhost/users?pageSize=30&pageIndex=1&sort=username,desc -X GET
```

Specify criteria: get all paged users which username contains 'ad'
```
curl http://localhost/users?username=ad -X GET
```

* **GET:** Find one user by id

Suppose the user id is 5e6916390b500a2ecde6aa40
 ```
 curl http://localhost/users/5e6916390b500a2ecde6aa40 -X GET
 ```

* **PATCH:** Update a user by id

change the username from 'admin' to 'admin2' with given user id
```
curl http://localhost/users/5e6916390b500a2ecde6aa40 -X PATCH -v -d "{\"username\":\"admin2\"}" -H "content-type: application/json"
```

* **DELETE:** Delete a user by id (logic delete)
```
curl http://localhost/users/5e6916390b500a2ecde6aa40 -X DELETE -v -H "content-type: application/json"
```

* **DELETE:** Delete a user by id (physical delete)
```
curl http://localhost/users/5e6916390b500a2ecde6aa40?physical=1 -X DELETE -v -H "content-type: application/json"
```

**Create Your Entity**

For example, create Product entity
```
notepad application/models/Product.php
```
```
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ProductRepository")
 * @ORM\Table(name="product",
 *     options={"charset":"utf8mb4", "row_format":"DYNAMIC", "comment":"Product Table"})
 */
class Product extends BaseEntity {
    /**
     * @ORM\Column(type="string", length=120, options={"default": "", "comment":"Product name"})
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=500, options={"default": "", "comment":"Product decription"})
     * @var string
     */
    private $description;

    //Getter Setter
    ...
}
```

Create Product repository
```
notepad application/repositories/ProductRepository.php
```
```
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductRepository extends BaseRepository
{
}
```

Create Product controller
```
notepad application/controllers/ProductController.php
```
```
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductController extends BaseController
{
    function __construct()
    {
        parent::__construct(Product::class);
    }
}
```

Add route config
```
notepad application/controllers/routes.php
```
```
...
$route['products'] = 'ProductController';
$route['products/(:any)'] = 'ProductController/one/id/$1';
```

Run table update script
```
cd tools
php cli-doctrine.php orm:schema-tool:update --dump-sql --force
``` 

Test your own entity CRUD
```
curl http://localhost/products -X POST ...
curl http://localhost/products -X GET ...
curl http://localhost/products/{id} -X GET ...
curl http://localhost/products/{id} -X PATCH ...
curl http://localhost/products/{id} -X DELETE ...
```

Can add custom logic before or after CRUD
```
notepad application/controllers/ProductController.php
```
```
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductController extends BaseController
{
    function __construct()
    {
        parent::__construct(Product::class);
    }
    
    protected function beforeCreate($entity)
    {//do something before create a product
    }

    protected function beforeUpdate($loadedEntity, $patch)
    {//do something before update a product
    }

    protected function afterDelete($entity)
    {//do something after delete a product
    }
}
```

#### Login & Authentication (JWT)
Edit rest config file to enable authentication and must change jwt key which is used to encrypt / decrypt access token.
```
notepad application/config/config_rest.php
```
```
...
$config['authc_enable'] = true;
...
$config['jwt_key'] = 'AnyString'; //must change it rather than HelloWorld
...
```
login api is excluded from authentication, you can add more exclusive apis.
```
...
$config['authc_exclude_uris'] = array(
    '/login'
);
...
```
When authentication is enabled, any api call except exclusives will go through authentication hook before controller
```
application/hooks/AuthHook.php
```

**Do Login**

Assuming we already have a user in database, call login api:
```
curl http://localhost/login -X POST -v -d "{\"username\":\"admin\",\"password\":\"admin\"}" -H "content-type: application/json"
```
If login successfully, accessToken will return:
```
{
    "errcode":0,
    "username":"admin",
    "accessToken":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjoiNWU2OTI4N2IxMjY1NzIyZWU4NmE4ZTJmIiwidXNlcm5hbWUiOiJhZG1pbiIsInBhc3N3b3JkIjoiYWRtaW4iLCJleHAiOjE1ODM5NTI0MzZ9.pZGgEJv1jVRUUK8c__ppz6-a3sLRRTUPEctIW5LWMi8"
}
```

**Test API with access token**

If not provide access token, token is invalid or token is expired: 
```
curl http://localhost/users -X GET
```
401 will return:
```
{
    "status": 401,
    "error": "Token not provided or invalid"
}
```
Provide access token (from login api) in request Authorization header (don't forget Bearer prefix)
```
curl http://localhost/users -X GET -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjoiNWU2OTI4N2IxMjY1NzIyZWU4NmE4ZTJmIiwidXNlcm5hbWUiOiJhZG1pbiIsInBhc3N3b3JkIjoiYWRtaW4iLCJleHAiOjE1ODM5NTI0MzZ9.pZGgEJv1jVRUUK8c__ppz6-a3sLRRTUPEctIW5LWMi8"
```

#### Package & Deploy
```
cd tools
php package.php --env production
```
```
cd tools
php package.php --env testing
```
```
cd tools
php package.php --env development
```
A zip archive generated with specified env.

Upload it to your web server through ftp and unzip, then deploying is done.

#### Links
* CodeIgniter: https://codeigniter.com
* Doctrine: https://www.doctrine-project.org
* Composer Mirror Site: https://developer.aliyun.com/composer
