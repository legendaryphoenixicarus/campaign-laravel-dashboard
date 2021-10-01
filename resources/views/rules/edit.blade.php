@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <a href="{{ route('rules') }}" class="btn btn-dark">Back</a><br><br>
            <div class="card">
                <div class="card-header">Edit Rule</div>

                    <div class="card-body">
                        <form action="{{ url('/rules/' . $rule->id) }}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name" class="col-6 control-label">Name: </label>
                                <div class="col-md-12">
                                    <input type="text" name="name" value="{{ $rule->name }}" class="form-control" required>
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
