<?php


namespace OFFLINE\Mall\Classes\Traits;

use October\Rain\Support\Collection;
use OFFLINE\Mall\Models\ImageSet;
use OFFLINE\Mall\Models\Product;
use OFFLINE\Mall\Models\Variant;
use System\Models\File;

trait Images
{
    /**
     * Returns the first available image.
     *
     * @return File
     */
    public function getImageAttribute()
    {
        return optional($this->main_image_set_images)->first();
    }

    /**
     * Returns the first available image. Alias of get_image_attribute
     *
     * @return File
     */
    public function getMainImageAttribute()
    {
        return $this->getImageAttribute();
    }

    /**
     * Return all images except the main image.
     *
     * @return Collection
     */
    public function getImagesAttribute()
    {
        return optional($this->main_image_set_images)->slice(1);
    }

    /**
     * Returns all images of the main image set.
     */
    public function getMainImageSetImagesAttribute()
    {
        return optional($this->main_image_set)->images;
    }

    /**
     * Return all available images.
     *
     * @return File
     */
    public function getAllImagesAttribute()
    {
        return $this->main_image_set_images;
    }

    /**
     * Returns the main image set.
     */
    public function getMainImageSetAttribute()
    {
        // Exactly one image set is available
        if ($this->image_sets instanceof ImageSet) {
            return $this->image_sets;
        }

        // No image sets available
        if ( ! $this->image_sets instanceof Collection || $this->image_sets->count() < 1) {
            return null;
        }

        return $this->image_sets->sortByDesc('is_main_set')->first();
    }
}
