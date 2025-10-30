<?php
/**
 * This file is part of the ForkBB <https://forkbb.ru, https://github.com/forkbb>.
 *
 * @copyright (c) Visman <mio.visman@yandex.ru, https://github.com/MioVisman>
 * @license   The MIT License (MIT)
 */

declare(strict_types=1);

namespace MioVisman\PortalExtension\Models\Pages\Admin;

use ForkBB\Core\Validator;
use ForkBB\Models\Page;
use ForkBB\Models\Pages\Admin;
use MioVisman\PortalExtension\Models\PortalPanel\Panel;
use function \ForkBB\__;

class Portal extends Admin
{
    protected function loadLang(): void
    {
        $this->c->Lang->load('validator');
        $this->c->Lang->load('admin_portal', '', $this->c->DIR_PORTAL_EXT . '/lang');
    }

    /**
     * Просмотр, редактирвоание и добавление панелей
     */
    public function view(array $args, string $method): Page
    {
        $this->loadLang();

        if ('POST' === $method) {
            $v = $this->c->Validator->reset()
                ->addRules([
                    'token'           => 'token:AdminPortal',
                    'form'            => 'required|array',
                    'form.*.position' => 'required|integer|min:0|max:9999999999',
                ])->addAliases([
                ])->addArguments([
                ])->addMessages([
                ]);

            if ($v->validation($_POST)) {
                $panels = $this->c->portalPanels->loadAll();

                foreach ($v->form as $key => $row) {
                    $panels[$key]->position = $row['position'];

                    $this->c->portalPanels->save($panels[$key]);
                }

                $this->c->portalPanels->reset();

                return $this->c->Redirect->page('AdminPortal')->message('Panels updated redirect', FORK_MESS_SUCC);
            }

            $this->fIswev  = $v->getErrors();
        }

        $this->nameTpl   = 'admin/form';
        $this->aIndex    = 'portal';
        $this->form      = $this->formView();
        $this->classForm = ['editforums', 'inline'];
        $this->titleForm = 'Portal';

        return $this;
    }

    /**
     * Подготавливает массив данных для формы
     */
    protected function formView(): array
    {
        $form = [
            'action' => $this->c->Router->link('AdminPortal'),
            'hidden' => [
                'token' => $this->c->Csrf->create('AdminPortal'),
            ],
            'sets'   => [],
            'btns'   => [
                'new' => [
                    'type'  => 'btn',
                    'value' => __('New panel'),
                    'href'  => $this->c->Router->link('AdminPortalPanelNew'),
                ],
                'update' => [
                    'type'  => 'submit',
                    'value' => __('Update positions'),
                ],
            ],
        ];

        $sides = [
            0 => 'Center Top',
            1 => 'Center Bottom',
            2 => 'Starting side',
            3 => 'Ending side',
        ];
        $panels = $this->c->portalPanels->loadAll();
        $side   = null;

        foreach ($panels as $panel) {
            if ($side !== $panel->location) {
                $side                             = $panel->location;
                $form['sets']["side{$side}-info"] = [
                    'inform' => [
                        [
                            'message' => $sides[$side],
                        ],
                    ],
                ];
            }

            $fields = [];
            $fields["name-btn{$panel->id}"] = [
                'class'   => ['name', 'forum'],
                'type'    => 'btn',
                'value'   => $panel->name,
                'caption' => 'Panel label',
                'href'    => $panel->linkEdit,
            ];
            $fields["form[{$panel->id}][position]"] = [
                'class'   => ['position', 'forum'],
                'type'    => 'number',
                'min'     => '0',
                'max'     => '9999999999',
                'value'   => $panel->position,
                'caption' => 'Position label',
            ];
            $fields["delete-btn{$panel->id}"] = [
                'class'    => ['delete', 'forum'],
                'type'     => 'btn',
                'value'    => '❌',
                'caption'  => 'Delete',
                'title'    => __('Delete'),
                'href'     => $panel->linkDelete,
            ];
            $form['sets']["panel{$panel->id}"] = [
                'class'  => $panel->enabled ? ['forum', 'inline'] : ['forum', 'inline', 'disabled'],
                'legend' => __($sides[$side]) . ' / ' . $panel->name,
                'fields' => $fields,
            ];
        }

        return $form;
    }

