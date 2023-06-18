@extends('admin.groups.layout')

@section('inner-page-content')
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-md-3 text-center">
          <span style="font-size: 3em;">{{ $group->users->count() }}</span>
          <h6 class="title-decorative">Members</h6>
        </div>
        <div class="col-md-3 text-center">
          <span style="font-size: 3em;">{{ $group->events->count() }}</span>
          <h6 class="title-decorative">Events</h6>
        </div>
        <div class="col-md-3 text-center">
          <span style="font-size: 3em;">{{ $group->textPosts->count() }}</span>
          <h6 class="title-decorative">Posts</h6>
        </div>
        <div class="col-md-3 text-center">
          <span style="font-size: 3em;">{{ $group->shoutouts->count() }}</span>
          <h6 class="title-decorative">Shoutouts</h6>
        </div>
      </div>
    </div>
  </div>

  <!-- <div class="row mt-3">
    <div class="col-md-12">
      <p><b>Group Leaderboard</b> (Past 30 Days)</p>
      <table class="table">
        <tr>
          <td></td>
          <td><b>Name</b></td>
          <td><b>Location</b></td>
          <td class="text-center"><b>Points</b></td>
        </tr>
        @if(false)
        @foreach($leaderboard as $spot)
        <tr>
          <td style="vertical-align: middle;">{{ $loop->iteration }}.</td>
          <td style="vertical-align: middle;">
            <a href="/admin/users/{{ $spot->user->id }}" class="d-flex align-items-center">
              <div style="height: 2em; width: 2em; border-radius: 50%; background-image: url('{{ $spot->user->photo_path }}'); background-size: cover; background-position: center;"></div>
              <div class="ml-3">
                <b>{{ $spot->user->name }}</b><br>
                {{ $spot->user->job_title }}
              </div>
            </a>
          </td>
          <td style="vertical-align: middle;" class="text-center">
            {{ $spot->location }}
          </td>
          <td style="vertical-align: middle;" class="text-center">
            {{ $spot->total }}
          </td>
        </tr>
        @endforeach
        @endif
      </table>
    </div>
  </div> -->
@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" integrity="sha256-Uv9BNBucvCPipKQ2NS9wYpJmi8DTOEfTA/nH2aoJALw=" crossorigin="anonymous"></script>
  <script>
    var ctx = document.getElementById('myChart');
    var myChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: JSON.parse('{!! json_encode($activity->keys()) !!}'),
        datasets: [{
          label: "",
          data: JSON.parse('{!! json_encode($activity->values()) !!}'),
          backgroundColor: "rgba(149,179,209,0.2)",
          borderColor: "rgba(48,99,150,1)",
          pointBackgroundColor: "rgba(48,99,150,1)",
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(151,187,205,1)",
        }],
      },
      options: {
        legend: {
          display: false,
        },
        title: {
          display: true,
          text: 'Group Activity (Past 14 days)'
        }
      }
  });
  </script>
@endsection