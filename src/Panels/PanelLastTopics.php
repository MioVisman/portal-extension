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

class PanelLastTopics
{
    public function __construct(protected Container $c)
    {
    }

    protected int $limit = 5;
    protected array $forums = [];
    protected int $preview = 1000;

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
                case 'forums':
                    $forums = \array_map('\\intval', \array_map('\\trim', \explode(',', $keyval[1])));
                    $forums = \array_filter($forums, function ($val) {return $val > 0;});

                    if (! empty($forums)) {
                        $this->forums = $forums;
                    }

                    break;
                case 'preview':
                    $preview = \intval($keyval[1]);

                    if ($preview > 500) {
                        $this->preview = $preview;
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

        $posts     = [];
        $userIds   = [];
        $forumsIds = \array_keys($this->getForums());

        if (
            empty($forumsIds)
            || empty($this->forums)
        ) {
            $this->forums = $forumsIds;
        } else {
            $this->forums = \array_intersect($this->forums, $forumsIds);
        }

        if (! empty($this->forums)) {
            $vars = [
                ':limit'  => $this->limit,
                ':forums' => $this->forums,
            ];
            $query = 'SELECT t.first_post_id
                FROM ::topics AS t
                WHERE t.forum_id IN (?ai:forums)
                ORDER BY t.posted DESC
                LIMIT ?i:limit';

            $idsList = $this->c->DB->query($query, $vars)->fetchAll(PDO::FETCH_COLUMN);
            $posts   = $this->c->posts->loadByIds($idsList);
        }

        foreach ($posts as $post) {
            if (\mb_strlen($post->message, 'UTF-8') > $this->preview) {
                \preg_match('%^.{0,' . $this->preview . '}(?=\s)%usD', $post->message, $matches);

                if ('' === $matches[0]) {
                    $post->__message = \mb_substr($post->message, 0, $this->preview, 'UTF-8') . ' ...';
                } else {
                    $post->__message = $matches[0] . ' ...';
                }

                $post->__needReadMore = true;
            }

            $userIds[$post->poster_id] = $post->poster_id;
        }

        if (! empty($userIds)) {
            $this->c->users->loadByIds($userIds);
        }

        $this->c->Lang->load('topic');

        return $this->c->View->fetch(
            'portal/panels/last_topics',
            [
                'panel'     => $panel,
                'posts'     => $posts,
                'userRules' => $this->c->userRules,
            ]
        );
    }
}
