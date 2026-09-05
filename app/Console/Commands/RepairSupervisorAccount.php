<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class RepairSupervisorAccount extends Command
{
    protected $signature = 'account:repair-supervisor {email} {password}';
    protected $description = 'Repair a supervisor account in the active application database';

    public function handle(): int
    {
        $user = User::where('email', strtolower(trim($this->argument('email'))))->first();
        if (!$user) {
            $this->error('No account exists with that email in the active database.');
            return self::FAILURE;
        }

        $user->forceFill([
            'role' => 'supervisor',
            'password' => Hash::make($this->argument('password')),
        ])->save();
        $this->info('Supervisor account repaired successfully.');
        return self::SUCCESS;
    }
}