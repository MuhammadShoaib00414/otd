importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
   
firebase.initializeApp({
    apiKey: "AIzaSyBBu6BFTm_EkEysdFsD8_QE6KU8vYbcKac",
    authDomain: "otd-test-ac146.firebaseapp.com",
    projectId: "otd-test-ac146",
    storageBucket: "otd-test-ac146.appspot.com",
    messagingSenderId: "1099080381870",
    appId: "1:1099080381870:web:7d73949b4e1619aeac043f"
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    // fcp_options.link field from the FCM backend service goes there, but as the host differ, it not handled by Firebase JS Client sdk, so custom handling
    if (event.notification && event.notification.data && event.notification.data.FCM_MSG && event.notification.data.FCM_MSG.notification) {
        const url = "/notifications";
        event.waitUntil(
            self.clients.matchAll({type: 'window'}).then( windowClients => {
                // Check if there is already a window/tab open with the target URL
                for (var i = 0; i < windowClients.length; i++) {
                    var client = windowClients[i];
                    // If so, just focus it.
                    if (client.url === url && 'focus' in client) {
                        return client.focus();
                    }
                }
                // If not, then open the target URL in a new window/tab.
                if (self.clients.openWindow) {
                    console.log("open window")
                    return self.clients.openWindow(url);
                }
            })
        )
    }
}, false);
  
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function({data:{title,body,icon}}) {
    const notificationOptions = {
        body: body,
        icon: '/logo',
        click_action: '/notifications',
    };
    console.log('background message');
    return self.registration.showNotification(title, notificationOptions);
});