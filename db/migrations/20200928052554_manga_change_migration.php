<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class MangaChangeMigration extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('manga');

        $table
            ->addColumn('pdf_id', 'string')
            ->renameColumn('dir', 'zip_id')
            ->update();
    }
}
