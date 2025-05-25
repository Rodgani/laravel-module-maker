<?php
declare(strict_types=1);

namespace RodGani\Services;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class MakeModuleService
{
    private const CONTROLLER = "controller";
    private const FACTORY = "factory";
    private const MIGRATION = "migration";
    private const SEEDER = "seeder";
    public static function generateDBModularFile(string $type, string $name, string $module, Command $console)
    {
        $migrationPath = database_path('migrations');
        $factoryPath = database_path('factories');
        $seederPath = database_path('seeders');

        match ($type) {
            'migration' => self::mkdir($migrationPath),
            'seeder'    => self::mkdir($seederPath),
            'factory'   => self::mkdir($factoryPath)
        };

        $beforeFiles = match ($type) {
            'migration' => collect(scandir($migrationPath)),
            'factory'   => collect(scandir($factoryPath)),
            'seeder'    => collect(scandir($seederPath)),
            default     => collect(),
        };

        // 🛠 Run the artisan make:* command
        Artisan::call("make:$type", ['name' => $name]);
        $console->info(trim(Artisan::output()));

        $afterFiles = match ($type) {
            'migration' => collect(scandir($migrationPath)),
            'factory'   => collect(scandir($factoryPath)),
            'seeder'    => collect(scandir($seederPath)),
            default     => collect(),
        };

        $newFiles = $afterFiles->diff($beforeFiles);

        foreach ($newFiles as $file) {
            if (!str_ends_with($file, '.php')) continue;

            $from = match ($type) {
                'migration' => $migrationPath . DIRECTORY_SEPARATOR . $file,
                'factory'   => $factoryPath . DIRECTORY_SEPARATOR . $file,
                'seeder'    => $seederPath . DIRECTORY_SEPARATOR . $file,
            };

            $to = match ($type) {
                'migration' => base_path("Modules/$module/Database/Migrations/$file"),
                'factory'   => base_path("Modules/$module/Database/Factories/$file"),
                'seeder'    => base_path("Modules/$module/Database/Seeders/$file"),
            };

            if (file_exists($to)) {
                self::unlinkPath($from, $to);
                $console->warn("⚠️ File already exists at $to — skipped.");
                continue;
            }

            if (!is_dir(dirname($to))) {
                mkdir(dirname($to), 0755, true);
            }

            rename($from, $to);
            $console->info("✅ $type file moved to $to");

            self::updateNamespace($to, $from, "Modules\\$module\\", $type, $name);
            self::unlinkPath($from, $to);
            return;
        }
    }

    public static function unlinkPath($unlinkPath, $relativePath): void
    {
        if (file_exists($unlinkPath)) {
            // 🗑️ Delete the new file
            unlink($unlinkPath);

            // 🧹 Remove empty parent directories based on relative path only
            $dir = dirname($unlinkPath);
            $segments = explode(DIRECTORY_SEPARATOR, $relativePath);

            // Limit cleanup only within the relative path structure
            foreach (array_reverse($segments) as $_) {
                if (is_dir($dir) && count(scandir($dir)) === 2) {
                    rmdir($dir);
                    $dir = dirname($dir);
                } else {
                    break;
                }
            }
        }
    }

    public static function updateNamespace(string $filePath, string $from, string $to, string $type, ?string $name = null): void
    {
        $contents = file_get_contents($filePath);

        if ($type === self::CONTROLLER) {
            $search = ["namespace $from", "use Illuminate\\Http\\Request;"];
            $replace = ["namespace $to", "use Illuminate\\Http\\Request;" . PHP_EOL . "use Modules\\Controller;"];
        } else if ($type === self::FACTORY) {
            $model = Str::ucfirst($name);
            $search = ["namespace Database\Factories", "\App\Model"];
            $replace = ["namespace $to" . "Database\\Factories", "\\$to" . "Models\\$model"];
        } elseif ($type === self::SEEDER) {
            $search = ["namespace Database\Seeders"];
            $replace = ["namespace $to" . "Database\\Seeders"];
        } else {
            $search = ["namespace $from"];
            $replace = ["namespace $to"];
        }

        $updated = str_replace($search, $replace, $contents);

        file_put_contents($filePath, $updated);

        self::unlinkPath($from, $to);
    }

    private static function mkdir($path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
