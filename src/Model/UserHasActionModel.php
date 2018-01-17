<?php

namespace App\Model;

use Cake\Database\Query;

class UserHasActionModel extends AbstractModel
{
    protected $table = 'user_has_actions';

    /**
     * Get all actions
     *
     * @return array
     */
    public function getAllActions(): array
    {
        $fields = [
            'id' => 'users.id',
            'first_name' => 'users.first_name',
            'last_name' => 'users.last_name',
            'name' => 'actions.name',
            'time' => 'user_has_actions.time',
        ];
        $query = $this->newSelect();
        $query->select($fields)
            ->join([
                [
                    'table' => 'actions',
                    'type' => 'INNER',
                    'conditions' => 'user_has_actions.actions_id = actions.id',
                ],
                [
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => 'user_has_actions.users_id = users.id',
                ],
            ]);
        $rows = $query->execute()->fetchAll('assoc');

        return $rows ?: [];
    }

    /**
     * Get last action
     *
     * @param string $userId
     * @return array
     */
    public function getLastAction(string $userId)
    {
        $query = $this->getQuery($userId);
        $action = $query->execute()->fetch('assoc');

        return $action ?: [];
    }

    /**
     * Get actions
     *
     * @param string $userId
     * @return array
     */
    public function getActions(string $userId)
    {
        $query = $this->getQuery($userId)->order(['user_has_actions.time'=>'ASC']);
        $actions = $query->execute()->fetchAll('assoc');

        return $actions ?: [];
    }

    /**
     * Check in user.
     *
     * @param string $userId
     * @return bool
     */
    public function checkIn(string $userId): bool
    {
        $row = [
            'users_id' => $userId,
            'actions_id' => 1,
            'time' => date('Y-m-d H:i:s'),
        ];

        return $this->insert($row);
    }

    /**
     * Check in user.
     *
     * @param string $userId
     * @return bool
     */
    public function checkOut(string $userId): bool
    {
        $row = [
            'users_id' => $userId,
            'actions_id' => 2,
            'time' => date('Y-m-d H:i:s'),
        ];

        return $this->insert($row);
    }

    /**
     * @param string $userId
     * @return Query
     */
    private function getQuery(string $userId): Query
    {
        $fields = [
            'id' => 'actions.id',
            'name' => 'actions.name',
            'time' => 'user_has_actions.time',
        ];
        $query = $this->newSelect();
        $query->select($fields)
            ->join([
                [
                    'table' => 'actions',
                    'type' => 'INNER',
                    'conditions' => 'user_has_actions.actions_id = actions.id',
                ],
            ])
            ->where(['user_has_actions.users_id' => $userId])
            ->order(['user_has_actions.time' => 'DESC']);

        return $query;
    }
}
