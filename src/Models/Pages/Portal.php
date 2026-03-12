<?php
/**
 * This file is part of the ForkBB <https://forkbb.ru, https://github.com/forkbb>.
 *
 * @copyright (c) Visman <mio.visman@yandex.ru, https://github.com/MioVisman>
 * @license   The MIT License (MIT)
 */

declare(strict_types=1);

namespace MioVisman\PortalExtension\Models\Pages;

use ForkBB\Models\Page;
use ForkBB\Models\Post\Post;
use ForkBB\Models\Topic\Topic;
use function \ForkBB\__;

class Portal extends Page
{
    /**
     * Перенаправление на главную страницу форума
     */
    public function toIndex(): Page
    {
        $this->c->curReqVisible = 0;

        return $this->c->Redirect->page('Portal');
    }

    /**
     * Подготовка данных для шаблона
     */
    public function view(): Page
    {
        $this->addTplDir();
        $this->c->Lang->load('portal', '', $this->c->DIR_PORTAL_EXT . '/lang');

        $this->fIndex         = 'portal';
        $this->identifier     = 'portal';
        $this->nameTpl        = 'portal/index';
        $this->onlinePos      = 'portal';
        $this->onlineDetail   = true;
        $this->onlineFilter   = false;
        $this->canonical      = $this->c->Router->link('Portal');
        $this->removeFWithNav = true;

        $panels = $this->c->portalPanels->displayPanels($this);
        $lctns  = [];
        $pids   = [];
        $tids   = [];
        $uids   = [];

        foreach ($panels as $panel) {
            $ids = $panel->prepareForHTML();

            if (! empty($ids['posts'])) {
                foreach ($ids['posts'] as $i) {
                    $pids[$i] = true;
                }
            }

            if (! empty($ids['topics'])) {
                foreach ($ids['topics'] as $i) {
                    $tids[$i] = true;
                }
            }
        }

        if (! empty($pids)) {
            $posts = $this->c->posts->loadByIds(\array_keys($pids));

            foreach ($posts as $post) {
                if ($post instanceof Post) {
                    $uids[$post->poster_id] = true;
                }
            }
        }

        if (! empty($tids)) {
            $topics = $this->c->topics->loadByIds(\array_keys($tids));

            foreach ($topics as $topic) {
                if ($topic instanceof Topic) {
                    $uids[$topic->poster_id] = true;
                }
            }
        }

        if (! empty($uids)) {
            $this->c->users->loadByIds(\array_keys($uids));
        }

        foreach ($panels as $panel) {
            $panel->createHTML();

            $lctns[$panel->location][] = $panel;
        }

        $this->locations = $lctns;

        return $this;
    }

    protected function addTplDir(): void
    {
        $this->c->View->addTplDir($this->c->DIR_PORTAL_EXT . '/templates', 9);
    }
}
