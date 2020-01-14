<?php

namespace App\Model;

use Cake\Database\Connection;
use Cake\Database\Query;
use Cake\Database\StatementInterface;

/**
 * Class AbstractModel
 */
abstract class AbstractModel implements ModelInterface
{
    protected $table = null;

    protected $connection = null;

    /**
     * AppTable constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection = null)
    {
        $this->connection = $connection;
    }

    /**
     * Get Query.
     *
     * @return Query
     */
    protected function newSelect(): Query
    {
        return $this->connection->newQuery()->from($this->table);
    }

    /**
     * Get all entries from database.
     *
     * @return array $rows
     */
    protected function getAll(): array
    {
        $query = $this->newSelect();
        $query->select('*');
        $rows = $query->execute()->fetchAll('assoc');

        return $rows;
    }

    /**
     * Insert into database.
     *
     * @param array $row with data to insertUser into database
     * @return int
     */
    protected function insert(array $row): int
    {
        return $this->connection->insert($this->table, $row)->lastInsertId();
    }

    /**
     * Update database
     *
     * @param string $where should be the id
     * @param array $row
     * @return StatementInterface
     */
    protected function update(array $row, array $where): StatementInterface
    {
        $query = $this->connection->newQuery();
        $query->update($this->table)
            ->set($row)
            ->where($where);
        return $query->execute();
    }
    /**
     * Delete from database.
     *
     * @param string $id
     * @return StatementInterface
     */
    protected function delete(string $id): StatementInterface
    {
        return $this->connection->delete($this->table, ['id' => $id]);
    }
}
