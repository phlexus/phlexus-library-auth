<?php
declare(strict_types=1);

namespace Phlexus\Libraries\Auth\Adapter;

use Phalcon\Di\DiInterface;
use Phlexus\Libraries\Auth\Manager;

/**
 * Plain text file Adapter
 *
 * Useful for tests and fast run authentications.
 *
 * DO NOT USE ON PRODUCTION!
 */
class PlainAdapter extends AbstractAdapter implements AdapterInterface
{
    /**
     * Delimiter between login and password on the line
     */
    const AUTH_LINE_DELIMITER = ':';

    /**
     * Path to auth file
     *
     * @var string
     */
    protected $authFilePath;

    /**
     * Associative array of login and passwords
     *
     * @var array
     */
    private $passwords;

    /**
     * Plain constructor.
     *
     * @param array $configurations
     * @param DiInterface $di
     * @throws AuthAdapterException
     */
    public function __construct(DiInterface $di, array $configurations)
    {
        $this->di = $di;
        $this->authFilePath = $configurations['auth_file_path'];

        if (!file_exists($this->authFilePath)) {
            throw new AuthAdapterException('Auth File do not exist: ' . $this->authFilePath);
        }

        if (!is_readable($this->authFilePath)) {
            throw new AuthAdapterException('Auth File is not readable: ' . $this->authFilePath);
        }

        $this->parseAuthFile();
    }

    /**
     * @param array $credentials
     * @return bool
     */
    public function login(array $credentials = []): bool
    {
        if (count($credentials) !== 2) {
            return false;
        }

        list($login, $password) = $credentials;
        if (!isset($this->passwords[$login]) || $this->passwords[$login] !== $password) {
            return false;
        }

        // TODO: Violation of SOLID Principles, make through config value, ex.: 'session_driver'
        $this->di->getShared('session')->set(Manager::SESSION_AUTH_KEY, $login);

        return true;
    }

    /**
     * @return bool
     */
    public function logout(): bool
    {
        $session = $this->di->getShared('session');
        $session->remove(Manager::SESSION_AUTH_KEY);

        return true;
    }

    /**
     * @return bool
     */
    public function isLogged(): bool
    {
        return $this->di->getShared('session')->has(Manager::SESSION_AUTH_KEY);
    }

    /**
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->di->getShared('session')->get(Manager::SESSION_AUTH_KEY);
    }

    /**
     * Parse auth file and build associative array
     * of logins and passwords
     *
     * @return void
     */
    protected function parseAuthFile(): void
    {
        $lines = file_get_contents($this->authFilePath);

        foreach (explode(PHP_EOL, $lines) as $line) {
            list($login, $password) = explode(self::AUTH_LINE_DELIMITER, $line);
            $this->passwords[$login] = $password;
        }
    }
}
