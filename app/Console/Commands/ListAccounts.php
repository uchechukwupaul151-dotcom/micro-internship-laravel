<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListAccounts extends Command
{
    protected $signature = 'account:list';
    protected $description = 'List registered account names, emails, and roles';

    public function handle(): int
    {
        $accounts = User::query()->orderBy('id')->get(['id', 'name', 'email', 'role']);
        if ($accounts->isEmpty()) {
            $this->warn('No accounts found in the active database.');
            return self::SUCCESS;
        }

        $this->table(['ID', 'Name', 'Email', 'Role'], $accounts->toArray());
        return self::SUCCESS;
    }
}