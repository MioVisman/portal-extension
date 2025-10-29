<?php
/**
 * This file is part of the ForkBB <https://forkbb.ru, https://github.com/forkbb>.
 *
 * @copyright (c) Visman <mio.visman@yandex.ru, https://github.com/MioVisman>
 * @license   The MIT License (MIT)
 */

declare(strict_types=1);

namespace MioVisman\PortalExtension\Listeners;

use ForkBB\Core\Event;
use ForkBB\Core\EventListener;

class Listener2 extends EventListener
{
    protected array $eventList = [
        'Pages\Admin:aNavigation:after' => 'aNavigationAfter',
    ];

    /**
     * Добавляет в меню админки пункт для администрирование портала
     */
    protected function aNavigationAfter(Event $event): bool
    {
        if ($this->c->user->isAdmin) {
            $event->nav['portal'] = [$this->c->Router->link('AdminPortal'), 'Portal'];
        }

        return true;
    }
}
