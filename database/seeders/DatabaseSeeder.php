<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Site;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            'name' => 'Nithushan',
            'email' => 'nithushan8@live.com',
            'password' => bcrypt('Nithu125@'),
        ]);

        $sites = [
            [
                'name' => 'insurance nova',
                'taboola_api_client_id' => '1b950ac73a1b49adb47542dfbde9eee7',
                'taboola_api_client_secret' => 'fe3f020b3b3b45ec911a1238e13f302b',
                'taboola_api_account_id' => 'taboolaaccount-tamilserialtodaygmailcom',
                'google_analytics_api_view_id' => '240064866',
            ],
            [
                'name' => 'sample 1',
            ],
            [
                'name' => 'sample 2'
            ],
            [
                'name' => 'sample 3'
            ]
        ];

        foreach ($sites as $site) {
            Site::insert($site);
        }
    }
}
