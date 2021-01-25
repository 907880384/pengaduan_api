'use strict'
var app = require('express');
var server = require('http').Server(app);
var io = require('socket.io')(server);
require('dotenv').config();

var redisHost = process.env.REDIS_HOST;
var redisPort = process.env.REDIS_PORT;
var serverPort = process.env.BROADCAST_PORT;
var ioRedis = require('ioredis');
var redis = new ioRedis(redisPort, redisHost);
var listChannel = {
  complaintChannel: 'complaint-channel',
  assignedComplaintChannel: 'assign-complaint'
}
var sourceData = {
  users: [],
}

redis.subscribe(listChannel.complaintChannel, () => {
  console.log(`Now you subscribe channel ${listChannel.complaintChannel}`);
});

redis.subscribe(listChannel.assignedComplaintChannel, () => {
  console.log(`Now you subscribe channel ${listChannel.assignedComplaintChannel}`);
});


redis.on('message', (channel,message) => {
  message = JSON.parse(message);
  console.log("Message On", message);
  console.log("Channel On", channel);
  io.emit(channel + ":" + message.event, message.data);
});

io.on("connection", function (socket) {
  socket.on("sendUserLogin", function (userId) {
    sourceData.users[userId] = socket.id;
    io.emit("sendDataUserActiveLogin", sourceData.users);
  });

  socket.on("disconnect", function () {
    var i = sourceData.users.indexOf(socket.id);
    sourceData.users.splice(i, 1, 0);
    io.emit('sendDataUserActiveLogin', sourceData.users);
  });
});


server.listen(serverPort, () => {
  console.log(`Socket server is running on port ${serverPort}`);
});
