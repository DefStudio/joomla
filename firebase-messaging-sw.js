importScripts('https://www.gstatic.com/firebasejs/5.2.0/firebase-app.js');
        importScripts('https://www.gstatic.com/firebasejs/5.2.0/firebase-messaging.js');
        var config = {
        messagingSenderId: "402598571789"
        };
        firebase.initializeApp(config);
        self.addEventListener('notificationclick', e => {
        let found = false;
        let f = clients.matchAll({
        includeUncontrolled: true,
        type: 'window'
        })
        .then(function (clientList) {
        for (let i = 0; i < clientList.length; i ++) {
        if (clientList[i].url === e.notification.data.click_action) {
        found = true;
        clientList[i].focus();
        break;
        }
        }
        if (! found) {
        clients.openWindow(e.notification.data.click_action).then(function (windowClient) {});
        }
        });
        e.notification.close();
        e.waitUntil(f);
        });
        var messaging = firebase.messaging();
        messaging.setBackgroundMessageHandler(function(payload){
        return self.registration.showNotification(payload.data.title,
        Object.assign({data: payload.data}, payload.data));
        });