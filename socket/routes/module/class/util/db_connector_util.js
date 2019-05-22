// mysql
const mysql = require('mysql2');
var pool = mysql.createPool({
  host: process.env.DB_HOST || 'localhost',
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASS || 'password',
  database: process.env.DB_NAME || 'sinclo_db'
});

module.exports.getPool = function() {
  return pool;
};