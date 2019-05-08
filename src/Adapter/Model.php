<?php declare(strict_types=1);

namespace Phlexus\Libraries\Auth\Adapter;

use Phalcon\DiInterface;
use Phalcon\Mvc\ModelInterface;

/**
 * Auth Model Adapter
 *
 * Make auth with Phalcon Model
 *
 * @package Phlexus\Libraries\Auth\Adapter
 */
class Model extends AbstractAdapter
{
    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @var string
     */
    protected $loginField;

    /**
     * @var string
     */
    protected $passwordField;

    /**
     * @var string
     */
    protected $userIdField;

    /**
     * @var array
     */
    protected $authData;

    /**
     * @var DiInterface
     */
    protected $di;

    /**
     * @var ModelInterface|null
     */
    protected $user;

    /**
     * Model constructor.
     *
     * @param array $modelData
     * @param array $authData
     * @param DiInterface $di
     */
    public function __construct(array $modelData, array $authData, DiInterface $di)
    {
        $this->modelClass = $modelData['model'];
        $this->loginField = $modelData['fields']['login'];
        $this->passwordField = $modelData['fields']['password'];
        $this->userIdField = $modelData['fields']['id'];

        $this->authData = $authData;
        $this->di = $di;
    }

    /**
     * @return bool
     */
    public function login(): bool
    {
        $modelUser = call_user_func([$this->modelClass, 'findFirst'], [
            $this->loginField . ' = ?login',
            'bind' => [
                'login' => $this->authData['login'],
            ],
        ]);

        if (!$modelUser instanceof ModelInterface) {
            return false;
        }

        $security = $this->di->getShared('security');
        if (!$security->checkHash($this->authData['password'], $modelUser->readAttribute($this->passwordField))) {
            return false;
        }

        $this->user = $modelUser;

        return true;
    }

    /**
     * @return bool
     */
    public function logout(): bool
    {
        // TODO
    }

    /**
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        // TODO
    }

    public function getUser()
    {
        if ($this->user instanceof ModelInterface) {
            return $this->user;
        }

        // TODO
    }
}
