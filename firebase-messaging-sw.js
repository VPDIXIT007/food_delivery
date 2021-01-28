importScripts('https://www.gstatic.com/firebasejs/8.0.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.0.1/firebase-analytics.js');
importScripts('https://www.gstatic.com/firebasejs/8.0.1/firebase-messaging.js');

/*
var firebaseConfig = {
    apiKey: "AIzaSyDsRoYUkVAmpjUeyjSQjxIcPSg8Us5WXF4",
    authDomain: "el-order-58003.firebaseapp.com",
    databaseURL: "https://el-order-58003.firebaseio.com",
    projectId: "el-order-58003",
    storageBucket: "el-order-58003.appspot.com",
    messagingSenderId: "129287823104",
    appId: "1:129287823104:web:849ac6770bbaaa478a1de5",
    measurementId: "G-B6F3HYQQ13"
};
*/
var firebaseConfig = {
    apiKey: "AIzaSyA-xZHyg5TfODQd36knDWPMsWT57z-R2Sg",
    authDomain: "el-order-30130.firebaseapp.com",
    databaseURL: "https://el-order-30130.firebaseio.com",
    projectId: "el-order-30130",
    storageBucket: "el-order-30130.appspot.com",
    messagingSenderId: "277138525933",
    appId: "1:277138525933:web:c1a80a3ed701293ede46e4",
    measurementId: "G-183DPNZLJB"
  };
firebase.initializeApp(firebaseConfig);
//firebase.analytics();
const messaging=firebase.messaging();

messaging.setBackgroundMessageHandler(function (payload) {
    console.log(payload);
    const notification=JSON.parse(payload);
    const notificationOption={
        body:notification.body,
        icon:notification.icon
    };
    return self.registration.showNotification(payload.notification.title,notificationOption);
});