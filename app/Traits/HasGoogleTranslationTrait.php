<?php

namespace App\Traits;

use App\Helpers\Helper;
use Spatie\Translatable\Exceptions\AttributeIsNotTranslatable;
use Spatie\Translatable\HasTranslations;


trait HasGoogleTranslationTrait
{
    use HasTranslations;

  /**
   * @throws AttributeIsNotTranslatable
   */
  public function setTranslations(string $key, array $translations): self
    {
        $this->guardAgainstNonTranslatableAttribute($key);

        foreach ($translations as $locale => $translation) {


            if (is_null($translation)) {
                $translation = Helper::translate($locale, $translations[config("translatable.fallback_locale")]);

                $this->setTranslation($key, $locale, $translation);

            } else {

                $this->setTranslation($key, $locale, $translation);

            }
        }
        return $this;
    }

    public function getTranslationWithFallback(string $key, string $locale, bool $useFallbackLocale = true)
    {
        $translation = $this->getTranslation($key, $locale);

        if ($translation !== null) {
            return $translation;
        }

        return $this->getTranslation($key, $useFallbackLocale);
    }

}
