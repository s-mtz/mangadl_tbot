<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class MessagesMigration extends AbstractMigration
{
    /**
     * [change description]
     *
     * 'chat_id' -> is the user telegram id
     * 'content' -> user message input content
     * 'type' -> type of message as crawler , manga, pdf-zip , garbage
     * 'time' -> to momment that the message was sent
     *
     * @return  void    [return description]
     */
    public function change(): void
    {
        $table = $this->table('messages');

        $table
            ->addColumn('chat_id', 'string')
            ->addColumn('content', 'text')
            ->addColumn('type', 'string')
            ->addColumn('time', 'biginteger')

            ->create();
    }
}
