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

class Listener1 extends EventListener
{
    protected array $eventList = [
        'bootstrap:start'                         => 'start',
        'Controllers\Routing:routing:beforeRoute' => 'beforeRoute',
        'Models\Page:boardNavigation:after'       => 'boardNavigationAfter',
        'Models\Page:prepare:after'               => 'prepareAfter',
        'Models\Page:crumbs:after'                => 'crumbsAfter',
    ];

    /**
     * Добавление данных в Container
     */
    protected function start(Event $event): bool
    {
        $this->c->DIR_PORTAL_EXT = \realpath(__DIR__ . '/../..');

        return true;
    }

    /**
     * Добавление данных в Container при необходимости
     * Изменяет ссылки на главную страницу форума
     * Добавляет ссылки на страницы портала
     */
    protected function beforeRoute(Event $event): bool
    {
        if (false === $this->c->isInit('DIR_PORTAL_EXT')) {
            $this->c->DIR_PORTAL_EXT = \realpath(__DIR__ . '/../..');
        }

        if (1 === $this->c->user->g_read_board) {
            // изменить старые ссылки
            $event->router->add(
                $event->router::GET,
                '/forums',
                'Index:view',
                'Index'
            );
            $event->router->add(
                $event->router::GET,
                '/index.php',
                'Portal:toIndex'
            );
            $event->router->add(
                $event->router::GET,
                '/index.html',
                'Portal:toIndex'
            );
            // добавить новые ссылки
            $event->router->add(
                $event->router::GET,
                '/',
                'Portal:view',
                'Portal'
            );

            if ($this->c->user->isAdmin) {
                $event->router->add(
                    $event->router::DUO,
                    '/admin/portal',
                    'AdminPortal:view',
                    'AdminPortal'
                );
                $event->router->add(
                    $event->router::DUO,
                    '/admin/portal/new_panel',
                    'AdminPortal:editPanel',
                    'AdminPortalPanelNew'
                );
                $event->router->add(
                    $event->router::DUO,
                    '/admin/portal/edit_panel/{id|i:[1-9]\d*}',
                    'AdminPortal:editPanel',
                    'AdminPortalPanelEdit'
                );
                $event->router->add(
                    $event->router::DUO,
                    '/admin/portal/delete_panel/{id|i:[1-9]\d*}',
                    'AdminPortal:deletePanel',
                    'AdminPortalPanelDelete'
                );

            }
        }

        return true;
    }

    /**
     * Добавляет пункт Portal в начало главного меню
     */
    protected function boardNavigationAfter(Event $event): bool
    {
        $event->navGen = \array_merge([
            'portal' => [
                $this->c->Router->link('Portal'),
                'Portal',
                'Portal',
            ]
        ], $event->navGen);

        return true;
    }

    /**
     * Добавляет ссылку на стиль для портала
     * Сначала ищет файл в текущем стиле пользователя
     * Иначе подключает файл из расширение через симлинк (симлинк задан в composer.json для расширения)
     */
    protected function prepareAfter(Event $event): bool
    {
        $this->c->Lang->load('portal', '', $this->c->DIR_PORTAL_EXT . '/lang');

        $event->page->pageHeader('portalStyle', 'link', 9000, [
            'rel'  => 'stylesheet',
            'type' => 'text/css',
            'href' => $event->page->publicLink("/style/{$this->c->user->style}/portal.css", true)
                ?: $event->page->publicLink('/style/portal-extension/portal.css'),
        ]);

        if (! empty($this->c->config->i_portal_mmpos)) {
            $event->page->removeFWithNav = 1 === $this->c->config->i_portal_mmpos ? false : true;
        }

        return true;
    }

    /**
     * Добавляет в начало хлебных крошек пункт указывающий на портал
     */
    protected function crumbsAfter(Event $event): bool
    {
        $event->list[] = [$this->c->Router->link('Portal'), 'Portal', null, 'portal', $event->active, $event->ext];

        return true;
    }
}
