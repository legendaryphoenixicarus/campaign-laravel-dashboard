@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><strong>Rules</strong></div>

                  <div class="card-body">
                      <div class="btn-group pull-right">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">NEW RULE</button>
                        <div class="dropdown-menu">
                            @foreach (config('app.actions') as $value => $action)
                                <a class="dropdown-item" href="{{ url('/rules/create?action_id=' . $value) }}">{{ $action }}</a>
                            @endforeach
                        </div>
                      </div>
                      <br><br>
                      <div class="table-responsive">
                          <table id="example" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Action</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rules as $rule)
                                    <tr>
                                        <td><a href="{{ url('/rules/' . $rule->id . '/edit') }}">{{ $rule->name }}</a></td>
                                        <td>{{ config('app.actions')[$rule->action_id] }}</td>
                                        <td>
                                            <form action="{{ url('/rules/' . $rule->id) }}" method="post" class="form-inline">
                                                @csrf
                                                @method('DELETE')
                                                <div class="btn-group">
                                                    <a href="{{ url('/rules/' . $rule->id . '/edit') }}" class="btn btn-success">Edit</a>
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
