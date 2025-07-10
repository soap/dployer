<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use BezhanSalleh\FilamentShield\Commands\SuperAdminCommand;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Models\User;
use App\Models\Team;

class CreateSuperAdminUser extends SuperAdminCommand
{
    protected function createSuperAdmin(): Authenticatable
    {
        return tap(static::getUserModel()::create([
            'name' => text(label: 'Name', required: true),
            'email' => text(
                label: 'Email address',
                required: true,
                validate: fn (string $email): ?string => match (true) {
                    ! filter_var($email, FILTER_VALIDATE_EMAIL) => 'The email address must be valid.',
                    static::getUserModel()::where('email', $email)->exists() => 'A user with this email address already exists',
                    default => null,
                },
            ),
            'password' => Hash::make(password(
                label: 'Password',
                required: true,
                validate: fn (string $value) => match (true) {
                    strlen($value) < 8 => 'The password must be at least 8 characters.',
                    default => null
                }
            )),
        ]), function (User $user) {
            $this->createTeam($user);   
        });
    }

        /**
     * Create a personal team for the user.
     */
    protected function createTeam(User $user): void
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));
    }
}
