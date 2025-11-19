<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportNextFasterData extends Command
{
    protected $signature = 'import:data {sql_file? : Path to the SQL dump file (defaults to database/data/data.sql)}';

    protected $description = 'Import sample data from PostgreSQL dump';

    public function handle(): int
    {
        $sqlFile = $this->argument('sql_file') ?? base_path('database/data/data.sql');

        // Check for compressed version if uncompressed doesn't exist
        if (! file_exists($sqlFile)) {
            $gzFile = $sqlFile.'.gz';
            if (file_exists($gzFile)) {
                $this->info("Decompressing {$gzFile}...");
                exec("gunzip -k ".escapeshellarg($gzFile));

                if (! file_exists($sqlFile)) {
                    $this->error('Failed to decompress file');

                    return self::FAILURE;
                }
            } else {
                $this->error("File not found: {$sqlFile}");
                $this->newLine();
                $this->info('Please ensure the SQL dump file exists at:');
                $this->comment('  database/data/data.sql or database/data/data.sql.gz');
                $this->newLine();
                $this->info('Or provide a custom path:');
                $this->comment('  php artisan import:data /path/to/data.sql');

                return self::FAILURE;
            }
        }

        // Disable foreign key constraints temporarily
        $this->disableForeignKeyChecks();

        // Clear existing data
        $this->info('Clearing existing data...');
        DB::table('products')->truncate();
        DB::table('subcategories')->truncate();
        DB::table('subcollections')->truncate();
        DB::table('categories')->truncate();
        DB::table('collections')->truncate();

        // Import collections
        $this->info('Importing collections...');
        $this->importTableFromFile($sqlFile, 'collections', function ($line) {
            [$id, $name, $slug] = explode("\t", $line);

            return [
                'id' => $id,
                'name' => $name,
                'slug' => $slug,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        // Import categories
        $this->info('Importing categories...');
        $this->importTableFromFile($sqlFile, 'categories', function ($line) {
            [$slug, $name, $collectionId, $imageUrl] = array_pad(explode("\t", $line), 4, null);

            return [
                'slug' => $slug,
                'name' => $name,
                'collection_id' => $collectionId,
                'image_url' => $imageUrl === '\\N' ? null : $imageUrl,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        // Import subcollections
        $this->info('Importing subcollections...');
        $this->importTableFromFile($sqlFile, 'subcollections', function ($line) {
            [$id, $name, $categorySlug] = explode("\t", $line);

            return [
                'id' => $id,
                'name' => $name,
                'category_slug' => $categorySlug,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        // Import subcategories
        $this->info('Importing subcategories...');
        $this->importTableFromFile($sqlFile, 'subcategories', function ($line) {
            [$slug, $name, $subcollectionId, $imageUrl] = array_pad(explode("\t", $line), 4, null);

            return [
                'slug' => $slug,
                'name' => $name,
                'subcollection_id' => $subcollectionId,
                'image_url' => $imageUrl === '\\N' ? null : $imageUrl,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        // Import products
        $this->info('Importing products...');
        $this->importTableFromFile($sqlFile, 'products', function ($line) {
            [$slug, $name, $description, $price, $subcategorySlug, $imageUrl] = array_pad(explode("\t", $line), 6, null);

            return [
                'slug' => $slug,
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'subcategory_slug' => $subcategorySlug,
                'image_url' => $imageUrl === '\\N' ? null : $imageUrl,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        // Re-enable foreign key constraints
        $this->enableForeignKeyChecks();

        $this->info('✓ Data import completed successfully!');

        return self::SUCCESS;
    }

    private function disableForeignKeyChecks(): void
    {
        $driver = DB::getDriverName();

        match ($driver) {
            'mysql' => DB::statement('SET FOREIGN_KEY_CHECKS=0'),
            'sqlite' => DB::statement('PRAGMA foreign_keys = OFF'),
            'pgsql' => DB::statement('SET CONSTRAINTS ALL DEFERRED'),
            default => null,
        };
    }

    private function enableForeignKeyChecks(): void
    {
        $driver = DB::getDriverName();

        match ($driver) {
            'mysql' => DB::statement('SET FOREIGN_KEY_CHECKS=1'),
            'sqlite' => DB::statement('PRAGMA foreign_keys = ON'),
            'pgsql' => DB::statement('SET CONSTRAINTS ALL IMMEDIATE'),
            default => null,
        };
    }

    private function importTableFromFile(string $sqlFile, string $tableName, callable $transformer): void
    {
        $file = fopen($sqlFile, 'r');
        $inCopyBlock = false;
        $batch = [];
        $count = 0;
        $bar = null;

        while (($line = fgets($file)) !== false) {
            // Check if we're entering this table's COPY block
            if (str_contains($line, "COPY public.{$tableName}")) {
                $inCopyBlock = true;
                $this->info("Starting import for {$tableName}...");

                continue;
            }

            // Check if we're leaving the COPY block
            if ($inCopyBlock && str_starts_with(trim($line), '\\.')) {
                $inCopyBlock = false;

                // Insert remaining records
                if (! empty($batch)) {
                    DB::table($tableName)->insert($batch);
                    $batch = [];
                }

                if ($bar) {
                    $bar->finish();
                    $this->newLine();
                }

                $this->info("✓ Imported {$count} {$tableName}");

                break;
            }

            // Process data lines
            if ($inCopyBlock && ! empty(trim($line))) {
                if ($bar === null) {
                    $bar = $this->output->createProgressBar();
                    $bar->start();
                }

                $batch[] = $transformer(trim($line));
                $count++;

                // Insert in batches of 1000
                if (count($batch) >= 1000) {
                    DB::table($tableName)->insert($batch);
                    $batch = [];
                    $bar->advance(1000);
                }
            }
        }

        fclose($file);
    }
}
