<?php
/**
 * This file is part of the ForkBB <https://forkbb.ru, https://github.com/forkbb>.
 *
 * @copyright (c) Visman <mio.visman@yandex.ru, https://github.com/MioVisman>
 * @license   The MIT License (MIT)
 */

declare(strict_types=1);

namespace MioVisman\PortalExtension\Actions;

use ForkBB\Core\Container;
use ForkBB\Models\Extension\AbstractActions;

class Actions extends AbstractActions
{
    public function install(): bool
    {
        //portal_panels
        $schema = [
            'FIELDS' => [
                'id'          => ['SERIAL', false],
                'enabled'     => ['TINYINT(1)', false, 1],
                'location'    => ['TINYINT UNSIGNED', false, 0],
                'position'    => ['INT(10) UNSIGNED', false, 0],
                'name'        => ['VARCHAR(255)', false, ''],
                'template'    => ['VARCHAR(255)', false, ''],
                'content'     => ['MEDIUMTEXT', false],
            ],
            'PRIMARY KEY' => ['id'],
        ];
        $this->c->DB->createTable('::portal_panels', $schema);

        //portal_pages
        $schema = [
            'FIELDS' => [
                'id'          => ['SERIAL', false],
                'enabled'     => ['TINYINT(1)', false, 1],
                'position'    => ['INT(10) UNSIGNED', false, 0],
                'title'       => ['VARCHAR(255)', false, ''],
                'content'     => ['MEDIUMTEXT', false],
            ],
            'PRIMARY KEY' => ['id'],
        ];
        $this->c->DB->createTable('::portal_pages', $schema);

        \error_log('install');
        return true;
    }

    public function uninstall(): bool
    {
        \error_log('uninstall');
        return true;
    }

    public function updown(): bool
    {
        \error_log('updown');
        return true;
    }

    public function enable(): bool
    {
        \error_log('enable');
        return true;
    }

    public function disable(): bool
    {
        \error_log('disable');
        return true;
    }
}
