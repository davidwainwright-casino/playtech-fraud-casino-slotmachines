<?php

namespace Laravel\Nova\Actions;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Rap2hpoutre\FastExcel\FastExcel;

class ExportAsCsv extends Action
{
    /**
     * The XHR response type on executing the action.
     *
     * @var string
     */
    public $responseType = 'blob';

    /**
     * All of the defined action fields.
     *
     * @var \Illuminate\Support\Collection
     */
    public $actionFields;

    /**
     * The custom query callback.
     *
     * @var (\Closure(\Illuminate\Database\Eloquent\Builder, \Laravel\Nova\Fields\ActionFields):\Illuminate\Database\Eloquent\Builder)|null
     */
    public $withQueryCallback;

    /**
     * The custom field callback.
     *
     * @var (\Closure(\Laravel\Nova\Http\Requests\NovaRequest):array<int, \Laravel\Nova\Fields\Field>)|null
     */
    public $withFieldsCallback;

    /**
     * The custom format callback.
     *
     * @var (\Closure(\Illuminate\Database\Eloquent\Model):array<string, mixed>)|null
     */
    public $withFormatCallback;

    /**
     * Indicates action events should be logged for models.
     *
     * @var bool
     */
    public $withoutActionEvents = true;

    /**
     * Construct a new action instance.
     *
     * @param  string|null  $name
     * @return void
     */
    public function __construct($name = null)
    {
        $this->name = $name;
        $this->actionFields = collect();
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        if ($this->withFieldsCallback instanceof Closure) {
            $this->actionFields = $this->actionFields->merge(call_user_func($this->withFieldsCallback, $request));
        }

        return $this->actionFields->all();
    }

    /**
     * Perform the action request using custom dispatch handler.
     *
     * @param  \Laravel\Nova\Http\Requests\ActionRequest  $request
     * @param  \Laravel\Nova\Actions\Response  $response
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @return \Laravel\Nova\Actions\Response
     */
    protected function dispatchRequestUsing(ActionRequest $request, Response $response, ActionFields $fields)
    {
        $this->then(function ($results) {
            return $results->first();
        });

        $query = $request->toSelectedResourceQuery();

        $query->when($this->withQueryCallback instanceof Closure, function ($query) use ($fields) {
            return call_user_func($this->withQueryCallback, $query, $fields);
        });

        $eloquentGenerator = function () use ($query) {
            foreach ($query->cursor() as $model) {
                yield $model;
            }
        };

        $filename = $fields->get('filename') ?? sprintf('%s-%d.csv', $this->uriKey(), now()->format('YmdHis'));

        $extension = 'csv';

        if (Str::contains($filename, '.')) {
            [$filename, $extension] = explode('.', $filename);
        }

        $exportFilename = sprintf(
            '%s.%s',
            $filename,
            $fields->get('writerType') ?? $extension
        );

        return $response->successful([
            (new FastExcel($eloquentGenerator()))->download($exportFilename, $this->withFormatCallback),
        ]);
    }

    /**
     * Specify a callback that modifies the query used to retrieve the selected models.
     *
     * @param  (\Closure(\Illuminate\Database\Eloquent\Builder, \Laravel\Nova\Fields\ActionFields):\Illuminate\Database\Eloquent\Builder)|null  $withQueryCallback
     * @return $this
     */
    public function withQuery($withQueryCallback)
    {
        $this->withQueryCallback = $withQueryCallback;

        return $this;
    }

    /**
     * Specify a callback that defines the fields that should be present within the generated file.
     *
     * @param  (\Closure(\Laravel\Nova\Http\Requests\NovaRequest):array<int, \Laravel\Nova\Fields\Field>)|null  $withFieldsCallback
     * @return $this
     */
    public function withFields($withFieldsCallback)
    {
        $this->withFieldsCallback = $withFieldsCallback;

        return $this;
    }

    /**
     * Specify a callback that defines the field formatting for the generated file.
     *
     * @param  (\Closure(\Illuminate\Database\Eloquent\Model):array<string, mixed>)|null  $withFormatCallback
     * @return $this
     */
    public function withFormat($withFormatCallback)
    {
        $this->withFormatCallback = $withFormatCallback;

        return $this;
    }

    /**
     * Add a Select field to the action that allows the selection of the generated file's type.
     *
     * @param  (\Closure(\Laravel\Nova\Http\Requests\NovaRequest):?string)|string|null  $default
     * @return $this
     */
    public function withTypeSelector($default = null)
    {
        $this->actionFields->push(
            Select::make('Type', 'writerType')->options(function () {
                return [
                    'csv' => 'CSV (.csv)',
                    'xlsx' => 'Excel (.xlsx)',
                ];
            })->default($default)->rules(['required', Rule::in(['csv', 'xlsx'])])
        );

        return $this;
    }

    /**
     * Add a Text field to the action to allow users to define the generated file's name.
     *
     * @param  (\Closure(\Laravel\Nova\Http\Requests\NovaRequest):?string)|string|null  $default
     * @return $this
     */
    public function nameable($default = null)
    {
        $this->actionFields->push(
            Text::make('Filename', 'filename')->default($default)->rules(['required', 'min:1'])
        );

        return $this;
    }

    /**
     * Get the displayable name of the action.
     *
     * @return string
     */
    public function name()
    {
        return $this->name ?: 'Export As CSV';
    }

    /**
     * Mark the action as a standalone action.
     *
     * @return $this
     */
    public function standalone()
    {
        throw new InvalidArgumentException('The Export As CSV action may not be registered as a standalone action.');
    }
}
