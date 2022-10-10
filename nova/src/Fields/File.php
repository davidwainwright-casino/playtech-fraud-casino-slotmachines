<?php

namespace Laravel\Nova\Fields;

use Closure;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Contracts\Deletable as DeletableContract;
use Laravel\Nova\Contracts\Downloadable as DownloadableContract;
use Laravel\Nova\Contracts\Storable as StorableContract;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @method static static make(mixed $name, string|callable|null $attribute = null, string|null $disk = null, callable|null $storageCallback = null)
 */
class File extends Field implements StorableContract, DeletableContract, DownloadableContract
{
    use Storable,
        Deletable,
        AcceptsTypes,
        HasDownload,
        HasThumbnail,
        HasPreview,
        SupportsDependentFields;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'file-field';

    /**
     * The callback that should be executed to store the file.
     *
     * @var callable(\Laravel\Nova\Http\Requests\NovaRequest, object, string, string, ?string, ?string):mixed
     */
    public $storageCallback;

    /**
     * The callback that should be used to determine the file's storage name.
     *
     * @var (callable(\Illuminate\Http\Request):string)|null
     */
    public $storeAsCallback;

    /**
     * The column where the file's original name should be stored.
     *
     * @var string
     */
    public $originalNameColumn;

    /**
     * The column where the file's size should be stored.
     *
     * @var string
     */
    public $sizeColumn;

    /**
     * The text alignment for the field's text in tables.
     *
     * @var string
     */
    public $textAlign = 'center';

    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var bool
     */
    public $showOnIndex = false;

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|callable|null  $attribute
     * @param  string|null  $disk
     * @param  (callable(\Laravel\Nova\Http\Requests\NovaRequest, object, string, string, ?string, ?string):mixed)|null  $storageCallback
     * @return void
     */
    public function __construct($name, $attribute = null, $disk = null, $storageCallback = null)
    {
        parent::__construct($name, $attribute);

        $this->disk($disk);

        $this->prepareStorageCallback($storageCallback);

        $this->thumbnail(function () {
            return null;
        })->preview(function () {
            return null;
        })->download(function ($request, $model) {
            $name = $this->originalNameColumn ? $model->{$this->originalNameColumn} : null;

            return Storage::disk($this->getStorageDisk())->download($this->value, $name);
        })->delete(function () {
            if ($this->value) {
                Storage::disk($this->getStorageDisk())->delete($this->value);

                return $this->columnsThatShouldBeDeleted();
            }
        });
    }

    /**
     * Prepare the storage callback.
     *
     * @param  (callable(\Laravel\Nova\Http\Requests\NovaRequest, object, string, string, ?string, ?string):mixed)|null  $storageCallback
     * @return void
     */
    protected function prepareStorageCallback($storageCallback)
    {
        $this->storageCallback = $storageCallback ?? function ($request, $model, $attribute, $requestAttribute) {
            return $this->mergeExtraStorageColumns($request, [
                $this->attribute => $this->storeFile($request, $requestAttribute),
            ]);
        };
    }

    /**
     * Store the file on disk.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $requestAttribute
     * @return string
     */
    protected function storeFile($request, $requestAttribute)
    {
        if (! $this->storeAsCallback) {
            return $request->file($requestAttribute)->store($this->getStorageDir(), $this->getStorageDisk());
        }

        return $request->file($requestAttribute)->storeAs(
            $this->getStorageDir(), call_user_func($this->storeAsCallback, $request), $this->getStorageDisk()
        );
    }

    /**
     * Merge the specified extra file information columns into the storable attributes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $attributes
     * @return array
     */
    protected function mergeExtraStorageColumns($request, array $attributes)
    {
        $file = $request->file($this->attribute);

        if ($this->originalNameColumn) {
            $attributes[$this->originalNameColumn] = $file->getClientOriginalName();
        }

        if ($this->sizeColumn) {
            $attributes[$this->sizeColumn] = $file->getSize();
        }

        return $attributes;
    }

