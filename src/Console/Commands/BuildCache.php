<?php

namespace Core\Translate\Console\Commands;

use Core\Translate\Models\Translation;
use Illuminate\Cache\CacheManager;
use Illuminate\Console\Command;

class BuildCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translation:build-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vygeneruje překladové soubory z db';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param CacheManager $cache
     *
     * @return mixed
     */
    public function handle(CacheManager $cache)
    {
        $res = Translation::all();

        $groups = $res->groupBy('lang');

        $bar = $this->output->createProgressBar($groups->count());

        foreach ($groups as $key => $group) {
            $cacheKey = sprintf('translations.%s', $key);

            $array = array();
            foreach ($group->pluck('value', 'key') as $key => $value) {
                array_set($array, $key, $value);
            }

            $cache->forever($cacheKey, $array);

            $bar->advance();
        }

    }
}
