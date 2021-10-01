@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <a href="{{ route('rules') }}" class="btn btn-dark">Back</a><br><br>
            <div class="card">
                <div class="card-header">Add new Rule</div>

                    <div class="card-body">
                        <form action="{{ url('/rules') }}" method="post" class="form-horizontal">
                            @csrf
                            <div class="form-group">
                                <label for="name" class="col-6 control-label">Name: </label>
                                <div class="col-md-12">
                                    <input type="text" id="name" name="name" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="action_id" class="col-6 control-label">Action: </label>
                                <div class="col-md-12">
                                    <input type="text" id="action_id" name="action_id" value="{{ config('app.actions')[$action_id] }}" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-6 control-label">If</label>
                                <div class="col-md-12 form-inline">
                                    <div class="inline-block">
                                        <select name="conditions[][source]" class="form-control">
                                            <option value="campaign.cost" selected>Campaign Spend</option>
                                        </select>
                                    </div>&nbsp
                                    <div class="inline-block">
                                        <select name="conditions[][operation]" class="form-control">
                                            <option value=">" selected>> greater than</option>
                                        </select>
                                    </div>&nbsp
                                    <div class="inline-block">
                                        <input type="text" name="conditions[][value]" value="10.00" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-6 control-label">And</label>
                                <div class="col-md-12 form-inline">
                                    <div class="inline-block">
                                        <select name="conditions[][source]" class="form-control" required>
                                            <option selected>Select Condition Source</option>
                                            @foreach (config('app.condition_resources') as $value => $resource)
                                                <option value="{{ $value }}">{{ $resource }}</option>
                                            @endforeach
                                        </select>
                                    </div>&nbsp
                                    <div class="inline-block">
                                        <select name="conditions[][operation]" class="form-control" required>
                                            <option selected>Select Condition Operation</option>
                                            @foreach (config('app.condition_operations') as $value => $operation)
                                                <option value="{{ $value }}">{{ $operation }}</option>
                                            @endforeach
                                        </select>
                                    </div>&nbsp
                                    <div class="inline-block">
                                        <input type="text" name="conditions[][value]" value="10.00" class="form-control" required>
                                    </div>
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
