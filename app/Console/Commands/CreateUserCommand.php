<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create users by using command';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info(PHP_EOL.'User creation via command line');
        $user['name'] = $this->ask('Name:');
        $user['email'] = $this->ask('Email:');
        $user['password'] = $this->secret('Password:');

        $selected_role = $this->choice('Role: ', ['admin', 'editor'], 1);

        $role = Role::where('name', $selected_role)->first();

        if (! $role) {
            $this->error('Role not found!');

            return -1;
        }

        if (! $this->validate($user)) {
            return -1;
        }

        DB::transaction(function () use ($user, $role) {
            $created_user = User::create([...$user, 'password' => Hash::make($user['password'])]);
            $created_user->roles()->attach($role->id);
        });

        $this->info("User {$user['email']} created successfully");

        return 0;
    }

    private function validate($user)
    {
        $validator = Validator::make($user, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|min:8|max:16',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return false;
        }

        return true;
    }
}
