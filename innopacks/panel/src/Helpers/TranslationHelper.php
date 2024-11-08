<?php
namespace InnoShop\Panel\Helpers;

use Stichoza\GoogleTranslate\GoogleTranslate;
class TranslationHelper
{
    public static function translateAllToEnglish(array $data): array
    {
        $translator = new GoogleTranslate();
        $translator->setTarget('en'); // Set target language to English

        // Walk through each item and translate if it's a string, except for excluded keys
        array_walk_recursive($data, function (&$item, $key) use ($translator) {
            // If the item is a string and the key is not "locale", translate it
            if (is_string($item) && $key !== 'locale') {
                $item = $translator->translate($item);
            }
        });

        return $data;
    }
}
