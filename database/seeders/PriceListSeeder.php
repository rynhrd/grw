<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class PriceListSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    DB::table('price_list')->insert([
      [
        'deskripsi' => 'Reguler Wash',
        'product' => 'RW-01',
        'cc_motor' => '110cc',
        'price' => 15000
      ],
      [
        'deskripsi' => 'Reguler Wash',
        'product' => 'RW-02',
        'cc_motor' => '125cc-150cc',
        'price' => 20000
      ],
      [
        'deskripsi' => 'Reguler Wash',
        'product' => 'RW-03',
        'cc_motor' => '250cc >>',
        'price' => 30000
      ],
      [
        'deskripsi' => 'Premium Wash',
        'product' => 'PW-01',
        'cc_motor' => '110cc',
        'price' => 25000
      ],
      [
        'deskripsi' => 'Premium Wash',
        'product' => 'PW-02',
        'cc_motor' => '125cc-150cc',
        'price' => 30000
      ],
      [
        'deskripsi' => 'Premium Wash',
        'product' => 'PW-03',
        'cc_motor' => '250cc >>',
        'price' => 40000
      ]
    ]);
  }
}