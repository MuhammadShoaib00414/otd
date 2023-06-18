window.addEventListener('DOMContentLoaded', function(event) {
  console.log('DOM fully loaded and parsed');
  websdkready();
});

function websdkready() {
  var testTool = window.testTool;
  if (testTool.isMobileDevice()) {
    vConsole = new VConsole();
  }

  // it's option if you want to change the WebSDK dependency link resources. setZoomJSLib must be run at first
  // if (!china) ZoomMtg.setZoomJSLib('https://source.zoom.us/1.9.8/lib', '/av'); // CDN version default
  // else ZoomMtg.setZoomJSLib('https://jssdk.zoomus.cn/1.9.8/lib', '/av'); // china cdn option
  // ZoomMtg.setZoomJSLib('http://localhost:9999/node_modules/@zoomus/websdk/dist/lib', '/av'); // Local version default, Angular Project change to use cdn version
  ZoomMtg.preLoadWasm(); // pre download wasm file to save time.

  var API_KEY = "YOUR_API_KEY";

  var meetingConfig = testTool.getMeetingConfig();
    if (!meetingConfig.mn || !meetingConfig.name) {
      alert("Meeting number or username is empty");
      return false;
    }
    console.log('config', meetingConfig);
    var signature = false;
    $.ajax({
      method: 'GET',
      url: '/zoom/signature',
      data: {
        meeting_number: meetingConfig.mn,
        role: 0,
      },
      success: function(result){
        signature = result.replaceAll('"', '');
        startMeeting(meetingConfig, signature);
      }
    });
}