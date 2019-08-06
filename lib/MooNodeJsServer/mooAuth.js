//var mooDB;
var mooDB = require("./mooDB");
var mooSocket = require('./mooSocket');
var mooNotification = require('./mooNotification');
var mooUser = require('./mooUser');
var cache = require("./mooCache");
var log = require("./mooLog");
var allowPassword = true; // Default is false
var events = require('events');
var md5 = require('md5');
var uuidv4 = require('uuid/v4');
function getAuthenQuery(socket) {
    var query = mooDB.sqlString.checkTokenIsExists;
    if(socket.handshake.query.chat_token.includes("app_")){
        query = mooDB.sqlString.checkAcessTokenExists;
        return mooDB.mysql.format(query, [socket.handshake.query.chat_token.replace("app_","")]);
    }
    return mooDB.mysql.format(query, [socket.handshake.query.chat_token]);
}

function authIsFailure(socket){
    socket.emit("userIsLogged", 0);
}

function authIsSuccessful(socket,userId,status){
    socket.isLogged = true;
    socket.userId = parseInt(userId);
    socket.join('mooUser.' + socket.userId);
    mooUser.setStatus(socket.userId, status);

    if (mooSocket.isUserFirstTimeConnecting(socket.userId) && !mooUser.isOffline(socket.userId)) {
        mooNotification.imOnline(socket.userId);
        // Hacking for display all users
        //mooUser.imOnline(socket.userId)
        // End hacking
    }
    mooNotification.imLogged(socket);
    mooSocket.add1ToNumberUsersSocket(socket.userId);
    mooDB.query(mooDB.mysql.format(mooDB.sqlString.getMyStatCached, [socket.userId]), function (err, rows) {
        if (err) {
            log.error("mooDB.sqlString.getMyStatCached", err);
        } else {
            if (rows.length == 0) {

            }else{

                if(rows[0].new_friend == 1 || rows[0].new_block == 1 || rows[0].new_profile == 1){
                    cache.emptyQuery(socket.userId);
                    mooDB.query(mooDB.mysql.format(mooDB.sqlString.setMyStatCached, [socket.userId]), function (err, rows) {
                        if (err) {
                            log.error("mooDB.sqlString.setMyStatCached", err);
                        } else {

                        }}
                    );
                    // Hacking for 'Do not show my online status'

                    if(rows[0].new_profile == 1){
                        mooUser.updateHideMyOnlineStatus(socket.userId,true);
                    }
                }


            }
        }

        mooSocket.initFriendsAndBlocker(socket);
        mooUser.updateHideMyOnlineStatus(socket.userId,false);
    });
}

