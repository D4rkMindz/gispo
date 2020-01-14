<?php

namespace App\Model;

use Cake\Database\Query;
use RuntimeException;

/**
 * Class UserModel
 */
class UserModel extends AbstractModel
{
    protected $table = 'users';

    /**
     * Get user ID by barcode.
     *
     * @param string $barcode
     * @return string
     */
    public function getIdByBarcode(string $barcode): string
    {
        $query = $this->newSelect();
        $query->select('id')->where(['barcode' => $barcode]);
        $row = $query->execute()->fetch('assoc');

        return $row ? $row['id'] : '';
    }

    /**
     * Get name by id.
     *
     * @param string $userId
     * @return string
     */
    public function getFullName(string $userId)
    {
        $query = $this->newSelect();
        $query->select(['first_name', 'last_name'])->where(['id' => $userId]);
        $row = $query->execute()->fetch('assoc');

        if (!$row) {
            throw new RuntimeException("No user (" . $userId . ") found");
        }

        return $row['first_name'] . ' ' . $row['last_name'];
    }

    /**
     * Get all users.
     *
     * @param int $limit
     * @return array
     */
    public function getAllUsers(?int $limit = 1000): array
    {
        $query = $this->getQuery($limit);
        $users = $query->execute()->fetchAll('assoc');

        if (empty($users)) {
            return [];
        }

        return $users;
    }

    /**
     * Get user.
     *
     * @param string $userId
     * @return array
     */
    public function getUser(string $userId): array
    {
        $query = $this->getQuery(1);
        $query->where(['users.id' => $userId]);
        $user = $query->execute()->fetch('assoc');

        return $user ?: [];
    }

    /**
     * Check if user exists by barcode
     *
     * @param string $barcode
     * @return bool
     */
    public function exists(string $barcode)
    {
        $query = $this->newSelect();
        $query->select([1]);
        $query->where(['users.barcode' => $barcode]);
        $user = $query->execute()->fetch();

        return !empty($user);
    }

    /**
     * Save a user.
     *
     * @param array $user
     * @return int
     */
    public function save(array $user)
    {
        return $this->insert($user);
    }

    /**
     * Find user by specific criteria
     *
     * @param string|null $barcode
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $email
     * @return array
     */
    public function findUser(
        string $barcode = null,
        string $firstName = null,
        string $lastName = null,
        string $email = null
    ) {
        $where = [];
        if (!empty($barcode)) {
            $where['barcode LIKE'] = '%' . trim($barcode) . '%';
        }

        if (!empty($firstName)) {
            $where['first_name LIKE'] = '%' . trim($firstName) . '%';
        }

        if (!empty($lastName)) {
            $where['last_name LIKE'] = '%' . trim($lastName) . '%';
        }

        if (!empty($email)) {
            $where['email LIKE'] = '%' . trim($email) . '%';
        }

        if (empty($where)) {
            $where = ['id' => 0];// to prevent any return data
        }

        $query = $this->getQuery(1000);
        $query->where($where);
        $rows = $query->execute()->fetchAll('assoc');

        return $rows ?: [];
    }

    /**
     * @param int $limit
     * @return Query
     */
    private function getQuery(int $limit): Query
    {
        $fields = [
            'id' => 'users.id',
            'first_name' => 'users.first_name',
            'last_name' => 'users.last_name',
            'email' => 'users.email',
            'barcode' => 'users.barcode',
            'photo_file_name' => 'users.photo_file_name',
        ];

        $query = $this->newSelect();
        $query->select($fields)
            ->limit($limit);

        return $query;
    }
}
