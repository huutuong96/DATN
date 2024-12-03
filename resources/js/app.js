// import Echo from "laravel-echo";
// import Pusher from "pusher-js";

// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: "4001aa49838fcbc98ecb",
//     cluster: 'ap1',
//     encrypted: false,
//     wsHost: window.location.hostname,
//     wsPort: 6001,
//     forceTLS: false,
//     disableStats: true,
// });

// Echo.channel("user-online").listen(".UserLoggedIn", (e) => {
//     console.log("Số lượng người dùng đang online:", e.onlineCount);
//     document.getElementById("online-count").innerText = e.onlineCount;
// });






// import Pusher from "pusher-js";

// const pusher = new Pusher("4001aa49838fcbc98ecb", {
//     cluster: "ap1",
//     forceTLS: true,
// });

// const channel = pusher.subscribe("user-online");
// channel.bind("UserLoggedIn", (data) => {
//     console.log("Số lượng người dùng đang online:", data.onlineCount);
//     document.getElementById("online-count").innerText = data.onlineCount;
// });




import { io } from "socket.io-client";

const socket = io("http://localhost:6001");

socket.on("connect", () => {
    console.log("Đã kết nối với WebSocket server");
});

socket.on("user-online", (data) => {
    console.log("Số lượng người dùng online:", data.onlineCount);
    document.getElementById("online-count").innerText = data.onlineCount + " Online";
});



