<?php
  
        return [
            'aws_access_key_id'             =>    env('AWS_ACCESS_KEY_ID'),
            'aws_secret_access_key'         =>    env('AWS_SECRET_ACCESS_KEY'),
            'aws_default_region'            =>    env('AWS_DEFAULT_REGION'),
            'aws_bucket'                    =>    env('AWS_BUCKET'),
            'aws_use_path_style_endpoint'   =>    env('AWS_USE_PATH_STYLE_ENDPOINT'),
            'aws_access_key_id'             =>    env('AWS_ACCESS_KEY_ID'),


            'pusher_key'                    =>    env('PUSHER_KEY'),
            'pusher_secret'                 =>    env('PUSHER_SECRET'),
            'pusher_app_id'                 =>    env('PUSHER_APP_ID'),
            'pusher_cluster'                =>    env('PUSHER_CLUSTER'),
            'mix_pusher_app_key'            =>    env('MIX_PUSHER_APP_KEY'),
            'mix_pusher_app_cluster'        =>    env('MIX_PUSHER_APP_CLUSTER'),


            'plivo_sid'                     =>    env('PLIVO_SID'),
            'plivo_token'                   =>    env('PLIVO_TOKEN'),
            'plivo_from'                    =>    env('PLIVO_FROM'),

            
            'zoom_api_key'                   =>    env('ZOOM_API_KEY'),
            'zoom_api_secret'                =>    env('ZOOM_API_SECRET'),
            'zoom_api_im_history_token'      =>    env('ZOOM_API_IM_HISTORY_TOKEN'),
            'zoom_api_url'                   =>    env('ZOOM_API_URL'),
           

            'vite_pusher_app_key'            =>    env('VITE_PUSHER_APP_KEY'),
            'vite_pusher_host'               =>    env('VITE_PUSHER_HOST'),
            'vite_pusher_port'               =>    env('VITE_PUSHER_PORT'),
            'vite_pusher_scheme'             =>    env('VITE_PUSHER_SCHEME'),
            'vite_pusher_app_cluster'        =>    env('VITE_PUSHER_APP_CLUSTER'),



        ];
?>