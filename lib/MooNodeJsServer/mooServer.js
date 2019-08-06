// Setup basic express server

var express = require('express');
var app = express();
var server = require('http').createServer(app);

// For https
var fs = require('fs');
var privateKey = fs.readFileSync('/root/ssl/social_server.key');
var certificate = fs.readFileSync('/root/ssl/social_pharmatalk_co_kr.crt');
var chain = fs.readFileSync('/root/ssl/social_pharmatalk_co_kr.ca-bundle');

var server = require('https').createServer({
    key: privateKey,
    cert: certificate,
    ca: chain
}, app);
// End For https

// For https GoDaddy SSL certs
/* Note: Node requires each certificate in the CA chain to be passed separately in an array
 * GoDaddy provides a cerficate file (gd_bundle.crt) probably looks like this
 * -----BEGIN CERTIFICATE-----
 * MIIE3jCCA...
 * -----END CERTIFICATE-----
 * -----BEGIN CERTIFICATE-----
 * MIIEADCCA...
 * -----END CERTIFICATE-----
 * Each certificate needs to be put in its own file (ie gd1.crt and gd2.crt) and read separately
*/
/*var fs = require('fs');
var server = require('https').createServer({
    key: fs.readFileSync('/private/etc/apache2/ssl/socialloft.com/socialloft.key'),
    cert: fs.readFileSync('/private/etc/apache2/ssl/socialloft.com/4ee14b2fb124e3.crt'),
    ca: [fs.readFileSync('/private/etc/apache2/ssl/socialloft.com/gd1.crt'), fs.readFileSync('/private/etc/apache2/ssl/socialloft.com/gd2.crt'), fs.readFileSync('/private/etc/apache2/ssl/socialloft.com/gd3.crt')]
}, app)*/

// End For https GoDaddy SSL certs
var io = require('socket.io')(server);
var port = process.env.PORT || 3000;

var mooEmitter = require('./mooEmitter');
var mooSocket = require('./mooSocket');
var mooNotification = require('./mooNotification');
mooNotification.setIO(io);

var auth = require("./mooAuth");
var user = require('./mooUser');
var log = require("./mooLog");
var fcm = require("./mooFcm");
fcm.setIO(io);

app.get('/',function(req,res){
    res.end("Chat server is running");
});

require('./mooConfig').getMooConfig(function (host, login, password, database, prefix, sourceSql, mysqlPort,salt) {
    // Setup moosocial integration
    require("./mooDB").config(host, login, password, database, prefix, sourceSql, mysqlPort);
    require("./mooBooting").run();
    mooEmitter.emit('mooConfigSuccess',host, login, password, database, prefix, sourceSql, mysqlPort,io,salt,app);
    server.listen(port, "0.0.0.0", "::0", function () {

        log.info(`Server listening at port ${port}` );

    });

// Routing
    app.use(express.static(__dirname + '/public'));


    io.on('connection', function (socket) {
        // Hacking for all "on" event to make sure the socked is authenticated
        socket.isLogged = false;
        socket.userId = 0;
        socket.myFriendsId = [];
        socket.myBlockersId = [];
        socket.roomsId = {actived: []};
        var onevent = socket.onevent;
        socket.onevent = function (packet) {
            
            if (socket.isLogged) {
                onevent.call(this, packet);
            }else{
                if(packet.hasOwnProperty('data')){
                    if (packet.data instanceof Array) {
                        
                        if(packet.data.length > 0){
                            switch(packet.data[0]) {
                                case 'getServerInfo':
                                case 'getMonitorMessages':
                                case 'getUsers':
                                case 'getRooms':
                                case 'authViaPassword':
                                case 'authViaToken':
                                case 'authRefreshToken':
                                    onevent.call(this, packet);
                                    break;
                                default:

                            }
                        }
                    }



                }
            }

        };
        // End hacking

        auth.execute(socket);
        socket.on("authViaPassword", function (email,pass) {
            auth.viaPassword(this,email,pass,salt);
        });
        socket.on("authViaToken", function (accessToken) {
            auth.viaToken(this,accessToken);
        });
        socket.on("authRefreshToken", function (refreshToken) {
            auth.refreshToken(this,refreshToken);
        });
        user.init(io);
        mooEmitter.emit('io_connection',io,socket);

        // User init
        socket.on("getMyFriendsOnline", function () {
            user.getMyFriendsOnline(this);
        });
        socket.on("getMyFriends", function (ids) {
            user.getMyFriendsLimit(this,ids);
        });
        socket.on("getUsers", function (ids) {
            user.getUsers(this,ids);
        });
        socket.on("getUsersByRoomIdsAtBooting", function (rIds) {
            user.getUsersInRooms(this,rIds);
        });

        socket.on("setOffline", function () {
            user.setOffline(this);
        });
        socket.on("setOnline", function () {
            user.setOnline(this);
        });
        socket.on("getMyGroups", function () {
            user.getMyGroupsConversations(this);
        });
        socket.on("startTyping", function (rId) {
            user.startTyping(this,rId);
        });
        socket.on("stopTyping", function (rId) {
            user.stopTyping(this,rId);
        });
        socket.on("searchFriend", function (name) {
            user.searchFriend(this,name);
        });
        socket.on("changeUserOnlineStatus", function (status) {
            user.changeUserOnlineStatus(this,status);
        });
        socket.on("stunTurnServer", function (token) {
            mooNotification.stunTurnServer(this, token);
        });
        // End user init

        // when` the user disconnects.. perform this
        socket.on('disconnect', function () {
            mooEmitter.emit('io_disconnect',io,socket);

            var userId = socket.userId;
            
            mooSocket.sub1FromNumberUsersSocket(userId);
            setTimeout(function () {

                if (mooSocket.isUserLatestConnecting(socket)) {
                    mooNotification.imOffline(userId);
                    // Hacking for display all users
                    //user.imOffline(userId);
                    // End hacking
                }
            }, 1100);
        });
        
        // Fcm init
        socket.on("saveFcmToken", function (user_id, token, client_type) {
            fcm.saveToken(user_id, token, client_type);
            fcm.cacheTokenList(user_id, token, client_type);
        });
        socket.on("removeFcmToken", function (user_id, token) {
            fcm.removeToken(user_id, token);
            fcm.clearCacheTokenList(user_id, token);
        });
        // End fcm init
    });
});


