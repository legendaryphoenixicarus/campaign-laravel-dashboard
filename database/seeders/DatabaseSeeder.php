<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Site;
use App\Models\Source;

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

        $columns = [
            [
                'page_id' => 'campaign_lvl',
                'columns' => 'status,campaign_name,bid,budget,clicks,sessions,ad_clicks,cpc,ad_cpc,ctr,ad_ctr,coverage,spent,ad_revenue,profit_lost,ad_rpm,ad_roas'
            ],
            [
                'page_id' => 'site_lvl',
                'columns' => 'blocking_level,site_name,clicks,ad_sessions,ad_clicks,cpc,ad_cpc,ctr,ad_ctr,coverage,spent,ad_revenue,profit_lost,ad_rpm,ad_roas,bid,avg_boost'
            ],
            [
                'page_id' => 'account_lvl',
                'columns' => 'blocking_level,site_name,clicks,ad_sessions,ad_clicks,cpc,ad_cpc,ctr,ad_ctr,coverage,spent,ad_revenue,profit_lost,ad_rpm,ad_roas'
            ]
        ];
        
        foreach ($columns as $column) {
            Source::insert($column);
        }
    }
}
