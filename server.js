var app = require('express');
var server = require('http').Server(app);
var io = require('socket.io')(server);
var Redis = require('ioredis');
var redis = new Redis();

const PORT = 8005;

var sourcesData = [];

server.listen(PORT, function() {
  console.log(`Listening On Port ${PORT}`)
});


redis.subscribe('complaint-channel', function() {
  console.log('Subscribed to Notif Channel');
})

redis.on('message', function(channel, message) {
  console.log("Redis On Message Run", {channel, message})

  message = JSON.parse(message);
  if(channel == 'complaint-channel') {
    console.log("Your channel name is ", channel);
  }
});