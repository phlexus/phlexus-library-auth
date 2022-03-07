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

namespace Phlexus\Libraries\Auth\Adapter;

use Phalcon\Di\DiInterface;

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
    private const AUTH_LINE_DELIMITER = ':';

    /**
     * Path to auth file
     *
     * @var string
     */
    protected string $authFilePath;

    /**
     * Associative array of login and passwords
     *
     * @var array
     */
    private array $passwords;

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
    public function validate(array $credentials): bool
    {
        if (count($credentials) !== 2) {
            return false;
        }

        list($login, $password) = $credentials;
        if (!isset($this->passwords[$login]) || $this->passwords[$login] !== $password) {
            return false;
        }

        $this->identity = $login;

        return true;
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
