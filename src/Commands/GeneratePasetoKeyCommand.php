<?php

declare(strict_types=1);

namespace MyDaniel\Paseto\Commands;

use Illuminate\Console\Command;

/**
 * Artisan command to generate a new Paseto secret key.
 */
class GeneratePasetoKeyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paseto:generate-key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a random 32-byte key for local Paseto tokens (hex-encoded)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $rawKey = random_bytes(32);
        } catch (\Exception $e) {
            $this->error('Could not generate a random key: '.$e->getMessage());

            return Command::FAILURE;
        }

        $hexKey = bin2hex($rawKey);

        $this->info('Paseto key generated successfully:');
        $this->comment($hexKey);

        if ($this->appendKeyToEnv($hexKey)) {
            $this->info('The key has been added to your .env file.');
        } else {
            $this->error('Failed to add the key to the .env file. Please add it manually.');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Append the generated key to the .env file.
     *
     * @param  string  $key
     *
     * @return bool
     */
    protected function appendKeyToEnv(string $key): bool
    {
        $envFilePath = base_path('.env');

        if (!file_exists($envFilePath)) {
            return false;
        }

        $envLine = 'PASETO_SECRET_KEY="'.$key.'"';

        $envContent = file_get_contents($envFilePath);

        if (str_contains($envContent, 'PASETO_SECRET_KEY')) {
            $this->warn('PASETO_SECRET_KEY already exists in your .env file. No changes were made.');

            return true;
        }

        return file_put_contents($envFilePath, PHP_EOL.$envLine, FILE_APPEND | LOCK_EX) !== false;
    }
}
