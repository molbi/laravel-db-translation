<?php

namespace Core\Translate\Console\Commands;

use Core\Translate\Models\Translation;
use Illuminate\Console\Command;

class FillTranslation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translation:fill-translations {locale?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interaktivní vyplnění překladů';

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
     * @return mixed
     *
     */
    public function handle()
    {
        $trans = Translation::whereNull('value')->orderBy('lang');

        if ($this->argument('locale')) {
            $trans->where('lang', $this->argument('locale'));
        }

        if ($trans->get()->isEmpty()) {
            $this->info('Není nic k překladu.');
            return;
        }

        foreach ($trans->get() as $tr) {
            $value = $this->ask('Zadejte překlad '. $tr->lang .' pro klíč: ' . $tr->key);

            $tr->update([
                'value' => $value,
            ]);

            $this->info(sprintf('Hodnota pro %s byla uložena.', $tr->key, $tr->lang));
        }

        $this->call('translation:build-cache');
    }
}
