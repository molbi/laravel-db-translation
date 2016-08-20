<?php

namespace Core\Translate\Console\Commands;

use Core\Translate\Models\Translation;
use Illuminate\Cache\CacheManager;
use Illuminate\Console\Command;

class DumpTranslation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translation:dump {locale?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vyprázdní db a odstraní cache file soubory';

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
        $res = Translation::orderBy('lang');

        if ($this->argument('locale')) {
            $res->where('lang', $this->argument('locale'));
        }

        $trans = $res->get();

        $groups = $trans->groupBy('lang');
        foreach ($groups as $key => $group) {
            $cacheKey = sprintf('translations.%s', $key);
            $cache->forget($cacheKey);
            $this->info('Odstranuji cache pro locale: ' . $key);
        }

        foreach($trans as $tr) {
            $tr->delete();
            $this->warn('Odstraňuji záznam z db: ' . $tr->key);
        }
    }
}