    /**
     * Дополнительная проверка content
     */
    public function vCheckContent(Validator $v, string $content): string
    {
        if (
            'empty' === $v->template
            && '' === $content
        ) {
            $v->addError('No template - content is required');
        }

        return $content;
    }

    /**
     * Редактирование панели
     * Создание новой панели
     */
    public function editPanel(array $args, string $method): Page
    {
        $panel = empty($args['id']) ? $this->c->portalPanels->create() : $this->c->portalPanels->load($args['id']);

        if (! $panel instanceof Panel) {
            return $this->c->Message->message('Bad request');
        }

        $this->loadLang();

        if (empty($args['id'])) {
            $marker          = 'AdminPortalPanelNew';
            $this->aCrumbs[] = [$this->c->Router->link($marker), 'Add panel head'];
            $this->titleForm = 'Add panel head';
            $this->classForm = ['createforum'];

        } else {
            $marker          = 'AdminPortalPanelEdit';
            $this->aCrumbs[] = [$this->c->Router->link($marker, $args), 'Edit panel head'];
            $this->aCrumbs[] = [null, ['"%s"', $panel->name]];
            $this->titleForm = 'Edit panel head';
            $this->classForm = ['editforum'];
        }

        if ('POST' === $method) {
            $v = $this->c->Validator->reset()
                ->addValidators([
                    'check_content' => [$this, 'vCheckContent'],
                ])->addRules([
                    'token'    => 'token:' . $marker,
                    'name'     => 'required|string:trim|max:255',
                    'enabled'  => 'required|integer|in:0,1',
                    'location' => 'required|integer|in:0,1,2,3',
                    'template' => 'required|string|in:' . \implode(',', \array_keys($panel->templates)),
                    'content'  => 'string:trim|max:' . $this->c->MAX_POST_SIZE . '|html|check_content',
                ])->addAliases([
                    'name'     => 'Panel name label',
                    'enabled'  => 'Panel enabled label',
                    'location' => 'Panel location label',
                    'template' => 'Panel template label',
                    'content'  => 'Panel content label',
                ])->addArguments([
                    'token' => $args,
                ]);

            $valid = $v->validation($_POST);

            $panel->name     = $v->name;
            $panel->enabled  = $v->enabled;
            $panel->location = $v->location;
            $panel->template = $v->template;
            $panel->content  = $v->content;

            if ($valid) {
                if (empty($args['id'])) {
                    $message         = 'Panel added redirect';
                    $panel->position = $this->panelPos($panel);

                } else {
                    $message = 'Panel updated redirect';
                }

                $this->c->portalPanels->save($panel);
                $this->c->portalPanels->reset();

                return $this->c->Redirect->url($panel->linkEdit)->message($message, FORK_MESS_SUCC);
            }

            $this->fIswev = $v->getErrors();
        }

        $this->nameTpl = 'admin/form';
        $this->aIndex  = 'portal';
        $this->form    = $this->formEditPanel($args, $panel, $marker);

        return $this;
    }

