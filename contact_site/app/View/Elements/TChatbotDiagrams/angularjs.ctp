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

      /* Model for scenario node */
      $scope.selectedScenario = $scope.scenarioArrayList[0];

      /* Model for link node */
      $scope.linkUrl = "";
      $scope.linkType = "same";

      /* Model for jump node */
      $scope.jumpTargetListOrigin = [{"key": "", "value": "ノード名を選択してください"}];
      $scope.jumpTargetList = $scope.jumpTargetListOrigin.concat();
      $scope.jumpTarget = $scope.jumpTargetList[0];

      /* Model for text node */
      $scope.speakTextList = [];
      $scope.speakTextTitle = "";

      /* Model for handle button */
      $scope.addBtnHide = false;
      $scope.deleteBtnHide = false;

      /* Model for branch node */
      $scope.branchTitle = "";
      $scope.branchTypeList = [
        {"key": "", "value": "表示形式を選択して下さい"},
        {"key": "1", "value": "ラジオボタン"},
        {"key": "2", "value": "ボタン"}
      ];
      $scope.branchType = $scope.branchTypeList[0];
      $scope.branchText = "";
      $scope.branchSelectionList = [];
      $scope.oldSelectionList = [];

      /* Cell data Storage  */
      $scope.currentEditCell = null;
      $scope.currentEditCellParent = null;

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
              'stroke-width': 2,
            },
            '.marker-target': {
              stroke: '#AAAAAA',
              fill: '#AAAAAA',
              d: 'M 14 0 L 0 7 L 14 14 z'
            },
            '.link-tools .link-tool .tool-remove circle': {
              'class': 'diagram'
            },
            '.marker-arrowhead[end="source"]': {d: 'M 0 0 z'},
            '.marker-arrowhead[end="target"]': {d: 'M 0 0 z'}
          }
        }),
        validateConnection: function(cellViewS, magnetS, cellViewT, magnetT, end, linkView) {
          //既に他ポートに接続しているout portは線を出さない
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
            /* init current cell to null */
            $scope.currentEditCell = null;
            /* when edit cell */
            if (!wasMoved && isNeedModalOpen(cellView)) {
              $scope.currentEditCell = setViewElement(cellView);
              $scope.currentEditCellParent = $scope.currentEditCell.getAncestors()[0];
              var modalData = processModalCreate(cellView);
              $compile(modalData.content)($scope);
              $timeout(function(){
                $scope.currentTop = null;
                modalOpen.call(window, modalData.content, modalData.id, modalData.name, 'moment');
                var frame = $('#popup-frame');
                frame.addClass("diagram-ui");
                /* Bind node name if diagram is text or scenario */
                if(frame.hasClass("p_diagrams_branch")){
                  $scope.titleHandler($scope.branchTitle, "分岐");
                }else if(frame.hasClass("p_diagrams_text")){
                  $scope.titleHandler($scope.speakTextTitle, "テキスト発言");
                }
                $scope.popupHandler();
                initPopupCloseEvent();
              });
            }

            $scope.removeToolScale();
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

      $scope.removeToolScale = function(){
        var links = $('.link-tool');
        for( var i = 0; i < links.length; i++ ){
          var tmp = $(links[i]).attr("transform").indexOf(" scale");
          if(tmp > -1){
            $(links[i]).attr("transform", $(links[i]).attr("transform").substr(0, tmp));
          }
        }
      };

      /* save Act */
      $('#submitBtn').on('click', function() {
        var graphJSON = graph.toJSON();
        for(var i = 0; i < graphJSON.cells.length; i++) {
          if(graphJSON.cells[i].attrs.nodeBasicInfo.nodeType === "start"){
            graphJSON.cells[i].attrs.nodeBasicInfo.messageIntervalSec = $scope.messageIntervalTimeSec;
            break;
          }
        }
        $('#TChatbotDiagramActivity').val(JSON.stringify(graphJSON));
        $('#TChatbotDiagramsEntryForm').submit();
      });

      /* bulkRegister Btn */
      $(document).on('click', '#bulkRegister',  function() {
        bulkRegister.open();
      });

      /* bulkRegister Event */
      var bulkRegister = {
        modalData: {},
        open: function() {
          try {
            this._createModal(this._getOverView($scope.branchType), this._getContent());
          } catch (e) {
            console.log(e + " ERROR DETECTED");
          }
        },
        _initPopupOverlapEvent: function() {
          popupEventOverlap.closePopup = function() {
            $scope.branchSelectionList = $("#bulk_textarea").val().split("\n");
            popupEventOverlap.closeNoPopupOverlap();
            $scope.$apply();
            $timeout(function() {
              popupEvent.resize();
              $scope.popupFix();
            })
          }
        },
        _createModal: function(overView, content) {
          $scope.currentTop = $('#popup-frame').offset().top;
          popupEventOverlap.initOverlap();
          popupEventOverlap.open(content, overView.class, overView.title);
          this._initPopupOverlapEvent();
        },
        _getOverView: function(type) {
          switch(Number(type.key)) {
            case 1:
            case 2:
              return {
                title: "選択肢の一括登録",
                class: "p_selection_bulk_register"
              };
            default:
              throw "noType"
          }
        },
        _getContent: function() {
          return '<div class="select-option-one-time-popup">\n' +
                 '    <p>選択肢として登録する内容を改行して設定してください。</p>\n' +
                 '\n' +
                 '    <textarea name=""  id="bulk_textarea" style="overflow: hidden; resize: none; font-size: 13px;" cols="48" rows="3" placeholder=' +
                 '"男性&#10;女性">' + $scope.branchSelectionList.join("\n") + '</textarea>\n' +
                 '</div>';
        }
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
            node[i].on('change:position', function(){
              wasMoved = true;
            });
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

        return {
          name: modalName,
          content: htmlCreator($scope.currentEditCellParent.attr('actionParam')),
          id: modalClass
        };
      }

      function initPopupCloseEvent() {
        popupEvent.closePopup = function(type) {
          switch (type) {
            case 1:
              /* save */
              saveEditNode();
              previewHandler.typeJump.editTargetName();
              popupEvent.closeNoPopup();
              $scope.addLineHeight();
              break;
            case 2:
              /* delete */
              popupEventOverlap.closePopup = function() {
                previewHandler.typeJump.deleteTargetName($scope.currentEditCell);
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

      $scope.addLineHeight = function(){
        /* To override svg */
        $("text:not(.label) > tspan:not(:first-child)").attr("dy", "1.6em");
      };

      function deleteEditNode() {
        if ($scope.currentEditCell == null || $scope.currentEditCellParent == null) return;
        /* bind node name list for jump node when delete text node or branch node */
        if ($scope.currentEditCellParent.attr('nodeType') === 'text'
            || $scope.currentEditCellParent.attr('nodeType') === 'branch') {
          bindJumpData($scope.currentEditCellParent);
        }
        $scope.currentEditCellParent.remove();
        $scope.currentEditCell = null;
        $scope.currentEditCellParent = null;
      }

      function saveEditNode() {
        if ($scope.currentEditCell && $scope.currentEditCellParent) {
          bindSingleView($scope.currentEditCellParent.attr('nodeBasicInfo/nodeType'));
        }
      }

      function bindJumpData() {
        graph.getElements();
      }

      function setViewElement(target) {
        //親エレメント直下の子エレメントを設定
        var viewNode = target.model;
        var childList = viewNode.getEmbeddedCells();
        if (childList.length > 0) {
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
        $scope.saveProcess[type].setView();
        $timeout(function(){
          $scope.currentEditCellParent.attr('actionParam', $scope.saveProcess[type].getData());
        });
      }

      $scope.saveProcess = {
        text: {
          setView: function() {
            $scope.currentEditCellParent.attr('.label/text',
                convertTextForTitle(convertTextLength($scope.speakTextTitle, 3), 'テキスト発言'));
            $scope.currentEditCell.attr('text/text', convertTextLength(
                textEditor.textLineSeparate($scope.speakTextList.filter(Boolean)[0]), 46));
            $scope.currentEditCellParent.attr('actionParam/text', null);
          },
          getData: function() {
            return {
              nodeName: $scope.speakTextTitle,
              text: $scope.speakTextList.filter(Boolean)
            };
          }
        },
        branch: {
          setView: function(){
            nodeEditHandler.typeBranch.branchPortController(
                $scope.branchSelectionList.filter(Boolean), $scope.currentEditCellParent.attr("actionParam"));
            $scope.currentEditCellParent.attr('.label/text',
                convertTextForTitle(convertTextLength($scope.branchTitle, 6), '分岐'));
            $scope.currentEditCellParent.attr('actionParam/selection', null);
            $scope.currentEditCell.attr('text/text', convertTextLength(textEditor.textLineSeparate($scope.branchText), 46));
          },
          getData: function(){
            return {
              nodeName: $scope.branchTitle,
              text: $scope.branchText,
              btnType: $scope.branchType.key,
              selection: $scope.branchSelectionList.filter(Boolean)
            };
          }
        },
        scenario: {
          setView: function(){
            if($scope.selectedScenario.key !== ""){
              $scope.currentEditCell.attr('text/text', convertTextLength($scope.selectedScenario.value, 14));
            }
          },
          getData: function(){
            return {
              scenarioId: $scope.selectedScenario.key
            };
          }
        },
        jump: {
          setView: function(){
            if($scope.jumpTarget.key !== ""){
              $scope.currentEditCell.attr('text/text', convertTextLength($scope.jumpTarget.value, 14));
            }
          },
          getData: function(){
            return {
              targetId: $scope.jumpTarget.key
            }
          }
        },
        link: {
          setView: function(){
            $scope.currentEditCell.attr('text/text', convertTextLength($scope.linkUrl, 28));
          },
          getData: function(){
            return {
              link: $scope.linkUrl,
              linkType: $scope.linkType
            }
          }
        },
        operator: {
          setView: function(){
          },
          getData: function(){
          }
        },
        cv: {
          setView: function(){
          },
          getData: function(){
          }
        }
      };

      function convertTextLength(text, regNum) {
        if (text) {
          return text.length > regNum ? (text).slice(0, regNum) + '...' : text;
        } else {
          return text;
        }
      }

      function convertTextForTitle(text, basicTitle) {
        return text ? text : basicTitle;
      }

      function createBranchHtml(nodeData) {
        if(nodeData.selection.length > 0){
          $scope.branchSelectionList = nodeData.selection
        } else {
          $scope.branchSelectionList.length = 0;
          $scope.branchSelectionList.push("");
        }
        $scope.oldSelectionList = $scope.branchSelectionList.concat();
        $scope.branchTitle = nodeData.nodeName;
        $scope.branchType.key = nodeData.btnType;
        $scope.branchText = nodeData.text;
        return $('<branch-modal></branch-modal>');
      }

      function createTextHtml(nodeData) {
        if(nodeData.text.length > 0){
          $scope.speakTextList = nodeData.text;
        } else {
          $scope.speakTextList.length = 0;
          $scope.speakTextList.push("");
        }
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

      $scope.btnClick = function(type, target, index){
        $scope.currentTop = $('#popup-frame').offset().top;
        switch (type) {
          case "add":
            target.splice(index + 1, 0, "");
            break;
          case "delete":
            target.splice(index, 1);
            break;
          default:
            break;
        }
        $timeout(function(){
          popupEvent.resize();
          $scope.popupFix();
        })
      };

      $scope.popupHandler = function(){
        var popup = $('#popup-frame.diagram-ui');
        popup.css({
          "margin": "0",
          "position": "absolute",
          "top": (window.innerHeight / 2 - popup.height() / 2 - $('#color-bar').height()),
          "left": (window.innerWidth  / 2 - popup.width() / 2 - $('#sidebar-main').width())
        });
        popup.draggable({
          scroll: false,
          cancel: "#popup-main, #popup-button, .p-personal-update",
          stop: function(e, ui) {
            /* restrict popup position */
            var popup = $('#popup-frame'),
                newTop = popup.offset().top,
                newLeft = popup.offset().left;
            if(ui.offset.top < 60){
              newTop = 60;
            }
            if(ui.offset.left < 80){
              newLeft = 80;
            }
            if(ui.offset.top + e.target.offsetHeight > window.innerHeight){
              newTop = window.innerHeight - e.target.offsetHeight;
            }
            if(ui.offset.left + e.target.offsetWidth > window.innerWidth){
              newLeft = window.innerWidth - e.target.offsetWidth;
            }
            popup.offset({
              top: newTop,
              left: newLeft
            });
          }
        });
      };

      $scope.$watch("speakTextList", function(){
        $scope.btnHandler($scope.speakTextList.length, 1, 5);
      }, true);

      $scope.$watch("branchSelectionList", function(){
        $scope.btnHandler($scope.branchSelectionList.length, 1, 10);
      }, true);

      $scope.btnHandler = function(amount, min, max){
        if(amount === min){
          $scope.addBtnHide = false;
          $scope.deleteBtnHide = true;
        } else if (amount === max) {
          $scope.addBtnHide = true;
          $scope.deleteBtnHide = false;
        } else {
          $scope.addBtnHide = false;
          $scope.deleteBtnHide = false;
        }
      };

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
            //呼び出される際に、一度リストの初期化を行う
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
        typeBranch: {
          branchPortController: function(newSelectionList) {
            var self = nodeEditHandler.typeBranch;
            for(var i = 0; i < newSelectionList.length; i++){
              /* Set rect height */
              self._resizeParentHeight(i);
              var port = self.portCreator(self._getSelfPosition(i), newSelectionList[i], self._getCoverOpacity(i, newSelectionList.length));
              $scope.currentEditCellParent.embed(port);
              initNodeEvent([port]);
              graph.addCell(port);
            }
          },
          _checkPastPortListFromCurrent: function(target) {
            if($scope.oldSelectionList.indexOf(target) === -1 ){
              /* Port is Added */
            }
          },
          _checkCurrentPortListFromPast: function(targetList) {
            for( var i = 0; i < $scope.oldSelectionList; i++ ){
              if(targetList.indexOf($scope.oldSelectionList[i]) === -1){
                /* Port is deleted */
              }
            }
          },
          _getSelfPosition :function(index) {
            return {
              x: $scope.currentEditCellParent.get('position').x,
              y: $scope.currentEditCellParent.get('position').y + 95 + index * 40
            }
          },
          _resizeParentHeight: function(index) {
            $scope.currentEditCellParent.get('size').height = 140 + index * 40;
          },
          _getCoverOpacity: function(index, maxLength){
            return {
              top : index === 0 ? "0" : "1",
              bot : index === maxLength - 1 ? "0" : "1"
            }
          },
          portCreator: function(position, text, opacity) {
            return new joint.shapes.devs.Model({
              position: {x: position.x + 5, y: position.y},
              size: {width: 190, height: 36},
              outPorts: ['out'],
              ports: {
                groups: {
                  'out': {
                    attrs: {
                      '.port-body': {
                        fill: "#DD82AB",
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
                        y: 3
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
                  'font-size': '14px',
                  fill: '#000',
                  y: 12
                },
                'rect.body': {
                  fill: '#FFFFFF',
                  stroke: false,
                  rx: 10,
                  ry: 10
                },
                nodeBasicInfo: {
                  nodeType: 'childPortNode',
                  nextNode: ''
                },
                '.cover_top': {
                  fill: '#FFFFFF',
                  width: 190,
                  height: 10,
                  'fill-opacity': opacity.top
                },
                '.cover_bottom': {
                  fill: '#FFFFFF',
                  width: 190,
                  height: 10,
                  transform: "translate(0 26)",
                  'fill-opacity': opacity.bot
                }
              },
              markup: '<rect class="body"/><text class="label"/><rect class="cover_top"/><rect class="cover_bottom"/>'
            });
          }
        }
      };
      
      var textEditor = {
        textLineSeparate: function(text){
          if(text == null) return;
          var self = textEditor;
          var originTextArray = text.split(/\r\n|\n/);
          var resultTextArray = [];
          for( var i = 0; i < originTextArray.length; i++ ){
            if( originTextArray[i].length > 12 ){
              Array.prototype.push.apply(resultTextArray, self.textLineCreate(originTextArray[i]));
            } else {
              resultTextArray.push(originTextArray[i]);
            }
          }
          if(resultTextArray.length > 3){
            resultTextArray.splice(3, resultTextArray.length - 3);
          }
          return resultTextArray.join("\n");
        },
        textLineCreate: function(textLine){
          var currentText = textLine;
          var textArray = [];
          var loopNum = currentText.length / 13;
          for( var i = 0; i < loopNum ; i++){
            textArray.push(currentText.substr(0, 13));
            currentText = currentText.substr(13);
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
            prefix = "";
            splitNum = 3;
          } else {
            prefix = "";
            splitNum = 6;
          }
          target.attr("actionParam/nodeName", defaultValue);
          target.attr(".label/text",
              convertTextForTitle(convertTextLength(defaultValue, splitNum), prefix));
        },
        typeJump: {
          editTargetName: function(){
            var allCells = graph.getCells();
            var targetCell = $scope.currentEditCell;
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

      $scope.widget = SimulatorService;
      var widgetSettings = <?= json_encode($widgetSettings, JSON_UNESCAPED_UNICODE) ?>;
      $scope.widget.settings = widgetSettings;

      $scope.$on('ngRepeatFinish', function(){
        popupEvent.resize();
        $scope.popupFix();
      });

      $scope.popupFix = function(){
        var popup = $('#popup-frame');
        popup.offset({
          top: $scope.currentTop ? $scope.currentTop : 300,
          left: popup.offset().left
        });
      };

      $scope.$watch("speakTextTitle", function(){
        $scope.titleHandler($scope.speakTextTitle, "テキスト発言");
      });

      $scope.$watch("branchTitle", function(){
        $scope.titleHandler($scope.branchTitle, "分岐");
      });

      $scope.titleHandler = function(target, prefix){
        $('#popup-title').text(prefix + $scope.getConjunction(target) + target);
      };

      $scope.getConjunction = function(target){
        var conjunction = "";
        if(target && target !== ""){
          conjunction = "："
        }
        return conjunction;
      };

    }]);

  sincloApp.directive('branchModal', function(){
    return {
      restrict: 'E',
      replace: true,
      template: '<div id=\'branch_modal\'>' +
          '<div id=\'branch_modal_editor\'>' +
          '<h3>設定</h3>' +
          '<div id=\'branch_modal_head\'>' +
          '<label for=\'node_name\'>ノード名</label>' +
          '<input ng-model="branchTitle" id=\'my_node_name\' name=\'node_name\' type=\'text\' placeholder=\'ノード名を入力して下さい\'/>' +
          '</div>' +
          '<div id=\'branch_modal_body\'>' +
          '<div class=\'branch_modal_setting_header\'>' +
          '<div class=\'flex_row_box\'>' +
          '<p>発言内容</p>' +
          '<textarea class="node_branch" ng-model="branchText"></textarea>' +
          '</div>' +
          '<div class=\'flex_row_box\'>' +
          '<label for=\'branch_button\'>表示形式</label>' +
          '<select name=\'branch_button\' id=\'branchBtnType\' ng-model="branchType" ng-options="btnType.value for btnType in branchTypeList track by btnType.key">' +
          '</select>' +
          '</div>' +
          '</div>' +
          '<div class=\'branch_modal_setting_content\'>' +
          '<div class=\'setting_row\' ng-repeat="selection in branchSelectionList track by $index">' +
          '<p>選択肢</p>' +
          '<input type="text" ng-model="branchSelectionList[$index]" />' +
          '<img src=\'/img/add.png?1530001126\' width=\'20\' height=\'20\' class=\'btn-shadow disOffgreenBtn\' ng-hide="addBtnHide" ng-click="btnClick(\'add\', branchSelectionList, $index)">' +
          '<img src=\'/img/dustbox.png?1530001127\' width=\'20\' height=\'20\' class=\'btn-shadow redBtn\' ng-hide="deleteBtnHide" ng-click="btnClick(\'delete\', branchSelectionList, $index)">' +
          '</div>' +
          '<div id="bulkRegister" class="btn-shadow disOffgreenBtn">選択肢を一括登録</div>'+
          '</div>' +
          '</div>' +
          '</div>' +
          '<div id=\'branch_modal_preview\'>' +
          '<h3>プレビュー</h3>' +
          '</div>' +
          '</div>'
    }
  }).directive('textModal', function($timeout){
    return {
      restrict: 'E',
      replace: true,
      template: '<div id=\'text_modal\'>' +
          '<div id=\'text_modal_editor\'>' +
          '<h3>設定</h3>' +
          '<div id=\'text_modal_head\'>' +
          '<label for=\'node_name\'>ノード名</label>' +
          '<input id=\'my_node_name\' name=\'node_name\' type=\'text\' placeholder=\'ノード名を入力して下さい\'ng-model="speakTextTitle"/>' +
          '</div>' +
          '<div id=\'text_modal_body\'>' +
          '<p>発言内容</p>' +
          '<div id="text_modal_contents" >' +
          '<div class=\'text_modal_setting\' ng-repeat="speakText in speakTextList track by $index" finisher>' +
          '<textarea class="node_text" ng-model="speakTextList[$index]"></textarea>' +
          '<img src=\'/img/add.png?1530001126\' width=\'20\' height=\'20\' class=\'btn-shadow disOffgreenBtn\' ng-hide="addBtnHide" ng-click="btnClick(\'add\', speakTextList, $index)">' +
          '<img src=\'/img/dustbox.png?1530001127\' width=\'20\' height=\'20\' class=\'btn-shadow redBtn\' ng-hide="deleteBtnHide" ng-click="btnClick(\'delete\', speakTextList, $index)">' +
          '</div>' +
          '</div>' +
          '</div>' +
          '</div>' +
          '<div id=\'text_modal_preview\'>' +
          '<h3>プレビュー</h3>' +
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
