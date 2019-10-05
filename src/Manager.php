<?php
declare(strict_types=1);

namespace Phlexus\Libraries\Auth;

use Phalcon\Di\Injectable;
use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\ManagerInterface as EventsManagerInterface;
use Phlexus\Libraries\Auth\Adapter\AdapterInterface as AuthAdapterInterface;
use Phlexus\Libraries\Auth\Adapter\AdapterInterface;
use Phlexus\Libraries\Auth\Adapter\AuthAdapterException;
use Phlexus\Libraries\Auth\Adapter\ModelAdapter;
use Phlexus\Libraries\Auth\Adapter\PlainAdapter;

/**
 * Class Auth Manager
 */
class Manager extends Injectable implements EventsAwareInterface
{
    /**
     * Model Adapter
     */
    const MODEL_ADAPTER = 'model';

    /**
     * Plain adapter
     */
    const PLAIN_ADAPTER = 'plain';

    /**
     * Session key for auth identity
     */
    const SESSION_AUTH_KEY = 'auth_session';

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * Manager constructor.
     *
     * @param string $adapterName
     * @param array $configurations
     * @throws AuthAdapterException
     * @throws AuthException
     */
    public function __construct(string $adapterName, array $configurations = [])
    {
        $this->initAdapter($adapterName, $configurations);

        if ($this->getDI()->has('eventsManager')) {
            $this->setEventsManager($this->getDI()->getShared('eventsManager'));
        }
    }

    /**
     * @return AdapterInterface
     */
    public function getAdapter(): AuthAdapterInterface
    {
        return $this->adapter;
    }

    /**
     * @return EventsManagerInterface
     */
    public function getEventsManager(): EventsManagerInterface
    {
        return $this->eventsManager;
    }

    /**
     * @param EventsManagerInterface $eventsManager
     * @return void
     */
    public function setEventsManager(EventsManagerInterface $eventsManager): void
    {
        $this->eventsManager = $eventsManager;
    }

    /**
     * @param array $credentials
     * @return bool
     */
    public function login(array $credentials = []): bool
    {
        $this->getEventsManager()->fire('auth:before_login', $this);
        $login = $this->adapter->login($credentials);
        $this->getEventsManager()->fire('auth:after_login', $this);

        return $login;
    }

    /**
     * @return bool
     */
    public function logout(): bool
    {
        $this->getEventsManager()->fire('auth:before_logout', $this);

        if (!$this->isLogged()) {
            $logout = true;
        } else {
            $logout = $this->adapter->logout();
        }

        $this->getEventsManager()->fire('auth:after_logout', $this);

        return $logout;
    }

    /**
     * @return bool
     */
    public function isLogged(): bool
    {
        return $this->adapter->isLogged();
    }

    /**
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->adapter->getIdentity();
    }

    /**
     * Init Adapter based on its name
     *
     * @param string $adapterName
     * @param array $configurations
     * @throws AuthAdapterException
     * @throws AuthException
     */
    protected function initAdapter(string $adapterName, array $configurations = []): void
    {
        switch ($adapterName) {
            case self::MODEL_ADAPTER:
                $this->adapter = new ModelAdapter($this->getDI(), $configurations);
                break;

            case self::PLAIN_ADAPTER:
                $this->adapter = new PlainAdapter($this->getDI(), $configurations);
                break;

            default:
                throw new AuthException('Auth driver not found.');
        }
    }
}
