<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="googlebot" content="noindex">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css" />
  <link rel="stylesheet" type="text/css" href="/css/admin/main.css">
  <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
  <title>Admin - {{ getSetting('name') }}</title>

  @yield('head')
  <?php
  $primary_color = App\Setting::where('name', 'admin_primary_color')->first();
  $secondary_color = App\Setting::where('name', 'admin_btn_secondary')->first();
  $danger_color = App\Setting::where('name', 'admin_btn_danger')->first();
  $warning_color = App\Setting::where('name', 'admin_btn_warning')->first();
  $info_color = App\Setting::where('name', 'admin_btn_info')->first();

  $dark_color = App\Setting::where('name', 'admin_btn_dark')->first();
  $success_color = App\Setting::where('name', 'admin_btn_success')->first();
  $light_color = App\Setting::where('name', 'admin_btn_light')->first();
  ?>
  <style>
    .btn-primary {
      background-color: <?php echo ($primary_color->value) ?>;
      border-color: <?php echo ($primary_color->value) ?>;
    }

    .btn-secondary {
      background-color: <?php echo ($secondary_color->value) ?>;
      border-color: <?php echo ($secondary_color->value) ?>;
    }

    .btn-info {
      background-color: <?php echo ($info_color->value) ?>;
      border-color: <?php echo ($info_color->value) ?>;
    }

    .btn-danger {
      background-color: <?php echo ($danger_color->value) ?>;
      border-color: <?php echo ($danger_color->value) ?>;
    }

    .btn-warning {
      background-color: <?php echo ($warning_color->value) ?>;
      border-color: <?php echo ($warning_color->value) ?>;
    }

    .btn-dark {
      background-color: <?php echo ($dark_color->value) ?>;
      border-color: <?php echo ($dark_color->value) ?>;
    }

    .btn-light {
      background-color: <?php echo ($light_color->value) ?>;
      border-color: <?php echo ($light_color->value) ?>;
    }

    .btn-success {
      background-color: <?php echo ($success_color->value) ?>;
      border-color: <?php echo ($success_color->value) ?>;
    }



    .no-underline:hover {
      text-decoration: none;
    }

    @media(max-width: 721px) {
      .admin-nav {
        position: fixed;
        top: 0;
        left: 0;
        transform: translateX(-100%);
        z-index: 1000;
        width: 100%;
        max-width: 250px;
        transition: transform 0.12s;
        padding-top: 1em;
      }

      .admin-nav-show {
        transform: translateX(0);
      }
    }

    a.foo-link:hover {
      color: white;
      text-decoration: none;
    }

    .footer-fixed {
      z-index: 1000;
      position: fixed;
      width: 100%;
      bottom: 0px;
    }
  </style>
  @stack('stylestack')
</head>

