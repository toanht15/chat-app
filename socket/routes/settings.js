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

var getWidgetSettingSql  = "SELECT ws.*, com.core_settings FROM m_widget_settings AS ws";
    getWidgetSettingSql += " INNER JOIN (SELECT * FROM m_companies WHERE company_key = ? AND del_flg = 0 ) AS com  ON ( com.id = ws.m_companies_id )";
    getWidgetSettingSql += " WHERE ws.del_flg = 0 ORDER BY id DESC LIMIT 1;";

var getTriggerListSql  = "SELECT am.* FROM t_auto_messages AS am ";
    getTriggerListSql += " INNER JOIN (SELECT * FROM m_companies WHERE company_key = ? AND del_flg = 0 ) AS com  ON ( com.id = am.m_companies_id )";
    getTriggerListSql += " WHERE am.active_flg = 0 AND am.del_flg = 0 AND am.action_type IN (?);";

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

            function isNumeric(str){
                var num = Number(str);
                if (isNaN(num)){
                  num = 0;
                }
                return num;
            }

            if ( rows.length > 0 && 'style_settings' in rows[0] ) {
                var core_settings = JSON.parse(rows[0].core_settings);
                var settings = JSON.parse(rows[0].style_settings);
                sendData['contract'] = core_settings;
                sendData['widget'] = {
                    display_type: isNumeric(rows[0].display_type),
                    showTime: isNumeric(settings.showTime),
                    showName: isNumeric(settings.showName),
                    showPosition: isNumeric(settings.showPosition),
                    maxShowTime: isNumeric(settings.maxShowTime),
                    title: settings.title,
                    showSubtitle: isNumeric(settings.showSubtitle),
                    subTitle: settings.subTitle,
                    showDescription: isNumeric(settings.showDescription),
                    description: settings.description,
                    mainColor: settings.mainColor,
                    stringColor: settings.stringColor,
                    showMainImage: settings.showMainImage,
                    mainImage: settings.mainImage,
                    chatTrigger: isNumeric(settings.chatTrigger),
                    radiusRatio: isNumeric(settings.radiusRatio)
                };

                actionTypeList = [];
                // チャット
                if (('chat' in core_settings) && core_settings['chat']) {
                    actionTypeList.push('1');
                }

                // 画面同期
                if (('synclo' in core_settings) && core_settings['synclo']) {
                    sendData['widget']['tel'] = settings.tel;
                    sendData['widget']['content'] = "";
                    if ( typeof(settings.content) === "string" ) {
                        sendData['widget']['content'] = settings.content.replace(/\r\n/g, '<br>');
                    }
                    sendData['widget']['time_text'] = settings.timeText;
                    sendData['widget']['display_time_flg'] = isNumeric(settings.displayTimeFlg);
                }

                pool.query(getTriggerListSql, [siteKey, actionTypeList.join(",")],
                    function(err, rows){
                        for(var i=0; i<rows.length; i++){
                            if ( !(rows[i].trigger_type in sendData['messages']) ) {
                                sendData['messages'] = [];
                            }
                            sendData['messages'].push({
                                "id": rows[i].id,
                                "sitekey": siteKey,
                                "activity": JSON.parse(rows[i].activity),
                                "action_type": isNumeric(rows[i].action_type),
                            });
                        }

                        /* Cross-Origin */
                        // http://stackoverflow.com/questions/18310394/no-access-control-allow-origin-node-apache-port-issue

                        // Website you wish to allow to connect
                        res.setHeader('Access-Control-Allow-Origin', '*');
                        // Request methods you wish to allow
                        res.setHeader('Access-Control-Allow-Methods', 'GET');
                        // Request headers you wish to allow
                        res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');
                        // Set to true if you need the website to include cookies in the requests sent
                        // to the API (e.g. in case you use sessions)
                        res.setHeader('Access-Control-Allow-Credentials', true);

                        /* no-cache */
                        // http://garafu.blogspot.jp/2013/06/ajax.html
                        res.setHeader("Cache-Control", "no-cache");
                        res.setHeader("Pragma", "no-cache");

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
