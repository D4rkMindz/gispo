<?php

namespace App\Model;

/**
 * Class RegisteredUserModel
 */
class RegisteredUserModel extends AbstractModel
{
    protected $table = 'registered_users';

    /**
     * Get a password for a user.
     *
     * @param string $username
     * @return string
     */
    public function getPasswordForUser(string $username)
    {
        $query = $this->newSelect();
        $query->select('password')->where(['username' => $username]);
        $row = $query->execute()->fetch('assoc');

        return !empty($row) ? $row['password'] : '';
    }
}
