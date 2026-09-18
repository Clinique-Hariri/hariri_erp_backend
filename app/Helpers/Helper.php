<?php

namespace App\Helpers;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Stichoza\GoogleTranslate\GoogleTranslate;

class Helper
{
  public static function translate($to, $word, $from = null): ?string
  {
    if (!is_null($word)) {
      $googleTranslate = new GoogleTranslate();
      if ($from == null) {
        $googleTranslate->setSource();
      } else {
        $googleTranslate->setSource($from);
      }
      $googleTranslate->setTarget($to);
      return $googleTranslate->translate($word);
    }
    return $word??"";
  }

  public static function getLocalesOrder(): array
  {
    $array = ['ar' => 'العربية', 'en' => 'English', 'fr' => 'Français'];

    $default =  app()->getLocale(); // this could be whatever you like

    $defaultVal = [];
    if(isset($array[$default])) {
      $defaultVal = $array[$default];
      unset($array[$default]);
    }


    asort($array);
    $array = array($default => $defaultVal) + $array;
    //dd($default);
    return $array;
  }

  /**
   * Build a relative URL (e.g. "storage/2/file.jpg") from a Media instance
   * stored on the public disk.
   */
  public static function mediaRelativeUrl(?Media $media): ?string
  {
    if (is_null($media)) {
      return null;
    }

    // getPathRelativeToRoot() returns e.g. "2/file.jpg" (no disk root, no domain).
    return 'storage/' . ltrim($media->getPathRelativeToRoot(), '/');
  }

}
