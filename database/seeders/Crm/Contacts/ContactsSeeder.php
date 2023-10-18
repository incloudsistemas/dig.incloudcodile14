<?php

namespace Database\Seeders\Crm\Contacts;

use App\Models\Crm\Contacts\Contact;
use App\Models\Crm\Contacts\Individual;
use App\Models\Crm\Contacts\LegalEntity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ContactsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncateContactTables();

        Individual::factory(500)->create()->each(function ($individual) {
            $this->command->info('Creating Individual Contact ' . $individual->name);

            Contact::factory()->create([
                'contactable_type' => Individual::class,
                'contactable_id'   => $individual->id,
            ]);
        });

        LegalEntity::factory(500)->create()->each(function ($legalEntity) {
            $this->command->info('Creating Legal Entity Contact ' . $legalEntity->name);

            Contact::factory()->create([
                'contactable_type' => LegalEntity::class,
                'contactable_id'   => $legalEntity->id,
            ]);
        });
    }

    private function truncateContactTables()
    {
        $this->command->info('Truncating Contact, Individual and LegalEntity tables');
        Schema::disableForeignKeyConstraints();

        DB::table('crm_contacts')->truncate();
        DB::table('crm_contact_individuals')->truncate();
        DB::table('crm_contact_legal_entities')->truncate();

        Schema::enableForeignKeyConstraints();
    }
}
