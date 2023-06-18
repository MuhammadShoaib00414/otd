function startMeeting(meetingConfig, signature) {
  var testTool = window.testTool;
  // get meeting args from url
  var tmpArgs = meetingConfig;
  var meetingConfig = {
    apiKey: tmpArgs.apiKey,
    meetingNumber: tmpArgs.mn,
    userName: tmpArgs.name,
    passWord: tmpArgs.pwd,
    leaveUrl: "/zoom/closing",
    role: 0,
    userEmail: tmpArgs.email,
    lang: tmpArgs.lang,
    signature: signature || "",
    china: tmpArgs.china === "1",
  };

  // a tool use debug mobile device
  if (testTool.isMobileDevice()) {
    vConsole = new VConsole();
  }
  console.log(JSON.stringify(ZoomMtg.checkSystemRequirements()));

  // it's option if you want to change the WebSDK dependency link resources. setZoomJSLib must be run at first
  // ZoomMtg.setZoomJSLib("https://source.zoom.us/1.9.8/lib", "/av"); // CDN version defaul
  if (meetingConfig.china)
    ZoomMtg.setZoomJSLib("https://jssdk.zoomus.cn/1.9.8/lib", "/av"); // china cdn option
  ZoomMtg.preLoadWasm();
  ZoomMtg.prepareJssdk();
  function beginJoin(signature) {
    ZoomMtg.init({
      leaveUrl: meetingConfig.leaveUrl,
      webEndpoint: meetingConfig.webEndpoint,
      disableCORP: !window.crossOriginIsolated, // default true
      // disablePreview: false, // default false
      success: function () {
        console.log(meetingConfig);
        console.log("signature", signature);
        ZoomMtg.i18n.load(meetingConfig.lang);
        ZoomMtg.i18n.reload(meetingConfig.lang);
        ZoomMtg.join({
          meetingNumber: meetingConfig.meetingNumber,
          userName: meetingConfig.userName,
          signature: signature,
          apiKey: meetingConfig.apiKey,
          userEmail: meetingConfig.userEmail,
          passWord: meetingConfig.passWord,
          success: function (res) {
            console.log("join meeting success");
            console.log("get attendeelist");
            ZoomMtg.getAttendeeslist({});

            ({
              success: function (res) {
                console.log("success getCurrentUser", res.result.currentUser);
              },
            });
          },
          error: function (res) {
            console.log(res);
          },
        });
      },
      error: function (res) {
        console.log(res);
      },
    });

    window.addEventListener('beforeunload', () => {
        document.getElementById('is_zoom_over').set('value', 'true');
        ZoomMtg.leaveMeeting();
    });

    ZoomMtg.inMeetingServiceListener('onUserJoin', function (data) {
      console.log('inMeetingServiceListener onUserJoin', data);
    });
  
    ZoomMtg.inMeetingServiceListener('onUserLeave', function (data) {
      console.log('inMeetingServiceListener onUserLeave', data);
    });
  
    ZoomMtg.inMeetingServiceListener('onUserIsInWaitingRoom', function (data) {
      console.log('inMeetingServiceListener onUserIsInWaitingRoom', data);
    });
  
    ZoomMtg.inMeetingServiceListener('onMeetingStatus', function (data) {
      console.log('inMeetingServiceListener onMeetingStatus', data);
    });
  }

  beginJoin(signature);
};