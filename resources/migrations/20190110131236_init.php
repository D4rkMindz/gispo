<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class Init extends AbstractMigration
{
    public function change()
    {
        $this->table("actions", [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'comment' => '',
                'row_format' => 'Dynamic',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'precision' => '10',
            ])
            ->addColumn('name', 'string', [
                'null' => false,
                'limit' => 45,
                'collation' => 'utf8_unicode_ci',
                'encoding' => 'utf8',
                'comment' => 'Check in or check out',
                'after' => 'id',
            ])
            ->create();
        $this->table("registered_users", [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'comment' => '',
                'row_format' => 'Dynamic',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'precision' => '10',
                'identity' => 'enable',
            ])
            ->addColumn('username', 'string', [
                'null' => false,
                'limit' => 80,
                'collation' => 'utf8_unicode_ci',
                'encoding' => 'utf8',
                'after' => 'id',
            ])
            ->addColumn('password', 'string', [
                'null' => false,
                'limit' => 255,
                'collation' => 'utf8_unicode_ci',
                'encoding' => 'utf8',
                'after' => 'username',
            ])
            ->create();
        $this->table("user_has_actions", [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'comment' => '',
                'row_format' => 'Dynamic',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'precision' => '10',
                'identity' => 'enable',
            ])
            ->addColumn('actions_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'precision' => '10',
                'after' => 'id',
            ])
            ->addColumn('users_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'precision' => '10',
                'after' => 'actions_id',
            ])
            ->addColumn('time', 'datetime', [
                'null' => false,
                'after' => 'users_id',
            ])
        ->addIndex(['users_id'], [
                'name' => 'fk_actions_has_users_users1_idx',
                'unique' => false,
            ])
        ->addIndex(['actions_id'], [
                'name' => 'fk_actions_has_users_actions_idx',
                'unique' => false,
            ])
            ->create();
        $this->table("users", [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'comment' => '',
                'row_format' => 'Dynamic',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'precision' => '10',
                'identity' => 'enable',
            ])
            ->addColumn('first_name', 'string', [
                'null' => false,
                'limit' => 255,
                'collation' => 'utf8_unicode_ci',
                'encoding' => 'utf8',
                'after' => 'id',
            ])
            ->addColumn('last_name', 'string', [
                'null' => false,
                'limit' => 255,
                'collation' => 'utf8_unicode_ci',
                'encoding' => 'utf8',
                'after' => 'first_name',
            ])
            ->addColumn('email', 'string', [
                'null' => true,
                'limit' => 255,
                'collation' => 'utf8_unicode_ci',
                'encoding' => 'utf8',
                'after' => 'last_name',
            ])
            ->addColumn('barcode', 'string', [
                'null' => false,
                'limit' => 255,
                'collation' => 'utf8_unicode_ci',
                'encoding' => 'utf8',
                'after' => 'email',
            ])
            ->addColumn('photo_file_name', 'string', [
                'null' => true,
                'limit' => 255,
                'collation' => 'utf8_unicode_ci',
                'encoding' => 'utf8',
                'after' => 'barcode',
            ])
            ->create();
    }
}
