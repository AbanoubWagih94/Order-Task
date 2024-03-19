<?php

namespace App\Services;


class ImageService
{
  public static function uploadImage($file, $name, $path)
  {
      $image = $file;
      $image_name = time() . '-' . $name . '.png';
      $image_path = $path;
      $image->move(public_path($image_path), $image_name);
      return $image_name;
  }
}
