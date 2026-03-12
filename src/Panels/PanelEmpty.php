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
use ForkBB\Models\Topic\Topic;
use ForkBB\Models\User\User;
use MioVisman\PortalExtension\Models\PortalPanel\Panel;

class PanelEmpty
{
    public function __construct(protected Container $c)
    {
    }

    protected int $tid = 0;

    public function prepare(Panel $panel): array
    {
        if (\preg_match('%^<!--\s+topic:([1-9]\d*)\s+-->%s', $panel->content, $matches)) {
            $this->tid        = (int) $matches[1];
            $panel->__content = \trim(\substr($panel->content, \strlen($matches[0])));

            return ['topics' => [$this->tid]];

        } else {
            return [];
        }
    }

    public function html(Panel $panel): string
    {
        $topic = null;
        $user  = null;

        if ($this->tid > 0) {
            $topic = $this->c->topics->get($this->tid);

            if ($topic instanceof Topic) {
                if (
                    $topic->poster_id < 1
                    || ! ($user = $this->c->users->get($topic->poster_id)) instanceof User
                ) {
                    $user = $this->c->users->guest([
                        'username' => $topic->poster,
                    ]);
                }
            }
        }

        return $this->c->View->fetch(
            'portal/panels/empty',
            [
                'panel'     => $panel,
                'user'      => $this->c->user,
                'userRules' => $this->c->userRules,
                'topic'     => $topic,
                'author'    => $user,
            ]
        );
    }
}