<body style="min-height: 100%;">

  <div class="admin-header px-3 py-1 d-flex justify-content-between align-items-center" style="z-index: 5000;">
    <a href="/home">
      <img src="{{ \App\Setting::where('name', 'logo')->first()->value }}" style="height: 3.5em; max-height: 70px; object-fit: cover;">
    </a>
    <span>Admin Area</span>
  </div>

  <div class="container-fluid" style="min-height: 100%;">
    <div class="row align-items-stretch justify-content-between" style="min-height: 100%;">

      <div class="col-12 ml-auto mr-0" style="padding-top: 75px;">
        @if(!empty(Session::get('message')))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong> <?php echo Session::get('message'); ?> </strong>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        @yield('page-content')
      </div>

    </div>
  </div>

  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  @yield('scripts')
  <script>
    $(document).ready(function() {
      var primary = '<?php echo ($primary_color->value); ?>'; // replace with your desired color
      var secondary = '<?php echo ($secondary_color->value); ?>'; // replace with your desired color
      var info = '<?php echo ($info_color->value); ?>'; // replace with your desired color
      var dark = '<?php echo ($dark_color->value); ?>'; // replace with your desired color
      var light_color = '<?php echo ($light_color->value); ?>'; // replace with your desired color


      // apply the selected color to the button
      $(".btn-primary").css("background-color", primary);
      $(".btn-info").css("background-color", info);
      $(".btn-outline-dark").css("border-color", dark);
      $(".btn-outline-secondary").css("border-color", secondary);
      $(".btn-light").css("border-color", light_color);

      // generate a lighter shade when the button is hovered over
      $(".btn-primary").mouseover(function() {
        $(this).css("background-color", lightenColor(primary, 20)); // adjust 20 to control the degree of lightness
      }, function() {
        $(this).css("background-color", lightenColor(primary, 20));
        $(this).css("border-color", lightenColor(primary, 20));
      });
      $(".btn-primary").mouseout(function() {
        $(this).css("background-color", primary);
        $(this).css("border-color", "");
      });
      // info secondary
      $(".btn-secondary").mouseover(function() {
        $(this).css("background-color", lightenColor(secondary, 20)); // adjust 20 to control the degree of lightness
      }, function() {
        $(this).css("background-color",  lightenColor(secondary, 20));
        $(this).css("border-color",  lightenColor(secondary, 20));
      });
      $(".btn-secondary").mouseout(function() {
        $(this).css("background-color", "");
        $(this).css("border-color", "");
      });

      // info light

      $(".btn-light").mouseover(function() {
        $(this).css("background-color", lightenColor(light_color, 20)); // adjust 20 to control the degree of lightness
      }, function() {
        $(this).css("background-color", lightenColor(light_color, 20));
        $(this).css("border-color", lightenColor(light_color, 20));
      });
      $(".btn-light").mouseout(function() {
        $(this).css("background-color", "");
        $(this).css("border-color", "");
      });
      // info color
      $(".btn-info").mouseover(function() {
        $(this).css("background-color", lightenColor(info, 20)); // adjust 20 to control the degree of lightness
      }, function() {
        $(this).css("background-color", lightenColor(info, 20));
        $(this).css("border-color", lightenColor(info, 20));
      });
      $(".btn-info").mouseout(function() {
        $(this).css("background-color", info);
        $(this).css("border-color", "");
      });
      // outline dark
      $(".btn-outline-dark").mouseover(function() {
        $(this).css("border-color", lightenColor(dark, 30)); // adjust 30 to control the degree of lightness
      }, function() {
        $(this).css("background-color", lightenColor(dark, 30));
        $(this).css("border-color", lightenColor(dark, 30));
      });
      $(".btn-outline-dark").mouseout(function() {
        $(this).css("background-color", "");
        $(this).css("border-color", dark);
      });

      // btn-outline-secondary
      $(".btn-outline-secondary").mouseover(function() {
        $(this).css("border-color", lightenColor(secondary, 30)); // adjust 30 to control the degree of lightness
      }, function() {
        $(this).css("background-color", lightenColor(secondary, 30));
        $(this).css("border-color", lightenColor(secondary, 30));
      });
      $(".btn-outline-secondary").mouseout(function() {
        $(this).css("background-color", "");
        $(this).css("border-color", secondary);
      });
      //btn-outline-primary
      $(".btn-outline-primary").mouseover(function() {
        $(this).css("border-color", lightenColor(primary, 30)); // adjust 30 to control the degree of lightness
      }, function() {
        $(this).css("background-color", lightenColor(primary, 30));
        $(this).css("border-color", lightenColor(primary, 30));
      });
      $(".btn-outline-primary").mouseout(function() {
        $(this).css("background-color", "");
        $(this).css("border-color", primary);
      });

    });


    // function to lighten a color by a specified percentage
    function lightenColor(color, percent) {
      var num = parseInt(color.replace("#", ""), 16),
        amt = Math.round(2.55 * percent),
        R = (num >> 16) + amt,
        G = (num >> 8 & 0x00FF) + amt,
        B = (num & 0x0000FF) + amt;
      return "#" + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 + (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 + (B < 255 ? B < 1 ? 0 : B : 255)).toString(16).slice(1);
    }
    $('#showMobileMenu').on('click', function(e) {
      e.preventDefault();
      $('.admin-nav').toggleClass('admin-nav-show');
    });
    $('body').click('on', function(e) {
      var menu = $('.admin-nav');
      var menuButton = $('#showMobileMenu');
      if (!menu.is(e.target) && menu.has(e.target).length === 0 && !menuButton.is(e.target) && menuButton.has(e.target).length === 0)
        $('.admin-nav').removeClass('admin-nav-show');
    });
  </script>
  @stack('scriptstack')
</body>

</html>