function generateToken(socket,uId) {
    var token = {
        access_token: uuidv4(),
        token_type: 'bearer',
        expires_in: 259200,
        refresh_token: uuidv4(),
        scope: null
    }
    var date = new Date((Math.round(Date.now() / 1000) + token.expires_in) * 1000);
    var expires = date.getFullYear() + '-' + (date.getMonth()+1) + '-' + date.getDate() + ' ' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds()
    var accessTokenRecord = {
        client_id:null,
        expires:expires,
        user_id:uId,
        scope:null,
        access_token:token.access_token
    }
    mooDB.query(mooDB.mysql.format(mooDB.sqlString.createAccessToken, accessTokenRecord), function (err, result) {
        if (err) {
            log.error("_queryCreateAccessToken err", err);
        } else {
            var date = new Date((Math.round(Date.now() / 1000) + 8209600) * 1000);
            var expires = date.getFullYear() + '-' + (date.getMonth()+1) + '-' + date.getDate() + ' ' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds()
            var refreshTokenRecord = {
                client_id:null,
                expires:expires,
                user_id:uId,
                scope:null,
                refresh_token:token.refresh_token
            }
            mooDB.query(mooDB.mysql.format(mooDB.sqlString.createRefreshToken, refreshTokenRecord), function (err, result) {
                if (err) {
                    log.error("_queryCreateRefreshToken err", err);
                } else {
                    socket.emit("generateTokenCallback", token);
                }
            });
        }
    });
}
function mooAuth() {
    events.EventEmitter.call(this);

    var initDataSource = function (mooDB) {
        mooDB = mooDB;

    };
    var isLogged = function (socket, callback) {
        if (socket.isLogged) {
            callback(socket);
        }
        return socket.isLogged;
    };
    var id = function (socket) {
        return socket.userId;
    };
    var execute = function (socket) {

        socket.isLogged = false;
        socket.userId = 0;
        // Check token is valided

        if ((socket.handshake.query.chat_token)) {
            // Hack for mooapp
            mooDB.query(getAuthenQuery(socket), function (err, rows) {
                if (err) {
                    //io.emit('error');
                } else {
                   
                    if (rows.length == 0) {
                        authIsFailure(socket);
                        //socket.emit("userIsLogged", 0);
                    } else {
                        authIsSuccessful(socket,rows[0].user_id,socket.handshake.query.chat_status);
                        /*
                        socket.isLogged = true;
                        socket.userId = rows[0].user_id;
                        socket.join('mooUser.' + socket.userId);
                        mooUser.setStatus(socket.userId, socket.handshake.query.chat_status);
                        
                        if (mooSocket.isUserFirstTimeConnecting(socket.userId) && !mooUser.isOffline(socket.userId)) {
                            mooNotification.imOnline(socket.userId);
                            // Hacking for display all users
                            //mooUser.imOnline(socket.userId)
                            // End hacking
                        }
                        mooNotification.imLogged(socket);
                        mooSocket.add1ToNumberUsersSocket(socket.userId); 
                        mooDB.query(mooDB.mysql.format(mooDB.sqlString.getMyStatCached, [socket.userId]), function (err, rows) {
                            if (err) {
                                log.error("mooDB.sqlString.getMyStatCached", err);
                            } else {
                                if (rows.length == 0) {

                                }else{

                                    if(rows[0].new_friend == 1 || rows[0].new_block == 1 || rows[0].new_profile == 1){
                                        cache.emptyQuery(socket.userId);
                                        mooDB.query(mooDB.mysql.format(mooDB.sqlString.setMyStatCached, [socket.userId]), function (err, rows) {
                                            if (err) {
                                                log.error("mooDB.sqlString.setMyStatCached", err);
                                            } else {

                                            }}
                                        );
                                        // Hacking for 'Do not show my online status'
                                        
                                        if(rows[0].new_profile == 1){
                                            mooUser.updateHideMyOnlineStatus(socket.userId,true);
                                        }
                                    }


                                }
                            }
                            
                            mooSocket.initFriendsAndBlocker(socket);
                            mooUser.updateHideMyOnlineStatus(socket.userId,false);
                        }); */

                    }
                }
            });
        } else {

        }


    };
    var viaPassword = function(socket,email,pass,globalSalt){
        if (!allowPassword){
            return;
        }
        mooDB.query(mooDB.mysql.format(mooDB.sqlString.getUserPassword, [email]), function (err, rows) {
            if (err) {
                log.error("mooDB.sqlString.getUserPassword", err,mooDB.mysql.format(mooDB.sqlString.getUserPassword, [email]));
            } else {

                if (rows.length == 0) {
                    authIsFailure(socket);
                } else {

                    var salt = (rows[0].salt == null)? "":rows[0].salt;
                    if (rows[0].password == md5(pass+globalSalt+salt)){
                        authIsSuccessful(socket,rows[0].id,1);
                        generateToken(socket,rows[0].id)
                    }else{
                        authIsFailure(socket);
                    }

                }
            }
        });
    }
    var viaToken = function(socket,accessToken){
        mooDB.query(mooDB.mysql.format(mooDB.sqlString.checkAccessToken, [accessToken]), function (err, rows) {
            if (err) {
                log.error("mooDB.sqlString.checkAccessToken", err);
            } else {
                if (rows.length == 0) {
                    authIsFailure(socket);
                    socket.emit("authViaTokenCallback", {error:"Token is invalid"});
                } else {
                    var now = Date.now() ;
                    var expires = new Date(rows[0].expires).getTime();
                    if (now < expires ){
                        authIsSuccessful(socket,rows[0].user_id,1);
                        socket.emit("authViaTokenCallback", {message:"Success",uid:rows[0].user_id});
                    }else{
                        authIsFailure(socket);
                        socket.emit("authViaTokenCallback", {error:"The access token provided has expired"});
                    }

                }
            }
        });
    }
    var refreshToken = function(socket,refreshToken){
        mooDB.query(mooDB.mysql.format(mooDB.sqlString.checkRefreshToken, [refreshToken]), function (err, rows) {
            if (err) {
                log.error("mooDB.sqlString.checkRefreshToken", err);
            } else {
                if (rows.length == 0) {
                    socket.emit("authRefreshTokenCallback", {error:"Refresh token is invalid"});
                } else {
                    var now = Date.now() ;
                    var expires = new Date(rows[0].expires).getTime();
                    if (now < expires ){
                        generateToken(socket,rows[0].user_id)
                        socket.emit("authRefreshTokenCallback", {message:"Success",uid:rows[0].user_id});
                    }else{
                        socket.emit("authRefreshTokenCallback", {error:"The refresh token provided has expired"});
                    }

                }
            }
        });
    }
    return {
        initDataSource: initDataSource,
        isLogged: isLogged,
        id: id,
        execute: execute,
        viaPassword:viaPassword,
        viaToken:viaToken,
        refreshToken:refreshToken,
        mooDB: mooDB
    };
};

module.exports = new mooAuth();