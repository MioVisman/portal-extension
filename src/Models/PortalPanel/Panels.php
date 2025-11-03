<?php
/**
 * This file is part of the ForkBB <https://forkbb.ru, https://github.com/forkbb>.
 *
 * @copyright (c) Visman <mio.visman@yandex.ru, https://github.com/MioVisman>
 * @license   The MIT License (MIT)
 */

declare(strict_types=1);

namespace MioVisman\PortalExtension\Models\PortalPanel;

use ForkBB\Models\Manager;
use ForkBB\Models\Page;
use MioVisman\PortalExtension\Models\PortalPanel\Panel;
use RuntimeException;

class Panels extends Manager
{
    const CACHE_KEY = 'display_panels';

    /**
     * Ключ модели для контейнера
     */
    protected string $cKey = 'PPanels';

    protected bool $allLoaded = false;

    /**
     * Создает новую модель
     */
    public function create(array $attrs = []): Panel
    {
        return $this->c->PortalPanelModel->setManager($this)->setModelAttrs($attrs);
    }

    /**
     * Загружает из БД все панели
     */
    public function loadAll(): array
    {
        if (true === $this->allLoaded) {
            return $this->repository; // ???? А если там не только объекты?
        }

        $query = 'SELECT pn.*
            FROM ::portal_panels AS pn
            ORDER BY pn.location, pn.position, pn.id';

        $stmt = $this->c->DB->query($query);

        $result = [];

        while ($cur = $stmt->fetch()) {
            $panel              = $this->create($cur);
            $result[$panel->id] = $panel;

            $this->set($panel->id, $panel);
        }

        $this->allLoaded = true;

        return $result;
    }

    public function load(int $id): ?Panel
    {
        $panel = $this->get($id);

        if ($panel instanceof Panel) {
            return $panel;

        } elseif (false === $panel) {
            return null;
        }

        $vars = [
            ':id' => $id,
        ];
        $query = 'SELECT pn.*
            FROM ::portal_panels AS pn
            WHERE pn.id=?i:id';

        $row = $this->c->DB->query($query, $vars)->fetch();

        if (empty($row)) {
            $panel = null;

            $this->set($id, false);

        } else {
            $panel = $this->create($row);

            $this->set($id, $panel);
        }

        return $panel;
    }

    public function save(Panel $panel): void
    {
        if (empty($panel->id)) {
            $this->Save->insert($panel);
            $this->set($panel->id, $panel);

        } else {
            $this->Save->update($panel);
        }
    }

    public function delete(Panel $panel): void
    {
        if ($panel->id < 1) {
            return;
        }

        $vars = [
            ':id' => $panel->id,
        ];
        $query = 'DELETE
            FROM ::portal_panels
            WHERE id=?i:id';

        $this->c->DB->exec($query, $vars);

        $this->allLoaded = false;
    }

    /**
     * Сбрасывает кеш
     */
    public function reset(): Panels
    {
        if (true !== $this->c->Cache->delete(self::CACHE_KEY)) {
            throw new RuntimeException('Unable to remove key from cache - ' . self::CACHE_KEY);
        }

        return $this;
    }

    public function displayPanels(Page $page): array
    {
        $panels = [];
        $data   = $this->c->Cache->get(self::CACHE_KEY);

        if (! \is_array($data)) {
            $query = 'SELECT pn.*
                FROM ::portal_panels AS pn
                WHERE pn.enabled=1
                ORDER BY pn.location, pn.position, pn.id';

            $data = $this->c->DB->query($query)->fetchAll();

            if (true !== $this->c->Cache->set(self::CACHE_KEY, $data)) {
                throw new RuntimeException('Unable to write value to cache - ' . self::CACHE_KEY);
            }
        }

        foreach ($data as $cur) {
            $cur['page']        = $page;
            $panel              = $this->create($cur);
            $panels[$panel->id] = $panel;
        }

        return $panels;
    }
}
