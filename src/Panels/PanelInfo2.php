<?php
/**
 * This file is part of the ForkBB <https://forkbb.org, https://github.com/forkbb>.
 *
 * @copyright (c) Visman <mio.visman@yandex.ru, https://github.com/MioVisman>
 * @license   The MIT License (MIT)
 */

declare(strict_types=1);

namespace MioVisman\PortalExtension\Panels;

use ForkBB\Core\Container;
use MioVisman\PortalExtension\Models\PortalPanel\Panel;
use PDO;

class PanelInfo2
{
    public function __construct(protected Container $c)
    {
    }

    public function prepare(Panel $panel): array
    {
        return [];
    }

    public function html(Panel $panel): string
    {
        $this->c->Lang->load('index');

        return $this->c->View->fetch(
            'portal/panels/info2',
            [
                'panel'     => $panel,
                'user'      => $this->c->user,
                'userRules' => $this->c->userRules,
                'stats'     => $this->c->stats,
                'online'    => $this->c->Online->calc($panel->page)->info(),
            ]
        );
    }
}
