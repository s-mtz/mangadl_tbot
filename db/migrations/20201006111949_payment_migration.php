<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PaymentMigration extends AbstractMigration
{
    /**
     * [change description]
     *
     * @return  void    [return description]
     */
    public function change(): void
    {
        $table = $this->table('payment');

        $table
            ->addColumn('chat_id', 'string')
            ->addColumn('limit', 'integer')
            ->addColumn('price', 'integer')
            ->addColumn('currency', 'integer')
            ->addColumn('type', 'text')
            ->addColumn('status', 'text')
            ->addColumn('time', 'biginteger')

            ->create();
    }
}
