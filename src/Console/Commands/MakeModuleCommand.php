<?php

declare(strict_types=1);

namespace RodGani\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use RodGani\Services\MakeModuleService;
use Symfony\Component\Finder\Finder;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {type} {name} {module}';
    protected $description = 'Generate Laravel class and move it to Modules';

    private $type;
    public function handle()
    {
        $type = Str::lower($this->argument('type'));
        $this->type = $type;
        $name = Str::studly($this->argument('name'));
        $module = Str::ucfirst($this->argument('module'));

        if (in_array($type, ['migration', 'seeder', 'factory'])) {
            return MakeModuleService::generateDBModularFile($type, $name, $module, $this);
        }
        // ✅ Snapshot app and migration directories
        $appPath = base_path('app');

        $beforeApp = collect($this->listPhpFiles($appPath));
        // 🛠 Run native make:* command
        Artisan::call("make:$type", ['name' => $name]);
        $this->info(trim(Artisan::output()));

        // 📦 Detect newly created file in app/
        $afterApp = collect($this->listPhpFiles($appPath));
        $newFile = $afterApp->diff($beforeApp)->first();

        if (!$newFile || !file_exists($newFile)) {
            $this->error("❌ Could not detect new file.");
            return;
        }

        // 📂 Move file into Modules/{Module}/<relative_path>
        $relativePath = Str::after($newFile, $appPath . DIRECTORY_SEPARATOR);
        $targetPath = base_path("Modules/$module/$relativePath");
        $unlinkPath = base_path("app/$relativePath");

        if (file_exists($targetPath)) {

            MakeModuleService::unlinkPath($unlinkPath, $relativePath);

            $this->warn("⚠️ File already exists at: Modules/$module/$relativePath");
            return; // Exit early or handle conflict as needed
        }

        if (!is_dir(dirname($targetPath))) {
            mkdir(dirname($targetPath), 0755, true);
        }

        rename($newFile, $targetPath);
        MakeModuleService::updateNamespace($targetPath, 'App\\', "Modules\\$module\\", $this->type);
        $this->removeEmptyDirectories(base_path('app'));

        $this->info("✅ " . ucfirst($type) . " move to Modules/$module/$relativePath");
    }

    public function removeEmptyDirectories(string $path)
    {
        foreach (scandir($path) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullPath = $path . DIRECTORY_SEPARATOR . $item;

            if (is_dir($fullPath)) {
                // Recursively clean subdirectories first
                $this->removeEmptyDirectories($fullPath);

                // After recursion, check if directory is now empty
                if (count(scandir($fullPath)) === 2) {
                    rmdir($fullPath);
                }
            }
        }
    }

    private function listPhpFiles($path): array
    {
        $finder = Finder::create()->files()->in($path)->name('*.php')->sortByName();
        return array_map(fn($file) => $file->getRealPath(), iterator_to_array($finder));
    }
}