    /**
     * Подготавливает массив данных для формы
     */
    protected function formEditPanel(array $args, Panel $panel, string $marker): array
    {
        $form = [
            'action' => $this->c->Router->link($marker, $args),
            'hidden' => [
                'token' => $this->c->Csrf->create($marker, $args),
            ],
            'sets'   => [],
            'btns'   => [
                'submit' => [
                    'type'  => 'submit',
                    'value' =>  __(empty($panel->id) ? 'Add' : 'Update'),
                ],
            ],
        ];

        $form['sets']['panel'] = [
            'fields' => [
                'name' => [
                    'type'      => 'text',
                    'maxlength' => '255',
                    'value'     => $panel->name,
                    'caption'   => 'Panel name label',
                    'required'  => true,
                ],
                'enabled' => [
                    'type'    => 'radio',
                    'value'   => $panel->enabled,
                    'values'  => [1 => __('Yes'), 0 => __('No')],
                    'caption' => 'Panel enabled label',
                ],
                'location' => [
                    'type'    => 'select',
                    'options' => [
                        0 => __('Center Top'),
                        1 => __('Center Bottom'),
                        2 => __('Starting side'),
                        3 => __('Ending side'),
                    ],
                    'value'   => $panel->location,
                    'caption' => 'Panel location label',
                ],
                'template' => [
                    'type'    => 'select',
                    'options' => \array_map('\\ForkBB\\__', $panel->templates),
                    'value'   => $panel->template,
                    'caption' => 'Panel template label',
                ],
                'content' => [
                    'type'      => 'textarea',
                    'value'     => $panel->content,
                    'maxlength' => $this->c->MAX_POST_SIZE,
                    'caption'   => 'Panel content label',
                    'help'      => 'Panel content help',
                ],
            ],
        ];

        return $form;
    }

    /**
     * Удаление панели
     */
    public function deletePanel(array $args, string $method): Page
    {
        $panel = $this->c->portalPanels->load($args['id']);

        if (! $panel instanceof Panel) {
            return $this->c->Message->message('Bad request');
        }

        $this->loadLang();

        if ('POST' === $method) {
            $v = $this->c->Validator->reset()
                ->addRules([
                    'token'     => 'token:AdminPortalPanelDelete',
                    'confirm'   => 'checkbox',
                    'delete'    => 'required|string',
                ])->addAliases([
                ])->addArguments([
                    'token' => $args,
                ]);

            if (
                ! $v->validation($_POST)
                || '1' !== $v->confirm
            ) {
                return $this->c->Redirect->page('AdminPortal')->message('No confirm redirect', FORK_MESS_WARN);
            }

            $this->c->portalPanels->delete($panel);
            $this->c->portalPanels->reset();

            return $this->c->Redirect->page('AdminPortal')->message('Panel deleted redirect', FORK_MESS_SUCC);
        }

        $this->nameTpl   = 'admin/form';
        $this->aIndex    = 'portal';
        $this->aCrumbs[] = [$panel->linkDelete, 'Delete panel head'];
        $this->aCrumbs[] = [null, ['"%s"', $panel->name]];
        $this->form      = $this->formDeletePanel($args, $panel);
        $this->classForm = ['deleteforum'];
        $this->titleForm = 'Delete panel head';

        return $this;
    }

    /**
     * Подготавливает массив данных для формы
     */
    protected function formDeletePanel(array $args, Panel $panel): array
    {
        return [
            'action' => $this->c->Router->link('AdminPortalPanelDelete', $args),
            'hidden' => [
                'token' => $this->c->Csrf->create('AdminPortalPanelDelete', $args),
            ],
            'sets'   => [
                'confirm' => [
                    'fields' => [
                        'confirm' => [
                            'caption' => 'Confirm delete',
                            'type'    => 'checkbox',
                            'label'   => ['I want to delete panel %s', $panel->name],
                            'checked' => false,
                        ],
                    ],
                ],
            ],
            'btns'   => [
                'delete' => [
                    'type'  => 'submit',
                    'value' => __('Delete'),
                ],
                'cancel' => [
                    'type'  => 'btn',
                    'value' => __('Cancel'),
                    'href'  => $this->c->Router->link('AdminPortal'),
                ],
            ],
        ];
    }

    /**
     * Вычисление позиции для (новой) панели
     */
    protected function panelPos(Panel $panel): int
    {
        if (\is_int($panel->position)) {
            return $panel->position;
        }

        $panels = $this->c->portalPanels->loadAll();
        $max    = 0;

        foreach ($panels as $cur) {
            if (
                $cur->location === $panel->location
                && $cur->position > $max
            ) {
                $max = $cur->position;
            }
        }

        return $max + 1;
    }
}
