<?php

namespace Database\Seeders;

use \App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    User::create([
      'name' => 'Super Admin',
      'email' => 'superadmin@grw.my.id',
      'username' => 'superadmin',
      'password' => Hash::make('password'),
      'email_verified_at' => now(),
      'remember_token' => Str::random(10),
      'role' => 'superadmin'
    ]);

    User::create([
      'name' => 'Admin',
      'email' => 'admin@grw.my.id',
      'username' => 'admin',
      'password' => Hash::make('password'),
      'email_verified_at' => now(),
      'remember_token' => Str::random(10),
      'role' => 'admin'
    ]);
  }
}