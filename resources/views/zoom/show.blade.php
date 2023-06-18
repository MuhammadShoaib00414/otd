<!DOCTYPE html>
<html lang="en" style="overflow-y: scroll !important;">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="origin-trial" content="{{ \App\Setting::where('name', 'origin_trial_key')->first()->value }}">
    <title>Zoom Meeting</title>
    <link rel="stylesheet" href="style.css">
    <link type="text/css" rel="stylesheet" href="https://source.zoom.us/2.4.0/css/bootstrap.css" />
    <link type="text/css" rel="stylesheet" href="https://source.zoom.us/2.4.0/css/react-select.css" />
    <style>
        #zmmtg-root {
            position:  relative !important;
            background-color:  white !important;
        }
    </style>
  </head>
  <body style="overflow-y: scroll;">

    <form class="navbar-form navbar-right d-none" id="meeting_form" style="dispaly:none !important;">
        <input type="hidden" name="display_name" id="display_name" value="{{ request()->user()->name }}" maxLength="100"
                placeholder="Name" class="form-control" required>
        <input type="hidden" name="meeting_number" id="meeting_number" value="{{ request()->meetingId }}" maxLength="200"
                style="width:150px" placeholder="Meeting Number" class="form-control" required>
        <input type="hidden" name="meeting_pwd" id="meeting_pwd" value="{{ request()->input('pwd') }}" style="width:150px"
                maxLength="32" placeholder="Meeting Password" class="form-control">
        <input type="hidden" name="meeting_name" id="meeting_name" value="{{ isset($callable) ? $callable->name : 'Zoom Call' }}">
        <input type="hidden" name="meeting_email" id="meeting_email" value="{{ request()->user()->email }}" style="width:150px"
                maxLength="32" placeholder="Email option" class="form-control">
        <input type="hidden" name="apiKey" id="apiKey" value="{{ config('otd.zoom_api_key') }}">

        <div class="form-group d-none" style="display:none !important">
            <select id="meeting_role">
                <option value=0>Attendee</option>
                <option value=1>Host</option>
                <option value=5>Assistant</option>
            </select>
        </div>
        <div class="form-group d-none" style="display:none !important">
            <select id="meeting_china">
                <option value=0>Global</option>
                <option value=1>China</option>
            </select>
        </div>

        <input type="hidden" value="" id="copy_link_value" />


    </form>

    <script type="text/javascript" src="/assets/js/jquery.min.js"></script>
    <script src="https://source.zoom.us/2.4.0/lib/vendor/react.min.js"></script>
    <script src="https://source.zoom.us/2.4.0/lib/vendor/react-dom.min.js"></script>
    <script src="https://source.zoom.us/2.4.0/lib/vendor/redux.min.js"></script>
    <script src="https://source.zoom.us/2.4.0/lib/vendor/redux-thunk.min.js"></script>
    <script src="https://source.zoom.us/2.4.0/lib/vendor/lodash.min.js"></script>
    <script src="https://source.zoom.us/zoom-meeting-2.4.0.min.js"></script>
    <script src="/assets/js/zoom/tools.js"></script>
    <script src="/assets/js/zoom/vconsole.min.js"></script>
    <script src="/assets/js/zoom/meeting.js"></script>
    <script src="/assets/js/zoom/index.js"></script>
  </body>
</html>