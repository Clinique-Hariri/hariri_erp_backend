<?php

namespace App\Helpers;

use Exception;
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

}
