const mysql = require('mysql2');
require('dotenv').config();

// Create the connection pool
const pool = mysql.createPool({
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'hk_isp_billing',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
});

// Promisify for Node.js async/await
const promisePool = pool.promise();

module.exports = promisePool;
