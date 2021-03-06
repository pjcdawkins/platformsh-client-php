<?php

namespace Platformsh\Client\Model;

/**
 * A user with access to a Platform.sh project.
 *
 * @property-read string $id
 * @property-read string $role
 */
class ProjectUser extends Resource
{

    /** @var array */
    protected static $required = ['email'];

    const ROLE_ADMIN = 'admin';
    const ROLE_VIEWER = 'viewer';

    /**
     * Get the account information for this user.
     *
     * @throws \Exception
     *
     * @return Account
     */
    public function getAccount()
    {
        $uuid = $this->getProperty('id');
        $url = $this->makeAbsoluteUrl('/api/users');
        $account = Account::get($uuid, $url, $this->client);
        if (!$account) {
            throw new \Exception("Account not found for user: " . $uuid);
        }
        return $account;
    }

    /**
     * Get the user's roles on environments.
     *
     * @param Environment[] $environments
     *
     * @return array
     *   An array of environment IDs mapped to roles ('admin', 'contributor',
     *   or 'viewer').
     */
    public function getEnvironmentRoles(array $environments)
    {
        $access = [];
        foreach ($environments as $environment) {
            $result = $this->sendRequest($environment->getUri() . '/access');
            if ($result['id'] === $this->id) {
                $access[$environment->id] = $result['role'];
            }
        }

        return $access;
    }

    /**
     * Get the user's SSH keys.
     *
     * @param int $limit
     *
     * @return SshKey_Platform[]
     */
    public function getSshKeys($limit = 0)
    {
        return SshKey_Platform::getCollection($this->getUri() . '/keys', $limit, [], $this->client);
    }

    /**
     * Add an SSH key.
     *
     * @param string $key
     *
     * @return SshKey_Platform
     */
    public function addSshKey($key)
    {
        return SshKey_Platform::create(['key' => $key], $this->getUri() . '/keys', $this->client);
    }

    /**
     * @inheritdoc
     */
    protected static function checkProperty($property, $value)
    {
        $errors = [];
        if ($property === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address: '$value'";
        }
        elseif ($property === 'role' && !in_array($value, [self::ROLE_ADMIN, self::ROLE_VIEWER])) {
            $errors[] = "Invalid role: '$value'";
        }
        return $errors;
    }

    /**
     * Check whether the user is editable.
     *
     * @return bool
     */
    public function isEditable()
    {
        return $this->operationAvailable('edit');
    }
}
