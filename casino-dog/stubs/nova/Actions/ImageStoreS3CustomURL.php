<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class ImageStoreS3CustomURL extends Action
{
    use InteractsWithQueue, Queueable;
    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $disk = config('casino-dog.s3_image_store.disk');
        $rand_prefix = $fields->rand_directory;
        $img_url = config('casino-dog.s3_image_store.image_source_url');

        if ($models->count() === 1) {
            $selectModel = $models->first();
            $file_url = $img_url.$selectModel->gid.'.png';
            \Wainwright\CasinoDog\Jobs\ImageStoreS3::dispatch($file_url, $disk, $rand_prefix, $selectModel->gid, );
        } else {
            foreach($models as $selectModel) {
                $file_url = $img_url.$selectModel->gid.'.png';
                \Wainwright\CasinoDog\Jobs\ImageStoreS3::dispatch($file_url, $disk, $rand_prefix, $selectModel->gid);
            }
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Heading::make('<p>Thumbnail download & uploader to self managed S3 storage systems. Your bucket should be publically accessible.</p><p>You need to have s3 disk driver installed and s3 disk enabled, read about storage on laravel documentation.</p>')->asHtml(),
            Text::make('Image URL', 'prefix_url')->help('Can be set in config/casino-dog.')->default(config('casino-dog.s3_image_store.image_source_url'))->rules('required', 'min:4', 'max:50'),
            Text::make('Random Directory', 'rand_directory')->readonly()->help('Random directory string in which batch will be placed.')->default('i-'.rand(10000, 9999999))->rules('required', 'min:4', 'max:30'),
        ];
    }
}
