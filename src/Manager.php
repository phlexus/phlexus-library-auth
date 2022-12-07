<?php

/**
 * This file is part of the Phlexus CMS.
 *
 * (c) Phlexus CMS <cms@phlexus.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phlexus\Libraries\Auth;

use Phalcon\Di\Injectable;
use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\ManagerInterface as EventsManagerInterface;
use Phalcon\Session\Manager as PhalconSession;
use Phlexus\Libraries\Auth\Adapter\AdapterInterface as AuthAdapterInterface;
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
    public const MODEL_ADAPTER = 'model';

    /**
     * Plain adapter
     */
    public const PLAIN_ADAPTER = 'plain';

    /**
     * Session key for auth identity
     */
    private const SESSION_AUTH_KEY = 'auth_session';

    /**
     * @var AdapterInterface
     */
    protected AuthAdapterInterface $adapter;

    /**
     * @var string|null
     */
    private $sessionAuthKey = null;

    /**
     * @var string|null
     */
    private $loginRedirect = null;

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
        $this->setloginRedirect($configurations['login_redirect']);

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
     * @param string $key
     */
    public function setSessionAuthKey(string $key): void
    {
        $this->sessionAuthKey = $key;
    }

    /**
     * @return string
     */
    public function getSessionAuthKey(): string
    {
        if ($this->sessionAuthKey === null) {
            return Manager::SESSION_AUTH_KEY;
        }

        return $this->sessionAuthKey;
    }

    /**
     * @param string $uri
     */
    public function setloginRedirect(string $key): void
    {
        $this->loginRedirect = $key;
    }

    /**
     * @return string
     */
    public function getloginRedirect(): string
    {
        return $this->loginRedirect;
    }

    /**
     * @param array $credentials
     * @return bool
     */
    public function login(array $credentials = []): bool
    {
        $eventsManager = $this->getEventsManager();

        /**
         * It is possible to stop login from event.
         * For that, it is necessary to attach event
         * to current type.
         */
        if (
            $eventsManager->hasListeners('auth:beforeLogin') &&
            !$eventsManager->fire('auth:beforeLogin', $this, $credentials)
        ) {
            return false;
        }

        $login = $this->adapter->validate($credentials);
        if ($login === true) {
            $this->getSession()->set($this->getSessionAuthKey(), $this->adapter->getIdentity());
        }

        if ($eventsManager->hasListeners('auth:afterLogin')) {
            $eventsManager->fire('auth:afterLogin', $this);
        }

        return $login;
    }

    /**
     * @return bool
     */
    public function logout(): bool
    {
        if ($this->getEventsManager()->fire('auth:beforeLogout', $this) === false) {
            return false;
        }

        if ($this->isLogged()) {
            $this->getSession()->remove($this->getSessionAuthKey());
        }

        $this->getEventsManager()->fire('auth:afterLogout', $this);

        return true;
    }

    /**
     * @return bool
     */
    public function isLogged(): bool
    {
        return $this->getSession()->has($this->getSessionAuthKey());
    }

    /**
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->getSession()->get($this->getSessionAuthKey());
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

    /**
     * @return PhalconSession
     */
    private function getSession(): PhalconSession
    {
        return $this->di->getShared('session');
    }
}
