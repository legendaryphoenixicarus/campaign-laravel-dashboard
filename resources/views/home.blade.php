@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Campaign advertising sites</div>

                  <div class="card-body">
                      <a href="{{ route('home.create') }}" class="btn btn-primary pull-right">Add new site</a>
                      <br><br>
                      <div class="table-responsive">
                          <table id="example" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>TABOOLA_API_CLIENT_ID</th>
                                    <th>TABOOLA_API_CLIENT_SECRET</th>
                                    <th>TABOOLA_API_ACCOUNT_ID</th>
                                    <th>ANALYTICS_VIEW_ID</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sites as $site)
                                    <tr>
                                        <td><a href="{{ url('/campaigns') }}">{{ $site->name }}</a></td>
                                        <td>{{ $site->taboola_api_client_id }}</td>
                                        <td>{{ $site->taboola_api_client_secret }}</td>
                                        <td>{{ $site->taboola_api_account_id }}</td>
                                        <td>{{ $site->google_analytics_api_view_id }}</td>
                                        <td>
                                            <form action="{{ url('/home/' . $site->id) }}" method="post" class="form-inline">
                                                @csrf
                                                @method('DELETE')
                                                <div class="btn-group">
                                                    <a href="{{ url('/home/' . $site->id . '/edit') }}" class="btn btn-success">Edit</a>
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </div>
                                            </form>
                                        </td>
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#example').DataTable();
        } );
    </script>
@endpush
