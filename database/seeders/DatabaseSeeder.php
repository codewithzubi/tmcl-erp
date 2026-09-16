<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // firstOrCreate so re-running `db:seed` on a database that already
        // has these (e.g. after a fresh migrate on a new environment) never
        // fails on the unique email/customer_code constraints. Password set
        // directly (not via the factory's ->toArray()) since User normally
        // hides that attribute from array/JSON output.
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => Hash::make('password'), 'email_verified_at' => now()],
        );

        // "UNDEFINED" — the fixed sentinel Customer used on the New
        // Slaughter form and elsewhere for a customer that isn't known yet.
        // Previously a real Customer row someone had to create by hand in
        // Setups; seeding it here means every fresh database (a new Railway
        // project, a factory-floor Local Server) has it automatically.
        Customer::firstOrCreate(
            ['customer_name' => 'UNDEFINED'],
            [
                'customer_code' => 'CUST-UNDEFINED',
                'customer_type' => 'Local',
                'company_name' => 'UNDEFINED',
                'contact_person' => 'UNDEFINED',
                'email' => 'undefined@example.com',
                'mobile' => '00000000000',
                'industry_type' => 'UNDEFINED',
                'customer_category' => 'UNDEFINED',
                'currency' => 'PKR',
                'payment_terms' => 'UNDEFINED',
                'billing_address' => 'UNDEFINED',
                'shipping_address' => 'UNDEFINED',
                'country' => 'UNDEFINED',
                'city' => 'UNDEFINED',
                'status' => 'Active',
            ],
        );
    }
}
