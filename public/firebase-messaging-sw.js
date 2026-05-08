importScripts('https://www.gstatic.com/firebasejs/12.3.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/12.3.0/firebase-messaging-compat.js');

const firebaseConfig = {
    apiKey: 'PASTE_FIREBASE_API_KEY_HERE',
    authDomain: 'PASTE_FIREBASE_AUTH_DOMAIN_HERE',
    projectId: 'PASTE_FIREBASE_PROJECT_ID_HERE',
    storageBucket: 'PASTE_FIREBASE_STORAGE_BUCKET_HERE',
    messagingSenderId: 'PASTE_FIREBASE_MESSAGING_SENDER_ID_HERE',
    appId: 'PASTE_FIREBASE_APP_ID_HERE',
    measurementId: 'PASTE_FIREBASE_MEASUREMENT_ID_HERE'
};

firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    const title = payload?.notification?.title ?? 'নতুন নোটিফিকেশন';
    const options = {
        body: payload?.notification?.body ?? '',
    };

    self.registration.showNotification(title, options);
});
