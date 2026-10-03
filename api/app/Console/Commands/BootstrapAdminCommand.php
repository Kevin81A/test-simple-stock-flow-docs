<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Application\Ports\Outbound\PasswordHasherInterface;
use App\Application\Ports\Outbound\UserRepositoryInterface;
use App\Domain\Entities\User;
use Illuminate\Console\Command;

final class BootstrapAdminCommand extends Command
{
    protected $signature = 'app:bootstrap-admin';
    protected $description = 'Bootstrap initial admin user from environment variables';

    public function handle(
        UserRepositoryInterface $userRepository,
        PasswordHasherInterface $passwordHasher
    ): int {
        $email = (string) env('ADMIN_EMAIL', '');
        $password = (string) env('ADMIN_PASSWORD', '');

        if (trim($email) === '' || trim($password) === '') {
            $this->warn('ADMIN_EMAIL or ADMIN_PASSWORD not set. Skipping admin bootstrap.');
            return 0;
        }

        $normalized = User::normalizeUsername($email);
        if ($userRepository->existsByUsername($normalized)) {
            $this->info("Admin user '{$normalized}' already exists.");
            return 0;
        }

        $id = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
        $hash = $passwordHasher->hash($password);
        $user = User::create($id, $normalized, $hash, User::ROLE_ADMIN);

        $userRepository->save($user);
        $this->info("Admin user '{$normalized}' created successfully.");

        return 0;
    }
}
