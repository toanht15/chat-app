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

      /* Set basic data */
      $scope.widget = SimulatorService;
      var widgetSettings = <?= json_encode($widgetSettings, JSON_UNESCAPED_UNICODE) ?>;
      $scope.widget.settings = widgetSettings;


      $scope.valueDiffChecker = {
        branch: function(target){
          return $scope.branchTitle === target.attr("actionParam/nodeName")
              && $scope.branchText === target.attr("actionParam/text")
              && $scope.branchType.key === target.attr("actionParam/btnType")
              && $scope.compareArray($scope.branchSelectionList, target.attr("actionParam/selection"));
        },
        text: function(target){
          return $scope.speakTextTitle === target.attr("actionParam/nodeName")
              && $scope.compareArray($scope.speakTextList, target.attr("actionParam/text"));
        },
        scenario: function(target){
          return $scope.selectedScenario.key === target.attr("actionParam/scenarioId");
        },
        jump: function(target){
          return $scope.jumpTarget.key === target.attr("actionParam/targetId");
        },
        link: function(target){
          return $scope.linkUrl === target.attr("actionParam/link")
              && $scope.linkType === target.attr("actionParam/linkType");
        },
        operator: function(){
          return true;
        },
        cv: function(){
          return true;
        }
      };

      $scope.compareArray = function(source, target){
        var t1, t2;
        var arr1 = source.filter(Boolean).concat();
        var arr2 = target.filter(Boolean).concat();
        if(arr1.length !== arr2.length) return false;
        for(var i = 0; i < arr2.length; i++) {
          t1 = arr1[i] == null ? "" : arr1[i];
          t2 = arr2[i] == null ? "" : arr2[i];
          if (t1 !== t2) return false;
        }
        return true;
      };





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

      /* Model for branch customize */
      $scope.isCustomize = false;
      $scope.radioBackgroundColor = "";
      $scope.radioActiveColor = "";
      $scope.radioBorderColor = "";
      $scope.radioNoneBorder = false;
      $scope.buttonUIBackgroundColor = "";
      $scope.buttonUITextColor = "";
      $scope.buttonUITextAlign ="2";
      $scope.buttonUIActiveColor = "";
      $scope.buttonUIBorderColor = "";
      $scope.outButtonUINoneBorder = false;

      /* Model for validation */

      /* Model for tooltip */
      $scope.toolTipElement = null;
      $scope.moveX = 0;
      $scope.moveY = 0;

      /* Cell data Storage  */
      $scope.currentEditCell = null;
      $scope.currentEditCellParent = null;

      /* Valid connection flag */
      $scope.isValidConnection = null;


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
            $scope.isValidConnection = true;
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

      paper.scale(0.7);
      graph.addCell(startNode());

      var dragReferencePosition = null;
      var dataForUpdate = $('#TChatbotDiagramActivity').val();

      if (dataForUpdate !== null && dataForUpdate !== '') {
        graph.fromJSON(JSON.parse(dataForUpdate));
        setTimeout(function(){
          dataForUpdate = JSON.parse(dataForUpdate);
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
            $scope.currentEditCellParent = null;
            if(cellView.model.attr("nodeBasicInfo/nodeType") === "childViewNode"
                || cellView.model.attr("nodeBasicInfo/nodeType") === "childPortNode") {
              if($scope.checkTextEnd(cellView.model.attr("text/text"))) {
                $scope.createToolTip(cellView.model);
              }
            }
            /* when edit cell */
            if (!wasMoved && isNeedModalOpen(cellView)) {
              $scope.currentEditCell = setViewElement(cellView);
              $scope.currentEditCellParent = $scope.currentEditCell.getAncestors()[0];
              var modalData = processModalCreate(cellView);
              $scope.initValidation();
              $compile(modalData.content)($scope);
              $timeout(function(){
                $scope.currentTop = null;
                modalOpen.call(window, modalData.content, modalData.id, modalData.name, 'moment');
                var frame = $('#popup-frame');
                var background = $('#popup-bg');
                background.append(frame);
                background.css("overflow","auto");
                $('#shortMessage').remove();
                frame.addClass("diagram-ui");

                /* Bind node name if diagram is text or scenario */
                if(frame.hasClass("p_diagrams_branch")){
                  $scope.titleHandler($scope.branchTitle, "分岐");
                  $scope.autoResize($("textarea"), true);
                }else if(frame.hasClass("p_diagrams_text")){
                  $scope.titleHandler($scope.speakTextTitle, "テキスト発言");
                  var elements = $("textarea");
                  for(var i = 0; i < elements.length; i++){
                    $scope.autoResize($(elements[i]), true);
                  }
                }

                $scope.popupHandler();
                $scope.popupInit(frame);
                initPopupCloseEvent();
                /* Install jscolor after create modal */
                $(window)[0].jscolor.installByClassName('jscolor');
              });
            }

            $scope.removeToolScale();
            wasMoved = false;
          });

      paper.on('blank:pointerdown', function(e, x, y) {
        dragReferencePosition = {x: x * paper.scale().sx, y: y * paper.scale().sy};
      });

      paper.on('cell:mouseenter', function(e) {
        if(e.model.attr("nodeBasicInfo/nodeType") === "childViewNode"
        || e.model.attr("nodeBasicInfo/nodeType") === "childPortNode") {
          if($scope.checkTextEnd(e.model.attr("text/text"))) {
            $scope.createToolTip(e.model);
          }
        }
      });

      paper.on('cell:mouseleave', function(e) {
        $scope.removeTooltip(e);
      });

      paper.on('cell:pointerdown', function(e) {
        $scope.removeTooltip(e);
      });

      $scope.removeTooltip = function(e){
        if(e.model.attr("nodeBasicInfo/nodeType") === "childViewNode"
        || e.model.attr("nodeBasicInfo/nodeType") === "childPortNode") {
          var tooltips = $('.diagram_tooltip');
          for( var i = 0; i < tooltips.length; i++ ){
            $('#t_chatbot_diagrams_idx')[0].removeChild(tooltips[i]);
          }
        }
      };

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
        $scope.colorizePort(linkView);
      });
      

      graph.on('remove', function(deleteView, b) {
        if (deleteView.isLink() && deleteView.attributes.target.id) {
          resetNextNode(deleteView.attributes.source.id);
        }

        if( deleteView.isLink() && !$scope.isValidConnection ){
          $scope.grayColorizePort(deleteView);
        }

        $scope.isValidConnection = false;

      });

      $('input[type=range]').on('input', function(e) {
        paper.scale((e.target.value - 1.5) / 5);
      });

      $(canvas).mousemove(function(e) {
        if (dragReferencePosition) {
          $scope.moveX = e.offsetX - dragReferencePosition.x;
          $scope.moveY = e.offsetY - dragReferencePosition.y
          paper.translate(
              $scope.moveX,
              $scope.moveY
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

      $scope.checkTextEnd = function(text){
        return text.slice(-3) === "...";
      };

      $scope.createToolTip = function(cell){
        var text = cell.attr("nodeBasicInfo/tooltip");
        var position = cell.get("position");
        var height = cell.get("size").height;
        $scope.toolTipElement = $("<ul class='diagram_tooltip'><li></li></ul>");
        $scope.toolTipElement.find("li").text(text);
        $scope.toolTipElement.offset({
          top: (position.y  + height) * paper.scale().sy + 185 + $scope.moveY,
          left: position.x * paper.scale().sx + 220 + $scope.moveX
        });
        $("#t_chatbot_diagrams_idx").append($scope.toolTipElement);
        if(window.innerHeight < $scope.toolTipElement.outerHeight() + $scope.toolTipElement.offset().top) {
          $scope.toolTipElement.offset({
            top: position.y* paper.scale().sy -$scope.toolTipElement.outerHeight() + 140 + $scope.moveY,
            left: position.x * paper.scale().sx + 220 + $scope.moveX
          });
        }
      };

      $scope.grayColorizePort = function(link) {
        try{
          var source = graph.getCell(link.get("source").id);
          source.portProp("out", "attrs/.port-body/fill", "#C0C0C0");
          var target = graph.getCell(link.get("target").id);
          if(Object.keys(target.graph._in[target.id]).length === 0) {
            target.portProp("in", "attrs/.port-body/fill", "#C0C0C0");
          }
        } catch (e) {
          console.log(e + "ERROR DETECTED!");
        }

      };
      
      $scope.colorizePort = function(linkView) {
        var source = linkView.sourceView.model;
        var typeS = source.attributes.ports.groups.out.attrs.type;
        source.portProp("out", "attrs/.port-body/fill", $scope.getPortColor(typeS, "out"));
        var target = linkView.targetView.model;
        var typeT = target.attributes.ports.groups.in.attrs.type;
        target.portProp("in", "attrs/.port-body/fill", $scope.getPortColor(typeT, "in"));
      };
      
      $scope.getPortColor = function(type, cond){
        switch(type) {
          case "text":
            return cond === "in" ? "#D48BB3" : "#EFD6E4";
          case "branch":
            return cond === "in" ? "#c73576" : "#DD82AB";
          case "scenario":
            return cond === "in" ? "#82c0cd" : "#C8E3E8";
          case "jump":
            return cond === "in" ? "#c8d627" : "#DFE679";
          case "link":
            return cond === "in" ? "#845d9e" : "#B39CC3";
          case "operator":
            return cond === "in" ? "#98B5E0" : "#E7EEF7";
          case "cv":
            return cond === "in" ? "#A2CCBA" : "#E4F0EB";
          default:
            return "#BDC6CF";
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
              return {
                title: "選択肢の一括登録",
                class: "p_selection_bulk_register"
              };
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
              saveEditNode( function(){
                previewHandler.typeJump.editTargetName();
                popupEvent.closeNoPopup(true);
                $scope.addLineHeight();
              });
              break;
            case 2:
              /* delete */
              popupEventOverlap.closePopup = function() {
                previewHandler.typeJump.deleteTargetName($scope.currentEditCell);
                deleteEditNode();
                popupEventOverlap.closeNoPopupOverlap();
                popupEvent.closeNoPopup(true);
              };
              popupEventOverlap.open('現在のノードを削除します。よろしいですか？',"p_diagram_delete_alert" ,"削除の確認");
              break;
            case 3:
              var notChange = $scope.valueDiffChecker[$scope.currentEditCellParent.attr("nodeBasicInfo/nodeType")]($scope.currentEditCellParent);
              if(notChange == null || notChange){
                popupEvent.close();
              } else {
                popupEventOverlap.initOverlap();
                popupEventOverlap.open("内容が保存されていません。編集を終了しますか？", "p_confirm_diagram_change", "確認してください");
                popupEventOverlap.closePopup = function(){
                  popupEventOverlap.closeNoPopupOverlap();
                  popupEvent.closeNoPopup();
                }
              }
              break;
            default:
              break;
          }
        };
      }

      $scope.addLineHeight = function(){
        /* To override svg */
        $("text:not(.label) > tspan:not(:first-child)").attr("dy", "20px");
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

      function saveEditNode( callback ) {
        if ($scope.currentEditCell && $scope.currentEditCellParent) {
          bindSingleView($scope.currentEditCellParent.attr('nodeBasicInfo/nodeType'), callback);
        }
      }

      function bindJumpData() {
        graph.getElements();
      }

      function setViewElement(target) {
        //親エレメント配下のエレメントを設定
        var viewNode = target.model;
        var childList = viewNode.getEmbeddedCells();
        if (childList.length === 0) {
          //子供がいない→自分が子供なので、親を取得し再度子供を全員取得
          childList = viewNode.getAncestors()[0].getEmbeddedCells();
        }
        for( var i = 0; i < childList.length; i++ ){
          if(childList[i].attr("nodeBasicInfo/nodeType") === "childViewNode") {
            viewNode = childList[i];
            break;
          }
        }
        return viewNode;
      }

      function bindSingleView(type, callback) {
        if($scope.saveProcess[type].validation()){
          $scope.$apply();
          $timeout(function(){
            $scope.popupPositionAdjustment();
          }, 370);
          return;
        }
        $scope.saveProcess[type].setView();
        $timeout(function(){
          $scope.currentEditCellParent.attr('actionParam', $scope.saveProcess[type].getData());
          callback();
        });
      }

      $scope.saveProcess = {
        text: {
          setView: function() {
            var text = $scope.speakTextList.filter(Boolean)[0];
            $scope.currentEditCellParent.attr('.label/text',
                convertTextForTitle(convertTextLength($scope.speakTextTitle, 22), 'テキスト発言'));
            $scope.currentEditCell.attr('text/text', convertTextLength(textEditor.textLineSeparate(text), 96));
            $scope.currentEditCell.attr('nodeBasicInfo/tooltip', text);
            $scope.currentEditCellParent.attr('actionParam/text', null);
          },
          getData: function() {
            return {
              nodeName: $scope.speakTextTitle,
              text: $scope.speakTextList.filter(Boolean)
            };
          },
          validation: function(){
            $scope.nodeNameIsEmpty = $scope.speakTextTitle === "";
            return $scope.nodeNameIsEmpty;
          }
        },
        branch: {
          setView: function(){
            nodeEditHandler.typeBranch.branchPortController($scope.branchSelectionList.filter(Boolean));
            $scope.currentEditCellParent.attr('.label/text',
                convertTextForTitle(convertTextLength($scope.branchTitle, 22), '分岐'));
            $scope.currentEditCell.attr('text/text', convertTextLength(textEditor.textLineSeparate($scope.branchText), 96));
            $scope.currentEditCell.attr('nodeBasicInfo/tooltip', $scope.branchText);
            $scope.currentEditCellParent.attr('actionParam/selection', null);
          },
          getData: function(){
            return {
              nodeName: $scope.branchTitle,
              text: $scope.branchText,
              btnType: $scope.branchType.key,
              selection: $scope.branchSelectionList.filter(Boolean),
              customizeDesign: $scope.selectCustomizeDesign()
            };
          },
          validation: function(){
            $scope.nodeNameIsEmpty = $scope.branchTitle === "";
            $scope.btnTypeIsEmpty = $scope.branchType.key === "";
            return $scope.nodeNameIsEmpty || $scope.btnTypeIsEmpty;
          }
        },
        scenario: {
          setView: function(){
            if($scope.selectedScenario.key !== ""){
              $scope.currentEditCell.attr('text/text', convertTextLength($scope.selectedScenario.value, 30));
              $scope.currentEditCell.attr('nodeBasicInfo/tooltip', $scope.selectedScenario.value);
            }
          },
          getData: function(){
            return {
              scenarioId: $scope.selectedScenario.key
            };
          },
          validation: function(){
            $scope.scenarioIsEmpty = $scope.selectedScenario.key === "";
            return $scope.scenarioIsEmpty;
          }
        },
        jump: {
          setView: function(){
            if($scope.jumpTarget.key !== ""){
              $scope.currentEditCell.attr('text/text', convertTextLength($scope.jumpTarget.value, 30));
              $scope.currentEditCell.attr('nodeBasicInfo/tooltip', $scope.jumpTarget.value);
            }
          },
          getData: function(){
            return {
              targetId: $scope.jumpTarget.key
            }
          },
          validation: function(){
            $scope.jumpTargetIsEmpty = $scope.jumpTarget.key === "";
            return $scope.jumpTargetIsEmpty;
          }
        },
        link: {
          setView: function(){
            $scope.currentEditCell.attr('text/text', convertTextLength($scope.linkUrl, 32));
            $scope.currentEditCell.attr('nodeBasicInfo/tooltip', $scope.linkUrl);
          },
          getData: function(){
            return {
              link: $scope.linkUrl,
              linkType: $scope.linkType
            }
          },
          validation: function(){
            $scope.linkIsEmpty = $scope.linkUrl === "";
            return $scope.linkIsEmpty;
          }
        },
        operator: {
          setView: function(){
          },
          getData: function(){
          },
          validation: function(){
          }
        },
        cv: {
          setView: function(){
          },
          getData: function(){
          },
          validation: function(){
          }
        }
      };

      $scope.initValidation = function() {
        $scope.nodeNameIsEmpty = false;
        $scope.btnTypeIsEmpty = false;
        $scope.jumpTargetIsEmpty = false;
        $scope.scenarioIsEmpty = false;
        $scope.linkIsEmpty = false;
      };

      $scope.selectCustomizeDesign = function() {
        var design;
        switch (Number($scope.branchType.key)) {
          case 1:
            design = $scope.getRadioCustomizeDesign;
            break;
          case 2:
            design = $scope.getButtonUICustomizeDesign;
            break;
          default:
            return {};
        }
        return design();
      };

      $scope.getRadioCustomizeDesign = function(){
        return {
          isCustomize: $scope.isCustomize,
          radioBackgroundColor: $scope.radioBackgroundColor,
          radioActiveColor: $scope.radioActiveColor,
          radioBorderColor: $scope.radioBorderColor,
          radioNoneBorder: $scope.radioNoneBorder
        }
      };

      $scope.getButtonUICustomizeDesign = function(){
        return {
          isCustomize: $scope.isCustomize,
          buttonUIBackgroundColor: $scope.buttonUIBackgroundColor,
          buttonUITextColor: $scope.buttonUITextColor,
          buttonUITextAlign: $scope.buttonUITextAlign,
          buttonUIActiveColor: $scope.buttonUIActiveColor,
          buttonUIBorderColor: $scope.buttonUIBorderColor,
          outButtonUINoneBorder: $scope.outButtonUINoneBorder
        }
      };


      function getTextLength(str, limit) {
        var result = 0;
        var isMax = true;
        for(var i=0;i<str.length;i++){
          var chr = str.charCodeAt(i);
          if(chr === 10){
            result = 0;
            limit -= 33;
          } else if(chr >= 0x00 && chr < 0x81
              || chr === 0xf8f0
              || chr >= 0xff61 && chr < 0xffa0
              || chr >= 0xf8f1 && chr < 0xf8f4){
            result += 1;
          } else {
            result += 2;
          }

          if (result > limit) {
            isMax = false;
            break;
          }
        }
        return {
          textNum: i,
          isEnd: isMax
        };
      }

      function convertTextLength(text, regNum) {
        var data = getTextLength(text, regNum);
        if (data.isEnd) {
          return text;
        } else {
          return (text).slice(0, data.textNum) + "...";
        }
      }

      function convertTextForTitle(text, basicTitle) {
        return text ? text : basicTitle;
      }

      function createBranchHtml(nodeData) {
        if(nodeData.selection.length > 0){
          $scope.branchSelectionList = nodeData.selection.concat();
        } else {
          $scope.branchSelectionList.length = 0;
          $scope.branchSelectionList.push("");
        }
        $scope.oldSelectionList = $scope.branchSelectionList.concat();
        $scope.branchTitle = nodeData.nodeName;
        $scope.branchType.key = nodeData.btnType;
        $scope.branchText = nodeData.text;
        $scope.initCustomizeColor(nodeData);
        return $('<branch-modal></branch-modal>');
      }

      function createTextHtml(nodeData) {
        if(nodeData.text.length > 0){
          $scope.speakTextList = nodeData.text.concat();
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
          "top": (window.innerHeight / 2 - popup.height() / 2),
          "left": (window.innerWidth  / 2 - popup.width() / 2)
        });
        popup.draggable({
          scroll: false,
          cancel: "#popup-main, #popup-button, .p-personal-update",
          stop: function(e, ui) {
            /* restrict popup position */
            var popup = $('#popup-frame'),
                newTop = popup.offset().top,
                newLeft = popup.offset().left;
            if(ui.offset.top < 0 ){
              newTop = 0;
            } else if( ui.offset.top + 100 > window.innerHeight ){
              newTop = window.innerHeight - 100;
            }
            if(ui.offset.left < 0 ){
              newLeft = 0;
            } else if(ui.offset.left + 100 > window.innerWidth){
              newLeft = window.innerWidth - 100;
            }

            popup.offset({
              top: newTop,
              left: newLeft
            });
          }
        });
      };

      $scope.$watch("speakTextList", function(){
        $scope.btnHandler($scope.speakTextList.length, 1, 1000);
      }, true);

      $scope.$watch("branchSelectionList", function(){
        $scope.btnHandler($scope.branchSelectionList.length, 1, 1000);
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

      $scope.popupInit = function(popup) {
        var newTop = popup.offset().top,
            newLeft = popup.offset().left;
        if(window.innerHeight < popup.height() || newTop < 0){
          newTop = 0;
        }
        if(window.innerWidth < popup.width() || newLeft < 0){
          newLeft = 0;
        }
        popup.offset({
          top: newTop,
          left: newLeft
        });
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
            self._checkCurrentPortListFromPast(newSelectionList);
            for(var i = 0; i < newSelectionList.length; i++){
              /* Set rect height */
              self._resizeParentHeight(i);
              var port = self.portCreator(self._getSelfPosition(i), convertTextLength(newSelectionList[i] ,22), newSelectionList[i], self._getCoverOpacity(i, newSelectionList.length));
              self._checkPastPortListFromCurrent(newSelectionList, i, port);
            }
          },
          _checkPastPortListFromCurrent: function(targetList, number, port) {
            if($scope.oldSelectionList.indexOf(targetList[number]) === -1 ){
              /* add port */
              $scope.currentEditCellParent.embed(port);
              initNodeEvent([port]);
              graph.addCell(port);
            } else {
              /* edit port */
              var childList = this._getCurrentPortList();
              for( var i = 0; i < childList.length; i++ ){
                if( childList[i].attr(".label/text") === targetList[number] ){
                  this._setSelfPosition(childList[i], this._getSelfPosition(number));
                  var topOpacity = 1,
                      bottomOpacity = 1;
                  if(number === 0){
                    topOpacity = 0;
                  }
                  if(number === targetList.length - 1){
                    bottomOpacity = 0;
                  }
                  childList[i].attr(".cover_top/fill-opacity", topOpacity)
                  .attr(".cover_bottom/fill-opacity", bottomOpacity);
                }
              }
            }
          },
          _checkCurrentPortListFromPast: function(targetList) {
            var childList = this._getCurrentPortList();
            for( var i = 0; i < childList.length; i++ ){
              if(targetList.indexOf(childList[i].attr("nodeBasicInfo/tooltip")) === -1){
                /* delete port */
                childList[i].remove();
              }
            }
          },
          _getCurrentPortList: function() {
            var list = $scope.currentEditCellParent.getEmbeddedCells();
            var targetList = [];
            for(var i = 0; i < list.length; i++){
              try{
                if(list[i].attr("nodeBasicInfo/nodeType") === "childPortNode") {
                  targetList.push(list[i]);
                }
              } catch (e) {
                console.log("undefined Port!!")
              }
            }
            return targetList;
          },
          _setSelfPosition: function(elm, position) {
            elm.set("position",position);
          },
          _getSelfPosition: function(index) {
            return {
              x: $scope.currentEditCellParent.get('position').x + 5,
              y: $scope.currentEditCellParent.get('position').y + 115 + index * 40
            }
          },
          _resizeParentHeight: function(index) {
            $scope.currentEditCellParent.get('size').height = 160 + index * 40;
          },
          _getCoverOpacity: function(index, maxLength){
            return {
              top : index === 0 ? "0" : "1",
              bot : index === maxLength - 1 ? "0" : "1"
            }
          },
          portCreator: function(position, text, originalText, opacity) {
            return new joint.shapes.devs.Model({
              position: {x: position.x , y: position.y},
              size: {width: 240, height: 36},
              outPorts: ['out'],
              ports: {
                groups: {
                  'out': {
                    attrs: {
                      '.port-body': {
                        fill: "#C0C0C0",
                        'fill-opacity': "0.9",
                        height: 30,
                        width: 30,
                        stroke: false,
                        rx: 3,
                        ry: 3
                      },
                      '.port-label': {
                        'font-size': 0
                      },
                      type: "branch"
                    },
                    position: {
                      name: 'absolute',
                      args: {
                        x: 235,
                        y: 3
                      }
                    },
                    z: 4,
                    markup: '<rect class="port-body"/>'
                  }
                }
              },
              attrs: {
                text: {
                  text: text,
                  'ref-width': '70%',
                  'font-size': '14px',
                  fill: '#000',
                  y: 12
                },
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
                  nextNode: '',
                  tooltip: originalText
                },
                '.cover_top': {
                  fill: '#FFFFFF',
                  width: 240,
                  height: 10,
                  'fill-opacity': opacity.top
                },
                '.cover_bottom': {
                  fill: '#FFFFFF',
                  width: 240,
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
        lineCounter: 1,
        textLineSeparate: function(text){
          if(text == null) return "";
          var self = textEditor;
          var originTextArray = text.split(/\r\n|\n/);
          var resultTextArray = [];
          for( var i = 0; i < originTextArray.length; i++ ){
            if( originTextArray[i].length > 15 ){
              Array.prototype.push.apply(resultTextArray, self.textLineCreate(originTextArray[i]));
            } else {
              resultTextArray.push(originTextArray[i]);
            }
          }
          textEditor.lineCounter = resultTextArray.length > 3 ? resultTextArray.length : 3;
          if(resultTextArray.length > 3){
            resultTextArray.splice(3, resultTextArray.length - 3);
          }
          return resultTextArray.join("\n");
        },
        textLineCreate: function(textLine){
          var currentText = textLine;
          var textArray = [];
          var loopNum = currentText.length / 16;
          for( var i = 0; i < loopNum ; i++){
            textArray.push(currentText.substr(0, 16));
            currentText = currentText.substr(16);
          }
          return textArray;
        },
        textLineHeightCoordinate: function(){
          var matrix = "matrix(1,0,0,1,0,0)" ;
          switch (textEditor.lineCounter) {
            case 1:
              /* Do nothing */
              break;
            case 2:
              matrix = "matrix(1,0,0,1,0,0)";
              break;
            case 3:
              matrix = "matrix(1,0,0,1,0,0)";
              break;
            default:
              /* Do nothing */
              break;
          }
          return matrix;
        }
      };

      var previewHandler = {
        setDefaultNodeName: function(source, target){
          //既に情報が入っている場合はreturnさせる
          if(target.attr("actionParam/nodeName") !== "") return;
          var prefix,
              splitNum;
          var defaultValue = source.attr(".label/text");
          if(target.attr("nodeBasicInfo/nodeType") === "text") {
            prefix = "";
            splitNum = 16;
          } else {
            prefix = "";
            splitNum = 16
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

      $scope.$on('ngRepeatFinish', function(){
        popupEvent.resize();
        $scope.popupFix();
      });

      $scope.popupFix = function(){
        var popup = $('#popup-frame');
        popup.offset({
          top: $scope.currentTop ? $scope.currentTop : window.innerHeight / 2 - popup.height() / 2,
          left: popup.offset().left
        });
      };

      $scope.$watch("speakTextTitle", function(){
        $scope.titleHandler($scope.speakTextTitle, "テキスト発言");
      });

      $scope.$watch("branchTitle", function(){
        $scope.titleHandler($scope.branchTitle, "分岐");
      });

      $scope.$watch("isCustomize", function(){
        $scope.popupPositionAdjustment();
      });

      $scope.$watch("branchType.key", function(){
        $scope.popupPositionAdjustment();
      });

      $scope.popupPositionAdjustment = function(){
        $scope.currentTop = $('#popup-frame').offset().top;
        $timeout(function(){
          popupEvent.resize();
          $scope.popupFix();
        });
      };

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

      $scope.initCustomizeColor = function(nodeData){
        if(nodeData.customizeDesign == null){
          /* Set object to process init function */
          nodeData.customizeDesign = {};
        }
        $scope.setAllCustomizeToData(nodeData.customizeDesign);
      };

      $scope.setAllCustomizeToData = function(custom){
        $scope.isCustomize = custom.isCustomize ? custom.isCustomize : false;
        $scope.radioBackgroundColor = custom.radioBackgroundColor ? custom.radioBackgroundColor : "#FFFFFF";
        $scope.radioActiveColor = custom.radioActiveColor ? custom.radioActiveColor : $scope.widget.settings.main_color;
        $scope.radioBorderColor = custom.radioBorderColor ? custom.radioBorderColor : $scope.widget.settings.main_color;
        $scope.radioNoneBorder = custom.radioNoneBorder ? custom.radioNoneBorder : false;
        $scope.buttonUIBackgroundColor = custom.buttonUIBackgroundColor ? custom.buttonUIBackgroundColor : $scope.widget.settings.re_text_color;
        $scope.buttonUITextColor = custom.buttonUITextColor ? custom.buttonUITextColor : $scope.widget.settings.re_background_color;
        $scope.buttonUITextAlign = custom.buttonUITextAlign ? custom.buttonUITextAlign : "2";
        $scope.buttonUIActiveColor = custom.buttonUIActiveColor ? custom.buttonUIActiveColor : $scope.getRawColor($scope.widget.settings.main_color, 0.5);
        $scope.buttonUIBorderColor = custom.buttonUIBorderColor ? custom.buttonUIBorderColor : "#E3E3E3";
        $scope.outButtonUINoneBorder = custom.outButtonUINoneBorder ? custom.outButtonUINoneBorder : false;
      };

      $scope.revertStandard = function(buttonType, colorType, elm){
        var target = $(elm.target.parentNode).find('input'),
            handler;
        switch(buttonType) {
          case "radio":
            handler = $scope.radioCustomizeHandler;
            break;
          case "button":
            handler = $scope.buttonUICustomizeHandler;
            break;
          default:
            return;
        }
        handler(colorType, target);
      };

      $scope.radioCustomizeHandler = function(colorType, target){
        var targetValue;
        switch(colorType) {
          case "bg":
            targetValue = $scope.radioBackgroundColor = "#FFFFFF";
            break;
          case "button":
            targetValue = $scope.radioActiveColor = $scope.widget.settings.main_color;
            break;
          case "border":
            targetValue = $scope.radioBorderColor = $scope.widget.settings.main_color;
            break;
          default:
            /* Do nothing */
        }
        target.css("background-color", targetValue);
      };

      $scope.buttonUICustomizeHandler = function(colorType, target){
        var targetValue;
        switch(colorType) {
          case "bg":
            targetValue = $scope.buttonUIBackgroundColor = $scope.widget.settings.re_text_color;
            break;
          case "text":
            targetValue = $scope.buttonUITextColor = $scope.widget.settings.re_background_color;
            break;
          case "select":
            targetValue = $scope.buttonUIActiveColor = $scope.getRawColor($scope.widget.settings.main_color, 0.5);
            break;
          case "border":
            targetValue = $scope.buttonUIBorderColor = "#E3E3E3";
            break;
          default:
            /* Do nothing */
        }
        target.css("background-color", targetValue);
      };

      $scope.getRawColor = function(hex, opacity) {
        if (!opacity) {
          opacity = 0.1;
        }
        var code = hex.substr(1), r, g, b;
        if (code.length === 3) {
          r = String(code.substr(0, 1)) + String(code.substr(0, 1));
          g = String(code.substr(1, 1)) + String(code.substr(1, 1));
          b = String(code.substr(2)) + String(code.substr(2));
        } else {
          r = String(code.substr(0, 2));
          g = String(code.substr(2, 2));
          b = String(code.substr(4));
        }

        var balloonR = String(Math.floor(255 - (255 - parseInt(r, 16)) * opacity));
        var balloonG = String(Math.floor(255 - (255 - parseInt(g, 16)) * opacity));
        var balloonB = String(Math.floor(255 - (255 - parseInt(b, 16)) * opacity));
        var codeR = parseInt(balloonR).toString(16);
        var codeG = parseInt(balloonG).toString(16);
        var codeB = parseInt(balloonB).toString(16);

        return ('#' + codeR + codeG + codeB).toUpperCase();
      };


      $scope.autoResize = function(e, forceProcess){
        if(e == null && !forceProcess) return;
        var elm = e.target ? e.target : e[0];
        var maxRow = elm.dataset.maxRow || 10;
        var fontSize = parseFloat(elm.style.fontSize, 10);
        var borderSize = parseFloat(elm.style.borderWidth, 10) * 2;  // 行数計算のため、templateにて設定したボーダーサイズを取得(上下/左右)
        var paddingSize = parseFloat(elm.style.padding, 10) * 2;     // 表示高さの計算のため、templateにて設定したテキストエリア内の余白を取得(上下/左右)
        var lineHeight = parseFloat(elm.style.lineHeight, 10);
        // テキストエリアの要素のサイズから、borderとpaddingを引いて文字入力可能なサイズを取得する
        var areaWidth = elm.getBoundingClientRect().width - borderSize - paddingSize;

        // フォントサイズとテキストエリアのサイズを基に、行数を計算する
        var textRow = 0;
        elm.value.split('\n').forEach(function(string) {
          var stringWidth = string.length * fontSize;
          textRow += Math.max(Math.ceil(stringWidth / areaWidth), 1);
        });

        // 表示する行数に応じて、テキストエリアの高さを調整する
        if (textRow > maxRow) {
          elm.style.height = (maxRow * (fontSize * lineHeight)) + paddingSize + 'px';
          elm.style.overflow = 'auto';
        } else {
          elm.style.height = (textRow * (fontSize * lineHeight)) + paddingSize + 'px';
          elm.style.overflow = 'hidden';
        }
        $scope.popupPositionAdjustment();
      };

      /** ==========================
       * Simulator Methods
       * =========================== */
      // シミュレーターの起動
      this.openSimulator = function() {
        $scope.actionListOrigin = graph.toJSON();
        $scope.$broadcast('openSimulator', $scope.actionListOrigin);
        // シミュレータ起動時、強制的に自由入力エリアを有効の状態で表示する
        $scope.$broadcast('switchSimulatorChatTextArea', true);
      };
      /* =========================== */

    }]);

  sincloApp.controller('DialogController', [
    '$scope',
    '$timeout',
    'SimulatorService',
    function($scope, $timeout, SimulatorService) {
      //thisを変数にいれておく
      var self = this;
      $scope.setActionList = [];
      $scope.widget = SimulatorService;

      /**
       * シミュレーションの起動(ダイアログ表示)
       * @param Object activity 実行可能なシナリオ
       */
      $scope.$on('openSimulator', function(event, activity) {
        var diagrams = activity;
        $scope.setActionList = diagrams;
        var defaultHeight = 101;
        if (document.getElementById('maximum_description') != null) {
          defaultHeight += 40;
        }
        $('#tchatbotdiagrams_simulator_wrapper').show();
        $timeout(function() {
          $scope.$apply();
        }).then(function() {
          $('#simulator_popup').css({
            width: $('#sincloBox').outerWidth() + 28 + 'px',
            height: $('#sincloBox').outerHeight() + defaultHeight + 'px'
          });
          $scope.actionInit();
        }, 0);
      });

      $(document).on('onWidgetSizeChanged', function(e) {
        var defaultHeight = 101;
        if (document.getElementById('maximum_description') != null) {
          defaultHeight += 40;
        }
        $('#simulator_popup').css({
          width: $('#sincloBox').outerWidth() + 28 + 'px',
          height: $('#sincloBox').outerHeight() + defaultHeight + 'px'
        });
      });

      // next hearing action
      $scope.$on('nextHearingAction', function() {
        $scope.setActionList[$scope.actionStep].hearings[$scope.hearingIndex].skipped = true;
        $scope.hearingIndex++;
        var actionDetail = $scope.setActionList[$scope.actionStep];
        if (typeof actionDetail.hearings[$scope.hearingIndex] === 'undefined' &&
            !(actionDetail.isConfirm === '1' && ($scope.hearingIndex === actionDetail.hearings.length))) {
          $scope.hearingIndex = 0;
          self.disableHearingInput($scope.actionStep);
          $scope.actionStep++;
        }
        $scope.doAction();
      });

      // シミュレーションで受け付けた受信メッセージ
      $scope.$on('receiveVistorMessage', function(event, message, prefix) {
        // 対応するアクションがない場合は何もしない
        if (typeof $scope.setActionList[$scope.actionStep] === 'undefined') {
          return;
        }

        if ($scope.setActionList[$scope.actionStep].actionType == <?= C_SCENARIO_ACTION_HEARING ?>) {
          var actionDetail = $scope.setActionList[$scope.actionStep];

          if ($scope.hearingIndex < actionDetail.hearings.length) {
            // 入力された文字列を改行ごとに分割し、適切な入力かチェックする
            var inputType = actionDetail.hearings[$scope.hearingIndex].inputType;
            var regex = new RegExp($scope.inputTypeList[inputType].rule.replace(/^\/(.+)\/$/, '$1'));
            var isMatched = message.split(/\r\n|\n/).every(function(string) {
              return string.length >= 1 ? regex.test(string) : true;
            });
            if (isMatched) {
              // 変数の格納
              var storageParam = [];
              LocalStorageService.setItem('chatbotVariables', [
                {
                  key: actionDetail.hearings[$scope.hearingIndex].variableName,
                  value: message
                }]);
              // 次のアクション
              $scope.hearingIndex++;
              if (typeof actionDetail.hearings[$scope.hearingIndex] === 'undefined' &&
                  !(actionDetail.isConfirm === '1' && ($scope.hearingIndex === actionDetail.hearings.length))) {
                $scope.hearingIndex = 0;
                self.disableHearingInput($scope.actionStep);
                $scope.actionStep++;
              }
            } else {
              // 入力エラー
              $scope.hearingInputResult = false;
            }
          } else if (actionDetail.isConfirm === '1' && ($scope.hearingIndex === actionDetail.hearings.length) &&
              $scope.replaceVariable(actionDetail.cancel) === message) {
            // 最初から入力し直し
            $scope.hearingIndex = 0;
          } else {
            // 次のアクション
            $scope.hearingIndex++;
            if (typeof actionDetail.hearings[$scope.hearingIndex] === 'undefined' &&
                !(actionDetail.isConfirm === '1' && ($scope.hearingIndex === actionDetail.hearings.length))) {
              $scope.hearingIndex = 0;
              self.disableHearingInput($scope.actionStep);
              $scope.actionStep++;
            }
          }
          $scope.doAction();
        } else if ($scope.setActionList[$scope.actionStep].actionType == <?= C_SCENARIO_ACTION_SELECT_OPTION ?>) {
          // 選択肢
          var storageParam = [];
          LocalStorageService.setItem('chatbotVariables', [
            {
              key: $scope.setActionList[$scope.actionStep].selection.variableName,
              value: message
            }]);
          $scope.actionStep++;
          $scope.doAction();
        } else if ($scope.setActionList[$scope.actionStep].actionType == <?= C_SCENARIO_ACTION_RECEIVE_FILE ?>) {
          $scope.actionStep++;
          $scope.doAction();
        } else if ($scope.setActionList[$scope.actionStep].actionType == <?= C_SCENARIO_ACTION_BULK_HEARING ?>) {
          chatBotTyping();
          $.post("<?=$this->Html->url(['controller' => 'CompanyData', 'action' => 'parseSignature'])?>",
              JSON.stringify({
                'accessToken': 'x64rGrNWCHVJMNQ6P4wQyNYjW9him3ZK',
                'targetText': message
              }), null, 'json').done(function(result) {
            setTimeout(function() {
              $scope.$broadcast('addReForm', {
                prefix: 'action' + $scope.actionStep + '_bulk-hearing',
                isConfirm: true,
                bulkHearings: $scope.setActionList[$scope.actionStep].multipleHearings,
                resultData: result
              });
              $scope.$broadcast('switchSimulatorChatTextArea', false);
              chatBotTypingRemove();
            }, parseInt($scope.setActionList[$scope.actionStep].messageIntervalTimeSec, 10) * 1000);
          });
        }
      });

      $scope.$on('pressFormOK', function(event, message) {
        $('#chatTalk > div:last-child').fadeOut('fast').promise().then(function() {
          var saveValue = [];
          Object.keys(message).forEach(function(elm) {
            saveValue.push({
              key: elm,
              value: message[elm].value
            });
          });
          $scope.$broadcast('addReForm', {
            prefix: 'action' + $scope.actionStep + '_bulk-hearing',
            isConfirm: false,
            bulkHearings: $scope.setActionList[$scope.actionStep].multipleHearings,
            resultData: {data: message}
          });
          LocalStorageService.setItem('chatbotVariables', saveValue);
          $scope.actionStep++;
          $scope.doAction();
        });
      });

      $scope.addVisitorHearingMessage = function(message) {
        var actionDetail = $scope.setActionList[$scope.actionStep];

        if ($scope.hearingIndex < actionDetail.hearings.length) {
          var uiType = actionDetail.hearings[$scope.hearingIndex].uiType;

          if (uiType === '1' || uiType === '2') {
            // 入力された文字列を改行ごとに分割し、適切な入力かチェックする
            var inputType = actionDetail.hearings[$scope.hearingIndex].inputType;
            var regex = new RegExp($scope.inputTypeList[inputType].rule.replace(/^\/(.+)\/$/, '$1'));
            var isMatched = message.split(/\r\n|\n/).every(function(string) {
              return string.length >= 1 ? regex.test(string) : true;
            });
            if (isMatched) {
              LocalStorageService.setItem('chatbotVariables', [
                {
                  key: actionDetail.hearings[$scope.hearingIndex].variableName,
                  value: message
                }]);
              // 次のアクション
              $scope.hearingIndex++;
              if (typeof actionDetail.hearings[$scope.hearingIndex] === 'undefined' &&
                  !(actionDetail.isConfirm === '1' && ($scope.hearingIndex === actionDetail.hearings.length))) {
                $scope.hearingIndex = 0;
                $scope.actionStep++;
              }
            } else {
              // 入力エラー
              $scope.hearingInputResult = false;
            }
          } else {
            LocalStorageService.setItem('chatbotVariables', [
              {
                key: actionDetail.hearings[$scope.hearingIndex].variableName,
                value: message
              }]);
            // 次のアクション
            $scope.hearingIndex++;
            if (typeof actionDetail.hearings[$scope.hearingIndex] === 'undefined' &&
                !(actionDetail.isConfirm === '1' && ($scope.hearingIndex === actionDetail.hearings.length))) {
              $scope.hearingIndex = 0;
              self.disableHearingInput($scope.actionStep);
              $scope.actionStep++;
            }
          }
        } else if (actionDetail.isConfirm === '1' && ($scope.hearingIndex === actionDetail.hearings.length) &&
            $scope.replaceVariable(actionDetail.cancel) === message) {
          angular.forEach(actionDetail.hearings, function(hearing) {
            hearing.canRestore = true;
          });
          // 最初から入力し直し
          $scope.hearingIndex = 0;
        } else {
          // 次のアクション
          $scope.hearingIndex++;
          if (typeof actionDetail.hearings[$scope.hearingIndex] === 'undefined' &&
              !(actionDetail.isConfirm === '1' && ($scope.hearingIndex === actionDetail.hearings.length))) {
            $scope.hearingIndex = 0;
            self.disableHearingInput($scope.actionStep);
            $scope.actionStep++;
          }
        }
        $scope.doAction();
      };

      // handle hearing re-select
      $scope.reSelectionHearing = function(message, actionStep, hearingIndex) {
        $scope.hearingIndex = hearingIndex;
        $scope.actionStep = actionStep;
        var actionDetail = $scope.setActionList[actionStep];
        var uiType = actionDetail.hearings[hearingIndex].uiType;
        // テキストタイプ
        if (uiType === '1' || uiType === '2') {
          // 入力された文字列を改行ごとに分割し、適切な入力かチェックする
          var inputType = actionDetail.hearings[hearingIndex].inputType;
          var regex = new RegExp($scope.inputTypeList[inputType].rule.replace(/^\/(.+)\/$/, '$1'));
          var isMatched = message.split(/\r\n|\n/).every(function(string) {
            return string.length >= 1 ? regex.test(string) : true;
          });
          if (isMatched) {
            // 変数の格納
            LocalStorageService.setItem('chatbotVariables', [
              {
                key: actionDetail.hearings[hearingIndex].variableName,
                value: message
              }]);
            // 次のアクション
            $scope.hearingIndex++;
            if (typeof actionDetail.hearings[$scope.hearingIndex] === 'undefined' &&
                !(actionDetail.isConfirm === '1' && ($scope.hearingIndex === actionDetail.hearings.length))) {
              $scope.hearingIndex = 0;
              self.disableHearingInput($scope.actionStep);
              $scope.actionStep++;
            }
          } else {
            // 入力エラー
            $scope.hearingInputResult = false;
          }
        } else {
          // 変数の格納
          LocalStorageService.setItem('chatbotVariables', [
            {
              key: actionDetail.hearings[hearingIndex].variableName,
              value: message
            }]);
          // 次のアクション
          $scope.hearingIndex++;
          if (typeof actionDetail.hearings[$scope.hearingIndex] === 'undefined' &&
              !(actionDetail.isConfirm === '1' && ($scope.hearingIndex === actionDetail.hearings.length))) {
            $scope.hearingIndex = 0;
            self.disableHearingInput($scope.actionStep);
            $scope.actionStep++;
          }
        }

        $scope.doAction();
      };

      // シミュレーションの終了(ダイアログ非表示)
      $scope.closeSimulator = function() {
        $scope.actionStop();
        $('#tchatbotdiagrams_simulator_wrapper').hide();
      };

      // アクションの開始
      $scope.actionInit = function() {
        $scope.beginNodeId = '';
        $scope.currentNodeId = '';

        for(var i=0; i < $scope.setActionList.cells.length; i++) {
          if($scope.setActionList.cells[i].type !== 'devs.Model') continue;
          var node = $scope.setActionList.cells[i];
          if(node.attrs.nodeBasicInfo.nodeType === 'start') {
            $scope.beginNodeId = node.id;
            $scope.currentNodeId = node.attrs.nodeBasicInfo.nextNodeId;
          }
          break;
        }

        // シミュレーション上のメッセージをクリアする
        $scope.$broadcast('removeMessage');
        $scope.doAction();
      };

      $scope.$watch('actionStep', function() {
        $scope.widget.setCurrentActionStep($scope.actionStep);
      });

      $scope.$watch('hearingIndex', function() {
        $scope.widget.setCurrentHearingIndex($scope.hearingIndex);
      });

      // アクションの停止
      $scope.actionStop = function() {
        $timeout.cancel($scope.simulatorTimer);
      };

      // アクションのクリア(アクションを最初から実行し直す)
      $scope.actionClear = function() {
        $scope.actionStop();
        $scope.actionInit();
        $scope.setActionList = $scope.actionListOrigin;
      };

      /**
       * アクションの実行
       * @param String setTime 基本設定のメッセージ間隔に関わらず、メッセージ間隔を指定
       */
      $scope.receiveFileEventListener = null;
      $scope.firstActionFlg = true;
      $scope.doAction = function(setTime) {
        if (true) {
          // メッセージ間隔
          var time = parseInt(2, 10) * 1000;
          var actionNode = $scope.findNodeById($scope.currentNodeId);

          chatBotTyping();

          $timeout.cancel($scope.actionTimer);
          $scope.actionTimer = $timeout(function() {
            switch(actionNode.attrs.nodeBasicInfo.nodeType) {
              case 'branch': // 分岐
                $scope.doBranchAction(actionNode);
                break;
              case 'text': // テキスト発言
                break;
              case 'scenario': // シナリオ呼び出し
                break;
              case 'jump': // ジャンプ
                break;
              case 'link': // リンク
                break;
              case 'operator': // オペレータ呼び出し
                break;
              case 'cv': //CVポイント
                break;
            }
          }, time);
        } else {
          setTimeout(chatBotTypingRemove, 801);
          $scope.actionStop();
        }
      };

      $scope.findNodeById = function(nodeId) {
        var targetNode = {};
        Object.keys($scope.setActionList.cells).some(function(idx, arrIdx, arr){
          var node = $scope.setActionList.cells[idx];
          if(node.id.indexOf(nodeId) !== -1) {
            targetNode = node;
            return true;
          }
        });
        return targetNode;
      };

      $scope.isMatch = function(targetValue, condition) {
        switch (Number(condition.matchValueType)) {
          case 1: // いずれかを含む場合
            return $scope.matchCaseInclude(targetValue, $scope.splitMatchValue(condition.matchValue), condition.matchValuePattern);
          case 2: // いずれも含まない場合
            return $scope.matchCaseExclude(targetValue, $scope.splitMatchValue(condition.matchValue), condition.matchValuePattern);
          default:
            return false;
        }
      };

      $scope.doBranchOnCondAction = function(condition, callback) {
        switch (Number(condition.actionType)) {
          case 1:
            $scope.$broadcast('addReMessage', $scope.replaceVariable(condition.action.message),
                'action' + $scope.actionStep);
            $scope.actionStep++;
            $scope.doAction();
            break;
          case 2:
            // シナリオ呼び出し
            var targetScenarioId = condition.action.callScenarioId;
            console.log('targetScenarioId : %s', targetScenarioId);
            if (targetScenarioId === 'self') {
              var activity = {};
              activity.scenarios = $scope.actionListOrigin;
              $scope.setActionList = $scope.setCalledScenario(activity, condition.action.executeNextAction == 1);
              $scope.doAction();
            } else {
              self.getScenarioDetail(targetScenarioId, condition.action.executeNextAction == 1);
            }
            break;
          case 3:
            $scope.actionStop();
            // シナリオ終了
            break;
          case 4:
            $scope.actionStep++;
            $scope.doAction();
            // 何もしない（次のアクションへ）
            break;
        }
      };

      $scope.splitMatchValue = function(val) {
        var splitedArray = [];
        val.split('"').forEach(function(currentValue, index, array) {
          if (array.length > 1) {
            if (index !== 0 && index % 2 === 1) {
              // 偶数個：そのまま文字列で扱う
              if (currentValue !== '') {
                splitedArray.push(currentValue);
              }
            } else {
              if (currentValue) {
                var trimValue = currentValue.trim(),
                    splitValue = trimValue.replace(/　/g, ' ').split(' ');
                splitedArray = splitedArray.concat($.grep(splitValue, function(e) {
                  return e !== '';
                }));
              }
            }
          } else {
            var trimValue = currentValue.trim(),
                splitValue = trimValue.replace(/　/g, ' ').split(' ');
            splitedArray = splitedArray.concat($.grep(splitValue, function(e) {
              return e !== '';
            }));
          }
        });
        return splitedArray;
      };

      $scope.matchCaseInclude = function(val, words, pattern) {
        console.log('_matchCaseInclude : %s <=> %s', words, val);
        var result = false;
        for (var i = 0; i < words.length; i++) {
          if (words[i] === '') {
            continue;
          }
          var word = words[i].replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
          var preg;
          if (!pattern || pattern === '1') {
            // 完全一致
            preg = new RegExp('^' + word + '$');
          } else {
            // 部分一致
            preg = new RegExp(word);
          }

          result = preg.test(val);

          if (result) { // いずれかを含む
            break;
          }
        }
        return result;
      };

      $scope.matchCaseExclude = function(val, words, pattern) {
        for (var i = 0; i < words.length; i++) {
          if (words[i] === '') {
            if (words.length > 1 && i === words.length - 1) {
              break;
            }
            continue;
          } else {
            var word = words[i].replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
            var preg;
            if (!pattern || pattern === '1') {
              // 完全一致
              preg = new RegExp('^' + word + '$');
            } else {
              // 部分一致
              preg = new RegExp(word);
            }
            var exclusionResult = preg.test(val);
            if (exclusionResult) {
              // 含んでいる場合はNG
              return false;
            }
          }
        }
        //最後まで含んでいなかったらOK
        return true;
      };

      /**
       * ヒアリングアクションの実行
       * @param Object actionDetail アクションの詳細
       */
      $scope.doBranchAction = function(node) {
        chatBotTypingRemove();
        var nodeId = node.id;
        var buttonType = node.attrs.actionParam.btnType;
        var message = node.attrs.actionParam.text;
        var selections = $scope.getBranchSelection(node);
        var labels = $scope.getBranchLabels(node, Object.keys(selections));
        $scope.$broadcast('addReBranchMessage', nodeId, buttonType, message, selections, labels);
      };

      $scope.getBranchSelection = function(node) {
        var itemIds = node.embeds;
        var map = {};
        var baseData = $scope.setActionList.cells;
        for (var i = 0; i < itemIds.length; i++) {
          for (var nodeIndex = 0; nodeIndex <
          baseData.length; nodeIndex++) {
            if(baseData[nodeIndex]['type'] !== 'devs.Model') continue;
            console.log('baseData.id:%s itemId:%s baseData.attrs.nodeBasicInfo.nodeType: %s', baseData[nodeIndex]['id'], itemIds[i], baseData[nodeIndex]['attrs']['nodeBasicInfo']['nodeType']);
            if (baseData[nodeIndex]['id'] === itemIds[i] &&
                baseData[nodeIndex]['attrs']['nodeBasicInfo'] &&
                'childPortNode'.indexOf(baseData[nodeIndex]['attrs']['nodeBasicInfo']['nodeType']) !== -1 &&
                baseData[nodeIndex]['attrs']['nodeBasicInfo']['nextNodeId']) {
              map[itemIds[i]] = baseData[nodeIndex]['attrs']['nodeBasicInfo']['nextNodeId'];
            }
          }
        }
        return map;
      };

      $scope.getBranchLabels = function(node, idKeys) {
        var labels = node.attrs.actionParam.selection;
        var map = {};
        for (var i = 0; i < labels.length; i++) {
          map[idKeys[i]] = labels[i];
        }
        return map;
      };

      $scope.doBulkHearingAction = function(actionDetail) {
        if (actionDetail.multipleHearings) {
          $scope.$broadcast('allowInputLF', true, '1');
          $scope.$broadcast('switchSimulatorChatTextArea', true);
          $scope.$broadcast('disableHearingInputFlg');
        }
      };

      /**
       * メッセージ内の変数を、ローカルストレージ内のデータと置き換える
       * @param String message 変数を含む文字列
       * @return String        置換後の文字列
       */
      $scope.replaceVariable = function(message) {
        message = message ? message : '';
        return message.replace(/{{(.+?)\}}/g, function(param) {
          var name = param.replace(/^{{(.+)}}$/, '$1');
          return LocalStorageService.getItem('chatbotVariables', name) || name;
        });
      };

      /**
       * メッセージ内の変数を、ローカルストレージ内のデータと置き換える、が、ない場合は空文字列を返す
       * @param String message 変数を含む文字列
       * @return String        置換後の文字列
       */
      $scope.replaceVariableWithEmpty = function(message) {
        message = message ? message : '';
        return message.replace(/{{(.+?)\}}/g, function(param) {
          var name = param.replace(/^{{(.+)}}$/, '$1');
          return LocalStorageService.getItem('chatbotVariables', name) || '';
        });
      };

      /**
       * メッセージ内の変数を、ローカルストレージ内のデータと置き換え、数字にする
       * @param String message 変数を含む文字列
       * @return String        置換後の文字列（数値）
       */
      $scope.replaceIntegerVariable = function(message) {
        message = message ? message : '';
        return message.replace(/{{(.+?)\}}/g, function(param) {
          var name = param.replace(/^{{(.+)}}$/, '$1');
          return Number(self.toHalfWidth(LocalStorageService.getItem('chatbotVariables', name))) || Number(name);
        });
      };

      /**
       * 呼び出し先のシナリオ詳細を取得する
       * @param String scenarioId 呼び出し先シナリオID
       * @param String isNext     呼び出したシナリオ終了後、次のアクションを続けるか
       */
      this.getScenarioDetail = function(scenarioId, isNext) {
        $.ajax({
          url: "<?= $this->Html->url('/TChatbotScenario/remoteGetActionDetail') ?>",
          type: 'post',
          dataType: 'json',
          data: {
            id: scenarioId
          },
          cache: false,
          timeout: 10000
        }).done(function(data) {
          console.info('successed get scenario detail.');
          try {
            var activity = JSON.parse(data['TChatbotScenario']['activity']);
            // 取得したシナリオのアクション情報を、setActionList内に詰める
            var scenarios = $scope.setCalledScenario(activity, isNext);
            $scope.setActionList = scenarios;
          } catch (e) {
            $scope.actionStep++;
          }
        }).fail(function(jqXHR, textStatus, errorThrown) {
          // エラー情報を出力する
          console.warn('failed get scenario detail');
          console.error(errorThrown);

          $scope.actionStep++;
        }).always(function() {
          // アクションを実行する
          $scope.doAction();
        });
      };

      $scope.setCalledScenario = function(activity, isNext) {
        var scenarios = {};
        var idx = 0;
        angular.forEach($scope.setActionList, function(scenario, key) {
          if (key == $scope.actionStep) {
            for (var exKey in activity.scenarios) {
              scenarios[idx++] = activity.scenarios[exKey];
            }
          } else if (isNext == 1 || key <= $scope.actionStep) {
            scenarios[idx++] = $scope.setActionList[key];
          }
        });
        return scenarios;
      };

      /**
       * 計算・変数操作のアクション実行
       * @param Object actionDetail アクション詳細
       */
      this.doControlVariable = function(actionDetail) {
        actionDetail['calcRules'].forEach(function(calcRule) {
          try {
            var result = calcRule.formula;
            if (Number(calcRule.calcType) === <?= C_SCENARIO_CONTROL_INTEGER ?>) {
              result = self.toHalfWidth($scope.replaceIntegerVariable(result));
              result = Number(eval(result));
              result = self.roundResult(result, calcRule.significantDigits, calcRule.rulesForRounding);
              if (isNaN(result)) {
                throw new Error('Not a Number');
              }
            } else {
              result = self.adjustString($scope.replaceVariableWithEmpty(result));
            }
            LocalStorageService.setItem('chatbotVariables', [
              {
                key: calcRule.variableName,
                value: String(result)
              }]);
          } catch (e) {
            console.log(e);
            LocalStorageService.setItem('chatbotVariables', [
              {
                key: calcRule.variableName,
                value: '計算エラー'
              }]);
          }
        });
        $scope.actionStep++;
        $scope.doAction();
      };

      this.roundResult = function(value, digits, roundRule) {
        var index = Math.pow(10, digits - 1);
        // 1桁目指定の場合は整数部だけ取り出して計算
        if (Number(digits) === 0) {
          if (value > 0) {
            value = Math.floor(value);
          } else {
            value = Math.ceil(value);
          }
        }
        switch (Number(roundRule)) {
          case 1:
            //四捨五入の場合
            value = Math.round(value * index) / index;
            break;
          case 2:
            //切り捨ての場合
            value = Math.floor(value * index) / index;
            break;
          case 3:
            //切り上げの場合
            value = Math.ceil(value * index) / index;
            break;
          default:
            //デフォルトは四捨五入
            value = Math.round(value * index) / index;
        }

        return value;
      };
      this.adjustString = function(formula) {
        if (formula.indexOf('&') != -1) {
          var itemArray = formula.split('&');
          formula = '';
          itemArray.forEach(function(item) {
            formula += item;
          });
        }
        return formula;
      };

      /**
       *  全角⇒半角のキャスト
       *
       */
      this.toHalfWidth = function(formula) {
        var halfWidth = formula.replace(/[！-～]/g,
            function(tmpStr) {
              return String.fromCharCode(tmpStr.charCodeAt(0) - 0xFEE0);
            }
        );
        return halfWidth.replace(/”/g, '"').
            replace(/’/g, '\'').
            replace(/￥/g, '\\').
            replace(/　/g, ' ').
            replace(/～/g, '~');
      };

      /**
       * 外部システム連携のAPI実行(Controllerを呼び出す)
       * @param Object actionDetail アクション詳細
       */
      this.callExternalApi = function(actionDetail) {
        // パラメーターの設定
        var requestHeaders = [];
        if (typeof actionDetail.requestHeaders !== 'undefined') {
          requestHeaders = actionDetail.requestHeaders.map(function(param) {
            return {'name': $scope.replaceVariable(param.name), 'value': $scope.replaceVariable(param.value)};
          });
        }
        var sendData = {
          'url': encodeURI($scope.replaceVariable(actionDetail.url)),
          'methodType': actionDetail.methodType,
          'requestHeaders': requestHeaders,
          'requestBody': $scope.replaceVariable(actionDetail.requestBody),
          'responseType': actionDetail.responseType,
          'responseBodyMaps': actionDetail.responseBodyMaps
        };

        $.ajax({
          url: "<?= $this->Html->url('/Notification/callExternalApi') ?>",
          type: 'post',
          dataType: 'json',
          data: {
            apiParams: JSON.stringify(sendData)
          },
          cache: false,
          timeout: 10000
        }).done(function(data) {
          console.info('successed calling external api.');
          var storageParam = [];
          data.result.forEach(function(param) {
            storageParam.push({key: param.variableName, value: param.value});
          });
          LocalStorageService.setItem('chatbotVariables', storageParam);
        }).fail(function(error) {
          console.error('failed calling external api.', error.statusText);
        }).always(function() {
          $scope.actionStep++;
          $scope.doAction();
        });
      };

      // handle next button click
      $(document).on('click', '.nextBtn', function() {
        var numbers = $(this).attr('id').match(/\d+/g).map(Number);
        var actionStep = numbers[0];
        var hearingIndex = numbers[1];

        var variable = $scope.setActionList[actionStep].hearings[hearingIndex].variableName;
        var message = LocalStorageService.getItem('chatbotVariables', variable);
        $scope.$broadcast('addSeMessage', $scope.replaceVariable(message),
            'action' + actionStep + '_hearing' + $scope.hearingIndex);
        $(this).hide();

        $scope.hearingIndex++;
        var actionDetail = $scope.setActionList[actionStep];
        if (typeof actionDetail.hearings[$scope.hearingIndex] === 'undefined' &&
            !(actionDetail.isConfirm === '1' && ($scope.hearingIndex === actionDetail.hearings.length))) {
          $scope.hearingIndex = 0;
          self.disableHearingInput($scope.actionStep);
          $scope.actionStep++;
        }

        $scope.doAction();
      });

      // disable input after hearing finish
      this.disableHearingInput = function(actionIndex) {
        $scope.$broadcast('switchSimulatorChatTextArea', false);
        $('#sincloBox input[name*="action' + actionIndex + '"]').prop('disabled', true);
        $('#sincloBox select[id*="action' + actionIndex + '"]').prop('disabled', true);
        $('#sincloBox [id^="action' + actionIndex + '"][id*="underline"]').
            find('.sinclo-text-line').
            removeClass('underlineText');
        $('#sincloBox [id^="action' + actionIndex + '"][id*="calendar"]').addClass('disabledArea');
        $('#sincloBox [id^="action' + actionIndex + '"][id*="carousel"]').addClass('disabledArea');
        $('#sincloBox [id^="action' + actionIndex + '"] .sinclo-button').prop('disabled', true).css('background-color', '#DADADA');
        $('#sincloBox [id^="action' + actionIndex + '"] .sinclo-button-ui').prop('disabled', true).css('background-color', '#DADADA');
        $('#sincloBox [id^="action' + actionIndex + '"][id*="sinclo-checkbox"]').addClass('disabledArea');
        $('#sincloBox [id^="action' + actionIndex + '"][id$="next"]').hide();
        $scope.$broadcast('disableHearingInputFlg');
      };

      this.handleReselectionInput = function(message, actionStep, hearingIndex) {
        var variable = $scope.setActionList[actionStep].hearings[hearingIndex].variableName;
        var isRestore = $scope.setActionList[actionStep].restore;
        var item = LocalStorageService.getItem('chatbotVariables', variable);
        var skipped = $scope.setActionList[actionStep].hearings[hearingIndex].skipped;
        if (isRestore) {
          if (!item && !skipped) {
            // first time input
            $('#action' + actionStep + '_hearing' + hearingIndex + '_question').find('.nextBtn').hide();
            $scope.addVisitorHearingMessage(message);
          } else if ((!item && skipped) ||
              (item && ($scope.setActionList[actionStep].hearings[hearingIndex].uiType === '7' || $scope.setActionList[actionStep].hearings[hearingIndex].uiType === '8' || $scope.setActionList[actionStep].hearings[hearingIndex].uiType === '9' || item !== message))) {
            $('#action' + actionStep + '_hearing' + hearingIndex + '_question').find('.nextBtn').hide();
            $('#action' + actionStep + '_hearing' + hearingIndex + '_question').parent().nextAll('div').remove();
            $scope.reSelectionHearing(message, actionStep, hearingIndex);
          } else if ($scope.setActionList[actionStep].hearings[hearingIndex].uiType === '6') {
            $('#action' + actionStep + '_hearing' + hearingIndex + '_question').find('.nextBtn').hide();
            $('#action' + actionStep + '_hearing' + hearingIndex + '_question').parent().nextAll('div').remove();
            $scope.reSelectionHearing(message, actionStep, hearingIndex);
          }

          if ($scope.setActionList[actionStep].hearings[hearingIndex].uiType === '9') {
            $scope.$broadcast('addCheckboxMessage', $scope.replaceVariable(message),
                'action' + actionStep + '_hearing' + $scope.hearingIndex, $scope.setActionList[actionStep].hearings[hearingIndex].settings.checkboxSeparator);
          } else {
            $scope.$broadcast('addSeMessage', $scope.replaceVariable(message),
                'action' + actionStep + '_hearing' + $scope.hearingIndex);
          }

        } else {
          if (!item && !skipped) {
            // first time input
            $scope.addVisitorHearingMessage(message);
          } else {
            $('#action' + actionStep + '_hearing' + hearingIndex + '_question').parent().nextAll('div').remove();
            $scope.reSelectionHearing(message, actionStep, hearingIndex);
          }
          if ($scope.setActionList[actionStep].hearings[hearingIndex].uiType === '9') {
            $scope.$broadcast('addCheckboxMessage', $scope.replaceVariable(message),
                'action' + actionStep + '_hearing' + $scope.hearingIndex,  $scope.setActionList[actionStep].hearings[hearingIndex].settings.checkboxSeparator);
          } else {
            $scope.$broadcast('addSeMessage', $scope.replaceVariable(message),
                'action' + actionStep + '_hearing' + $scope.hearingIndex);
          }
        }
      };

      this.handleDiagramReselectionInput = function(message, type, nodeId) {
        $scope.$broadcast('addSeMessage', message,
            'anwer_' + type * '_' + nodeId);
      };

      // handle radio button click
      $(document).on('change', '#chatTalk input[type="radio"]', function() {
        var prefix = $(this).attr('id').replace(/-sinclo-radio[0-9a-z-]+$/i, '');
        var message = $(this).val().replace(/^\s/, '');
        var isConfirm = prefix.indexOf('confirm') !== -1 ? true : false;
        var name = $(this).attr('name');

        var numbers = prefix.match(/\d+/g).map(Number);
        var actionStep = numbers[0];
        var hearingIndex = numbers[1];
        if (isConfirm) {
          // confirm message
          $scope.addVisitorHearingMessage(message);
          $scope.$broadcast('addSeMessage', $scope.replaceVariable(message),
              'action' + actionStep + '_hearing_confirm');
          $('input[name=' + name + '][type="radio"]').prop('disabled', true);
          // ラジオボタンを非活性にする
          self.disableHearingInput($scope.actionStep);
          $('[id^="action' + actionStep + '_hearing"][id$="_question"]').removeAttr('id');
        } else {
          self.handleReselectionInput(message, actionStep, hearingIndex);
        }
      });

      // プルダウンの選択
      $(document).on('change', '#chatTalk select', function() {
        var prefix = $(this).attr('id').replace(/-sinclo-pulldown[0-9a-z-]+$/i, '');
        var message = $(this).val().replace(/^\s/, '');

        var numbers = prefix.match(/\d+/g).map(Number);
        var actionStep = numbers[0];
        var hearingIndex = numbers[1];

        if (message !== '選択してください') {
          self.handleReselectionInput(message, actionStep, hearingIndex);
        } else {
          $(this).parents('.sinclo_re').find('.nextBtn').hide();
        }
      });

      $(document).on('click', '#chatTalk .carousel-container .thumbnail', function() {
        var prefix = $(this).attr('id');
        var numbers = prefix.match(/\d+/g).map(Number);
        var actionStep = numbers[0];
        var hearingIndex = numbers[1];
        var imageIndex = numbers[2];
        var message = $scope.setActionList[actionStep].hearings[hearingIndex].settings.images[imageIndex].answer;
        self.handleReselectionInput(message, actionStep, hearingIndex);
      });

      // カレンダーの選択
      $(document).on('change', '#chatTalk .flatpickr-input', function() {
        var prefix = $(this).attr('id').replace(/-sinclo-datepicker[0-9a-z-]+$/i, '');
        var message = $(this).val().replace(/^\s/, '');

        var numbers = prefix.match(/\d+/g).map(Number);
        var actionStep = numbers[0];
        var hearingIndex = numbers[1];
        self.handleReselectionInput(message, actionStep, hearingIndex);
      });

      // ボタンの選択
      $(document).on('click', '#chatTalk .sinclo-button', function() {
        $(this).parents('div.sinclo-button-wrap').find('.sinclo-button').removeClass('selected');
        $(this).addClass('selected');
        var prefix = $(this).parents('div.sinclo-button-wrap').attr('id').replace(/-sinclo-button[0-9a-z-]+$/i, '');
        var message = $(this).text().replace(/^\s/, '');

        if($(this).data('nid') && (this).data('nextNid')) {

        } else {
          var numbers = prefix.match(/\d+/g).map(Number);
          var actionStep = numbers[0];
          var hearingIndex = numbers[1];
          self.handleReselectionInput(message, actionStep, hearingIndex);
        }
      });

      $(document).on('click', '#chatTalk .sinclo-button-ui', function() {
        $(this).parent('div').find('.sinclo-button-ui').removeClass('selected');
        $(this).addClass('selected');
        var prefix = $(this).parents('div').attr('id').replace(/-sinclo-button[0-9a-z-]+$/i, '');
        var message = $(this).text().replace(/^\s/, '');

        var numbers = prefix.match(/\d+/g).map(Number);
        var actionStep = numbers[0];
        var hearingIndex = numbers[1];
        self.handleReselectionInput(message, actionStep, hearingIndex);
      });
      // button ui
      $(document).on('click', '#chatTalk .sinclo-button-ui', function() {
        $(this).parent('div').find('.sinclo-button-ui').removeClass('selected');
        $(this).addClass('selected');
        var prefix = $(this).parents('div').attr('id').replace(/-sinclo-button[0-9a-z-]+$/i, '');
        var message = $(this).text().replace(/^\s/, '');

        var numbers = prefix.match(/\d+/g).map(Number);
        var actionStep = numbers[0];
        var hearingIndex = numbers[1];
        self.handleReselectionInput(message, actionStep, hearingIndex);
      });

      $(document).on('click', '#chatTalk .checkbox-submit-btn', function() {
        $(this).addClass('disabledArea');
        var prefix = $(this).parents('div').attr('id').replace(/-sinclo-checkbox[0-9a-z-]+$/i, '');
        var message = [];
        $(this).parent('div').find('input:checked').each(function(e) {
          message.push($(this).val());
        });

        var separator = ',';
        switch (Number($(this).parents('div').attr('data-separator'))) {
          case 1:
            separator = ',';
            break;
          case 2:
            separator = '/';
            break;
          case 3:
            separator = '|';
            break;
          default:
            separator = ',';
            break;
        }

        message = message.join(separator);
        var numbers = prefix.match(/\d+/g).map(Number);
        var actionStep = numbers[0];
        var hearingIndex = numbers[1];
        self.handleReselectionInput(message, actionStep, hearingIndex);
      });

      $(document).on('change', '#chatTalk input[type="checkbox"]', function() {
        if ($(this).is('checked')) {
          $(this).prop('checked', false);
        }

        if ($(this).parent().parent().find('input:checked').length > 0) {
          $(this).parent().parent().find('.checkbox-submit-btn').removeClass('disabledArea');
        } else {
          $(this).parent().parent().find('.checkbox-submit-btn').addClass('disabledArea')
        }
      });

      $(document).on('click', '#chatTalk .checkbox-submit-btn', function() {
        $(this).addClass('disabledArea');
        var prefix = $(this).parents('div').attr('id').replace(/-sinclo-checkbox[0-9a-z-]+$/i, '');
        var message = [];
        $(this).parent('div').find('input:checked').each(function(e) {
          message.push($(this).val());
        });

        var separator = ',';
        switch (Number($(this).parents('div').attr('data-separator'))) {
          case 1:
            separator = ',';
            break;
          case 2:
            separator = '/';
            break;
          case 3:
            separator = '|';
            break;
          default:
            separator = ',';
            break;
        }

        message = message.join(separator);
        var numbers = prefix.match(/\d+/g).map(Number);
        var actionStep = numbers[0];
        var hearingIndex = numbers[1];
        self.handleReselectionInput(message, actionStep, hearingIndex);
      });

      $(document).on('change', '#chatTalk input[type="checkbox"]', function() {
        if ($(this).is('checked')) {
          $(this).prop('checked', false);
        }

        if ($(this).parent().parent().find('input:checked').length > 0) {
          $(this).parent().parent().find('.checkbox-submit-btn').removeClass('disabledArea');
        } else {
          $(this).parent().parent().find('.checkbox-submit-btn').addClass('disabledArea')
        }
      });

      // re-input text type
      $(document).on('click', '#chatTalk .underlineText', function() {
        var prefix = $(this).parents('.liBoxRight, .liRight').attr('id');
        var numbers = prefix.match(/\d+/g).map(Number);
        var actionStep = numbers[0];
        var hearingIndex = numbers[1];
        $scope.actionStep = actionStep;
        $scope.hearingIndex = hearingIndex;
        var actionDetail = $scope.setActionList[actionStep];
        var hearingDetail = $scope.setActionList[actionStep].hearings[hearingIndex];

        var variable = $scope.setActionList[actionStep].hearings[hearingIndex].variableName;
        var value = LocalStorageService.getItem('chatbotVariables', variable);
        $('#action' + actionStep + '_hearing' + hearingIndex + '_question').parent().nextAll('div').remove();

        if (hearingDetail.uiType == <?= C_SCENARIO_UI_TYPE_ONE_ROW_TEXT ?>) {
          $('#miniSincloChatMessage').val(value);
          $scope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1', hearingDetail.uiType,
              hearingDetail.required);
          $scope.$broadcast('allowInputLF', false, hearingDetail.inputType);
          var strInputRule = $scope.inputTypeList[hearingDetail.inputType].inputRule;
          $scope.$broadcast('setInputRule', strInputRule.replace(/^\/(.+)\/$/, '$1'));
        }

        if (hearingDetail.uiType == <?= C_SCENARIO_UI_TYPE_MULTIPLE_ROW_TEXT ?>) {
          $('#sincloChatMessage').val(value);
          $scope.$broadcast('switchSimulatorChatTextArea', actionDetail.chatTextArea === '1', hearingDetail.uiType,
              hearingDetail.required);
          $scope.$broadcast('allowSendMessageByShiftEnter', true, hearingDetail.inputType);
          var strInputRule = $scope.inputTypeList[hearingDetail.inputType].inputRule;
          $scope.$broadcast('setInputRule', strInputRule.replace(/^\/(.+)\/$/, '$1'));
        }
      });
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
          '<div class="node_name_valid_margin">' +
          '<span class="diagram_valid" ng-show="nodeNameIsEmpty">ノード名を入力して下さい</span>' +
          '</div>' +
          '<div id=\'branch_modal_body\'>' +
          '<div class=\'branch_modal_setting_header\'>' +
          '<div class=\'flex_row_box\'>' +
          '<p>発言内容</p>' +
          '<resize-textarea ng-keyup="autoResize($event, true)" ng-keydown="autoResize($event, true)" ng-model="branchText"></resize-textarea>' +
          '</div>' +
          '<div class="m40">' +
          '<div class=\'flex_row_box\'>' +
          '<label for=\'branch_button\'>表示形式</label>' +
          '<select name=\'branch_button\' id=\'branchBtnType\' ng-model="branchType" ng-options="btnType.value for btnType in branchTypeList track by btnType.key">' +
          '</select>' +
          '<div id="bulkRegister" class="btn-shadow disOffgreenBtn">選択肢を一括登録</div>'+
          '</div>' +
          '<div class="btn_valid_margin">' +
          '<span class="diagram_valid" ng-show="btnTypeIsEmpty">表示形式を選択してください</span>' +
          '</div>' +
          '</div>' +
          '<radio-customize ng-show="branchType.key == \'1\'"></radio-customize>' +
          '<button-customize ng-show="branchType.key == \'2\'"></button-customize>' +
          '</div>' +
          '<div class=\'branch_modal_setting_content\'>' +
          '<div class=\'setting_row\' ng-repeat="selection in branchSelectionList track by $index">' +
          '<p>選択肢</p>' +
          '<input type="text" ng-model="branchSelectionList[$index]" />' +
          '<img src=\'/img/add.png?1530001126\' width=\'20\' height=\'20\' class=\'btn-shadow disOffgreenBtn\' ng-hide="addBtnHide" ng-click="btnClick(\'add\', branchSelectionList, $index)">' +
          '<img src=\'/img/dustbox.png?1530001127\' width=\'20\' height=\'20\' class=\'btn-shadow redBtn\' ng-hide="deleteBtnHide" ng-click="btnClick(\'delete\', branchSelectionList, $index)">' +
          '</div>' +
          '</div>' +
          '</div>' +
          '</div>' +
          '<div id=\'branch_modal_preview\'>' +
          '<h3>プレビュー</h3>' +
          '<div class="diagram_preview_area">' +
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
          '<span class="diagram_valid node_name_valid_margin" ng-show="nodeNameIsEmpty">ノード名を入力して下さい</span>' +
          '<div id=\'text_modal_body\'>' +
          '<p>発言内容</p>' +
          '<div id="text_modal_contents" >' +
          '<div class=\'text_modal_setting\' ng-repeat="speakText in speakTextList track by $index" finisher>' +
          '<resize-textarea ng-keyup="autoResize($event, true)" ng-keydown="autoResize($event, true)" ng-model="speakTextList[$index]"></resize-textarea>' +
          '<img src=\'/img/add.png?1530001126\' width=\'20\' height=\'20\' class=\'btn-shadow disOffgreenBtn\' ng-hide="addBtnHide" ng-click="btnClick(\'add\', speakTextList, $index)">' +
          '<img src=\'/img/dustbox.png?1530001127\' width=\'20\' height=\'20\' class=\'btn-shadow redBtn\' ng-hide="deleteBtnHide" ng-click="btnClick(\'delete\', speakTextList, $index)">' +
          '</div>' +
          '</div>' +
          '</div>' +
          '</div>' +
          '<div id=\'text_modal_preview\'>' +
          '<h3>プレビュー</h3>' +
          '<div class="diagram_preview_area">' +
          '<preview-text ng-repeat="text in speakTextList" ng-model="textPreview">' +
          '</preview-text>' +
          '</div>' +
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
          '<div class="link_valid_margin">' +
          '<span class="diagram_valid" ng-show="linkIsEmpty">URLを入力して下さい</span>' +
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
      template: '<div>' +
          '<div id=\'scenario_modal\'>' +
          '<label for=\'scenario\'>シナリオ名</label>' +
          '<select name=\'scenario\' id=\'callTargetScenario\'ng-model="selectedScenario" ng-options="sc.value for sc in scenarioArrayList track by sc.key">' +
          '</select>' +
          '</div>' +
          '<div class="scenario_valid_margin">' +
          '<span class="diagram_valid" ng-show="scenarioIsEmpty">シナリオを選択してください</span>' +
          '</div>' +
          '</div>'
    }
  }).directive('jumpModal', function(){
    return {
      restrict: 'E',
      replace: true,
      template: '<div>' +
          '<div id=\'jump_modal\'>' +
          '<label for=\'jump\'>ノード名</label>' +
          '<select ng-model="jumpTarget" name=\'jump\' id=\'jumpTargetNode\' ng-options="jump.value for jump in jumpTargetList track by jump.key">' +
          '<select>' +
          '</div>' +
          '<div  class="jump_valid_margin">' +
          '<span class="diagram_valid" ng-show="jumpTargetIsEmpty">ジャンプ先を選択してください</span>' +
          '</div>' +
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
  }).directive('radioCustomize', function(){
    return {
      restrict: 'E',
      replace: true,
      require: '^ngModel',
      template: '<div class="customize_form">' +
          '<label><input type="checkbox" ng-model="isCustomize">デザインをカスタマイズする</label>' +
          '<div ng-show="isCustomize" class="customize_area radio_customize">' +
          '<span class="customize_row">' +
          '<label>ラジオボタン背景色</label>' +
          '<input class="jscolor {hash:true}" type="text" ng-model="radioBackgroundColor" maxlength="7">' +
          '<span class="greenBtn btn-shadow revert-button" ng-click=\'revertStandard("radio","bg",$event)\'>標準に戻す</span>' +
          '</span>' +
          '<span class="customize_row">' +
          '<label>ラジオボタンの色</label>' +
          '<input class="jscolor {hash:true}" type="text" ng-model="radioActiveColor" maxlength="7">' +
          '<span class="greenBtn btn-shadow revert-button" ng-click=\'revertStandard("radio","button",$event)\'>標準に戻す</span>' +
          '</span>' +
          '<span class="customize_row">' +
          '<label>ラジオボタン枠線色</label>' +
          '<input class="jscolor {hash:true}" type="text" ng-model="radioBorderColor" maxlength="7">' +
          '<span class="greenBtn btn-shadow revert-button" ng-click=\'revertStandard("radio","border",$event)\' >標準に戻す</span>' +
          '</span>' +
          '</div>' +
          '</div>'
    }
  }).directive('buttonCustomize', function(){
    return {
      restrict: 'E',
      replace: true,
      require: '^ngModel',
      template: '<div class="customize_form">' +
          '<label><input type="checkbox" ng-model="isCustomize">デザインをカスタマイズする</label>' +
          '<div ng-show="isCustomize" class="customize_area button_customize">' +
          '<span class="customize_row">' +
          '<label>ボタン背景色</label>' +
          '<input class="jscolor {hash:true}" type="text" ng-model="buttonUIBackgroundColor" maxlength="7">' +
          '<span class="greenBtn btn-shadow revert-button"  ng-click=\'revertStandard("button","bg",$event)\'>標準に戻す</span>' +
          '</span>' +
          '<span class="customize_row">' +
          '<label>ボタン文字色</label>' +
          '<input class="jscolor {hash:true}" type="text" ng-model="buttonUITextColor" maxlength="7">' +
          '<span class="greenBtn btn-shadow revert-button" ng-click=\'revertStandard("button","text",$event)\'>標準に戻す</span>' +
          '</span>' +
          '<span class="customize_row">' +
          '<label>ボタン文字位置</label>' +
          '<div>' +
          '<label><input type="radio" value="1" ng-model="buttonUITextAlign">左寄せ</label>' +
          '<label><input type="radio" value="2" ng-model="buttonUITextAlign">中央寄せ</label>' +
          '<label><input type="radio" value="3" ng-model="buttonUITextAlign">右寄せ</label>' +
          '</div>' +
          '</span>' +
          '<span class="customize_row">' +
          '<label>ボタン選択色</label>' +
          '<input class="jscolor {hash:true}" type="text" ng-model="buttonUIActiveColor" maxlength="7">' +
          '<span class="greenBtn btn-shadow revert-button" ng-click=\'revertStandard("button","select",$event)\'>標準に戻す</span>' +
          '</span>' +
          '<span class="customize_row">' +
          '<label>ボタン枠線色</label>' +
          '<input class="jscolor {hash:true}" type="text" ng-model="buttonUIBorderColor" maxlength="7">' +
          '<span class="greenBtn btn-shadow revert-button" ng-click=\'revertStandard("button","border",$event)\'>標準に戻す</span>' +
          '</span>' +
          '</div>' +
          '</div>'
    }
  }).directive('resizeTextarea', function() {
    return {
      restrict: 'E',
      replace: true,
      template: '<textarea class="resize" style="font-size: 13px; border-width: 1px; padding: 5px; line-height: 1.5;"></textarea>',
      link: function(scope, element, attr){
        scope.autoResize(element, false);
      }
    };
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
  });

  /**
   * ウィジェット設定取得
   * @return Object
   */
  function getWidgetSettings() {
    var json = JSON.parse(document.getElementById('TChatbotDiagramsWidgetSettings').value);
    var widgetSettings = [];
    for (var item in json) {
      widgetSettings[item] = json[item];
    }
    widgetSettings.show_name = <?=C_WIDGET_SHOW_COMP?>; // 表示名を企業名に固定する
    return widgetSettings;
  }
</script>
