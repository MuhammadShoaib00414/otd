<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Success - Directory</title>

  <link href="{{ asset('css/main.css') }}" rel="stylesheet">
</head>
<body class="bg-light">

  <main class="main height-100" role="main">
    <div class="container height-100">
      <div class="row align-items-center height-100 w-100">
        <div class="col-sm-12 col-md-10 col-lg-8 mx-auto my-4">

          <div class="text-center">
            <h1 class="h3 mb-4">Success</h1>
          </div>

          <div class="card">
            <div class="card-body">
              <div class="m-sm-4">
                <h4 class="card-title mb-3">Welcome!</h4>
                <p class="mb-3">{!! nl2br(App\Setting::where('name', '=', 'account_created_message')->first()->value) !!}</p>
                <div class="text-center">
                  <a dusk="lets-go" href="/onboarding" class="btn btn-primary">I agree, let's go</a>
                </div>
              </div>
            </div>
          </div><!-- /.card -->
        </div>
      </div><!-- /.row -->
    </div>
  </main>

  <script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
