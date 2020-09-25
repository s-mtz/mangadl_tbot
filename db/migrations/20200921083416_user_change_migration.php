<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UserChangeMigration extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('user');

        $table->removeColumn('type', 'string')->update();
    }
}
