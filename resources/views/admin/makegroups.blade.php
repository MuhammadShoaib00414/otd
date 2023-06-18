@extends('admin.layout')

@section('page-content')
  <h5>Make Groups Script</h5>
  <hr>
    <form>
      <label for="size">Max users per group</label>
      <div class="input-group mb-3" style="max-width: 250px;">
        <input type="text" value="{{ $maxSize }}" class="form-control" name="size" id="size" aria-describedby="basic-addon3">
        <div class="input-group-append">
          <button type="submit" class="btn btn-primary">Go</button>
        </div>
      </div>
    </form>
  <hr>
    <p>Total groups: {{ count($groups) }}</p>
    <p>Total users: {{ $totalUserCount }} ({{ $missingUserCount }} missing)</p>
  <hr>
  @foreach($groups as $name => $users)
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between">
          <span>
            @if($users->filter(function ($user) { return ($user->groups->count() == 1); })->count() > 0)
            <i class="fas fa-circle mr-2" style="color: red;"></i>
            @else
              <i class="fas fa-circle mr-2" style="color: #12bf12;"></i>
            @endif
           <a href="#" data-toggle="collapse" data-target="#collapse{{ $loop->index }}" aria-expanded="true" aria-controls="collapse{{ $loop->index }}">
            {{ $name }}
          </a>
        </span>
        <span>({{ $users->count() }} users)</span>
      </div>
      <div id="collapse{{ $loop->index }}" class="collapse">
        <table class="table mb-0">
          @foreach($users as $user)
          <tr>
            <td>
              @if($user->groups->count() == 1)
              <span class="badge badge-primary mr-2">1</span>
              @else
              <span class="badge badge-secondary mr-2">{{ $user->groups->count() }}</span>
              @endif
              <a href="/admin/users/{{ $user->id }}">{{ $user->name }}</a>
            </td>
            <td>
              {{ $user->job_title }}
            </td>
            <td>
              {{ $user->company }}
            </td>
            <td>
              Hustles: {{ $user->categories()->limit(3)->orderBy('name', 'desc')->pluck('name')->implode(',') }}
            </td>
            <td>
              Interests: {{ $user->keywords()->limit(3)->orderBy('name', 'desc')->pluck('name')->implode(',') }}
            </td>
            <td>
              Skills: {{ $user->skills()->limit(3)->orderBy('name', 'desc')->pluck('name')->implode(', ') }}
            </td>
            <td class="text-right">
              <a href="/admin/users/{{ $user->id }}/edit">Edit</a> - 
              <a href="/admin/users/{{ $user->id }}">View</a>
            </td>
          </tr>
          @endforeach
        </table>
        <div class="card-body" style="border-top: 1px #eee solid;">
          @foreach($users as $user){{ $user->id }}{{ (!$loop->last) ? ',' : '' }}@endforeach</div>
      </div>
    </div>
  @endforeach
  @if($missingUsers->count())
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between">
        <span>
          @if($missingUsers->filter(function ($user) { return ($user->groups->count() == 1); })->count() > 0)
            <i class="fas fa-circle mr-2" style="color: red;"></i>
          @else
           <i class="fas fa-circle mr-2" style="color: #12bf12;"></i>
          @endif
          <a href="#" data-toggle="collapse" data-target="#collapseMissing" aria-expanded="true" aria-controls="collapseMissing">
            Not Grouped
          </a>
          </span>
        <span>
          ({{ $missingUsers->count() }} users)
        </span>
      </div>
      <div id="collapseMissing" class="collapse">
        <table class="table mb-0">
          @foreach($missingUsers as $user)
          <tr>
            <td>
              @if($user->groups->count() == 1)
              <span class="badge badge-primary mr-2">1</span>
              @else
              <span class="badge badge-secondary mr-2">{{ $user->groups->count() }}</span>
              @endif
              <a href="/admin/users/{{ $user->id }}">{{ $user->name }}</a>
            </td>
            <td>
              {{ $user->job_title }}
            </td>
            <td>
              {{ $user->company }}
            </td>
            <td>
              @if($user->categories()->count())
              <b>Hustles:</b> {{ $user->categories()->limit(3)->orderBy('name', 'desc')->pluck('name')->implode(',') }}
              @endif
            </td>
            <td>
              @if($user->keywords()->count())
              <b>Interests:</b> {{ $user->keywords()->limit(3)->orderBy('name', 'desc')->pluck('name')->implode(',') }}
              @endif
            </td>
            <td>
              @if($user->skills()->count())
              <b>Skills:</b> {{ $user->skills()->limit(3)->orderBy('name', 'desc')->pluck('name')->implode(', ') }}
              @endif
            </td>
            <td class="text-right">
              <a href="/admin/users/{{ $user->id }}/edit">Edit</a> - 
              <a href="/admin/users/{{ $user->id }}">View</a>
            </td>
          </tr>
          @endforeach
        </table>
        <div class="card-body" style="border-top: 1px #eee solid;">
          @foreach($missingUsers as $user){{ $user->id }}{{ (!$loop->last) ? ',' : '' }}@endforeach</div>
        </div>
      </div>
    </div>
  @endif
@endsection