@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/campaigns') }}">Campaign level Summary</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Site level Campaign Summary</li>
                </ol>
            </nav>
            <div class="row">
                <div class="offset-md-8 col-md-4">
                    <div class="pull-right">
                        <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span></span> <i class="fa fa-caret-down"></i>
                        </div>
                        <span id="start-date" data-start-date="{{ $start_date }}"></span>
                        <span id="end-date" data-end-date="{{ $end_date }}"></span>
                    </div>
                </div>
            </div><br>
            <div class="card">
                <div class="card-header" id="campaign-id" data-campaign-id="{{ $campaign->id }}">{{ $campaign->name }}</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#columnsModal">Filter Columns</button><br><br>
                        <table id="example" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Blocking State</th>
                                    <th>Site</th>
                                    <!-- <th>Viewable Impressions</th> -->
                                    <th>
                                        Taboola Clicks
                                        ({{ $total_summary['taboola_clicks'] }})
                                    </th>
                                    <th>
                                        Sessions
                                        ({{ $total_summary['ad_sessions'] }})
                                    </th>
                                    <th>
                                        AdSense Clicks
                                        ({{ $total_summary['ad_clicks'] }})
                                    </th>
                                    <th>Actual CPC</th>
                                    <th>Ads CPC</th>
                                    <th>Taboola CTR</th>
                                    <th>AdSense CTR</th>
                                    <th>Coverage</th>
                                    <!-- <th>vCTR</th> -->
                                    <!-- <th>Conversion Rate</th> -->
                                    <!-- <th>Conversions</th> -->
                                    <!-- <th>CPA</th> -->
                                    <!-- <th>vCPM</th> -->
                                    <!-- <th>CPM</th> -->
                                    <th>
                                        Total Spend
                                        ({{ $total_summary['total_spend'] }})
                                    </th>
                                    <th>
                                        Total Revenue
                                        ({{ $total_summary['total_revenue'] }})
                                    </th>
                                    <th class="bg-{{ $total_summary['profit_lost'] < 0 ? 'danger' : 'success' }}">
                                        Profit/Lost
                                        @if ($total_summary['profit_lost'] < 0)
                                            ({{ $total_summary['profit_lost'] }})
                                        @else
                                            {{ $total_summary['profit_lost'] }}
                                        @endif
                                    </th>
                                    <th class="bg-primary">AdSense RPM</th>
                                    <th>Roas</th>
                                    <th>Bid</th>
                                    <th>Avg Boost</th>
                                    <!-- <th>Pageviews per session</th> -->
                                    <!-- <th>AdSense Page Impressions</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($summary_reports as $index => $summary_report)
                                    <tr data-site="{{ $summary_report->site }}" class="@if(isset($summary_report->profit_lost)) {{ $summary_report->profit_lost > 0 ? 'table-success' : 'table-danger' }} @endif">
                                        <td>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input toggle-status" id="switch{{ $index }}" name="example" @if($summary_report->blocking_level == "NONE") {{'checked'}}@endif />
                                                <label class="custom-control-label" for="switch{{ $index }}">{{ $summary_report->blocking_level }}</label>
                                            </div>
                                        </td>
                                        <td class="site-name">{{ $summary_report->site_name }}</td>
                                        <!-- <td>{{ $summary_report->visible_impressions }}</td> -->
                                        <td>{{ $summary_report->clicks }}</td>
                                        <td>@if(isset($summary_report->ad_sessions)) {{ $summary_report->ad_sessions }}@endif</td>
                                        <td>@if(isset($summary_report->ad_clicks)) {{ $summary_report->ad_clicks }}@endif</td>
                                        <td>{{ $summary_report->cpc }}</td>

                                        <!-- ads cpc -->
                                        <td>@if(isset($summary_report->ad_clicks)) @if ($summary_report->ad_clicks) {{ round($summary_report->ad_revenue / $summary_report->ad_clicks, 3) }} @else {{ 0 }} @endif @endif</td>
                                        <!-- <td>@if(isset($summary_report->ad_cpc)) {{ $summary_report->ad_cpc }}@endif</td> -->
                                        
                                        <!-- <td>{{ round($summary_report->vctr, 2) }}</td> -->
                                        <td>{{ round($summary_report->ctr, 3) . ' %' }}</td>
                                        <td>@if(isset($summary_report->ad_ctr)) {{ round($summary_report->ad_ctr, 2) . ' %' }}@endif</td>
                                        <td>@if(isset($summary_report->coverage)) {{ $summary_report->coverage }} @endif</td>
                                        
                                        <!-- <td>{{ $summary_report->cpa_conversion_rate }}</td> -->
                                        <!-- <td>{{ $summary_report->cpa_actions_num }}</td> -->
                                        <!-- <td>{{ $summary_report->cpa_clicks }}</td> -->
                                        <!-- <td>{{ $summary_report->vcpm }}</td> -->
                                        <!-- <td>{{ $summary_report->cpm }}</td> -->
                                        <td>{{ $summary_report->spent }}</td>
                                        <td>@if(isset($summary_report->ad_revenue)) {{ round($summary_report->ad_revenue, 3) }} @endif</td>
                                        
                                        <!-- profit/lost -->
                                        <td>@if(isset($summary_report->profit_lost)) {{ $summary_report->profit_lost }} @endif</td>
                                        
                                        <td class="bg-info">@if(isset($summary_report->ad_rpm)) {{ round($summary_report->ad_rpm, 3) }}@endif</td>
                                        <td>@if(isset($summary_report->ad_roas)) {{ $summary_report->ad_roas . ' %' }}@endif</td>
                                        <td class="bid" data-current-boost="{{ $summary_report->avg_boost }}">
                                            {{ round($summary_report->bid, 3, PHP_ROUND_HALF_UP) }}
                                        </td>
                                        <td class="flex flex-row justify-center" style="display: flex; flex-direction: row; width: 250px;">
                                            <input data-suffix="%" value="{{ $summary_report->avg_boost }}" class="avg-boost" min="-100" max="100" type="number" data-decimals="2" step="10"/>
                                            <button type="button" class="btn btn-sm btn-primary set-cpc-modification">Set</button>
                                        </td>
                                        <!-- <td>@if(isset($summary_report->ad_views_per_session)) {{ round($summary_report->ad_views_per_session, 3) }}@endif</td> -->
                                        <!-- <td>@if(isset($summary_report->ad_impressions)) {{ $summary_report->ad_impressions }}@endif</td> -->
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer"></div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade bd-example-modal-sm" id="columnsModal" tabindex="-1" role="dialog" aria-labelledby="columnsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="columnsModalLabel">Filterable columns</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <form action="/sources" id="columns-form" method="post">
                @csrf
                @method('PUT')
                @foreach ($filterable_columns as $column => $column_name)
                    <div class="form-check">
                        <label class="form-check-label" for="{{ $column }}">
                            <input type="checkbox" class="form-check-input toggle-vis" id="{{ $column }}" name="columns[]" value="{{ $column }}" data-column="{{ $loop->index }}" @if ($sources->contains($column)) {{ 'checked' }} @endif>{{ $column_name }}
                        </label>
                    </div>
                @endforeach
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" id="set-filtered-columns" class="btn btn-primary" data-dismiss="modal">Save changes</button>
        </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // input spinner
        $("input[type='number']").inputSpinner();
    
        // datatables
        var table = $('#example').DataTable({
            "pageLength": 20,
            "lengthMenu": [ 10, 20, 50, 75, 100 ]
        });

        function setColumnVisible(check) {
            // Get the column API object
            var column = table.column( $(check).attr('data-column') );

            // Toggle the visibility
            column.visible( $(check).prop('checked') );
        }

        // get all filtered columns from database and set visible of columns according to the filtered columns
        $('.toggle-vis').each(function () {
            setColumnVisible(this);
        })

        // filter columns
        $('.toggle-vis').on( 'click', function (e) {
            setColumnVisible(this);
        });

        // daterangepicker
        // var start = moment().subtract(29, 'days');
        var start = moment($('#start-date').data('start-date'));
        var end = moment($('#end-date').data('end-date'));

        function cb(start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange').daterangepicker({
            startDate:start,
            endDate: end,
            ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);

        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
            var startDate = picker.startDate.format('YYYY-MM-DD');
            var endDate = picker.endDate.format('YYYY-MM-DD');

            // redirect to this page
            window.location.href = '/campaigns/' + $('#campaign-id').data('campaign-id') + '/site_level?start_date=' + startDate + '&end_date=' + endDate;
        });

        
        $(document).on('change', '.toggle-status', function() {
            const campaign = $('#campaign-id').data('campaign-id');
            const site = $(this).closest('tr').data('site');
            var patchOperation = "ADD";
            if ($(this).prop('checked')) {
                patchOperation = "REMOVE";
            }

            sendAjaxRequest('/campaigns/' + campaign, "POST", {
                "publishers" : [
                    site
                ],
                "patch_operation" : patchOperation
            });
        });

        $(document).on('click', '.set-cpc-modification', function() {
            const campaign = $('#campaign-id').data('campaign-id');
            const site = $(this).closest('tr').data('site');
            const avgBoost = $(this).closest('td').find('.avg-boost').val();
            const cpcModification = 1 + avgBoost / 100;
            
            // change bid value for updated value
            const bid = $(this).closest('tr').find('.bid').text();
            const previousBoost = $(this).closest('tr').find('.bid').data('current-boost');
            $(this).closest('tr').find('.bid').html(round(bid / (1 + previousBoost) * cpcModification));
            
            sendAjaxRequest('/campaigns/' + campaign, "PATCH", {
                "target": site,
                "cpc_modification": cpcModification
            });
        })
        
        // save filtered columns in the database
        $(document).on('click', '#set-filtered-columns', function () {
            var form = $('#columns-form');

            var formData = form.serializeArray();
            console.log(formData);

            sendAjaxRequest('/sources/site_lvl', 'PUT', formData)
        });

        function sendAjaxRequest(url, method, data) {
            $("#overlay").fadeIn(300);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: method,
                url: url,
                data: data,
                complete: function (response) {
                    setTimeout(function(){
                        $("#overlay").fadeOut(300);
                    },500);
                }
            });
        };
    });
</script>
@endpush