<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class MangaMigration extends AbstractMigration
{
    /**
     * [change description]
     *
     * 'dir' -> directory of where the manga is saved
     * 'crawler' -> the existing mangas website in storage
     * 'manga' -> the existing manga name in storage
     * 'chapter'-> the existing manga chapters in storage
     * 'time' -> the momment manga was added to storage as pdf and zip
     *
     * @return  void    [return description]
     */
    public function change(): void
    {
        $table = $this->table('manga');

        $table
            ->addColumn('dir', 'text')
            ->addColumn('crawler', 'text')
            ->addColumn('manga', 'text')
            ->addColumn('chapter', 'integer')
            ->addColumn('time', 'biginteger')

            ->create();
    }
}
