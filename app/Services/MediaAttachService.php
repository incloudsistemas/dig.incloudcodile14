<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Storage;

class MediaAttachService
{
    public function __construct(protected Media $media)
    {
        $this->media = $media;
    }
    
    public function mutateFormDataToCreate(Model $ownerRecord, array $data): array
    {
        $morphMap = Relation::morphMap();
        $data['model_type'] = array_search(get_class($ownerRecord), $morphMap, true);
        $data['model_id'] = $ownerRecord->id;

        $data['collection_name'] = 'attachments';
        $data['disk'] = 'public';

        $data['manipulations'] = $data['manipulations'] ?? [];
        $data['custom_properties'] = $data['custom_properties'] ?? [];
        $data['generated_conversions'] = $data['generated_conversions'] ?? [];
        $data['responsive_images'] = $data['responsive_images'] ?? [];

        $newArray = [];
        foreach ($data['file_name'] as $fileName) {
            $tempArray = $data;
            $tempArray['file_name'] = $fileName;
            $tempArray['mime_type'] = Storage::disk('public')
                ->mimeType($fileName);
            $tempArray['size'] = Storage::disk('public')
                ->size($fileName);

            $newArray[] = $tempArray;
        }

        return $newArray;
    }

    public function mutateFormDataToEdit(Media $media, array $data): array
    {
        if ($media->file_name !== $data['file_name']) {
            $data['mime_type'] = Storage::disk('public')
                ->mimeType($data['file_name']);
            $data['size'] = Storage::disk('public')
                ->size($data['file_name']);
        }

        return $data;
    }
}
