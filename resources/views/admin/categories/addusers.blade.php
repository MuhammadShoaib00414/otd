@extends('admin.layout')

@section('page-content')
    <div>
        <div class="d-flex justify-content-between align-items-middle">
            <h5 class="mr-3 mt-1">Edit {{ $taxonomy->name }}</h5>
        </div>
        @if(Session::has('success'))
              <div class="alert alert-primary alert-dismissible fade show" role="alert">
                  {!! Session::get('success') !!}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
          @elseif(Session::has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  {!! Session::get('error') !!}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
          @endif
        <div class="card">
            <div class="card-body">
                <form action="/admin/categories/{{ $taxonomy->id }}/add-users" method="post" id="app">
                    @csrf
                    <div class="form-group">
                      <label for="option">{{ $taxonomy->singular_name }}</label>
                      <select class="custom-select d-block" name="option_id" style="max-width: 600px;">
                        @foreach($taxonomy->ordered_grouped_options as $group => $options)
                          <optgroup label="{{ $group }}">
                            @foreach($options as $option)
                              <option value="{{ $option->id }}">{{ $option->name }}</option>
                            @endforeach
                          </optgroup>
                        @endforeach
                      </select>
                    </div>

                    <hr>
                    <a class="btn btn-primary mt-2 mb-4" data-toggle="collapse" href="#byUser" role="button" aria-expanded="false" aria-controls="byUser">Select Users</a>
                    <a class="btn btn-primary mt-2 ml-2 mb-4" data-toggle="collapse" href="#byGroup" role="button" aria-expanded="false" aria-controls="byGroup">Add By Group</a>
                    <div class="collapse mb-2" id="byUser">
                      <div class="col-md-6 card card-body">
                        @foreach($users as $user)
                            <div class="form-check">
                              <input id="user{{ $user->id }}" type="checkbox" name="users[]" value="{{ $user->id }}">
                              <label class="form-check-label" for="user{{ $user->id }}">
                                {{ $user->name }}
                              </label>
                            </div>
                        @endforeach
                      </div>
                    </div>

                    <div class="collapse mb-2" id="byGroup">
                      <div class="col-md-6 card card-body">
                        <span class="text-muted mb-2">This will add each selected group's users to this group.</span>
                        @each('admin.groups.partials.groupcheckbox', $groups, 'otherGroup')
                      </div>
                    </div>
                    

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Add Users</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection