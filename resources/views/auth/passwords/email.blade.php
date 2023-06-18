<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Reset Password | OTD</title>

  <link href="{{ asset('css/main.css') }}" rel="stylesheet">
  @include('partials.themestyles')
</head>
<body class="bg-lightest-brand">
  <main class="main h-100" role="main">
    <div class="container h-100">
      <div class="row align-items-center h-100">
        <div class="col-sm-10 col-md-8 col-lg-6 mx-auto my-4">

          <div class="text-center mt-5">
            <h1 class="h3">Reset password</h1>
            <p class="lead">
              Enter your email to reset your password.
            </p>
          </div>


          <div class="card mt-4">
            <div class="card-body">
              <div class="m-sm-4">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('password.email') }}">
                  @csrf
                  <div class="form-group">
                    <label>Email</label>
                    <input class="form-control form-control-lg{{ $errors->has('email') ? ' is-invalid' : '' }}"" type="email" name="email" placeholder="Enter your email" value="{{ old('email') }}" required/>
                  </div>
                  <div class="text-center mt-3">
                    <button type="submit" class="btn btn-lg btn-primary">Reset password</button>
                  </div>
                </form>
              </div>
            </div>
          </div><!-- /.card -->
        </div>
      </div><!-- /.row -->
    </div>
  </main>
</body>
</html>
