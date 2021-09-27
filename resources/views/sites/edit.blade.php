@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <a href="{{ route('home') }}" class="btn btn-dark">Back</a><br><br>
            <div class="card">
                <div class="card-header">Edit site</div>

                    <div class="card-body">
                        <form action="{{ url('/home/' . $site->id) }}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name" class="col-6 control-label">Name: </label>
                                <div class="col-md-12">
                                    <input type="text" name="name" value="{{ $site->name }}" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="taboola_api_client_id" class="col-md-6 control-label">Taboola API CLIENT_ID: </label>
                                <div class="col-md-12">
                                    <input type="text" name="taboola_api_client_id" value="{{ $site->taboola_api_client_id }}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="taboola_api_client_secret" class="col-md-6 control-label">Taboola API CLIENT_SECRET: </label>
                                <div class="col-md-12">
                                    <input type="text" name="taboola_api_client_secret" value="{{ $site->taboola_api_client_secret }}" class="form-control" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="taboola_api_account_id" class="col-md-6 control-label">Taboola API ACCOUNT_ID: </label>
                                <div class="col-md-12">
                                    <input type="text" name="taboola_api_account_id" value="{{ $site->taboola_api_account_id }}" class="form-control" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="google_analytics_api_view_id" class="col-md-6 control-label">Google Analytics API VIEW_ID: </label>
                                <div class="col-md-12">
                                    <input type="text" name="google_analytics_api_view_id" value="{{ $site->google_analytics_api_view_id }}" class="form-control" >
                                </div>
                            </div>
                            <hr>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary pull-right">Save</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
