<?php

namespace Database\Seeders;

use App\Models\Renter;
use Illuminate\Database\Seeder;
use DB;
use Schema;
use Artisan;

class WilayaCommuneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if table exist
        if (! Schema::hasTable('communes')) {
            Artisan::call('migrate');
        }

        //$wilayas = DB::table('wilayas')->count();
        $communes = DB::table('communes')->count();

        if (!$wilayas && !$communes) {
            $this->loadData();
            $this->command->info("Success!! wilayas and communes are loaded successfully");
            return;
        }

        $this->command->comment("Communes already loaded");
    }


    protected function loadData()
    {
        //$this->insertWilayas();
        $this->insertCommunes();
    }

    protected function insertWilayas()
    {
        // Load wilayas from json
        $wilayas_json = json_decode(file_get_contents(database_path('seeders/json/Wilaya_Of_Algeria.json')));

        // Insert Wilayas
        $data = [];
        foreach ($wilayas_json as $wilaya) {
            $data[] = [
                'id'          => $wilaya->id,
                'name'        => $wilaya->name,
                'arabic_name' => $wilaya->ar_name,
                'longitude'   => $wilaya->longitude,
                'latitude'    => $wilaya->latitude,
                'created_at'  => now(),
            ];
        }
        DB::table('wilayas')->insert($data);
    }

    protected function insertCommunes()
    {
        // Load wilayas from json
        $communes_json = json_decode(file_get_contents(database_path('algeria_cities.json')));

        // Insert communes
        $data = [];
        foreach ($communes_json as $commune) {
            $data[] = [
                'id'          => $commune->id,
                'commune_name' => $commune->commune_name_ascii,
                'commune_arabic_name' => $commune->commune_name,
                'daira_name'   => $commune->daira_name_ascii,
                'daira_arabic_name'   => $commune->daira_name,
                'wilaya_code'   => $commune->wilaya_code,
                'wilaya_name'    => $commune->wilaya_name_ascii,
                'wilaya_arabic_name'    => $commune->wilaya_name,
                'created_at'  => now(),
            ];
        }
        DB::table('communes')->insert($data);
    }
}
