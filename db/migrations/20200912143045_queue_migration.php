<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class QueueMigration extends AbstractMigration
{
    /**
     * [change description]
     *
     * 'chat_id' -> is the user telegram id
     * 'crawler' -> the asked manga website
     * 'manga' -> the asked manga name
     * 'chapter'-> the asked manga chapter
     * 'type' -> user type as normal or permium
     * 'time' -> the momment that the request was sent
     * 'status' -> status of request which is devided to pending , procecing , finished and error
     *
     * @return  void    [return description]
     */
    public function change(): void
    {
        $table = $this->table('queue');

        $table
            ->addColumn('chat_id', 'string')
            ->addColumn('crawler', 'text')
            ->addColumn('manga', 'text')
            ->addColumn('chapter', 'integer')
            ->addColumn('type', 'string')
            ->addColumn('time', 'biginteger')
            ->addColumn('status', 'text')

            ->create();
    }
}