    /**
     * Get an array of the columns that should be deleted and their values.
     *
     * @return array
     */
    protected function columnsThatShouldBeDeleted()
    {
        $attributes = [$this->attribute => null];

        if ($this->originalNameColumn) {
            $attributes[$this->originalNameColumn] = null;
        }

        if ($this->sizeColumn) {
            $attributes[$this->sizeColumn] = null;
        }

        return $attributes;
    }

    /**
     * Get the disk that the field is stored on.
     *
     * @return string|null
     */
    public function getStorageDisk()
    {
        return $this->disk ?: config('nova.storage_disk', 'public');
    }

    /**
     * Specify the callback that should be used to store the file.
     *
     * @param  callable(\Laravel\Nova\Http\Requests\NovaRequest, object, string, string, ?string, ?string):mixed  $storageCallback
     * @return $this
     */
    public function store(callable $storageCallback)
    {
        $this->storageCallback = $storageCallback;

        return $this;
    }

    /**
     * Specify the callback that should be used to determine the file's storage name.
     *
     * @param  callable(\Illuminate\Http\Request):string  $storeAsCallback
     * @return $this
     */
    public function storeAs(callable $storeAsCallback)
    {
        $this->storeAsCallback = $storeAsCallback;

        return $this;
    }

    /**
     * Specify the callback that should be used to retrieve the thumbnail URL.
     *
     * @param  callable(mixed, string, mixed):?string  $thumbnailUrlCallback
     * @return $this
     */
    public function thumbnail(callable $thumbnailUrlCallback)
    {
        $this->thumbnailUrlCallback = $thumbnailUrlCallback;

        return $this;
    }

    /**
     * Specify the callback that should be used to retrieve the preview URL.
     *
     * @param  callable(mixed, ?string, mixed):?string  $previewUrlCallback
     * @return $this
     */
    public function preview(callable $previewUrlCallback)
    {
        $this->previewUrlCallback = $previewUrlCallback;

        return $this;
    }

    /**
     * Specify the column where the file's original name should be stored.
     *
     * @param  string  $column
     * @return $this
     */
    public function storeOriginalName($column)
    {
        $this->originalNameColumn = $column;

        return $this;
    }

    /**
     * Specify the column where the file size should be stored.
     *
     * @param  string  $column
     * @return $this
     */
    public function storeSize($column)
    {
        $this->sizeColumn = $column;

        return $this;
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  object  $model
     * @return void
     */
    public function fillForAction(NovaRequest $request, $model)
    {
        if (isset($request[$this->attribute])) {
            $model->{$this->attribute} = $request[$this->attribute];
        }
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return mixed
     */
    protected function fillAttribute(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if (is_null($file = $request->file($requestAttribute)) || ! $file->isValid()) {
            return;
        }

        $hasExistingFile = ! is_null($this->getStoragePath());

        $result = call_user_func(
            $this->storageCallback,
            $request,
            $model,
            $attribute,
            $requestAttribute,
            $this->getStorageDisk(),
            $this->getStorageDir()
        );

        if ($result === true) {
            return;
        }

        if ($result instanceof Closure) {
            return $result;
        }

        if (! is_array($result)) {
            return $model->{$attribute} = $result;
        }

        foreach ($result as $key => $value) {
            $model->{$key} = $value;
        }

        if ($this->isPrunable() && $hasExistingFile) {
            return function () use ($model, $request) {
                call_user_func(
                    $this->deleteCallback,
                    $request,
                    $model,
                    $this->getStorageDisk(),
                    $this->getStoragePath()
                );
            };
        }
    }

    /**
     * Get the full path that the field is stored at on disk.
     *
     * @return string|null
     */
    public function getStoragePath()
    {
        return $this->value;
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'thumbnailUrl' => $this->resolveThumbnailUrl(),
            'previewUrl' => $this->resolvePreviewUrl(),
            'downloadable' => $this->downloadsAreEnabled && isset($this->downloadResponseCallback) && ! empty($this->value),
            'deletable' => isset($this->deleteCallback) && $this->deletable,
            'acceptedTypes' => $this->acceptedTypes,
        ]);
    }
}
