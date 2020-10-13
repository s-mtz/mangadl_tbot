<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UpdatePaymentMigration extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('payment');

        $table
            ->addColumn('token', 'text')
            ->changeColumn('currency', 'string')
            ->update();
    }
}
