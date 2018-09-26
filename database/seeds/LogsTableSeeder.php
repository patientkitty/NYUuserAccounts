<?php
/**
 * Created by PhpStorm.
 * User: ss9545
 * Date: 26/09/2018
 * Time: 11:12 AM
 */
use Illuminate\Database\Seeder;
use App\Models\Logs;

class LogsTableSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 3; $i++) {
            Logs::create([
                'NetID' => 'ss9545' . $i,
                'userName' => 'Sam Shen',
            ]);
        }
    }
}