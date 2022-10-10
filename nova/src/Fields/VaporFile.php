<?php

namespace Laravel\Nova\Fields;

use Closure;
use Exception;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Contracts\Deletable as DeletableContract;
use Laravel\Nova\Contracts\Downloadable as DownloadableContract;
use Laravel\Nova\Contracts\Storable as StorableContract;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @method static static make(mixed $name, string|null $attribute = null, callable|null $storageCallback = null)
 */
class VaporFile extends Field implements StorableContract, DeletableContract, DownloadableContract
{
    use AcceptsTypes,
        Deletable,
        HasDownload,
        HasPreview,
        HasThumbnail,
        Storable,
        SupportsDependentFields;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'vapor-file-field';

    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var bool
     */
    public $showOnIndex = false;

    /**
     * The text alignment for the field's text in tables.
     *
     * @var string
     */
    public $textAlign = 'center';

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
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  (callable(\Laravel\Nova\Http\Requests\NovaRequest, object, string, string, ?string, ?string):mixed)|null  $storageCallback
     * @return void
     */
    public function __construct($name, $attribute = null, $storageCallback = null)
    {
        parent::__construct($name, $attribute);

        $this->prepareStorageCallback($storageCallback);

        $this->thumbnail(function () {
            return null;
        })->preview(function () {
            return null;
        })->download(function ($request, $model) {
            return Storage::disk($this->getStorageDisk())->download($this->value);
        })->delete(function () {
            if ($this->value) {
                Storage::disk($this->getStorageDisk())->delete($this->value);

                return $this->columnsThatShouldBeDeleted();
            }
        });
    }

    /**
     * Set the name of the disk the file is stored on by default.
     *
     * @param  string  $disk
     * @return $this
     *
     * @throws \Exception
     */
    public function disk($disk)
    {
        throw new Exception('You cannot set the disk used for Vapor file fields.');
    }

    /**
     * Get the disk that the field is stored on.
     *
     * @return string|null
     */
    public function getStorageDisk()
    {
        return 's3';
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
        return with($request->input('vaporFile')[$requestAttribute]['key'], function ($key) use ($request) {
            $fileName = $this->storeAsCallback
                ? call_user_func($this->storeAsCallback, $request)
                : str_replace('tmp/', '', $key);

            Storage::disk($this->getStorageDisk())->copy($key, $this->getStorageDir().'/'.$fileName);

            return ltrim($this->getStorageDir().'/'.$fileName, '/');
        });
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
        if ($this->originalNameColumn) {
            $attributes[$this->originalNameColumn] = $request->input($this->attribute);
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

        return $attributes;
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
        if (is_null(optional($request->input('vaporFile'))[$requestAttribute])) {
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
