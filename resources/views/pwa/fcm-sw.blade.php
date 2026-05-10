importScripts('https://www.gstatic.com/firebasejs/12.3.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/12.3.0/firebase-messaging-compat.js');

const firebaseConfig = @json($firebaseConfig);
const requiredKeys = ['apiKey', 'authDomain', 'projectId', 'storageBucket', 'messagingSenderId', 'appId'];
const hasConfig = requiredKeys.every((key) => typeof firebaseConfig[key] === 'string' && firebaseConfig[key].length > 0);

if (hasConfig) {
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    messaging.onBackgroundMessage((payload) => {
        const title = payload?.notification?.title ?? 'নতুন নোটিফিকেশন';
        const options = {
            body: payload?.notification?.body ?? '',
            icon: payload?.notification?.icon ?? '/images/image_14.png',
            badge: payload?.notification?.badge ?? '/images/image_14.png',
            data: payload?.data ?? {},
        };

        self.registration.showNotification(title, options);
    });
}
