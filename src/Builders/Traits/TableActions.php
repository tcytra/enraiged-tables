<?php

namespace Enraiged\Tables\Builders\Traits;

use Enraiged\Contracts\ProvidesActions;

trait TableActions
{
    /** @var  array  The table actions. */
    protected array $actions;

    /**
     *  Return the table actions for the provided resource.
     *
     *  @return array
     */
    public function actions()
    {
        return $this->actions;
    }

    /**
     *  Return the table row actions for the provided resource.
     *
     *  @param  \Illuminate\Database\Eloquent\Model  $model
     *  @return array
     */
    public function actionsForRow($model): array
    {
        return $model->actions($this->actions)
            ->filter(fn ($action)
                => key_exists('type', $action) && $action['type'] === 'row')
            ->asRoutableActions($this->request, $model, $this->prefix)
            ->transform(fn ($action)
                => collect($action)
                    ->except(['secure', 'secureAll', 'secureAny'])
                    ->toArray())
            ->toArray();
    }

    /**
     *  Return the table global actions for the provided resource.
     *
     *  @return array
     */
    protected function tableActions(): array
    {
        $model = gettype($this->model) === 'string'
            ? new $this->model
            : $this->model;

        if ($model instanceof ProvidesActions && count($this->actions)) {
            $actions = $model->actions($this->actions)
                ->transform(fn ($action)
                    => [...$action, 'type' => key_exists('type', $action) ? $action['type'] : 'table'])
                ->filter(fn ($action)
                    => $action['type'] !== 'row')
                ->asRoutableActions($this->request, $model, $this->prefix)
                ->transform(fn ($action)
                    => collect($action)
                        ->except(['secure', 'secureAll', 'secureAny'])
                        ->toArray())
                ->toArray();

            return [
                ...$actions,
                ...collect($this->actions)
                    ->filter(fn ($action)
                        => key_exists('type', $action) && $action['type'] === 'row')
                    ->toArray()
            ];
        }

        return [];
    }

    /**
     *  Determine if this table has actions for rows.
     *
     *  @return bool
     */
    public function hasRowActions(): bool
    {
        return gettype($this->actions) === 'array'
            && collect($this->actions)
                ->filter(fn ($action) => $this->isRowAction($action))
                ->count()
            > 0;
    }

    /**
     *  Determine whether an action is required per row.
     *
     *  @param  $action
     *  @return bool
     */
    private function isRowAction($action): bool
    {
        if (key_exists('type', $action)) {
            if ((gettype($action['type']) === 'string' && $action['type'] === 'row')) {
                return true;
            }

            if (gettype($action['type']) === 'array' && in_array('row', $action['type'])) {
                return true;
            }
        }

        return false;
    }

    /**
     *  Remove an action from the table structure.
     *
     *  @param  string  $index
     *  @return self
     */
    public function removeAction($index)
    {
        $this->actions = collect($this->actions)
            ->except($index)
            ->toArray();

        return $this;
    }

    /**
     *  Remove an action from the table structure.
     *
     *  @param  string  $index
     *  @param  boolean|\Closure  $condition
     *  @return self
     */
    public function removeActionIf($index, $condition)
    {
        if ($condition instanceof \Closure) {
            $condition = $condition();
        }

        if ($condition) {
            $this->removeAction($index);
        }

        return $this;
    }
}
