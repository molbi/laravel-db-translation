<?php

namespace Core\Translate;


use Core\Translate\Models\Translation;
use Illuminate\Translation\LoaderInterface;
use Illuminate\Translation\Translator as BaseTranslator;

/**
 * Class Translator
 * @package Core\Translate
 */
class Translator extends BaseTranslator
{

    /**
     * @var array
     */
    private $config;

    /**
     * Translator constructor.
     *
     * @param LoaderInterface $loader
     * @param string          $locale
     * @param                 $config
     */
    public function __construct(LoaderInterface $loader, $locale, $config)
    {
        parent::__construct($loader, $locale);
        $this->config = $config;
    }

    /**
     * Get the translation for the given key.
     *
     * @param  string      $key
     * @param  array       $replace
     * @param  string|null $locale
     * @param  bool        $fallback
     *
     * @return string|array|null
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        list($namespace, $group, $item) = $this->parseKey($key);

        // Here we will get the locale that should be used for the language line. If one
        // was not passed, we will use the default locales which was given to us when
        // the translator was instantiated. Then, we can load the lines and return.
        $locales = $fallback ? $this->parseLocale($locale) : [$locale ?: $this->locale];

        foreach ($locales as $locale) {
            $this->load($namespace, $group, $locale);

            $line = $this->getLine($namespace, $group, $locale, $item, $replace);

            if (!is_null($line)) {
                break;
            }
        }

        // If the line doesn't exist, we will return back the key which was requested as
        // that will be quick to spot in the UI if language keys are wrong or missing
        // from the application's language files. Otherwise we can return the line.
        if (!isset($line)) {

            $this->insertKey($key, $group, $locales[0]);

            return $key;
        }

        return $line;
    }

    /**
     * @param $key
     * @param $group
     * @param $locale
     */
    private function insertKey($key, $group, $locale)
    {
        if (!isset($this->config['insert-enable']) || !$this->config['insert-enable']) {
            return;
        }

        try {
            Translation::create([
                'lang'  => $locale,
                'group' => $group,
                'key'   => $key,
            ]);
        } catch (\Exception $e) {

        }

    }
}
