<?php
/**
 * This file is part of the ForkBB <https://forkbb.ru, https://github.com/forkbb>.
 *
 * @copyright (c) Visman <mio.visman@yandex.ru, https://github.com/MioVisman>
 * @license   The MIT License (MIT)
 */

declare(strict_types=1);

namespace MioVisman\PortalExtension\Panels;

use ForkBB\Core\Container;
use MioVisman\PortalExtension\Models\PortalPanel\Panel;
use PDO;

class PanelInfo
{
    public function __construct(protected Container $c)
    {
    }

    public function html(Panel $panel): string
    {
        $this->c->Lang->load('index');

        // крайний пользователь // ???? может в stats переместить?
        // копия из Page/Index
        $this->c->stats->userLast = [
            'name' => $this->c->stats->userLast['username'],
            'link' => $this->c->userRules->viewUsers
                ? $this->c->Router->link(
                    'User',
                    [
                        'id'   => $this->c->stats->userLast['id'],
                        'name' => $this->c->Func->friendly($this->c->stats->userLast['username']),
                    ]
                )
                : null,
        ];

        return $this->c->View->fetch(
            'portal/panels/info',
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
