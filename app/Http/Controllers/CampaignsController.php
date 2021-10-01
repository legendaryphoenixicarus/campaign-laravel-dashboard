<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Analytics;
use Spatie\Analytics\Period;
use Carbon\Carbon;

class CampaignsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the campaigns.
     * 
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function get_campaign_level_summary(Request $request)
    {
        if ($request->has('start_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        } else {
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d');
        }
        
        // init cURL handler
        // $curl = curl_init();

        // $analytics = $this->initializeAnalytics();
        // $response = $this->getReport($analytics);
        // $this->printResults($response);exit;

        // get all campaign_level_reports
        $response = $this->get_campaign_data(
            "https://backstage.taboola.com/backstage/api/1.0/" . env('TABOOLA_API_ACCOUNT_ID') . "/reports/campaign-summary/dimensions/campaign_breakdown?start_date=" . $start_date . "&end_date=" . $end_date . "&include_multi_conversions=true"
        );

        // // get only details about campaign_level_reports
        $campaign_level_reports = json_decode($response)->results;

        // dump($campaign_level_reports);

        // get campaign level summary report
        $response = $this->get_campaign_data(
            "https://backstage.taboola.com/backstage/api/1.0/" . env('TABOOLA_API_ACCOUNT_ID') . "/campaigns"
        );

        $campaigns = json_decode($response)->results;

        // curl_close($curl);

        $startDate = Carbon::createFromDate($start_date);
        $endDate = Carbon::createFromDate($end_date);

        // dd($campaigns);

        $analytics_reports = Analytics::performQuery(
            // Period::days(2),
            Period::create($startDate, $endDate),
            'ga:pageviews',
            [
                'dimensions' => 'ga:campaign',
                // 'filters' => 'ga:landingPagePath!@/admin/',
                'sort' => '-ga:sessions',
                'max-results' => 50000,
                'metrics' => 'ga:sessions, ga:pageviewsPerSession, ga:adsenseRevenue, ga:totalPublisherRevenuePer1000Sessions,  ga:adsenseAdsClicks, ga:adsenseCTR, ga:ROAS, ga:adsenseCoverage, ga:cpc',
            ]
        );

        // dump($analytics_reports);exit;

        $all_data = [];

        $total_summary = [
            'taboola_clicks' => 0,
            'ad_sessions' => 0,
            'ad_clicks' => 0,
            'total_spend' => 0,
            'total_revenue' => 0,
            'profit_lost' => 0,
        ];

        foreach ($campaigns as $key => $campaign) {
            foreach ($campaign_level_reports as $campaign_level_report) {
                if ($campaign_level_report->campaign == $campaign->id) {
                    $campaign_level_report->status = $campaign->is_active;
                    $campaign_level_report->bid = $campaign->cpc;
                    $campaign_level_report->budget = $campaign->daily_cap;
                    // $campaign_level_report->total_spend = $campaign->spent;

                    $all_data[$key] = $campaign_level_report;

                    // total summary
                    $total_summary['taboola_clicks'] += $all_data[$key]->clicks;
                    $total_summary['total_spend'] += $all_data[$key]->spent;
                }
            }


            if (isset($all_data[$key]) && $analytics_reports->rows) {
                
                foreach ($analytics_reports->rows as $analytics_report) {
                    $campaign_name = substr($campaign->tracking_code, strpos($campaign->tracking_code, "utm_campaign=") + 13);
                    // dump($analytics_report[0]);exit;
                    if ($campaign_name == $analytics_report[0]) {
                        // dump($analytics_report[1]);
                        // dd($all_data[$key]);
                        $all_data[$key]->ad_sessions = $analytics_report[1];
                        $all_data[$key]->ad_views_per_session = $analytics_report[2];
                        $all_data[$key]->ad_revenue = round($analytics_report[3], 3);
                        $all_data[$key]->ad_rpm = $analytics_report[4];
                        $all_data[$key]->ad_clicks = $analytics_report[5];
                        $all_data[$key]->ad_ctr = $analytics_report[6];
                        $all_data[$key]->coverage = round($analytics_report[8], 3);
                        $all_data[$key]->ad_cpc = $analytics_report[9];
                        
                        // custom calculations
                        // $all_data[$key]->ad_roas = $analytics_report[7];
                        if ($all_data[$key]->spent) {
                            $all_data[$key]->ad_roas = round($all_data[$key]->ad_revenue / $all_data[$key]->spent, 3) * 100;
                        }
                        $all_data[$key]->profit_lost = $all_data[$key]->ad_revenue - $all_data[$key]->spent;

                        // total summary
                        $total_summary['ad_sessions'] += $all_data[$key]->ad_sessions;
                        $total_summary['ad_clicks'] += $all_data[$key]->ad_clicks;
                        $total_summary['total_revenue'] += $all_data[$key]->ad_revenue;
                        $total_summary['profit_lost'] += $all_data[$key]->profit_lost;
                    }
                }
            }
        }
        // dd($all_data);
        // exit;

        return view('campaigns.campaign_level', [
            "campaign_level_reports" => $all_data,
            "total_summary" => $total_summary,
            "start_date" => $start_date,
            "end_date" => $end_date
        ]);
    }

    /**
     * Show the campaign details.
     *
     * @param int $id
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function get_site_level_summary(Request $request, $id)
    {
        if ($request->has('start_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        } else {
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d');
        }

        // get single campaign
        $response = $this->get_campaign_data(
            "https://backstage.taboola.com/backstage/api/1.0/" . env('TABOOLA_API_ACCOUNT_ID') . "/campaigns/" . $id . "?start_date=" . $start_date . "&end_date=" . $end_date . "&campaign=" . $id,
        );

        // get only details about campaign
        $campaign = json_decode($response);

        // dump($campaign);

        // get site level summary report per the campaign
        $response = $this->get_campaign_data(
            "https://backstage.taboola.com/backstage/api/1.0/" . env('TABOOLA_API_ACCOUNT_ID') . "/reports/campaign-summary/dimensions/site_breakdown?start_date=" . $start_date . "&end_date=" . $end_date . "&campaign=" . $id
        );

        $summary_reports = json_decode($response)->results;

        // dump($summary_reports);

        $startDate = Carbon::createFromDate($start_date);
        $endDate = Carbon::createFromDate($end_date);

        $analytics_reports = Analytics::performQuery(
            Period::create($startDate, $endDate),
            'ga:pageviews',
            [
                'dimensions' => 'ga:campaign, ga:sourceMedium',
                'sort' => '-ga:sessions',
                'max-results' => 50000,
                'metrics' => 'ga:sessions, ga:ROAS, ga:adsenseRevenue, ga:pageviewsPerSession,  ga:adsenseAdsClicks, ga:adsensePageImpressions, ga:adsenseCTR, ga:totalPublisherRevenuePer1000Sessions, ga:adsenseCoverage, ga:cpc',
            ]
        );

        // dump($analytics_reports);exit;
        $total_summary = [
            'taboola_clicks' => 0,
            'ad_sessions' => 0,
            'ad_clicks' => 0,
            'total_spend' => 0,
            'total_revenue' => 0,
            'profit_lost' => 0,
        ];

        $all_data = [];

        foreach ($summary_reports as $key => $summary_report) {
            $summary_report->bid = $campaign->cpc;

            foreach ($campaign->publisher_bid_modifier->values as $publisher_bid_modifier) {
                if ($publisher_bid_modifier->target == $summary_report->site) {
                    $summary_report->bid = $campaign->cpc * $publisher_bid_modifier->cpc_modification;
                    $summary_report->avg_boost = ($publisher_bid_modifier->cpc_modification - 1) * 100;
                }
            }

            foreach ($campaign->publisher_targeting->value as $site) {
                if ($site == $summary_report->site) {
                    $summary_report->blocking_level = "CAMPAIGN";
                }
            }

            $all_data[$key] = $summary_report;

            // total summary
            $total_summary['taboola_clicks'] += $all_data[$key]->clicks;
            $total_summary['total_spend'] += $all_data[$key]->spent;

            foreach ($analytics_reports as $analytics_report) {
                $campaign_name = substr($campaign->tracking_code, strpos($campaign->tracking_code, "utm_campaign=") + 13);
                if ($campaign_name == $analytics_report[0]) {
                    // dump($analytics_report[0]);exit;
                    if ($analytics_report[1] == 'taboola / ' . $summary_report->site) {                    
                        $all_data[$key]->ad_sessions = $analytics_report[2];

                        // swap result columns revenue<->views_per_session
                        $all_data[$key]->ad_views_per_session = $analytics_report[5];
                        $all_data[$key]->ad_revenue = round($analytics_report[4], 3);
                        $all_data[$key]->ad_clicks = $analytics_report[6];
                        $all_data[$key]->ad_impressions = $analytics_report[7];
                        $all_data[$key]->ad_ctr = $analytics_report[8];
                        $all_data[$key]->ad_rpm = $analytics_report[9];
                        $all_data[$key]->coverage = round($analytics_report[10], 3);
                        $all_data[$key]->ad_cpc = $analytics_report[11];

                        // custom calculations
                        // $all_data[$key]->ad_roas = $analytics_report[3];
                        if ($all_data[$key]->spent) {
                            $all_data[$key]->ad_roas = round($all_data[$key]->ad_revenue / $all_data[$key]->spent, 3) * 100;
                        }
                        $all_data[$key]->profit_lost = $all_data[$key]->ad_revenue - $all_data[$key]->spent;

                        // total summary
                        $total_summary['ad_sessions'] += $all_data[$key]->ad_sessions;
                        $total_summary['ad_clicks'] += $all_data[$key]->ad_clicks;
                        $total_summary['total_revenue'] += $all_data[$key]->ad_revenue;
                        $total_summary['profit_lost'] += $all_data[$key]->profit_lost;
                    }
                }
            }
        }

        return view('campaigns.site_level', [
            'campaign' => $campaign,
            "total_summary" => $total_summary,
            'summary_reports' => $all_data,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }

    
    /**
     * Show the publsher details.
     *
     * @param int $id
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function get_account_level_summary(Request $request)
    {
        if ($request->has('start_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        } else {
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d');
        }

        // init cURL handler
        $curl = curl_init();

        // get all publishers
        $response = $this->get_campaign_data(
            "https://backstage.taboola.com/backstage/api/1.0/" . env('TABOOLA_API_ACCOUNT_ID') . "/reports/campaign-summary/dimensions/site_breakdown?start_date=" . $start_date . "&end_date=" . $end_date
        );

        // get only details about publishers
        $publishers = json_decode($response)->results;

        // dump($publishers);exit;
        curl_close($curl);
        
        $startDate = Carbon::createFromDate($start_date);
        $endDate = Carbon::createFromDate($end_date);

        $analytics_reports = Analytics::performQuery(
            Period::create($startDate, $endDate),
            'ga:pageviews',
            [
                'dimensions' => 'ga:medium',
                'sort' => '-ga:sessions',
                'max-results' => 50000,
                'metrics' => 'ga:sessions, ga:ROAS, ga:pageviewsPerSession, ga:adsenseRevenue,  ga:adsenseAdsClicks, ga:adsensePageImpressions, ga:adsenseCTR, ga:totalPublisherRevenuePer1000Sessions, ga:adsenseCoverage, ga:cpc',
            ]
        );

        // dump($analytics_reports);exit;
        $total_summary = [
            'taboola_clicks' => 0,
            'ad_sessions' => 0,
            'ad_clicks' => 0,
            'total_spend' => 0,
            'total_revenue' => 0,
            'profit_lost' => 0,
        ];

        foreach ($publishers as $key => $publisher) {
            foreach ($analytics_reports as $analytics_report) {
                if ($publisher->site == $analytics_report[0]) {
                    $publisher->ad_sessions = $analytics_report[1];
                    // $publisher->ad_roas = $analytics_report[2];
                    // swap result columns revenue<->views_per_session
                    $publisher->ad_views_per_session = $analytics_report[4];
                    $publisher->ad_revenue = round($analytics_report[4], 3);
                    $publisher->ad_clicks = $analytics_report[5];
                    $publisher->ad_impressions = $analytics_report[6];
                    $publisher->ad_ctr = $analytics_report[7];
                    $publisher->ad_rpm = $analytics_report[8];
                    $publisher->coverage = round($analytics_report[9], 3);
                    $publisher->ad_cpc = $analytics_report[10];
                    
                    // custom calculations
                    if ($publisher->spent) {
                        $publisher->ad_roas = round($publisher->ad_revenue / $publisher->spent, 3) * 100;
                    }
                    $publisher->profit_lost = $publisher->ad_revenue - $publisher->spent;

                    $total_summary['taboola_clicks'] += $publisher->clicks;
                    $total_summary['total_spend'] += $publisher->spent;
                    $total_summary['ad_sessions'] += $publisher->ad_sessions;
                    $total_summary['ad_clicks'] += $publisher->ad_clicks;
                    $total_summary['total_revenue'] += $publisher->ad_revenue;
                    $total_summary['profit_lost'] += $publisher->profit_lost;
                }
            }
        }

        return view('campaigns.account_level', [
            "publishers" => $publishers,
            "total_summary" => $total_summary,
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
    }


    /**
     * Get all/a campaign data
     *
     * @param string $url
     * @return \Illuminate\Http\Response
     */
    private function get_campaign_data($url)
    {
        // init curl handler
        $curl = curl_init();

        $access_token = $this->get_taboola_access_token();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $access_token
            ],
        ]);

        $response = curl_exec($curl);
        
        if ($err = curl_error($curl)) {
            echo "cURL Error #:" . $err . "\n";

            return false;
        }

        return $response;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_campaign_level(Request $request, $id)
    {
        $data = json_encode($request->body);

        $response = $this->update_campaign($data, "POST", $id);

        return response()->json([
            'state' => 'success',
            'result' => $response
        ]);
    }

    /**
     * Patch the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_bid_modifiers(Request $request, $id)
    {
        $curl = curl_init();

        // dump($request->all());exit;

        $access_token = $this->get_taboola_access_token();

        // get single campaign
        $response = $this->get_campaign_data(
            "https://backstage.taboola.com/backstage/api/1.0/" . env('TABOOLA_API_ACCOUNT_ID') . "/campaigns/" . $id
        );

        // get only details about campaign
        $campaign = json_decode($response);

        $patch_operation = "ADD";

        foreach ($campaign->publisher_bid_modifier->values as $publisher_bid_modifier) {
            if ($publisher_bid_modifier->target == $request->target) {
                $patch_operation = "REPLACE";
                break;
            }
        }

        $data = json_encode([
            "patch_operation" => $patch_operation,
            "publisher_targeting" => null,
            "auto_publisher_targeting" => null,
            "publisher_bid_modifier" =>  [
                "values" => [
                    [
                        "target" => $request->target,
                        "cpc_modification" => $request->cpc_modification,
                    ]
                ]
            ]
        ]);

        $response = $this->update_campaign($data, "PATCH", $id);

        return response()->json([
            'state' => 'success',
            'result' => $response
        ]);
    }

    /**
     * Patch the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_publisher_targeting(Request $request, $id)
    {
        // get current campaign
        $response = $this->get_campaign_data(
            "https://backstage.taboola.com/backstage/api/1.0/" . env('TABOOLA_API_ACCOUNT_ID') . "/campaigns/" . $id
        );

        // get only details about campaign
        $campaign = json_decode($response);

        // request data
        $data = json_encode([
            "patch_operation" => $request->patch_operation,
            "publisher_targeting" => [
                // "type" => "EXCLUDE",
                // "value" => $request->publishers,
                // "href" => null
                "publishers" => $request->publishers
            ]
        ]);

        // dump($data);exit;

        $response = $this->update_campaign($data, "PATCH", $id);

        return response()->json([
            'result' => $response
        ]);
    }

    /**
     * Block publishers at account level
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     */
    public function block_publishers(Request $request)
    {
        // $url = "https://backstage.taboola.com/backstage/api/1.0/" . env('TABOOLA_API_ACCOUNT_ID') . "/block-publisher";

        // $response = $this->get_campaign_data($url);

        // dump($request->all());exit;

        $curl = curl_init();
        $access_token = $this->get_taboola_access_token();

        // // request data
        // $data = json_encode([
        //     "patch_operation" => $request->patch_operation,
        //     "sites" => [
        //         // "type" => "EXCLUDE",
        //         // "value" => $request->publishers,
        //         // "href" => null
        //         "publishers" => $request->publishers
        //     ]
        // ]);

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://backstage.taboola.com/backstage/api/1.0/" . env('TABOOLA_API_ACCOUNT_ID') . "/block-publisher",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PATCH",
            CURLOPT_POSTFIELDS => json_encode($request->all()),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $access_token,
                "Accept: application/json",
                "Content-Type: application/json",
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;

            return response()->json([
                "state" => "failed",
                "result" => $err
            ]);
        }

        return response()->json([
            "state" => "suceess",
            "result" => $response
        ]);
    }

    /**
     * Configuration for account credentials
     */
    public function config_data()
    {
        \App\Models\User::truncate();
        // $users = \App\Models\User::all();

        // dd($users);

        return redirect('/');
    }

    /**
     * Patch the specified resource in storage.
     *
     * @param  JSON $data
     * @param  string $request_method
     * @param  int $campaign_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function update_campaign($data, $request_method, $campaign_id)
    {
        $curl = curl_init();

        $access_token = $this->get_taboola_access_token();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://backstage.taboola.com/backstage/api/1.0/" . env('TABOOLA_API_ACCOUNT_ID') . "/campaigns/" . $campaign_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $request_method,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $access_token,
                "Accept: application/json",
                "Content-Type: application/json",
            ],
        ]);

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;

            return false;
        }

        return $response;
    }

    
    /**
     * Get access token from taboola API with client credentials
     *
     * @param int $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    private function get_taboola_access_token()
    {
        // init cURL handler
        $curl = curl_init();

        // get access_token with client credentials
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://backstage.taboola.com/backstage/oauth/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "client_id=" . env('TABOOLA_API_CLIENT_ID') . "&client_secret=" . env('TABOOLA_API_CLIENT_SECRET') . "&grant_type=client_credentials",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/x-www-form-urlencoded"
            ],
        ]);

        $response = curl_exec($curl);

        if ($err = curl_error($curl)) {
            echo "cURL Error #:" . $err . "\n";

            curl_close($curl);

            return false;
        }

        curl_close($curl);

        // get access_token
        $access_token = json_decode($response)->access_token;

        return $access_token;
    }

    // // Load the Google API PHP Client Library.
    // require_once __DIR__ . '/vendor/autoload.php';


    // /**
    //  * Initializes an Analytics Reporting API V4 service object.
    //  *
    //  * @return An authorized Analytics Reporting API V4 service object.
    //  */
    // function initializeAnalytics()
    // {

    //     // Use the developers console and download your service account
    //     // credentials in JSON format. Place them in this directory or
    //     // change the key file location if necessary.
    //     $KEY_FILE_LOCATION = config('analytics.service_account_credentials_json');

    //     // Create and configure a new client object.
    //     $client = new Google_Client();
    //     $client->setApplicationName("Hello Analytics Reporting");
    //     $client->setAuthConfig($KEY_FILE_LOCATION);
    //     $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
    //     $analytics = new Google_Service_AnalyticsReporting($client);

    //     return $analytics;
    // }


    // /**
    //  * Queries the Analytics Reporting API V4.
    //  *
    //  * @param service An authorized Analytics Reporting API V4 service object.
    //  * @return The Analytics Reporting API V4 response.
    //  */
    // function getReport($analytics) {

    //     // Replace with your view ID, for example XXXX.
    //     $VIEW_ID = env('ANALYTICS_VIEW_ID');

    //     // Create the DateRange object.
    //     $dateRange = new Google_Service_AnalyticsReporting_DateRange();
    //     $dateRange->setStartDate("7daysAgo");
    //     $dateRange->setEndDate("today");

    //     // Create the Metrics object.
    //     $sessions = new Google_Service_AnalyticsReporting_Metric();
    //     $sessions->setExpression("ga:sessions");
    //     $sessions->setAlias("sessions");

    //     // Create the ReportRequest object.
    //     $request = new Google_Service_AnalyticsReporting_ReportRequest();
    //     $request->setViewId($VIEW_ID);
    //     $request->setDateRanges($dateRange);
    //     $request->setMetrics(array($sessions));

    //     $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
    //     $body->setReportRequests( array( $request) );
    //     return $analytics->reports->batchGet( $body );
    // }


    // /**
    //  * Parses and prints the Analytics Reporting API V4 response.
    //  *
    //  * @param An Analytics Reporting API V4 response.
    //  */
    // function printResults($reports) {
    //     for ( $reportIndex = 0; $reportIndex < count( $reports ); $reportIndex++ ) {
    //         $report = $reports[ $reportIndex ];
    //         $header = $report->getColumnHeader();
    //         $dimensionHeaders = $header->getDimensions();
    //         $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
    //         $rows = $report->getData()->getRows();

    //         for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
    //             $row = $rows[ $rowIndex ];
    //             $dimensions = $row->getDimensions();
    //             $metrics = $row->getMetrics();
    //             for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
    //                 print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "\n");
    //             }

    //             for ($j = 0; $j < count($metrics); $j++) {
    //                 $values = $metrics[$j]->getValues();
    //                 for ($k = 0; $k < count($values); $k++) {
    //                     $entry = $metricHeaders[$k];
    //                     print($entry->getName() . ": " . $values[$k] . "\n");
    //                 }
    //             }
    //         }
    //     }
    // }

}
