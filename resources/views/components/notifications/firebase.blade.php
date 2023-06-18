 <!-- The core Firebase JS SDK is always required and must be listed first -->
<script type="module" src="https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js"></script>
<script type="module" src="https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js"></script>

<!-- TODO: Add SDKs for Firebase products that you want to use
    https://firebase.google.com/docs/web/setup#available-libraries -->

<script type="module">
    // Your web app's Firebase configuration
    const firebaseConfig = {
        apiKey: "AIzaSyBBu6BFTm_EkEysdFsD8_QE6KU8vYbcKac",
        authDomain: "otd-test-ac146.firebaseapp.com",
        projectId: "otd-test-ac146",
        storageBucket: "otd-test-ac146.appspot.com",
        messagingSenderId: "1099080381870",
        appId: "1:1099080381870:web:7d73949b4e1619aeac043f"
      };
    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);

    if (!firebase.messaging.isSupported()) {
        $('#push-notificaitons-unsupported').removeClass('d-none');
        var messaging = false;
        var isMessagingSupported = false;
    }
    else {
        var messaging = firebase.messaging();
        var isMessagingSupported = true;
    }

    function initFirebaseMessagingRegistration() {
        if(!isMessagingSupported)
            return false;
        messaging.requestPermission().then(function () {
            return messaging.getToken()
        }).then(function(token) {
            $.ajax({
                type: "POST",
                url: "{{ route('save-token') }}",
                data: {
                    token: token,
                    'type': 'this_device',
                }
            });

        }).catch(function (err) {
            console.log(`Token Error :: ${err}`);
        });
    }

    initFirebaseMessagingRegistration();
    if(isMessagingSupported)
    {
        messaging.onMessage(function(payload){
            const title = payload.notification.title;
            const options = {
                body: payload.notification.body,
                badge: "{{ Config::get('app.url') }}/logo",
                icon: "{{ Config::get('app.url') }}/logo",
                click_action: "{{ Config::get('app.url') }}/notifications",
            };
            console.log('new notificaiton:', title);

            if (!("Notification" in window))
                console.log("This browser does not support system notifications.");
            else if (Notification.permission === "granted") {
                var notification = new Notification(title,options);
                notification.onclick = function(event) {
                    event.preventDefault();
                    window.open(options.click_action, '_blank');
                    notification.close();
                }
            }
        });
    }
</script>