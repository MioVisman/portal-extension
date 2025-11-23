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

        $lctns = [];

        foreach ($this->c->portalPanels->displayPanels($this) as $panel) {
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
