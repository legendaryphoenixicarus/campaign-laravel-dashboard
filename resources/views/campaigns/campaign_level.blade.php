@extends('layouts.app')

<!-- @push('styles')
<style>
  .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20px; }
  .toggle.ios .toggle-handle { border-radius: 20px; }
</style>

@endpush -->
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
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
                <div class="card-header">Campaign level reports</div>

                  <div class="card-body">
                      
                      <div class="table-responsive">
                          <!-- Button trigger modal -->
                          <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#columnsModal">Filter Columns</button><br><br>
                          <table id="example" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Name</th>
                                    <th>Bid</th>
                                    <th>Budget</th>
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
                                    <th>AdSense CPC</th>
                                    <th>Taboola CTR</th>
                                    <th>AdSense CTR</th>
                                    <th>Coverage</th>
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
                                    <th class="bg-primary">Ads RPM</th>
                                    <th>Roas</th>
                                    <!-- <th>Pageviews per session</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($campaign_level_reports as $index => $campaign_level_report)
                                <tr class="campaign @if(isset($campaign_level_report->profit_lost)) {{ $campaign_level_report->profit_lost > 0 ? 'table-success' : 'table-danger' }} @endif">
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input toggle-status" id="switch{{ $index }}" name="example" @if(isset($campaign_level_report->status) && $campaign_level_report->status) {{'checked'}} @endif />
                                            <label class="custom-control-label" for="switch{{ $index }}">{{ $campaign_level_report->status ? 'ON' : 'OFF' }}</label>
                                        </div>
                                    </td>
                                    <td class="campaign-id" data-campaign-id="{{ $campaign_level_report->campaign }}"><a href="{{ url('/campaigns/' . $campaign_level_report->campaign . '/site_level?start_date=' . $start_date . '&end_date=' . $end_date ) }}">{{ $campaign_level_report->campaign_name }}</a></td>
                                    <td>
                                        <div class="flex flex-row justify-center" style="display: flex; flex-direction: row;">
                                            <input type="text" value="@if(isset($campaign_level_report->bid)){{ $campaign_level_report->bid }}@endif" class="form-control bid-value" style="width:80px">
                                            <button type="button" class="btn btn-sm btn-primary set-bid">Set</button>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex; flex-direction: row;">
                                            <input type="text" value="@if(isset($campaign_level_report->budget)){{ $campaign_level_report->budget }}@endif"  class="form-control budget-value" style="width:80px">
                                            <button type="button" class="btn btn-sm btn-primary set-budget">Set</button>
                                        </div>
                                    </td>
                                    <td>{{ $campaign_level_report->clicks }}</td>
                                    <td>@if(isset($campaign_level_report->ad_sessions)) {{ $campaign_level_report->ad_sessions }}@endif</td>
                                    <td>@if(isset($campaign_level_report->ad_clicks)) {{ $campaign_level_report->ad_clicks }}@endif</td>

                                    <!-- taboola actual cpc -->
                                    <td>{{ $campaign_level_report->cpc }}</td>

                                    <!-- ads cpc -->
                                    <td>@if(isset($campaign_level_report->ad_clicks)) @if ($campaign_level_report->ad_clicks) {{ round($campaign_level_report->ad_revenue / $campaign_level_report->ad_clicks, 3) }} @else {{ 0 }} @endif @endif</td>
                                    <!-- <td>@if(isset($campaign_level_report->ad_cpc)) {{ $campaign_level_report->ad_cpc }} @endif</td> -->

                                    <td>{{ round($campaign_level_report->ctr, 3) . ' %' }}</td>
                                    <td>@if(isset($campaign_level_report->ad_ctr)) {{ round($campaign_level_report->ad_ctr, 2) . ' %' }}@endif</td>
                                    <td>@if(isset($campaign_level_report->coverage)) {{ $campaign_level_report->coverage }} @endif</td>
                                    <td>{{ $campaign_level_report->spent }}</td>
                                    <td>@if(isset($campaign_level_report->ad_revenue)) {{ $campaign_level_report->ad_revenue }}@endif</td>
                                    
                                    <!-- profit/lost -->
                                    <td>@if(isset($campaign_level_report->profit_lost)) {{ $campaign_level_report->profit_lost }} @endif</td>

                                    <td class="bg-info">@if(isset($campaign_level_report->ad_rpm)) {{ round($campaign_level_report->ad_rpm, 2) }}@endif</td>
                                    <td>@if(isset($campaign_level_report->ad_roas)) {{ $campaign_level_report->ad_roas . ' %' }}@endif</td>
                                    <!-- <td>@if(isset($campaign_level_report->ad_views_per_session)) {{ round($campaign_level_report->ad_views_per_session, 2) }}@endif</td> -->
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
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
            window.location.href = '/campaigns?start_date=' + startDate + '&end_date=' + endDate;
        });

        // change bid(cpc)
        $(document).on('click', '.set-bid', function () {
            const bid = $(this).closest('td').find('.bid-value').val();
            const campaign = $(this).closest('tr').find('.campaign-id').data('campaign-id');

            sendAjaxRequest('/campaigns/' + campaign, "PUT", {
                "body" : {
                    'cpc' : bid
                },
            });
        });

        // change budget(daily_cap)
        $(document).on('click', '.set-budget', function () {
            const budget = $(this).closest('td').find('.budget-value').val();
            const campaign = $(this).closest('tr').find('.campaign-id').data('campaign-id');

            sendAjaxRequest('/campaigns/' + campaign, "PUT", {
                "body" : {
                    'daily_cap' : budget
                },
            });
        });

        $(document).on('change', '.toggle-status', function() {
            const campaign = $(this).closest('tr').find('.campaign-id').data('campaign-id');

            sendAjaxRequest('/campaigns/' + campaign, "PUT", {
                "body" : {
                    'is_active' : $(this).prop('checked')
                }
            });
        })
        
        // save filtered columns in the database
        $(document).on('click', '#set-filtered-columns', function () {
            var form = $('#columns-form');

            var formData = form.serializeArray();
            console.log(formData);

            sendAjaxRequest('/sources/campaign_lvl', 'PUT', formData)
        });

        function sendAjaxRequest(url, method, data) {
            $("#overlay").fadeIn(300);???
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