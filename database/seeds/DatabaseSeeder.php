<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $data = [];
        foreach(range(1, 20) as $i)
        {
            $name = Str::random(10 + $i);
            $date = now()->sub(new \DateInterval(sprintf('P%dD', $i)));
            $data[] = ['name' => $name, 'created_at' => $date, 'updated_at' => $date];
        }
        DB::table('folders')->insert($data);
    }
}
