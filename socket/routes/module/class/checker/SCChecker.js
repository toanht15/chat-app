const common = require('../../common');
const CommonUtil = require('../util/common_utility');
const list = require('../../company_list');
var SharedData = require('../../shared_data');

module.exports = class SCChecker {

  constructor() {
    this.dayMap = {
      0: 'sun',
      1: 'mon',
      2: 'tue',
      3: 'wed',
      4: 'thu',
      5: 'fri',
      6: 'sat'
    };
  }

  widgetCheck(d, callback) {
    return this.scCheck(1, d, callback);
  }

  sendCheck(d, callback) {
    return this.scCheck(2, d, callback);
  }

  scCheck(type, d, callback) {
    var companyId = list.companyList[d.siteKey],
        siteKey = d.siteKey,
        ret = false,
        message = null,
        now = new Date(),
        nowDay = now.getDay(),
        day = '',
        timeData = [],
        publicHolidayData = null,
        active_flg = '',
        check = '',
        outside_hours_sorry_message = '',
        wating_call_sorry_message = '',
        no_standby_sorry_message = '',
        dateParse = Date.parse(now),
        date = now.getFullYear() + '/' +
            ('0' + (now.getMonth() + 1)).slice(-2) + '/' +
            ('0' + (now.getDate())).slice(-2) + ' ';

    if (common.operationHourSettings[siteKey] != '') {
      for (var i = 0; i <
      common.operationHourSettings[siteKey].length; i++) {
        dayType = JSON.parse(common.operationHourSettings[siteKey][i].type);
        //営業時間設定の条件が「毎日」の場合
        if (dayType == 1) {
          day = this.dayMap[nowDay];
          timeData = JSON.parse(
              common.operationHourSettings[siteKey][i].time_settings).everyday[day];
          publicHolidayData = JSON.parse(
              common.operationHourSettings[siteKey][i].time_settings).everyday['pub'];
        }
        //営業時間設定の条件が「平日・週末」の場合
        else {
          var dayType = 'week';
          if (nowDay == 1 || nowDay == 2 || nowDay == 3 || nowDay == 4 ||
              nowDay == 5) {
            dayType = 'week';
          } else {
            dayType = 'weekend';
          }
          timeData = JSON.parse(
              common.operationHourSettings[siteKey][i].time_settings).weekly[dayType];
          publicHolidayData = JSON.parse(
              common.operationHourSettings[siteKey][i].time_settings).weekly['weekpub'];
        }
        active_flg = JSON.parse(
            common.operationHourSettings[siteKey][i].active_flg);
      }
    }

    if (common.chatSettings[siteKey].sorry_message == '') {
      //営業時間外sorryメッセージ
      outside_hours_sorry_message = common.chatSettings[siteKey].outside_hours_sorry_message;
      //待ち呼sorryメッセージ
      wating_call_sorry_message = common.chatSettings[siteKey].wating_call_sorry_message;
      //待機なしsorryメッセージ
      no_standby_sorry_message = common.chatSettings[siteKey].no_standby_sorry_message;
    } else {
      //営業時間外sorryメッセージ
      outside_hours_sorry_message = common.chatSettings[siteKey].sorry_message;
      //待ち呼sorryメッセージ
      wating_call_sorry_message = common.chatSettings[siteKey].sorry_message;
      //待機なしsorryメッセージ
      no_standby_sorry_message = common.chatSettings[siteKey].sorry_message;
    }

    // ウィジェットが非表示の場合
    if (type == 1 && this.isWidgetHidden(siteKey)) {
      return callback(true,
          {opFlg: false, message: no_standby_sorry_message});
    } else if (type == 2 && this.isWidgetHidden(siteKey)) {
      //営業時間を利用する場合
      if (active_flg == 1) {
        for (var i2 = 0; i2 <
        common.publicHolidaySettingsArray.length; i2++) {
          //祝日の場合
          if (this.isTodayPublicHoliday(now, i2)) {
            //祝日の営業時間設定が「休み」でない場合
            if (this.isTimeSettingActive(publicHolidayData)) {
              for (var i = 0; i < publicHolidayData.length; i++) {
                var endTime = publicHolidayData[i].end;
                // 営業時間の終了時刻が24:00の場合
                if (publicHolidayData[i].end == '24:00') {
                  endTime = '23:59:59';
                }
                // 営業時間内の場合
                if (this.isTimeSettingInTime(date, publicHolidayData, i,
                    dateParse, endTime)) {
                  return callback(true,
                      {opFlg: false, message: no_standby_sorry_message});
                  break;
                }
              }
            }
            //営業時間外の場合
            return callback(true,
                {opFlg: false, message: outside_hours_sorry_message});
          }
        }
        // 祝日でない場合、営業時間設定が「休み」でない場合
        if (this.isTimeSettingActive(timeData)) {
          for (var i = 0; i < timeData.length; i++) {
            var endTime = timeData[i].end;
            // 営業時間の終了時刻が024:00の場合
            if (timeData[i].end == '24:00') {
              endTime = '23:59:59';
            }
            // 営業時間内の場合
            if (this.isTimeSettingInTime(date, timeData, i, dateParse,
                endTime)) {
              check = true;
              return callback(true,
                  {opFlg: false, message: no_standby_sorry_message});
              break;
            }
          }
          //営業時間外の場合
          if (check != true) {
            return callback(true,
                {opFlg: false, message: outside_hours_sorry_message});
          }
        }
      }
      //営業時間を利用しない場合
      else {
        return callback(true,
            {opFlg: false, message: no_standby_sorry_message});
      }
    }

    // ウィジェット表示のジャッジの場合、常に表示は必ずtrue
    if (type === 1 && this.isWidgetAlwaysOpen(siteKey)) {
      return callback(true,
          {opFlg: true, message: no_standby_sorry_message});
    }
    // ウィジェット表示のジャッジの場合、営業時間内のみ表示するの場合、営業時間内の場合はtrue
    if (type === 1 &&
        this.isWidgetActiveInOperatingHour(siteKey) &&
        active_flg == 1) {
      // 祝日の場合
      for (var i2 = 0; i2 <
      common.publicHolidaySettingsArray.length; i2++) {
        if (this.isTodayPublicHoliday(now, i2)) {
          if (this.isTimeSettingActive(publicHolidayData)) {
            for (var i = 0; i < publicHolidayData.length; i++) {
              var endTime = publicHolidayData[i].end;
              // 営業時間の終了時刻が24:00の場合
              if (publicHolidayData[i].end == '24:00') {
                endTime = '23:59:59';
              }
              if (this.isTimeSettingInTime(date, publicHolidayData, i,
                  dateParse, endTime)) {
                return callback(true,
                    {opFlg: true, message: no_standby_sorry_message});
                break;
              }
            }
          }
          return callback(true,
              {opFlg: false, message: outside_hours_sorry_message});
        }
      }
      // 祝日でない場合、営業時間設定が「休み」でない場合
      if (this.isTimeSettingActive(timeData)) {
        for (var i = 0; i < timeData.length; i++) {
          var endTime = timeData[i].end;
          // 営業時間の終了時刻が24:00の場合
          if (timeData[i].end == '24:00') {
            endTime = '23:59:59';
          }
          // 営業時間内の場合
          if (this.isTimeSettingInTime(date, timeData, i, dateParse, endTime)) {
            return callback(true,
                {opFlg: true, message: no_standby_sorry_message});
            break;
          }
        }
      }
      return callback(true,
          {opFlg: false, message: outside_hours_sorry_message});
    }

    // チャット上限数を設定していない場合
    if (this.isSCFlgInactive(siteKey)) {
      // オペレーターが待機している場合
      if (type === 1 &&
          (Number(
              common.widgetSettings[siteKey].style_settings.displayStyleType) ===
              2 &&
              this.getOperatorCnt(siteKey) > 0)
      ) {
        return callback(true,
            {opFlg: true, message: outside_hours_sorry_message});
      }
      // 営業時間設定を利用している場合
      if (active_flg === 1) {
        //祝日の場合
        for (var i2 = 0; i2 <
        common.publicHolidaySettingsArray.length; i2++) {
          if (this.isTodayPublicHoliday(now, i2)) {
            check = true;
            //祝日の営業時間設定が「休み」でない場合
            if (this.isTimeSettingActive(publicHolidayData)) {
              for (var i = 0; i < publicHolidayData.length; i++) {
                var endTime = publicHolidayData[i].end;
                // 営業時間の終了時刻が24:00の場合
                if (publicHolidayData[i].end == '24:00') {
                  endTime = '23:59:59';
                }
                //営業時間内の場合
                if (this.isTimeSettingInTime(date, publicHolidayData, i,
                    dateParse, endTime)) {
                  // オペレータが待機している場合
                  if (this.isOperatorActive(siteKey)) {
                    return callback(true, {
                      opFlg: true,
                      message: null,
                      in_flg: common.chatSettings[siteKey].in_flg
                    });
                  }
                  //オペレータが待機していない場合
                  else {
                    ret = false;
                    message = no_standby_sorry_message;
                  }
                }
                //営業時間外の場合
                else {
                  ret = false;
                  message = outside_hours_sorry_message;
                }
              }
            }
            //祝日の営業時間設定が「休み」の場合
            else {
              ret = false;
              message = outside_hours_sorry_message;
            }
          }
        }

        //祝日でない場合
        if (check != true) {
          //営業時間設定が「休み」の場合
          if (!this.isTimeSettingActive(timeData)) {
            ret = false;
            message = outside_hours_sorry_message;
          }
          //営業時間設定が「休み」でない場合
          else {
            for (var i = 0; i < timeData.length; i++) {
              var endTime = timeData[i].end;
              // 営業時間の終了時刻が24:00の場合
              if (timeData[i].end == '24:00') {
                endTime = '23:59:59';
              }
              //営業時間内
              if (this.isTimeSettingInTime(date, timeData, i, dateParse,
                  endTime)) {
                check = true;
                //オペレータが待機している場合
                if (this.isOperatorActive(siteKey)) {
                  return callback(true, {
                    opFlg: true,
                    message: null,
                    in_flg: Number(common.chatSettings[siteKey].in_flg)
                  });
                }
                //オペレータが待機していない場合
                else {
                  ret = false;
                  message = no_standby_sorry_message;
                }
              }
            }
            // 営業時間外の場合
            if (check != true) {
              ret = false;
              message = outside_hours_sorry_message;
            }
          }
        }
      }
      //営業時間設定を利用していない場合
      else {
        //オペレータが待機している場合
        if (this.isOperatorActive(siteKey)) {
          return callback(true,
              {
                opFlg: true,
                message: null,
                in_flg: common.chatSettings[siteKey].in_flg
              });
        }
        //オペレータが待機していない場合
        else {
          ret = false;
          message = no_standby_sorry_message;
        }
      }
    }


    // チャット上限数を設定している場合
    else if (!this.isSCFlgInactive(siteKey)) {
      if (type === 1 &&
          this.isWidgetShowStyleMinimize(siteKey) &&
          this.isSCListExists(siteKey)) {
        var userIds = Object.keys(SharedData.scList[siteKey].user);
        if (userIds.length !== 0) {
          for (var i = 0; i < userIds.length; i++) {
            if (Number(SharedData.scList[siteKey].user[userIds[i]]) ===
                Number(SharedData.scList[siteKey].cnt[userIds[i]])) continue;
            return callback(true,
                {opFlg: true, message: outside_hours_sorry_message});
          }
        }
      }
      //営業時間設定を利用している場合
      if (active_flg === 1) {
        for (var i2 = 0; i2 <
        common.publicHolidaySettingsArray.length; i2++) {
          //祝日の場合
          if (this.isTodayPublicHoliday(now, i2)) {
            check = true;
            //祝日の営業時間設定が「休み」でない場合
            if (this.isTimeSettingActive(publicHolidayData)) {
              for (var i = 0; i < publicHolidayData.length; i++) {
                var endTime = publicHolidayData[i].end;
                // 営業時間の終了時刻が24:00の場合
                if (publicHolidayData[i].end == '24:00') {
                  endTime = '23:59:59';
                }
                //営業時間内の場合
                if (this.isTimeSettingInTime(date, publicHolidayData, i,
                    dateParse, endTime)) {
                  //オペレータが待機している場合
                  if (this.isOperatorActive(siteKey)) {
                    // チャット上限数をみる
                    if (this.isSCListExists(siteKey)) {
                      var userIds = Object.keys(
                          SharedData.scList[siteKey].user);
                      if (userIds.length !== 0) {
                        for (var i3 = 0; i3 < userIds.length; i3++) {
                          if (Number(
                              SharedData.scList[siteKey].user[userIds[i]]) ===
                              Number(
                                  SharedData.scList[siteKey].cnt[userIds[i]])) continue;
                          return callback(true, {
                            opFlg: true,
                            message: null,
                            in_flg: common.chatSettings[siteKey].in_flg
                          });
                        }
                        //上限数を超えている場合
                        if (ret != true) {
                          ret = false;
                          message = wating_call_sorry_message;
                        }
                      }
                    }
                  }
                  //待機中のオペレータがいない場合
                  else {
                    ret = false;
                    message = no_standby_sorry_message;
                  }
                }
                //営業時間外の場合
                else {
                  ret = false;
                  message = outside_hours_sorry_message;
                }
              }
            }
            //休みの設定にしているとき
            else {
              ret = false;
              message = outside_hours_sorry_message;
            }
          }
        }

        //祝日でない場合
        if (check != true) {
          //営業時間設定が「休み」の場合
          if (!this.isTimeSettingActive(timeData)) {
            ret = false;
            message = outside_hours_sorry_message;
          }
          //営業時間設定が「休み」でない場合
          else {
            for (var i = 0; i < timeData.length; i++) {
              var endTime = timeData[i].end;
              // 営業時間の終了時刻が24:00の場合
              if (timeData[i].end == '24:00') {
                endTime = '23:59:59';
              }
              //営業時間内の場合
              if (this.isTimeSettingInTime(date, timeData, i, dateParse,
                  endTime)) {
                check = true;
                //オペレータが待機している場合
                if (this.isOperatorActive(siteKey)) {
                  // チャット上限数をみる
                  if (this.isSCListExists(siteKey)) {
                    var userIds = Object.keys(
                        SharedData.scList[siteKey].user);
                    if (userIds.length !== 0) {
                      for (var i2 = 0; i2 < userIds.length; i2++) {
                        if (Number(
                            SharedData.scList[siteKey].user[userIds[i]]) ===
                            Number(
                                SharedData.scList[siteKey].cnt[userIds[i]])) continue;
                        return callback(true, {
                          opFlg: true,
                          message: null,
                          in_flg: Number(common.chatSettings[siteKey].in_flg)
                        });
                      }
                      //上限数を超えている場合
                      if (ret != true) {
                        ret = false;
                        message = wating_call_sorry_message;
                      }
                    }
                  }
                }
                //待機中のオペレータがいない場合
                else {
                  ret = false;
                  message = no_standby_sorry_message;
                }
              }
            }
            // 営業時間外の場合
            if (check != true) {
              ret = false;
              message = outside_hours_sorry_message;
            }
          }
        }
      }
      //営業時間設定を利用しない場合
      else {
        //オペレータが待機している場合
        if (this.isOperatorActive(siteKey)) {
          // チャット上限数をみる
          if (this.isSCListExists(siteKey)) {
            var userIds = Object.keys(SharedData.scList[siteKey].user);
            if (userIds.length !== 0) {
              for (var i = 0; i < userIds.length; i++) {
                if (Number(SharedData.scList[siteKey].user[userIds[i]]) ===
                    Number(
                        SharedData.scList[siteKey].cnt[userIds[i]])) continue;
                return callback(true,
                    {
                      opFlg: true,
                      message: null,
                      in_flg: Number(common.chatSettings[siteKey].in_flg)
                    });
              }
              //上限数を超えている場合
              if (ret != true) {
                ret = false;
                message = wating_call_sorry_message;
              }
            }
          }
        }
        //待機中のオペレータがいない場合
        else {
          ret = false;
          message = no_standby_sorry_message;
        }
      }
    }
    return callback(true, {opFlg: ret, message: message});
  }

  isSCListExists(siteKey) {
    return CommonUtil.isKeyExists(SharedData.scList, siteKey);
  }

  isOperatorActive(siteKey) {
    return (this.isWidgetShowStyleMinimize(siteKey) &&
        this.getOperatorCnt(siteKey) >
        0) ||
        (this.isWidgetShowStyleMaximize(siteKey) &&
            this.getOperatorCnt(siteKey) >
            0) ||
        (Number(
            common.widgetSettings[siteKey].style_settings.displayStyleType) ===
            4 &&
            this.getOperatorCnt(siteKey) > 0);
  }

  isWidgetShowStyleMaximize(siteKey) {
    return Number(
        common.widgetSettings[siteKey].style_settings.displayStyleType) ===
        1;
  }

  isWidgetShowStyleMinimize(siteKey) {
    return Number(
        common.widgetSettings[siteKey].style_settings.displayStyleType) ===
        2;
  }

  isSCFlgInactive(siteKey) {
    return Number(common.chatSettings[siteKey].sc_flg) === 2;
  }

  isWidgetActiveInOperatingHour(siteKey) {
    return common.widgetSettings[siteKey].style_settings.displayType === 4;
  }

  isWidgetAlwaysOpen(siteKey) {
    return common.widgetSettings[siteKey].style_settings.displayType === 1;
  }

  isTimeSettingInTime(date, publicHolidayData, i, dateParse, endTime) {
    return Date.parse(
        new Date(date + publicHolidayData[i].start)) <=
        dateParse && dateParse <
        Date.parse(new Date(date + endTime));
  }

  isTimeSettingActive(timeSetting) {
    return timeSetting[0].start != '' &&
        timeSetting[0].end != '';
  }

  isTodayPublicHoliday(now, i2) {
    return (now.getFullYear() + '/' + '0' +
        (now.getMonth() + 1)).slice(-2) + '/' +
        ('0' + (now.getDate())).slice(-2) ==
        common.publicHolidaySettingsArray[i2].year + '/' +
        common.publicHolidaySettingsArray[i2].month + '/' +
        common.publicHolidaySettingsArray[i2].day;
  }

  isWidgetHidden(siteKey) {
    return common.widgetSettings[siteKey].style_settings.displayType === 3;
  }

  getOperatorCnt(siteKey) {
    var cnt = 0;
    if (CommonUtil.isset(SharedData.activeOperator[siteKey])) {
      var key = Object.keys(SharedData.activeOperator[siteKey]);
      cnt = key.length;
    }
    return cnt;
  }
};