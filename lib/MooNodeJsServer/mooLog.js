//var winston = require('winston');

/*
winston.add(winston.transports.File, 
    { filename: __dirname+'/log/error.log', 
      level: 'error', 
      timestamp: true, 
      json:false,
    });

var logger = new (winston.Logger)({
    transports: [
        new (winston.transports.Console),
        new (winston.transports.File)({
            name: 'info-file',
            filename: __dirname+'/log/info.log',
            level: 'info',
            timestamp: true,
            json:false
        }),
        new (winston.transports.File)({
            name: 'error-file',
            filename: __dirname+'/log/error.log',
            level: 'error',
            timestamp: true,
            json:false
        }),
        new (winston.transports.File)({
            name: 'handleExceptions-file',
            filename: __dirname+'/log/error-all.log',
            handleExceptions: true,
            humanReadableUnhandledException: true,
            timestamp: true,
            json:false
        })
    ]
});
*/
/*
const logger = winston.createLogger({
    level: 'info',
    format: winston.format.printf,
    transports: [
        //
        // - Write to all logs with level `info` and below to `combined.log`
        // - Write all logs error (and below) to `error.log`.
        //
        new winston.transports.File({ filename: __dirname+'/log/info.log', level: 'info' }),
        new winston.transports.File({ filename: __dirname+'/log/error.log', level: 'error' }),
    ]
});*/
const { createLogger, format, transports } = require('winston');
const { combine, timestamp, label, printf } = format;

const myFormat = printf(info => {
    return `${info.timestamp}  ${info.level}: ${info.message}`;
});

const logger = createLogger({
    format: combine(
        timestamp(),
        myFormat
    ),
    transports: [new transports.Console(),
        new transports.File({ filename: __dirname+'/log/info.log', level: 'info' }),
        new transports.File({ filename: __dirname+'/log/error.log', level: 'error' }),

    ]
});

info = function(mess){
    logger.info(mess)
}
error = function(mess,obj){
    logger.error(mess + JSON.stringify(obj))
}

module.exports = {
    info:info,
    error:error
}

