<?php
/**
 * Created by PhpStorm.
 * User: ryo.hosokawa
 * Date: 2019/02/20
 * Time: 18:26
 */
?>
<script type="text/javascript">
  'use strict';

  var sincloApp = angular.module('sincloApp', ['ngSanitize']);
  sincloApp.config(function($controllerProvider){
    sincloApp.controllerProvider = $controllerProvider;
  });
  sincloApp.controller('DiagramController', [
    '$scope', '$timeout', 'SimulatorService', '$compile', function($scope, $timeout, SimulatorService, $compile) {


      //メニューバーのアイコンにdraggableを付与
      $('#node_list > i ').each(function(index, target) {
        $(target).draggable({
          helper: 'clone'
        });
      });

      var nodeFactory = new NodeFactory();
      var wasMoved = false;
      var nodeTypeArray = [
        'branch',
        'text',
        'scenario',
        'jump',
        'link'
      ];


      /* Scenario List */
      $scope.scenarioList = <?= json_encode($scenarioList, JSON_UNESCAPED_UNICODE) ?>;
      $scope.scenarioArrayList = [{"key": "", "value":"シナリオを選択して下さい"}];
      for ( var idx in $scope.scenarioList ) {
        $scope.scenarioArrayList.push({
          "key": idx,
          "value": $scope.scenarioList[idx]
        });
      }
      /* Scenario List */

      /* Model for scenario node */
      $scope.selectedScenario = $scope.scenarioArrayList[0];
      /* Model for scenario node */

      /* Model for link node */
      $scope.linkUrl = "";
      $scope.linkType = "same";
      /* Model for link node */

      /* Jump list */
      $scope.jumpTargetListOrigin = [{"key": "", "value": "ノード名を選択してください"}];
      $scope.jumpTargetList = $scope.jumpTargetListOrigin.concat();
      /* Jump list  */

      /* Model for jump node */
      $scope.jumpTarget = $scope.jumpTargetList[0];
      /* Model for jump node */

      /* Model for text node */
      $scope.speakTextList = [];
      $scope.speakTextTitle = "";
      /* Model for text node */

      /* Model for handle button */
      $scope.addBtnHide = false;
      $scope.deleteBtnHide = false;
      /* Model for handle button */


      var currentEditCell = null;

      var nodeMaster = function(type, posX, posY) {
        var node = nodeFactory.createNode(type, posX, posY);
        graph.addCell(node);
        initNodeEvent(node);
        for(var i = 0; i < graph.getCells().length; i++) {
        }
      };

      var canvas = document.getElementById('canvas');
      $(canvas).droppable({
        drop: function(e, ui) {
          if (ui.draggable.attr('id') === 'popup-frame') return;
          var cursorPos = paper.clientToLocalPoint(ui.offset.left, ui.offset.top);
          nodeMaster(ui.draggable.attr('id'), cursorPos.x, cursorPos.y);
        }
      });

      var graph = new joint.dia.Graph;

      var paper = new joint.dia.Paper({
        el: canvas,
        width: canvas.width,
        height: canvas.height,
        gridSize: 5,
        model: graph,
        linkPinning: false,
        defaultLink: new joint.dia.Link({
          attrs: {
            '.connection': {
              stroke: '#AAAAAA',
              'stroke-width': 3
            },
            '.marker-target': {
              stroke: '#AAAAAA',
              fill: '#AAAAAA',
              d: 'M 15 0 L 0 6 L 0 9 L 15 15 z'
            },
            '.link-tools .link-tool .tool-remove circle': {
              'class': 'diagram'
            },
            '.marker-arrowhead[end="source"]': {d: 'M 0 0 z'},
            '.marker-arrowhead[end="target"]': {d: 'M 0 0 z'}
          }
        }),
        validateConnection: function(cellViewS, magnetS, cellViewT, magnetT, end, linkView) {
          if (cellViewS.model.attr('nodeBasicInfo/nextNodeId') && cellViewS.model.attr('nodeBasicInfo/nextNodeId') !==
              '') {
            linkView.model.remove();
            return false;
          }
          // in portからは矢印を表示させない
          if (magnetS && magnetS.getAttribute('port-group') === 'in') return false;
          // 同一Elementのout → in portは許容しない
          if (cellViewS === cellViewT) return false;

          // 子供から親への接続は許容しない
          var parents = cellViewS.model.getAncestors();
          if (parents.length > 0 && parents[0].id === cellViewT.model.id) {
            return false;
          }
          //既に他ポートに接続しているout portは線を出さない


          // outPortには入力させない
          return magnetT && magnetT.getAttribute('port-group') === 'in';
        },
        validateMagnet: function(cellView, magnet) {
          return magnet.getAttribute('magnet') !== 'passive';
        }
      });

      graph.addCell(startNode());

      var dragReferencePosition = null;
      var dataForUpdate = $('#TChatbotDiagramActivity').val();

      if (dataForUpdate !== null && dataForUpdate !== '') {
        graph.fromJSON(JSON.parse(dataForUpdate));
        setTimeout(function(){
          graph.resetCells(dataForUpdate.cells);
          initNodeEvent(graph.getCells());
        }, 500);
      }

      var allDrawnCellList = graph.getCells();
      for( var i = 0; i < allDrawnCellList; i++) {
      }

      paper.on('cell:pointerup',
          function(cellView, evt, x, y) {

            //init current edit cell to null;
            currentEditCell = null;
            if (!wasMoved && isNeedModalOpen(cellView)) {
              currentEditCell = setViewElement(cellView);
              if($('#popup-main > div')[0]) {
                $('#popup-main')[0].removeChild($('#popup-main > div')[0]);
              }
              var modalData = processModalCreate(cellView);
              $compile(modalData.content)($scope);
              $timeout(function(){
                modalOpen.call(window, modalData.content, modalData.id, modalData.name, 'moment');
                initPopupCloseEvent();
              });
            }
            wasMoved = false;
          });

      paper.on('blank:pointerdown', function(e, x, y) {
        dragReferencePosition = {x: x * paper.scale().sx, y: y * paper.scale().sy};
      });

      paper.on('blank:pointerup', function() {
        dragReferencePosition = null;
      });

      paper.on('link:connect', function(linkView, e) {
        try {
          linkView.sourceView.model.attr('nodeBasicInfo/nextNodeId', linkView.targetView.model.attributes.id);
          // 接続元が分岐　かつ　(接続先が テキスト　か　分岐)
          if( linkView.sourceView.model.attr('nodeBasicInfo/nodeType') === "childPortNode"
              &&( linkView.targetView.model.attr('nodeBasicInfo/nodeType') === "text"
                  ||linkView.targetView.model.attr('nodeBasicInfo/nodeType') === "branch" )) {
            previewHandler.setDefaultNodeName(linkView.sourceView.model, linkView.targetView.model);
          }
        } catch (e) {
          console.log('unexpected connect');
        }
      });

      graph.on('remove', function(deleteView, b) {
        if (deleteView.isLink() && deleteView.attributes.target.id) {
          resetNextNode(deleteView.attributes.source.id);
        }
      });

      $('input[type=range]').on('input', function(e) {
        paper.scale(e.target.value / 5);
      });

      $(canvas).mousemove(function(e) {
        if (dragReferencePosition) {
          paper.translate(
              e.offsetX - dragReferencePosition.x,
              e.offsetY - dragReferencePosition.y
          );
        }
      });

      /*paper関連ここまで*/

      // 保存ボタン
      $('#submitBtn').on('click', function(e) {
        // データをJSONにして送信
        $('#TChatbotDiagramActivity').val(exportJSON());
        $('#TChatbotDiagramsEntryForm').submit();
      });

      var addMoveFlg = function() {
        wasMoved = true;
      };

      function initNodeEvent(node) {
        for (var i = 0; i < node.length; i++) {

          if (node[i].attr('nodeBasicInfo/nodeType') === 'childViewNode'
              || node[i].attr('nodeBasicInfo/nodeType') === 'childPortNode') {
            node[i].on('change:position', childMove);
          }

          if (nodeTypeArray.indexOf(node[i].attr('nodeBasicInfo/nodeType')) > -1
              || node[i].attr('nodeBasicInfo/nodeType') === 'operator'
              || node[i].attr('nodeBasicInfo/nodeType') === 'cv') {
            node[i].on('change:position', addMoveFlg);
          }
        }
      }

      function childMove(elm, pos, self) {
        if (self.translateBy === elm.id) {
          var parent = elm.getAncestors()[0];
          if (parent) {
            parent.unembed(elm);
            parent.translate(self.tx, self.ty);
            parent.embed(elm);
          }
        }
      }

      function isNeedModalOpen(cell) {
        var type = cell.model.attr('nodeBasicInfo/nodeType');
        if (type != null) {
          return nodeTypeArray.indexOf(type) > -1
              || type === 'childViewNode'
              || type === 'childPortNode'
              || type === 'operator'
              || type === 'cv';
        }
        return false;
      }

      function processModalCreate(elm) {
        var type = elm.model.attr('nodeBasicInfo/nodeType');
        var htmlCreator,
            modalName,
            modalClass;
        if (elm.model.getAncestors()[0] != null) {
          type = elm.model.getAncestors()[0].attr('nodeBasicInfo/nodeType');
        }
        switch (type) {
          case 'branch':
            htmlCreator = createBranchHtml;
            modalName = '分岐';
            modalClass = 'p_diagrams_branch';
            break;
          case 'text':
            htmlCreator = createTextHtml;
            modalName = 'テキスト発言';
            modalClass = 'p_diagrams_text';
            break;
          case 'scenario':
            htmlCreator = createScenarioHtml;
            modalName = 'シナリオ呼出';
            modalClass = 'p_const_diagrams';
            break;
          case 'jump':
            htmlCreator = createJumpHtml;
            modalName = 'ジャンプ';
            modalClass = 'p_const_diagrams';
            break;
          case 'link':
            htmlCreator = createLinkHtml;
            modalName = 'リンク';
            modalClass = 'p_const_diagrams';
            break;
          case 'operator':
            htmlCreator = createOperatorHtml;
            modalName = 'オペレーター呼出';
            modalClass = 'p_close_diagrams';
            break;
          case 'cv':
            htmlCreator = createCvHtml;
            modalName = 'CVポイント';
            modalClass = 'p_close_diagrams';
            break;
          default:
            return null;
        }
        var nodeData = currentEditCell.attr('actionParam');
        if (currentEditCell.getAncestors()[0]) {
          nodeData = currentEditCell.getAncestors()[0].attr('actionParam');
        }

        return {
          name: modalName,
          content: htmlCreator(nodeData),
          id: modalClass
        };
      }

      function initPopupCloseEvent() {
        popupEvent.closePopup = function(type) {
          switch (type) {
            case 1:
              saveEditNode();
              previewHandler.typeJump.editTargetName();
              console.log($scope.selectedScenario);
              popupEvent.closeNoPopup();
              //保存処理
              break;
            case 2:
              //削除処理
              popupEventOverlap.closePopup = function() {
                previewHandler.typeJump.deleteTargetName(currentEditCell);
                deleteEditNode();
                popupEventOverlap.closeNoPopupOverlap();
                popupEvent.closeNoPopup()
              };
              popupEventOverlap.open('現在のノードを削除します。よろしいですか？',"p_diagram_delete_alert" ,"削除の確認");
              break;
            default:
              break;
          }
        };
      }

      function deleteEditNode() {
        if (currentEditCell == null) {
          //編集対象が無い場合は処理を実行しない
          return;
        }
        if (currentEditCell.getAncestors()[0]) {
          //テキスト発言、または条件分岐の場合、全てのジャンプアクションを取得
          if (currentEditCell.getAncestors()[0].attr('nodeType') === 'text'
              || currentEditCell.getAncestors()[0].attr('nodeType') === 'branch') {
            bindJumpData(currentEditCell.getAncestors()[0]);
          }
          currentEditCell.getAncestors()[0].remove();
        } else {
          currentEditCell.remove();
        }
        currentEditCell = null;
      }

      function saveEditNode() {
        if (currentEditCell == null) {
          //編集対象が無い場合は処理を実行しない
          return;
        }
        if (currentEditCell.getAncestors()[0]) {
          //シナリオ呼出・ジャンプ・リンクの場合
          bindSingleView(currentEditCell.getAncestors()[0].attr('nodeBasicInfo/nodeType'));
        } else {

        }

      }

      function bindJumpData() {
        graph.getElements();
      }

      function setViewElement(target) {
        //親エレメント直下の子エレメントを設定
        //
        var viewNode = target.model;
        var childList = viewNode.getEmbeddedCells();
        if (viewNode.attr('nodeBasicInfo/nodeType') === 'operator'
            || viewNode.attr('nodeBasicInfo/nodeType') === 'cv') {
          // オペレーター、 CVの場合は
          return viewNode;
        } else if (childList.length > 0) {
          //一番上のViewを基準にする
          //親の場合は、直下の子供
          viewNode = childList[0];
        } else {
          //一番上のViewを基準にする
          //子供の場合は、親を取得して子供に再設定する。
          viewNode = viewNode.getAncestors()[0].getEmbeddedCells()[0];
        }
        return viewNode;
      }

      function bindSingleView(type) {
        var viewText = '';
        var nodeParam = {};
        var target,
            nodeName,
            speakTextContents,
            btnType,
            selectionList;
        switch (type) {
          case 'text':
            nodeName = $('#my_node_name').val();
            speakTextContents = nodeEditHandler.typeText.convertContents($('.text_modal_setting'));
            currentEditCell.getAncestors()[0].attr('.label/text',
                convertTextForTitle(convertTextLength(nodeName, 3), 'テキスト発言'));
            currentEditCell.attr('text/text', convertTextLength(textEditor.textLineSeparate(speakTextContents[0]), 46));
            //配列は直接上書きができないので一度nullにする
            currentEditCell.getAncestors()[0].attr('actionParam/text', null);
            nodeParam = {
              nodeName: nodeName,
              text: speakTextContents
            };
            break;
          case 'branch':
            nodeName = $('#my_node_name').val();
            speakTextContents = $('.flex_row_box > textarea').val();
            btnType = $('#branchBtnType option:selected').val();
            selectionList = nodeEditHandler.typeBranch.convertContents($('.setting_row'));
            nodeEditHandler.typeBranch.handleBranchPorts(selectionList);
            //配列は直接上書きができないので一度nullにする
            currentEditCell.getAncestors()[0].attr('.label/text',
                convertTextForTitle(convertTextLength(nodeName, 6), '分岐'));
            currentEditCell.getAncestors()[0].attr('actionParam/selection', null);
            currentEditCell.attr('text/text', convertTextLength(textEditor.textLineSeparate(speakTextContents), 46));
            nodeParam = {
              nodeName: nodeName,
              text: speakTextContents,
              btnType: btnType,
              selection: selectionList
            };
            break;
          case 'scenario':
            target = $('#callTargetScenario option:selected');
            if(target.val() !== ""){
              viewText = target.text();
            }
            nodeParam = {
              scenarioId: target.val()
            };
            currentEditCell.attr('text/text', convertTextLength(viewText, 14));
            break;
          case 'jump':
            target = $('#jumpTargetNode option:selected');
            if(target.val() !== ""){
              viewText = target.text();
            }
            nodeParam = {
              targetId: target.val()
            };
            currentEditCell.attr('text/text', convertTextLength(viewText, 14));
            break;
          case 'link':
            viewText = $('#linkTarget').val();
            var radio = $('input[name=link_type]:checked').val();
            nodeParam = {
              link: viewText,
              linkType: radio
            };
            currentEditCell.attr('text/text', convertTextLength(viewText, 28));
            break;
          default:
            break;
        }
        currentEditCell.getAncestors()[0].attr('actionParam', nodeParam);
      }

      function convertTextLength(text, regNum) {
        if (text) {
          return text.length > regNum ? (text).slice(0, regNum) + '...' : text;
        } else {
          return text;
        }
      }

      function convertTextForTitle(text, basicTitle) {
        if (text) {
          return basicTitle + ':' + text;
        } else {
          return basicTitle;
        }
      }

      function createBranchHtml(nodeData) {
        //html.find('input[type=text]').val(nodeData.nodeName);
        //html.find('.flex_row_box > textarea').val(nodeData.text);
        //html.find('select').val(nodeData.btnType);
        //html = nodeEditHandler.typeBranch.createContents(html, nodeData);
        return $('<branch_modal></branch_modal>');
      }

      function createTextHtml(nodeData) {
        nodeEditHandler.typeText.createTextArray(nodeData);
        $scope.speakTextTitle = nodeData.nodeName;
        return $('<text-modal></text-modal>');
      }

      function createLinkHtml(nodeData) {
        $scope.linkUrl = nodeData.link;
        $scope.linkType = nodeData.linkType;
        return $('<link-modal></link-modal>');
      }

      function createScenarioHtml(nodeData) {
        $scope.selectedScenario.key = nodeData.scenarioId;
        return $('<scenario-modal></scenario-modal>');
      }

      function createJumpHtml(nodeData) {
        nodeEditHandler.typeJump.createJumpArray();
        $scope.jumpTarget.key = nodeData.targetId;
        return $('<jump-modal></jump-modal>');
      }

      function createOperatorHtml(nodeData) {
        return $('<operator-modal></operator-modal>');
      }

      function createCvHtml(nodeData) {
        return $('<cv-modal></cv-modal>');
      }

      $scope.addBtnClick = function(type, index){
        switch(type) {
          case "text":
            $scope.speakTextList.push({"content": ""});
            break;
          case "branch":
            break;
        }
      };
      
      $scope.deleteBtnClick = function(type, index){
        switch(type) {
          case "text":
            $scope.speakTextList.splice(index, 1);
            break;
          case "branch":
            break;
        }
        $timeout(function(){
          popupEvent.resize();
        })
      };

      $scope.$watch("speakTextList", function(){
        if($scope.speakTextList.length === 1) {
          $scope.addBtnHide = false;
          $scope.deleteBtnHide = true;
        } else if ($scope.speakTextList.length === 5) {
          $scope.addBtnHide = true;
          $scope.deleteBtnHide = false;
        } else {
          $scope.addBtnHide = false;
          $scope.deleteBtnHide = false;
        }
        $timeout(function(){
          $scope.$apply();
        })
      }, true);

      function resetNextNode(targetId) {
        var allElmList = graph.getElements();
        for (var i = 0; i < allElmList.length; i++) {
          if (allElmList[i].attributes.id === targetId) {
            allElmList[i].attr('nodeBasicInfo/nextNodeId', '');
          }
        }
      }

      var nodeEditHandler = {
        typeJump: {
          createJumpArray: function() {
            //呼び出される際に、一度リストの初期化を行っておく（concatは値渡し用）
            $scope.jumpTargetList = $scope.jumpTargetListOrigin.concat();
            var allElmList = graph.getElements();
            for (var i = 0; i < allElmList.length; i++) {
              if (allElmList[i].attr('nodeBasicInfo/nodeType') === 'text'
                  || allElmList[i].attr('nodeBasicInfo/nodeType') === 'branch') {
                if (allElmList[i].attr('actionParam/nodeName') !== '') {
                  //ノード名がある場合は、optionリストに追加
                  $scope.jumpTargetList.push({
                    "key": allElmList[i].attributes.id,
                    "value": allElmList[i].attr('actionParam/nodeName')
                  });
                }
              }
            }
          }
        },
        typeText: {
          convertContents: function(originContents) {
            return nodeEditHandler.textAreaToArray(originContents);
          },
          createTextArray: function(nodeData) {
            //いれる前に一旦空にする
            $scope.speakTextList = null;
            if (nodeData.text.length > 0) {
              for (var i = 0; i < nodeData.text.length; i++) {
                $scope.speakTextList = [];
                $scope.speakTextList.push({
                  "content": nodeData.text
                });
              }
            } else {
              $scope.speakTextList = [{"content" : ""}];
            }
          }
        },
        typeBranch: {
          convertContents: function(originContents) {
            return nodeEditHandler.textToArray(originContents);
          },
          createContents: function(html, nodeData) {
            if (nodeData.selection.length > 0) {
              html.find('.setting_row > input[type=text]').val(nodeData.selection[0]);
              for (var i = 1; i < nodeData.selection.length; i++) {
                var tmpClone = html.find('.setting_row').last().clone();
                tmpClone.find('input[type=text]').val(nodeData.selection[i]);
                console.log(tmpClone);
                html.find('.branch_modal_setting_content').append(tmpClone);
              }
            } else {

            }
            return html;
          },
          handleBranchPorts: function(additionalPortList) {
            var self = nodeEditHandler.typeBranch;
            var offsetMasterNodeHeight = 90;
            var addMasterNodeHeight = 0;
            var masterBranch = currentEditCell.getAncestors()[0];
            self.removeAllPortView(masterBranch);
            var masterViewData = masterBranch.attributes;
            for (var i = 0; i < additionalPortList.length; i++) {
              var port = self.portCreator(
                  masterViewData.position.x,
                  masterViewData.position.y,
                  additionalPortList[i],
                  90 + i * 35
              );
              addMasterNodeHeight += 35;
              currentEditCell.getAncestors()[0].embed(port);
              graph.addCell(port);
              initNodeEvent([port]);
            }
            self.resizeParent();
            masterViewData.size.height = offsetMasterNodeHeight + addMasterNodeHeight;
            console.log(masterViewData);

          },
          resizeParent: function() {

          },
          removeAllPortView: function(branch) {
            var childList = branch.getEmbeddedCells();
            for (var i = 0; i < childList.length; i++) {
              if (childList[i].attr('nodeBasicInfo/nodeType') === 'childPortNode') {
                childList[i].remove();
              }
            }
          },
          portCreator: function(posX, posY, text, additionalY) {
            return new joint.shapes.devs.Model({
              position: {x: posX + 5, y: posY + additionalY},
              size: {width: 190, height: 30},
              outPorts: ['out'],
              ports: {
                groups: {
                  'out': {
                    attrs: {
                      '.port-body': {
                        fill: "#F6ABAC",
                        'fill-opacity': "0.9",
                        height: 30,
                        width: 30,
                        stroke: false,
                        rx: 3,
                        ry: 3
                      },
                      '.port-label': {
                        'font-size': 0
                      }
                    },
                    position: {
                      name: 'absolute',
                      args: {
                        x: 185,
                        y: 0
                      }
                    },
                    z: 4,
                    markup: '<rect class="port-body"/>'
                  }
                }
              },
              attrs: {
                '.label': {
                  text: text,
                  'ref-width': '70%',
                  'font-size': '12px',
                  fill: '#000'
                },
                rect: {
                  fill: '#FFFFFF',
                  stroke: false
                },
                nodeBasicInfo: {
                  nodeType: 'childPortNode',
                  nextNode: ''
                }
              }
            });
          }
        },
        textToArray: function(contents) {
          var contentArray = [];
          for (var i = 0; i < contents.length; i++) {
            if ($(contents[i]).children('input[type=text]').val()) {
              contentArray.push($(contents[i]).children('input[type=text]').val());
            }
          }
          return contentArray;
        },
        textAreaToArray: function(contents) {
          var contentArray = [];
          for (var i = 0; i < contents.length; i++) {
            if ($(contents[i]).children('textarea').val()) {
              contentArray.push($(contents[i]).children('textarea').val());
            }
          }
          return contentArray;
        }
      };
      
      var textEditor = {
        textLineSeparate: function(text){
          var self = textEditor;
          var originTextArray = text.split(/\r\n|\n/);
          var resultTextArray = [];
          for( var i = 0; i < originTextArray.length; i++ ){
            if( originTextArray[i].length > 14 ){
              Array.prototype.push.apply(resultTextArray, self.textLineCreate(originTextArray[i]));
            } else {
              resultTextArray.push(originTextArray[i]);
            }
          }
          return resultTextArray.join("\n");
        },
        textLineCreate: function(textLine){
          var currentText = textLine;
          var textArray = [];
          var loopNum = currentText.length / 15;
          for( var i = 0; i < loopNum ; i++){
            textArray.push(currentText.substr(0, 15));
            currentText = currentText.substr(15);
          }
          return textArray;
        }
      };

      var previewHandler = {
        typeText: {
          addBalloon: function(index){
            var newBalloon = $('#text_modal_preview > div.chatTalk:first-child').clone();
            console.log(newBalloon.find('span').text());
            newBalloon.find('span').text("");
            console.log($($('#text_modal_preview')[index]));
            $($('#text_modal_preview')[index]).after(newBalloon);
          },
          removeBalloon: function(){

          }
        },
        setDefaultNodeName: function(source, target){
          //既に情報が入っている場合はreturnさせる
          if(target.attr("actionParam/nodeName") !== "") return;
          var prefix,
              splitNum;
          var defaultValue = source.attr(".label/text");
          if(target.attr("nodeBasicInfo/nodeType") === "text") {
            prefix = "テキスト発言";
            splitNum = 3;
          } else {
            prefix = "分岐";
            splitNum = 6;
          }
          target.attr("actionParam/nodeName", defaultValue);
          target.attr(".label/text",
              convertTextForTitle(convertTextLength(defaultValue, splitNum), prefix));
        },
        typeJump: {
          editTargetName: function(){
            var allCells = graph.getCells();
            var targetCell = currentEditCell;
            for(var i = 0; i < allCells.length; i++) {
              if(allCells[i].isElement()
                  && allCells[i].attr("nodeBasicInfo/nodeType") === "jump"
                  && allCells[i].attr("actionParam/targetId") === targetCell.getAncestors()[0].id){
                allCells[i].getEmbeddedCells()[0].attr("text/text", convertTextLength(targetCell.getAncestors()[0].attr("actionParam/nodeName"), 14));
              }
            }

          },
          deleteTargetName: function(targetCell){
            var allCells = graph.getCells();
            console.log(targetCell);
            for(var i = 0; i < allCells.length; i++) {
              if(allCells[i].isElement()
                  && allCells[i].attr("nodeBasicInfo/nodeType") === "jump"
                  && allCells[i].attr("actionParam/targetId") === targetCell.getAncestors()[0].id){
                allCells[i].attr("actionParam/targetId", "");
                allCells[i].getEmbeddedCells()[0].attr("text/text", "");
              }
            }
          }
        }
      };

      function exportJSON() {
        var json = JSON.stringify(graph.toJSON());
        console.log(json);
        return json;
      }

      $scope.widget = SimulatorService;
      var widgetSettings = <?= json_encode($widgetSettings, JSON_UNESCAPED_UNICODE) ?>;
      $scope.widget.settings = widgetSettings;

      $scope.$on('ngRepeatFinish', function(){
        popupEvent.resize();
      })


    }]);
  sincloApp.directive('branchModal', function(){
    return {
      restrict: 'E',
      replace: true,
      template: '<div id=\'branch_modal\'>' +
          '<div id=\'branch_modal_editor\'>' +
          '<div id=\'branch_modal_head\'>' +
          '<label for=\'node_name\'>ノード名</label>' +
          '<input id=\'my_node_name\' name=\'node_name\' type=\'text\' placeholder=\'ノード名を入力して下さい\'/>' +
          '</div>' +
          '<div id=\'branch_modal_body\'>' +
          '<div class=\'branch_modal_setting_header\'>' +
          '<div class=\'flex_row_box\'>' +
          '<p>発言内容</p>' +
          '<textarea class="node_branch"></textarea>' +
          '</div>' +
          '<div class=\'flex_row_box\'>' +
          '<label for=\'branch_button\'>表示形式</label>' +
          '<select name=\'branch_button\' id=\'branchBtnType\'>' +
          '<option>表示形式を選択して下さい</option>' +
          '<option value=\'1\'>ラジオボタン</option>' +
          '<option value=\'2\'>ボタン</option>' +
          '<select>' +
          '</div>' +
          '</div>' +
          '<div class=\'branch_modal_setting_content\'>' +
          '<div class=\'setting_row\'>' +
          '<p>選択肢</p>' +
          '<input type="text"/>' +
          '<img src=\'/img/add.png?1530001126\' width=\'20\' height=\'20\' class=\'btn-shadow disOffgreenBtn\' ng-hide="addBtnHide" ng-click="addBtnClick($event)">' +
          '<img src=\'/img/dustbox.png?1530001127\' width=\'20\' height=\'20\' class=\'btn-shadow redBtn\' ng-hide="deleteBtnHide" ng-click="deleteBtnClick($event)">' +
          '</div>' +
          '<div id="bulkRegister" class="btn-shadow disOffgreenBtn">選択肢を一括登録</div>'+
          '</div>' +
          '</div>' +
          '</div>' +
          '<div id=\'branch_modal_preview\'>' +
          '</div>' +
          '</div>'
    }
  }).directive('textModal', function($timeout){
    return {
      restrict: 'E',
      replace: true,
      template: '<div id=\'text_modal\'>' +
          '<div id=\'text_modal_editor\'>' +
          '<div id=\'text_modal_head\'>' +
          '<label for=\'node_name\'>ノード名</label>' +
          '<input id=\'my_node_name\' name=\'node_name\' type=\'text\' placeholder=\'ノード名を入力して下さい\'ng-model="speakTextTitle"/>' +
          '</div>' +
          '<div id=\'text_modal_body\'>' +
          '<p>発言内容</p>' +
          '<div id="text_modal_contents" >' +
          '<div class=\'text_modal_setting\' ng-repeat="speakText in speakTextList" finisher>' +
          '<textarea class="node_text" ng-model="speakText.content"></textarea>' +
          '<img src=\'/img/add.png?1530001126\' width=\'20\' height=\'20\' class=\'btn-shadow disOffgreenBtn\' ng-hide="addBtnHide" ng-click="addBtnClick(\'text\', $index)">' +
          '<img src=\'/img/dustbox.png?1530001127\' width=\'20\' height=\'20\' class=\'btn-shadow redBtn\' ng-hide="deleteBtnHide" ng-click="deleteBtnClick(\'text\', $index)">' +
          '</div>' +
          '</div>' +
          '</div>' +
          '</div>' +
          '<div id=\'text_modal_preview\'>' +
          '</div>' +
          '</div>'
    }
  }).directive('linkModal', function(){
    return {
      restrict: 'E',
      replace: true,
      template: '<div>' +
          '<div  id=\'link_modal\'>' +
          '<label for=\'link\'>遷移先URL</label>' +
          '<input id=\'linkTarget\' name=\'link\' type=\'text\' ng-model="linkUrl" placeholder=\'URLを入力して下さい\'/>' +
          '</div>' +
          '<div id=\'link_type_area\'>' +
          '<label><input type=\'radio\' ng-model="linkType" name=\'link_type\' value=\'same\'>ページ遷移する</label>' +
          '<label><input type=\'radio\' ng-model="linkType" name=\'link_type\' value=\'another\'>別タブで開く</label>' +
          '</div>' +
          '</div>'
    }
  }).directive('scenarioModal', function(){
    return {
      restrict: 'E',
      replace: true,
      template: '<div id=\'scenario_modal\'>' +
          '<label for=\'scenario\'>シナリオ名</label>' +
          '<select name=\'scenario\' id=\'callTargetScenario\'ng-model="selectedScenario" ng-options="sc.value for sc in scenarioArrayList track by sc.key">' +
          '</select>' +
          '</div>'
    }
  }).directive('jumpModal', function(){
    return {
      restrict: 'E',
      replace: true,
      template: '<div id=\'jump_modal\'>' +
          '<label for=\'jump\'>ノード名</label>' +
          '<select ng-model="jumpTarget" name=\'jump\' id=\'jumpTargetNode\' ng-options="jump.value for jump in jumpTargetList track by jump.key">' +
          '<select>' +
          '</div>'
    }
  }).directive('cvModal', function(){
    return {
      restrict: 'E',
      replace: true,
      template: '<p id="cv_modal">このノードに到達した場合、CVに登録します。</p>'
    }
  }).directive('operatorModal', function(){
    return {
      restrict: 'E',
      replace: true,
      template: '<p id="op_modal">このノードに到達した場合、オペレーターを呼び出します。</p>'
    }
  });

  // ng-repeat完了後にControllerへ通知
  sincloApp.directive('finisher', function($timeout){
    return {
      restrict: 'A',
      link: function(scope, element, attr){
        if ( scope.$last === true) {
          $timeout( function(){
            scope.$emit('ngRepeatFinish');
          });
        }
      }
    }
  })
</script>
