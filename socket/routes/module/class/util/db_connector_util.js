// mysql
const mysql = require('mysql');
var pool = mysql.createPool({
  connectionLimit: 20,
  host: process.env.DB_HOST || 'localhost',
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASS || 'password',
  database: process.env.DB_NAME || 'sinclo_db'
});

module.exports.getPool = function() {
  return pool;
};
