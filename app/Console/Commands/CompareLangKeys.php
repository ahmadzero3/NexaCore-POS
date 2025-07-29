<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CompareLangKeys extends Command
{
    protected $signature = 'lang:compare';
    protected $description = 'Compare language files and identify missing keys';

    /**
     * Command :
     *
     * php artisan lang:compare
     *
     */

    public function handle()
    {
        $languages = ['en', 'ar'];
        $basePath = base_path('lang');

        $allKeys = [];

        // Collect all keys from each language file
        foreach ($languages as $lang) {
            $langPath = $basePath . '/' . $lang;
            $files = File::allFiles($langPath);

            foreach ($files as $file) {
                $fileName = $file->getFilenameWithoutExtension();
                $translations = trans($fileName, [], $lang);

                if (is_array($translations)) {
                    foreach (array_keys($translations) as $key) {
                        $allKeys[$fileName][$lang][] = $key;
                    }
                }
            }
        }

        // Compare keys across languages
        foreach ($allKeys as $fileName => $keysByLang) {
            $this->info("Checking file: $fileName");

            $allFileKeys = array_unique(array_merge(...array_values($keysByLang)));

            foreach ($languages as $lang) {
                $missingKeys = array_diff($allFileKeys, $keysByLang[$lang] ?? []);
                if (!empty($missingKeys)) {
                    $this->warn("Missing keys in [$lang/$fileName.php]:");
                    foreach ($missingKeys as $key) {
                        $this->line("  - $key");
                    }
                }
            }
        }

        $this->info('Language comparison completed.');
    }
}
