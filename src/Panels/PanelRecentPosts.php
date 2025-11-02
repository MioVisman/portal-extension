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
use ForkBB\Models\Forum\Forum;
use MioVisman\PortalExtension\Models\PortalPanel\Panel;
use PDO;

class PanelRecentPosts
{
    public function __construct(protected Container $c)
    {
    }

    protected int $limit = 10;

    protected function initSettings(string $content): void
    {
        foreach (\explode("\n", $content) as $line) {
            $keyval = \explode(":", $line);

            if (2 !== \count($keyval)) {
                continue;
            }

            $keyval = \array_map('\\trim', $keyval);

            switch ($keyval[0]) {
                case 'limit':
                    $limit = \intval($keyval[1]);

                    if ($limit > 0) {
                        $this->limit = $limit;
                    }

                    break;
            }
        }
    }

    /**
     * Возвращает список доступных разделов
     */
    protected function getForums(): array
    {
        $root = $this->c->forums->get(0);

        return $root instanceof Forum ? $root->descendants : [];
    }

    public function html(Panel $panel): string
    {
        $this->initSettings($panel->content);

        $topics    = [];
        $forumsIds = \array_keys($this->getForums());

        if (! empty($forumsIds)) {
            $vars = [
                ':limit'  => $this->limit,
                ':forums' => $forumsIds,
            ];
            $query = 'SELECT t.id
                FROM ::topics AS t
                WHERE t.forum_id IN (?ai:forums) AND t.moved_to=0
                ORDER BY t.last_post DESC
                LIMIT ?i:limit';

            $idsList = $this->c->DB->query($query, $vars)->fetchAll(PDO::FETCH_COLUMN);
            $topics  = $this->c->topics->loadByIds($idsList);
        }

        //$this->c->Lang->load('topic');

        return $this->c->View->fetch(
            'portal/panels/recent_posts',
            [
                'panel'  => $panel,
                'topics' => $topics,
                'user'   => $this->c->user,
            ]
        );
    }
}
