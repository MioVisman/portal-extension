<?php
/**
 * This file is part of the ForkBB <https://forkbb.ru, https://github.com/forkbb>.
 *
 * @copyright (c) Visman <mio.visman@yandex.ru, https://github.com/MioVisman>
 * @license   The MIT License (MIT)
 */

declare(strict_types=1);

namespace MioVisman\PortalExtension\Models\PortalPanel;

use ForkBB\Models\DataModel;

class Panel extends DataModel
{
    /**
     * Ключ модели для контейнера
     */
    protected string $cKey = 'PPanel';

    protected function getlinkEdit(): string
    {
        return $this->c->Router->link(
            'AdminPortalPanelEdit',
            [
                'id' => $this->id,
            ]
        );
    }

    protected function getlinkDelete(): string
    {
        return $this->c->Router->link(
            'AdminPortalPanelDelete',
            [
                'id' => $this->id,
            ]
        );
    }

    public function createHTML(): void
    {
        $class      = '\\MioVisman\\PortalExtension\\Panels\\Panel' . \ucfirst($this->template);
        $model      = new $class($this->c);
        $this->html = \trim($model->html($this), "\n\r");
    }

    protected function gettemplates(): array
    {
        return [
            'empty'       => 'Empty template',
            'search'      => 'Search template',
            'lastTopics'  => 'Last topics template',
            'recentPosts' => 'Recent posts template',
            'info'        => 'Board info template',
        ];
    }
}
