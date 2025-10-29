<?php
/**
 * This file is part of the ForkBB <https://forkbb.ru, https://github.com/forkbb>.
 *
 * @copyright (c) Visman <mio.visman@yandex.ru, https://github.com/MioVisman>
 * @license   The MIT License (MIT)
 */

declare(strict_types=1);

namespace MioVisman\PortalExtension\Models\PortalPanel;

use ForkBB\Models\Action;
use MioVisman\PortalExtension\Models\PortalPanel\Panel;
use RuntimeException;

class Save extends Action
{
    /**
     * Обновляет раздел в БД
     */
    public function update(Panel $panel): Panel
    {
        if ($panel->id < 1) {
            throw new RuntimeException('The model does not have ID');
        }

        $modified = $panel->getModified();

        if (empty($modified)) {
            return $panel;
        }

        $values = $panel->getModelAttrs();
        $fields = $this->c->dbMap->portal_panels;
        $set = $vars = [];

        foreach ($modified as $name) {
            if (! isset($fields[$name])) {
                continue;
            }

            $vars[] = $values[$name];
            $set[]  = $name . '=?' . $fields[$name];
        }

        if (empty($set)) {
            return $panel;
        }

        $vars[] = $panel->id;
        $query = 'UPDATE ::portal_panels
            SET ' . \implode(', ', $set) . ' WHERE id=?i';

        $this->c->DB->exec($query, $vars);
        $panel->resModified();

        return $panel;
    }

    /**
     * Добавляет новый раздел в БД
     */
    public function insert(Panel $panel): int
    {
        if (null !== $panel->id) {
            throw new RuntimeException('The model has ID');
        }

        $attrs  = $panel->getModelAttrs();
        $fields = $this->c->dbMap->portal_panels;
        $set = $set2 = $vars = [];

        foreach ($attrs as $key => $value) {
            if (! isset($fields[$key])) {
                continue;
            }

            $vars[] = $value;
            $set[]  = $key;
            $set2[] = '?' . $fields[$key];
        }

        if (empty($set)) {
            throw new RuntimeException('The model is empty');
        }

        $query = 'INSERT INTO ::portal_panels (' . \implode(', ', $set) . ')
            VALUES (' . \implode(', ', $set2) . ')';

        $this->c->DB->exec($query, $vars);

        $panel->id = (int) $this->c->DB->lastInsertId();

        $panel->resModified();

        return $panel->id;
    }
}
