@extends('layouts.app')

@section('content')

<div class="main-container">
  <section class="bg-lightest-brand text-light">
      <div class="container">
          <!--end of row-->
          <div class="row justify-content-center">
              <div class="col-12 col-md-10 col-lg-8">
                <div class="mb-3">
                  <a href="/home"><i class="icon-chevron-small-left"></i> Your Dashboard</a>
                </div>
                  <form class="card card-sm" method="get" action="/search">
                      <div class="card-body row no-gutters align-items-center">
                          <div class="col-auto">
                              <i class="icon-magnifying-glass h4 text-body"></i>
                          </div>
                          <!--end of col-->
                          <div class="col">
                              <input class="form-control form-control-lg form-control-borderless" type="search" name="q" value="{{ $q }}" placeholder="Search by name, job title, company, city, or search" />
                          </div>
                          <!--end of col-->
                          <div class="col-auto">
                              <button class="btn btn-lg btn-secondary" type="submit">Search</button>
                          </div>
                          <!--end of col-->
                      </div>
                  </form>
                  @if($authUser->groups->count() > 1)
                  <div style="color: #343a40; font-weight: 600;">
                      Showing results from <div class="d-inline-block">
                        <a href="#" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle" style="color: #da7e6f;">{{ $groupDisplay }}</a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                          <a class="dropdown-item" href="/search?q={{ $q }}" style="color: #343a40;">All your groups</a>
                          @foreach($authUser->groups as $group)
                          <a class="dropdown-item" href="/search?group={{ $group->id }}&q={{ $q }}" style="color: #343a40;">{{ $group->name }}</a>
                          @endforeach
                        </div>
                      </div>
                    </div>
                    @endif
              </div>
              <!--end of col-->
          </div>
          <!--end of row-->
      </div>
      <!--end of container-->
  </section>
</div>

  <main class="main" role="main">

    <div class="bg-lightest-brand py-5">
      <div class="container">
        <div class="row justify-content-center align-items-stretch">
          @foreach($results as $result)
          <a href="/users/{{ $result->id }}" class="card col-md-4 mx-1 mb-2 px-3 no-underline" style="flex: 1; min-width: 300px;">
            <div class="card-body d-flex align-items-center justify-content-center">
              <div class="d-flex flex-column align-items-center justify-content-center">
                <div class="mb-2" style="height: 5.5em; width: 5.5em; border-radius: 50%; background-image: url('{{ $result->photo_path }}'); background-size: cover; background-position: center;">
                </div>
                <div class="pt-1 text-center">
                  <span class="d-block" style="color: #343a40; font-weight: 600;">{{ $result->name }}</span>
                  <span class="d-block card-subtitle my-1 text-muted">{{ $result->job_title }}</span>
                </div>
              </div>
            </div>
          </a>
          @endforeach
        </div><!-- /.row -->
      </div>
    </div>
  </main>
@endsection
