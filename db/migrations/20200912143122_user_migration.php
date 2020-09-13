<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UserMigration extends AbstractMigration
{
    /**
     * [change description]
     *
     * 'chat_id' -> is the user telegram id
     * 'type' -> user type as normal or permium (level)
     * 'time' -> the momment user registred first
     *
     * @return  void    [return description]
     */
    public function change(): void
    {
        $table = $this->table('user');

        $table
            ->addColumn('chat_id', 'string')
            ->addColumn('type', 'string')
            ->addColumn('time', 'biginteger')

            ->create();
    }
}
