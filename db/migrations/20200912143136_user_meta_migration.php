<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UserMetaMigration extends AbstractMigration
{
    /**
     * [change description]
     *
     * @return  void    [return description]
     */
    public function change(): void
    {
        $table = $this->table('user_meta');

        $table
            ->addColumn('chat_id', 'string')
            ->addColumn('key', 'string')
            ->addColumn('value', 'string')

            ->create();
    }
}
