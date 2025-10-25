<?php

namespace Enraiged\Tables\Builders;

use Enraiged\Tables\Contracts\ProvidesTableQuery;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class TableBuilder
{
    use Traits\BuilderConstructor,
        Traits\EloquentBuilder,
        Traits\Exportable,
        Traits\HttpRequest,
        Traits\SecurityAssertions,
        Traits\TableActions,
        Traits\TableColumns,
        Traits\TableFilters;

    /**
     *  Return the data for the table request.
     *
     *  @return array
     */
    public function data(): array
    {
        $query = $this instanceof ProvidesTableQuery
            ? $this->query()
            : App::make($this->model)::query();

        $this->builder($query)
            ->sort()
            ->filter()
            ->search()
            ->paginate();

        $pagination = (object) $this->pagination();

        return [
            'records' => $this->records(),
            'pagination' => [
                'dir' => $this->request->get('dir'),
                'page' => $pagination->current_page,
                'rows' => $pagination->per_page,
                'sort' => $this->request->get('sort'),
                'total' => $pagination->total,
            ],
        ];
    }

    /**
     *  Initiate the table export process.
     *
     *  @return \Enraiged\Exports\Models\Export
     */
    public function export()
    {
        $exporter = $this->exporter();

        return $exporter->process();
    }

    /**
     *  Return the assembled table template.
     *
     *  @return array
     */
    public function template(): array
    {
        $identity = $this->get('id') ?? trim($this->prefix, '.').'index';

        $template = [
            'actions' => $this->tableActions(),
            'class' => $this->get('class') ?? str_replace('.', '-', $identity),
            'columns' => $this->tableColumns(),
            'empty' => $this->get('empty'),
            'fetch' => $this->get('fetch'),
            'id' => $identity,
            'exportable' => $this->exportableConfiguration(),
            'key' => $this->get('key'),
            'model' => Str::snake(class_basename($this->model)),
            'pagination' => $this->get('pagination'),
            'selectable' => $this->selectable,
            'state' => $this->get('state'),
        ];

        if ($this->filters) {
            $filters = $this->tableFilters();

            if (count($filters)) {
                $template['filters'] = $filters;
            }
        }

        return $template;
    }
}
