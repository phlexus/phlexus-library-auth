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
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultInterface;
use Phalcon\Security;

/**
 * Auth Model Adapter
 *
 * Make auth with Phalcon Model
 */
class ModelAdapter extends AbstractAdapter implements AdapterInterface
{
    /**
     * Identity conditions string key
     */
    const IDENTITY_KEY = 'identity';

    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @var string
     */
    protected $identityField;

    /**
     * @var string
     */
    protected $passwordField;

    /**
     * @var string
     */
    protected $userIdField;

    /**
     * @var ResultInterface|null
     */
    protected $user;

    /**
     * Model constructor.
     *
     * @param array $configurations
     * @param DiInterface $di
     * @throws AuthAdapterException
     */
    public function __construct(DiInterface $di, array $configurations)
    {
        $fields = $configurations['fields'];

        $this->modelClass = $configurations['model'];
        $this->identityField = $fields[self::IDENTITY_KEY];
        $this->passwordField = $fields['password'];
        $this->userIdField = $fields['id'];

        $this->di = $di;

        if (!class_exists($this->modelClass)) {
            throw new AuthAdapterException('Model class do not exists. In ' . __CLASS__);
        }

        if (!$this->di->has('security')) {
            throw new AuthAdapterException('Security component as service provider is required. In' . __CLASS__);
        }
    }

    /**
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials): bool
    {
        /** @var Model $class */
        $class = new $this->modelClass;
        $primaryKey = $this->userIdField;

        $row = $class::findFirst([
            'columns' => [$primaryKey, $this->identityField, $this->passwordField],
            sprintf('%s = :%s:', $this->identityField, self::IDENTITY_KEY),
            'bind' => [
                self::IDENTITY_KEY => $credentials[$this->identityField],
            ],
        ]);

        if (!$row instanceof ResultInterface) {
            return false;
        }

        /** @var Security $security */
        $security = $this->di->getShared('security');
        if (!$security->checkHash($credentials['password'], $row->readAttribute($this->passwordField))) {
            return false;
        }

        $this->identity = $row->$primaryKey;

        return true;
    }
}
