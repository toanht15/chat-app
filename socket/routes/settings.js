var express = require('express');
var router = express.Router();

// mysql
var mysql = require('mysql'),
    pool = mysql.createPool({
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASS || 'password',
    database: process.env.DB_NAME || 'sinclo_db'
});

var getWidgetSettingSql  = "SELECT ws.* FROM m_widget_settings AS ws";
    getWidgetSettingSql += " INNER JOIN (SELECT * FROM m_companies WHERE company_key = ? AND del_flg = 0 ) AS com  ON ( com.id = ws.m_companies_id )";
    getWidgetSettingSql += " WHERE ws.del_flg = 0 ORDER BY id DESC LIMIT 1;";

var getTriggerListSql  = "SELECT am.* FROM t_auto_messages AS am ";
    getTriggerListSql += " INNER JOIN (SELECT * FROM m_companies WHERE company_key = ? AND del_flg = 0 ) AS com  ON ( com.id = am.m_companies_id )";
    getTriggerListSql += " WHERE am.active_flg = 0 AND am.del_flg = 0;";

/* GET home page. */
router.get("/", function(req, res, next) {
    if (  !('query' in req) || (('query' in req) && !('sitekey' in req['query'])) ) {
        var err = new Error('Not Found');
        err.status = 404;
        next(err);
        return false;
    }
    var siteKey = req['query']['sitekey'];
    var sendData = { widget: {}, messages: {}, contract: {}};
    pool.query(getWidgetSettingSql, siteKey,
        function(err, rows){

            if ( rows.length > 0 && 'style_settings' in rows[0] ) {
                var settings = JSON.parse(rows[0].style_settings);
                sendData['contract'] = {
                    chat: true,
                    synclo: true
                };
                sendData['widget'] = {
                    display_type: rows[0].display_type,
                    showPosition: settings.showPosition,
                    maxShowTime: settings.maxShowTime,
                    title: settings.title,
                    subTitle: settings.subTitle,
                    description: settings.description,
                    mainColor: settings.mainColor,
                    radiusRatio: settings.radiusRatio,
                    tel: settings.tel,
                    content: settings.content.replace(/\r\n/g, '<br>'),
                    time_text: settings.timeText,
                    display_time_flg: settings.displayTimeFlg
                };

                pool.query(getTriggerListSql, siteKey,
                    function(err, rows){
                        for(var i=0; i<rows.length; i++){
                            if ( !(rows[i].trigger_type in sendData['messages']) ) {
                                sendData['messages'][rows[i].trigger_type] = [];
                            }
                            sendData['messages'][rows[i].trigger_type].push({
                                "id": rows[i].id,
                                "sitekey": siteKey,
                                "activity": JSON.parse(rows[i].activity),
                                "action_type": rows[i].action_type,
                            });
                        }
                        // Website you wish to allow to connect
                        res.setHeader('Access-Control-Allow-Origin', '*');

                        // Request methods you wish to allow
                        res.setHeader('Access-Control-Allow-Methods', 'GET');

                        // Request headers you wish to allow
                        res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');

                        // Set to true if you need the website to include cookies in the requests sent
                        // to the API (e.g. in case you use sessions)
                        res.setHeader('Access-Control-Allow-Credentials', true);

                        res.send(sendData);

                    }
                );

            }
            else {
                var err = new Error('Not Found');
                err.status = 404;
                next(err);
                return false;
            }



        }
    );

    // res.render('index', { title: 'Settings' });
});

module.exports = router;
