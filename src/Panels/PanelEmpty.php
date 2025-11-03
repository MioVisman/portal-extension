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

class PanelEmpty
{
    public function __construct(protected Container $c)
    {
    }

    public function html(Panel $panel): string
    {
        return $this->c->View->fetch(
            'portal/panels/empty',
            [
                'panel'     => $panel,
                'user'      => $this->c->user,
                'userRules' => $this->c->userRules,
            ]
        );
    }
}
