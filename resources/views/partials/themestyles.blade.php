<?php
    $themeColors = getThemeColors();
    $primary = $themeColors->primary;
    $accent = $themeColors->accent;
    $navbar_bg = $themeColors->navbar_bg;
    $navbar_text = $themeColors->navbar_text;
?>
<style>
    body {
        background-color: {!! $themeColors->background !!};
    }
    .border-primary {
        border-color: {!! $accent['600'] !!};
    }
    .btn-primary {
        background-color: {!! $accent['600'] !!};
        border-color: {!! $accent['600'] !!};
    }
    .btn-primary:hover {
        background-color: {!! $accent['700'] !!};
        border-color: {!! $accent['700'] !!};
    }
    .btn-outline-primary {
        color: {!! $accent['600'] !!};
    }
    .btn-outline-primary:hover {
        background-color: {!! $accent['600'] !!};
        border-color: {!! $accent['600'] !!};
        color: #fff;
    }
    .btn-outline-primary:active {
        background-color: {!! $accent['600'] !!};
        border-color: {!! $accent['600'] !!};
    }
    .btn-secondary {
        background-color: {!! $primary['600'] !!};
        border-color: {!! $primary['600'] !!};
    }
    .btn-secondary:hover {
        background-color: {!! $primary['700'] !!};
        border-color: {!! $primary['700'] !!};
    }
    .btn-light {
        color: {!! $accent['400'] !!};
    }
    .btn-light:hover {
        color: {!! $accent['500'] !!};
    }
    .bg-lightest-brand {
        background-color: {!! $themeColors->background !!};
    }
    .bg-secondary-brand {
        background-color: {!! $primary['700'] !!};
    }
    .bg-light-secondary-brand {
        background-color: {!! $primary['100'] !!};
    }
    .bg-light-brand {
        background-color: {!! $primary['100'] !!};
    }
    .bg-secondary-light-brand {
        background-color: {!! $primary['100'] !!};
    }
    .page-item.active .page-link {
        background-color: {!! $accent['400'] !!};
        border-color: {!! $accent['400'] !!};
    }
    .nav-container {
        background-color: {!! $navbar_bg !!};
        color: {!! $navbar_text !!};
    }
    .bg-footer {
        background-color: {!! $navbar_bg !!};
        color: {!! $navbar_text !!};
    }
    /*.navbar-toggler {
        border: 1px solid {!! $navbar_text !!};
    }*/
    .nav-container .dropdown-toggle, .nav-container a {
        color: {!! $navbar_text !!};
    }
    .nav-container .dropdown-menu .dropdown-item {
        color: #222222;
    }
    .bg-footer {
        background-color: {!! $navbar_bg !!};
        color: {!! $navbar_text !!};
    }
    .bg-footer a {
      
        color: {!! $navbar_text !!};
    }
    a {
        color: {!! $primary['600'] !!};
        line-break: anywhere;
    }
    a:hover {
        color: {!! $primary['700'] !!};
    }
    #showMobileMenu {
        color: {!! $navbar_text !!};
    }
    .dropdown-item:active {
        background-color: {!! $primary['600'] !!};
        color: #fff;
    }
    .category-tag {
        background-color: {!! $accent['300'] !!};
        font-weight: 500;
        padding: 0.25em 0.75em 0.25em 1em;
        border-radius: 2em;
        color: #58677a;
    }
    .category-tag:hover {
        text-decoration: none;
        background-color: {!! $accent['400'] !!};
        color: white;
    }
    .question-mark {
        fill: {!! $primary['400'] !!};
    }
    .custom-radio .custom-control-input:checked~.custom-control-label:before
    {
        background-color: {!! $primary['300'] !!};
    }
    @media (max-width: 450px) {
      .font-size-sm-sm {
       font-size: 0.8em;
      }
    }
    @foreach($primary as $key => $color)
        .bg-primary-{{ $key }} { background-color: {!! $color !!}; }
        .hover\:bg-primary-{{ $key }}:hover { background-color: {!! $color !!}; }
        .text-primary-{{ $key }} { color: {!! $color !!}; }
        .hover\:text-primary-{{ $key }}:hover { color: {!! $color !!}; }
    @endforeach
    @foreach($accent as $key => $color)
        .bg-accent-{{ $key }} { background-color: {!! $color !!}; }
        .hover\:bg-accent-{{ $key }}:hover { background-color: {!! $color !!}; }
        .text-accent-{{ $key }} { color: {!! $color !!}; }
        .fill-accent-{{ $key }} { fill: {!! $color !!}; }
        .hover\:text-accent-{{ $key }}:hover { color: {!! $color !!}; }
    @endforeach
</style>