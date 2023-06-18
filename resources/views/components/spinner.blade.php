@once
    @push('stylestack')
        <style>
            .spinner {
                border: 5px solid #f3f3f3;
                border-top: 5px solid {{ getThemeColors()->primary['200'] }};
                border-radius: 50%;
                width: 50px;
                height: 50px;
                margin: auto;
                animation: spin 2s linear infinite;
              }
              @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
              }
        </style>
    @endpush
@endonce

<div class="spinner" id="{{ $spinnerId }}"></div>