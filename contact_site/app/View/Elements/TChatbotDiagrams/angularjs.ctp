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
    '$scope', '$timeout', 'SimulatorService', 'DiagramSimulatorService', '$compile', function($scope, $timeout, SimulatorService, DiagramSimulatorService, $compile) {

      /* Set basic data */
      $scope.widget = SimulatorService;
      $scope.diagramSimulatorService = DiagramSimulatorService;
      var widgetSettings = <?= json_encode($widgetSettings, JSON_UNESCAPED_UNICODE) ?>;
      $scope.widget.settings = widgetSettings;


      $scope.valueDiffChecker = {
        branch: function(target){
          return $scope.branchTitle === target.attr("actionParam/nodeName")
              && $scope.branchText === target.attr("actionParam/text")
              && $scope.branchType === target.attr("actionParam/btnType")
              && $scope.compareArray($scope.branchSelectionList, target.attr("actionParam/selection"));
        },
        text: function(target){
          return $scope.speakTextTitle === target.attr("actionParam/nodeName")
              && $scope.compareArray($scope.speakTextList, target.attr("actionParam/text"));
        },
        scenario: function(target){
          return $scope.selectedScenario.key === target.attr("actionParam/scenarioId")
              && $scope.callbackToDiagram === target.attr("actionParam/callbackToDiagram");
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

      /* something change flg */
      $scope.isChangedSomething = false;

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
      $scope.branchType = "";
      $scope.branchText = "";
      $scope.branchSelectionList = [];
      $scope.oldSelectionList = [];

      /* Model for branch customize */
      $scope.isCustomize = false;
      $scope.radioStyle = '1';
      $scope.radioEntireBackgroundColor = "";
      $scope.radioEntireActiveColor = "";
      $scope.radioTextColor = "";
      $scope.radioActiveTextColor = "";
      $scope.radioSelectionDistance = 4;
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

      window.onbeforeunload = function(){
        if($scope.isChangedSomething){
          return "行った変更が保存されない可能性があります。";
        }
      };



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
          $scope.isChangedSomething = true;
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

      // default value
      $scope.messageIntervalTimeSec = 2;
      $scope.setMessageInterval = function(){
        var allCellList = JSON.parse(dataForUpdate).cells;
        for( var i=0; i < allCellList.length; i++ ){
          if(allCellList[i].attrs.nodeBasicInfo.nodeType === "start"){
            $scope.messageIntervalTimeSec = allCellList[i].attrs.nodeBasicInfo.messageIntervalSec;
            break;
          }
        }
      };

      if (dataForUpdate !== null && dataForUpdate !== '') {
        var graphData = JSON.parse(dataForUpdate);
        Object.keys(graphData.cells).forEach(function(elm, idx, arr){
          if('devs.Model'.indexOf(graphData.cells[idx]['type']) === -1) return;
          if(graphData.cells[idx]['attrs']['actionParam']
              && graphData.cells[idx]['attrs']['nodeBasicInfo']
              && 'branch'.indexOf(graphData.cells[idx]['attrs']['nodeBasicInfo']['nodeType']) !== -1
              && graphData.cells[idx]['attrs']['actionParam']['selection']
              && typeof(graphData.cells[idx]['attrs']['actionParam']['selection'][0]) === 'string') {
            for(var i = 0; i < graphData.cells[idx]['attrs']['actionParam']['selection'].length; i++) {
              var label = graphData.cells[idx]['attrs']['actionParam']['selection'][i];
              graphData.cells[idx]['attrs']['actionParam']['selection'][i] = {
                type: "1", // 選択肢固定
                value: label
              };
            }
          }
        });
        console.log(graphData);
        graph.fromJSON(graphData);
        $scope.setMessageInterval();
        setTimeout(function(){
          dataForUpdate = graphData;
          graph.resetCells(dataForUpdate.cells);
          initNodeEvent(graph.getCells());
        }, 500);
      }





      paper.on('cell:pointerup',
          function(cellView, evt, x, y) {
            /* init current cell to null */
            $scope.currentEditCell = null;
            $scope.currentEditCellParent = null;
            if(cellView.model.attr("nodeBasicInfo/nodeType") === "childViewNode"
                || cellView.model.attr("nodeBasicInfo/nodeType") === "childPortNode"
                || cellView.model.attr("nodeBasicInfo/nodeType") === "childTextNode") {
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
                  $scope.changeTextTrigger($("textarea.for_modal"), true, $scope.branchText, "branch");
                  $('#popup-frame').css('height','80%');
                  $('#popup-content').css('height','100%');
                  popupEvent.resize = function() {};
                }else if(frame.hasClass("p_diagrams_text")){
                  $scope.titleHandler($scope.speakTextTitle, "テキスト発言");
                  var elements = $("textarea.for_modal");
                  for(var i = 0; i < elements.length ; i++){
                    if($scope.speakTextList[i] === "") continue;
                    $scope.changeTextTrigger($(elements[i]), true, $scope.speakTextList[i], i);
                  }
                  $('#popup-frame').css('height','');
                  $('#popup-content').css('height','auto');

                  popupEvent.resize = function() {
                    debugger;
                    var contHeight = $('#popup-content').height();
                    $('#popup-frame').css('top', 0).css('height', contHeight);
                    $scope.popupFix();
                  };
                }

                $scope.popupHandler();
                $scope.handleButtonCSS();
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
        || e.model.attr("nodeBasicInfo/nodeType") === "childPortNode"
        || e.model.attr("nodeBasicInfo/nodeType") === "childTextNode") {
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
        || e.model.attr("nodeBasicInfo/nodeType") === "childPortNode"
        || e.model.attr("nodeBasicInfo/nodeType") === "childTextNode") {
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
        $scope.isChangedSomething = true;
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
          $scope.moveY = e.offsetY - dragReferencePosition.y;
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
          source.attributes.ports.groups.out.attrs[".port-body"].fill = "#C0C0C0";
          var target = graph.getCell(link.get("target").id);
          if(Object.keys(target.graph._in[target.id]).length === 0) {
            target.portProp("in", "attrs/.port-body/fill", "#C0C0C0");
            target.attributes.ports.groups.in.attrs[".port-body"].fill = "#C0C0C0";
          }
        } catch (e) {
          console.log(e + "ERROR DETECTED!");
        }

      };

      $scope.colorizePort = function(linkView) {
        var source = linkView.sourceView.model;
        var typeS = source.attributes.ports.groups.out.attrs.type;
        source.portProp("out", "attrs/.port-body/fill", $scope.getPortColor(typeS, "out"));
        source.attributes.ports.groups.out.attrs[".port-body"].fill = $scope.getPortColor(typeS, "out");
        var target = linkView.targetView.model;
        var typeT = target.attributes.ports.groups.in.attrs.type;
        target.portProp("in", "attrs/.port-body/fill", $scope.getPortColor(typeT, "in"));
        target.attributes.ports.groups.in.attrs[".port-body"].fill = $scope.getPortColor(typeT, "in");
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
        $scope.isChangedSomething = false;
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
        textList: [],
        open: function() {
          try {
            this._initData($scope.branchSelectionList);
            this._createModal(this._getOverView($scope.branchType), this._getContent());
          } catch (e) {
            console.log(e + " ERROR DETECTED");
          }
        },
        _initData: function(list) {
          this.textList.length = 0;
          for(var i=0; i<list.length; i++){
            if(Number(list[i].type) === 1) {
              this.textList.push(list[i].value);
            }
          }
        },
        _initPopupOverlapEvent: function() {
          popupEventOverlap.closePopup = function() {
            $scope.branchSelectionList.length = 0;
            this.textList = $("#bulk_textarea").val().split("\n");
            for(var i=0; i < this.textList.length; i++){
              $scope.branchSelectionList.push({
                type: "1",
                value: this.textList[i]
              });
            }
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
          $scope.autoResizeTextArea();
          popupEventOverlap.resize();
          $('#bulk_textarea').bind('input', function() {
            $scope.autoResizeTextArea();
            popupEventOverlap.resize();
          });
          $(window).on('resize', function() {
            $scope.autoResizeTextArea();
            popupEventOverlap.resize();
          });
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
                 '"男性&#10;女性">' + this.textList.join("\n") + '</textarea>\n' +
                 '</div>';
        }
      };

      $scope.autoResizeTextArea = function() {
        var maxRow = 4;   // 表示可能な最大行数
        var fontSize = 13;  // 行数計算のため、templateにて設定したフォントサイズを取得
        var borderSize = 2;   // 行数計算のため、templateにて設定したボーダーサイズを取得(上下/左右)
        var paddingSize = 5;   // 表示高さの計算のため、templateにて設定したテキストエリア内の余白を取得(上下/左右)
        var lineHeight = 1.5; // 表示高さの計算のため、templateにて設定した行の高さを取得
        var elm = $('#bulk_textarea');
        // テキストエリアの要素のサイズから、borderとpaddingを引いて文字入力可能なサイズを取得する
        var areaWidth = elm[0].getBoundingClientRect().width - borderSize - paddingSize;
        // フォントサイズとテキストエリアのサイズを基に、行数を計算する
        var textRow = 1;
        elm[0].value.split('\n').forEach(function(string) {
          var stringWidth = string.length * fontSize;
          textRow += Math.max(Math.ceil(stringWidth / areaWidth), 1);
        });
        // 表示する行数に応じて、テキストエリアの高さを調整する
        if (textRow > maxRow) {
          elm.height((maxRow * (fontSize * lineHeight)) + paddingSize);
          elm.css('overflow', 'auto');
        } else {
          elm.height(textRow * (fontSize * lineHeight));
          elm.css('overflow', 'hidden');
        }
      };


      function initNodeEvent(node) {
        for (var i = 0; i < node.length; i++) {

          if (node[i].attr('nodeBasicInfo/nodeType') === 'childViewNode'
              || node[i].attr('nodeBasicInfo/nodeType') === 'childPortNode'
              || node[i].attr('nodeBasicInfo/nodeType') === 'childTextNode') {
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
              || type === 'childTextNode'
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
        var contentObj = $scope.currentEditCellParent.attr('actionParam');
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
            contentObj['value'] = $scope.currentEditCell.attr('nodeBasicInfo/tooltip');
            break;
          case 'jump':
            htmlCreator = createJumpHtml;
            modalName = 'ジャンプ';
            modalClass = 'p_const_diagrams';
            contentObj['value'] = $scope.currentEditCell.attr('nodeBasicInfo/tooltip');
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
          content: htmlCreator(contentObj),
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
                $scope.isChangedSomething = true;
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
            $scope.nodeNameIsNotUnique = $scope.isNotUniqueName($scope.speakTextTitle);
            return $scope.nodeNameIsEmpty || $scope.nodeNameIsNotUnique;
          }
        },
        branch: {
          setView: function(){
            nodeEditHandler.typeBranch.branchPortController($scope.branchSelectionList);
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
              btnType: $scope.branchType,
              selection: $scope.branchSelectionList.filter(Boolean),
              customizeDesign: $scope.selectCustomizeDesign()
            };
          },
          validation: function(){
            $scope.nodeNameIsEmpty = $scope.branchTitle === "";
            $scope.btnTypeIsEmpty = $scope.branchType === "";
            $scope.nodeNameIsNotUnique = $scope.isNotUniqueName($scope.branchTitle);
            return $scope.nodeNameIsEmpty || $scope.btnTypeIsEmpty || $scope.nodeNameIsNotUnique;
          }
        },
        scenario: {
          setView: function(){
            if($scope.selectedScenario.key !== ""){
              $scope.currentEditCell.attr('text/text', convertTextLength($scope.selectedScenario.value, 30));
              $scope.currentEditCell.attr('nodeBasicInfo/tooltip', $scope.selectedScenario.value);
            }
            if($scope.callbackToDiagram) {
              $scope.currentEditCellParent.addOutPort('out');
              $scope.currentEditCellParent.attr('.outCover', {
                fill: '#82c0cd',
                stroke: false,
                height: 33,
                width: 2,
                x: 250,
                y: 40
              });
              $scope.currentEditCellParent.changeOutGroup({
                attrs: {
                  '.port-body': {
                    fill: '#c0c0c0',
                    height: 33,
                    width: 33,
                    stroke: false,
                    rx: 5,
                    ry: 5,
                    'fill-opacity': "0.9"
                  },
                  '.port-label': {
                    'font-size': 0
                  },
                  type: 'scenario'
                },
                position: {
                  name: 'absolute',
                  args: {
                    x: 248,
                    y: 23,
                  }
                },
                z: 0,
                markup: '<rect class="port-body"/>'
              });
            } else {
              $scope.currentEditCellParent.removeOutPort('out');
            }
          },
          getData: function(){
            return {
              scenarioId: $scope.selectedScenario.key,
              callbackToDiagram: $scope.callbackToDiagram
            };
          },
          validation: function(){
            $scope.scenarioIsEmpty = $scope.selectedScenario.key === "";
            return $scope.scenarioIsEmpty;
          },
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

      $scope.isNotUniqueName = function(nodeName) {
        var cells = graph.getCells();
        for( var i = 0; i < cells.length; i++ ) {
          if( cells[i].attr("actionParam/nodeName") != null && cells[i] != $scope.currentEditCellParent ){
            if( nodeName !== "" && nodeName === cells[i].attr("actionParam/nodeName") ){
              return true;
            }
          }
        }
        return false;
      };

      $scope.initValidation = function() {
        $scope.nodeNameIsEmpty = false;
        $scope.nodeNameIsNotUnique = false;
        $scope.btnTypeIsEmpty = false;
        $scope.jumpTargetIsEmpty = false;
        $scope.scenarioIsEmpty = false;
        $scope.linkIsEmpty = false;
      };

      $scope.selectCustomizeDesign = function() {
        var design;
        switch (Number($scope.branchType)) {
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
          radioStyle: $scope.radioStyle,
          radioEntireBackgroundColor: $scope.radioEntireBackgroundColor,
          radioEntireActiveColor: $scope.radioEntireActiveColor,
          radioTextColor: $scope.radioTextColor,
          radioActiveTextColor: $scope.radioActiveTextColor,
          radioSelectionDistance: $scope.radioSelectionDistance,
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
          $scope.branchSelectionList.push({type: "1", value: ""});
        }
        //強制的に値渡しにする。
        $scope.oldSelectionList = JSON.parse(JSON.stringify($scope.branchSelectionList.concat()));
        $scope.branchTitle = nodeData.nodeName;
        $scope.branchType = nodeData.btnType;
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
        $scope.selectedScenario.value = nodeData.value;
        $scope.callbackToDiagram = nodeData.callbackToDiagram;
        return $('<scenario-modal></scenario-modal>');
      }

      function createJumpHtml(nodeData) {
        nodeEditHandler.typeJump.createJumpArray();
        $scope.jumpTarget.key = nodeData.targetId;
        $scope.jumpTarget.value = nodeData.value;
        return $('<jump-modal></jump-modal>');
      }

      function createOperatorHtml(nodeData) {
        return $('<operator-modal></operator-modal>');
      }

      function createCvHtml(nodeData) {
        return $('<cv-modal></cv-modal>');
      }

      $scope.btnClick = function(type, target, index, param){
        $scope.currentTop = $('#popup-frame').offset().top;
        switch (type) {
          case "add":
            target.splice(index + 1, 0, param);
            break;
          case "delete":
            target.splice(index, 1);
            break;
          default:
            break;
        }
        $timeout(function(){
          $scope.resetSelectionHeight();
          $scope.handleButtonCSS();
          popupEvent.resize();
          $scope.selectionHeightHandler();
          popupEvent.resize();
          $scope.popupFix();
        })
      };

      $scope.resetSelectionHeight = function(){
        $('.branch_modal_setting_content').css({
          "height": ""
        });
      };

      $scope.selectionHeightHandler = function(){
        var windowHeight = window.innerHeight;
        var popupHeight = $('#popup-frame').height();
        var selectContent = $('.branch_modal_setting_content');
        var prevMaxHeight = selectContent.css("max-height") ;
        selectContent.css({
          "max-height": ""
        });
        var delta = popupHeight - windowHeight;
        if( delta >= 0 ) {
          selectContent.css("max-height",prevMaxHeight !== "none" ? prevMaxHeight : selectContent.height() - delta);
          selectContent.css({
            "height": selectContent.height() - delta,
            "min-height": "100px"
          });
        }
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

      $scope.handleButtonCSS = function(){
        $timeout(function() {
          var spanList = [];
          var buttonComponent = $("#button_component");
          var buttonList = buttonComponent.find('button');
          var targetList = buttonComponent.children();
          /* 対象となるスパンが何番目かを設定 */
          for (var i = 0; i < targetList.length; i++) {
            if (targetList[i].children[0] && targetList[i].children[0].tagName === "SPAN") {
              spanList.push(i);
            }
          }
          /* ボタン全部のCSS(class)を取り除く */
          buttonList.removeClass("btn_top_radius");
          buttonList.removeClass("btn_bottom_radius");
          /* 最初と最後のボタンにCSS(class)を付与 */
          $(buttonList[0]).addClass("btn_top_radius");
          $(buttonList[buttonList.length - 1]).addClass("btn_bottom_radius");
          /* スパン要素前後にあるボタンにCSS(class)を付与する */
          for (var j = 0; j < spanList.length; j++) {
            if (spanList[j] !== 0) {
              if(targetList[spanList[j] - 1].children[0].tagName === "SPAN") continue;
              $(targetList[spanList[j] - 1].children[0]).addClass("btn_bottom_radius");
            }
            if (spanList[j] !== targetList.length - 1) {
              if(targetList[spanList[j] + 1].children[0].tagName === "SPAN") continue;
              $(targetList[spanList[j] + 1].children[0]).addClass("btn_top_radius");
            }
          }
        });
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
            newSelectionList = self._removeEmptyValue(newSelectionList);
            self._checkCurrentPortListFromPast(newSelectionList);
            var selectionLength = [];
            var count = 0;
            for (var i = 0; i < newSelectionList.length; i++) {
              if(Number(newSelectionList[i].type) === 1) {
                count++;
              } else {
                selectionLength.push(count);
                count = 0;
              }
            }
            selectionLength.push(count);
            var selectionLengthIndex = 0;
            var coverIndex = 0;
            for (var i = 0; i < newSelectionList.length; i++) {
              /* Set rect height */
              self._resizeParentHeight(i, newSelectionList);
              var cell;
              switch (Number(newSelectionList[i].type)) {
                case 1:
                  cell = self.portCreator(self._getSelfPosition(i, newSelectionList), convertTextLength(newSelectionList[i].value, 22),
                      newSelectionList[i].value.replace(/<[^>]*>/g, ''), self._getCoverOpacity(coverIndex, selectionLength[selectionLengthIndex]));
                  break;
                case 2:
                  cell = self.textRectCreator(self._getSelfPosition(i, newSelectionList), convertTextLength(newSelectionList[i].value, 22),
                      newSelectionList[i].value.replace(/<[^>]*>/g, ''), self._getCoverOpacity(coverIndex, selectionLength[selectionLengthIndex]));
                  coverIndex = 0;
                  selectionLengthIndex++;
                  break;
                default:
                  continue;
              }
              self._checkPastPortListFromCurrent(newSelectionList, i, coverIndex, selectionLength[selectionLengthIndex], cell);
              if(Number(newSelectionList[i].type) === 1) {
                coverIndex++;
              }
            }
          },
          _removeEmptyValue: function(newSelectionList) {
            var emptyList = [];
            for (var i = 0; i < newSelectionList.length; i++) {
              if (newSelectionList[i].value === "") {
                emptyList.unshift(i);
              }
            }
            for (var j = 0; j < emptyList.length; j++) {
              newSelectionList.splice(emptyList[j], 1);
            }
            return newSelectionList;
          },
          _checkPastPortListFromCurrent: function(targetList, number, coverIndex, groupListLength, port) {
            var textList = [];
            var typeList = [];
            for (var j = 0; j < $scope.oldSelectionList.length; j++) {
              textList.push($scope.oldSelectionList[j].value);
              typeList.push($scope.oldSelectionList[j].type);
            }
            var contentNum = textList.indexOf(targetList[number].value);
            if (contentNum === -1) {
              /* 追加するパターン */
              /* 過去にはないが、現在にあるパターン */
              $scope.currentEditCellParent.embed(port);
              initNodeEvent([port]);
              graph.addCell(port);
            } else {
              /* 追加するパターン */
              /* 両方にあるが、タイプが違うパターン */
              if (typeList[contentNum] !== targetList[number].type) {
                $scope.currentEditCellParent.embed(port);
                initNodeEvent([port]);
                graph.addCell(port);
              }
              /* 編集するパターン */
              /* 両方にあり、タイプも同じパターン */
              var childList = this._getCurrentPortList();
              for (var i = 0; i < childList.length; i++) {
                if (childList[i].attr("nodeBasicInfo/tooltip") === targetList[number].value) {
                  this._setSelfPosition(childList[i], this._getSelfPosition(number, targetList));
                  var topOpacity = 1,
                      bottomOpacity = 1;
                  if (coverIndex === 0) {
                    topOpacity = 0;
                  }
                  if (coverIndex === groupListLength - 1) {
                    bottomOpacity = 0;
                  }
                  childList[i].attr(".cover_top/fill-opacity", topOpacity).
                      attr(".cover_bottom/fill-opacity", bottomOpacity);
                }
              }
            }
          },
          _checkCurrentPortListFromPast: function(targetList) {
            var textList = [];
            var typeList = [];
            /* テキストと選択肢で内容が同一の場合は削除すること */
            for (var j = 0; j < targetList.length; j++) {
              textList.push(targetList[j].value);
              typeList.push(targetList[j].type);
            }

            var childList = this._getCurrentPortList();
            for (var i = 0; i < childList.length; i++) {
              var containNum = textList.indexOf(childList[i].attr("nodeBasicInfo/tooltip"));
              if (containNum === -1) {
                /* 過去には有るが、現在に見つからない場合は削除 */
                childList[i].remove();
              } else {
                /* 過去にも現在にも同名のテキストがあるが、タイプが違う場合は削除 */
                switch (Number(typeList[containNum])) {
                  case 1:
                    /* 現在は選択肢　過去は文章 */
                    if (childList[i].attr("nodeBasicInfo/nodeType") === "childTextNode") {
                      childList[i].remove();
                    }
                    break;
                  case 2:
                    /* 現在は文章　過去は選択肢 */
                    if (childList[i].attr("nodeBasicInfo/nodeType") === "childPortNode") {
                      childList[i].remove();
                    }
                    break;
                  default:
                }
              }
            }
          },
          _getCurrentPortList: function() {
            var list = $scope.currentEditCellParent.getEmbeddedCells();
            var targetList = [];
            for (var i = 0; i < list.length; i++) {
              try {
                if (list[i].attr("nodeBasicInfo/nodeType") === "childPortNode"
                    || list[i].attr("nodeBasicInfo/nodeType") === "childTextNode") {
                  targetList.push(list[i]);
                }
              } catch (e) {
                console.log("undefined Port!!")
              }
            }
            return targetList;
          },
          _setSelfPosition: function(elm, position) {
            elm.set("position", position);
          },
          _getSelfPosition: function(index, list) {
            var calcSize = 0;
            if(index > 0) {
              for(var i = 0; i <= index - 1; i++) {
                if(Number(list[i].type) === 2) {
                  calcSize += 30;
                } else if(Number(list[i].type) === 1) {
                  calcSize += 40;
                }
              }
            }
            return {
              x: $scope.currentEditCellParent.get('position').x + 5,
              y: $scope.currentEditCellParent.get('position').y + 115 + calcSize
            }
          },
          _resizeParentHeight: function(index, list) {
            var calcSize = 0;
            if(index > 0) {
              for(var i = 1; i <= index; i++) {
                if(Number(list[i].type) === 2) {
                  calcSize += 30;
                } else if(Number(list[i].type) === 1) {
                  calcSize += 40;
                }
              }
            }
            $scope.currentEditCellParent.get('size').height = 160 + calcSize;
          },
          _getCoverOpacity: function(index, maxLength) {
            return {
              top: index === 0 ? "0" : "1",
              bot: index === maxLength - 1 ? "0" : "1"
            }
          },
          portCreator: function(position, text, originalText, opacity) {
            return new joint.shapes.devs.Model({
              position: {x: position.x, y: position.y},
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
                  fill: '#FFF',
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
          },
          textPortCreator: function(position, text, originalText, opacity) {
            return new joint.shapes.devs.Model({
              position: {x: position.x, y: position.y},
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
                  fill: '#FFF',
                  y: 12
                },
                '.label': {
                  text: text,
                  'ref-width': '70%',
                  'font-size': '14px',
                  fill: '#FFF',
                  y: 12
                },
                'rect.body': {
                  fill: '#c73576',
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
                  fill: '#c73576',
                  width: 240,
                  height: 10,
                  'fill-opacity': opacity.top
                },
                '.cover_bottom': {
                  fill: '#c73576',
                  width: 240,
                  height: 10,
                  transform: "translate(0 26)",
                  'fill-opacity': opacity.bot
                }
              },
              markup: '<rect class="body"/><text class="label"/><rect class="cover_top"/><rect class="cover_bottom"/>'
            });
          },
          rectCreator: function(position, text, originalText, opacity) {
            return new joint.shapes.basic.Rect({
              position: {x: position.x, y: position.y},
              size: {width: 240, height: 36},
              attrs: {
                'rect.body': {
                  fill: "#FFFFFF",
                  stroke: false,
                  width: 240,
                  height: 36,
                  rx: 10,
                  ry: 10
                },
                text: {
                  text: text,
                  'ref-width': '70%',
                  'font-size': "14px",
                  fill: '#000',
                  y: 12
                },
                nodeBasicInfo: {
                  nodeType: "childTextNode",
                  tooltip: originalText
                },
                '.cover_top': {
                  fill: '#FFFFFF',
                  width: 240,
                  height: 10,
                  'fill-opacity': opacity.top,
                  stroke: false
                },
                '.cover_bottom': {
                  fill: '#FFFFFF',
                  width: 240,
                  height: 10,
                  transform: "translate(0 26)",
                  'fill-opacity': opacity.bot,
                  stroke: false
                }
              },
              markup: '<rect class="body"/><text class="label"/><rect class="cover_top"/><rect class="cover_bottom"/>'
            });
          },
          textRectCreator: function(position, text, originalText, opacity) {
            return new joint.shapes.basic.Rect({
              position: {x: position.x, y: position.y},
              size: {width: 240, height: 26},
              attrs: {
                'rect.body': {
                  fill: "#c73576",
                  stroke: false,
                  width: 240,
                  height: 26,
                  rx: 0,
                  ry: 0
                },
                text: {
                  text: text,
                  'ref-width': '70%',
                  'font-size': "14px",
                  fill: '#FFF',
                  y: 10
                },
                nodeBasicInfo: {
                  nodeType: "childTextNode",
                  tooltip: originalText
                },
                '.cover_top': {
                  fill: '#c73576',
                  width: 240,
                  height: 5,
                  'fill-opacity': opacity.top,
                  stroke: false
                },
                '.cover_bottom': {
                  fill: '#c73576',
                  width: 240,
                  height: 5,
                  transform: "translate(0 21)",
                  'fill-opacity': opacity.bot,
                  stroke: false
                }
              },
              markup: '<rect class="body"/><text class="label"/><rect class="cover_top"/><rect class="cover_bottom"/>'
            });
          }
        },
      };

      var textEditor = {
        lineCounter: 1,
        textLineSeparate: function(text){
          if(text == null) return "";
          // タグを全て外す
          text = text.replace(/<[^>]*>/g, '');
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
          if($scope.isNotUniqueName(source.attr("nodeBasicInfo/tooltip"))) return;
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
        $scope.handleButtonCSS();
        popupEvent.resize();
        $scope.popupFix();
      });

      $scope.popupFix = function(){
        var popup = $('#popup-frame');
        popup.offset({
          top: window.innerHeight / 2 - popup.height() / 2,
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

      $scope.$watch("branchType", function(){
        $scope.popupPositionAdjustment();
      });
      $scope.$watch("radioStyle", function(){
        $scope.popupPositionAdjustment();
        console.log($scope.radioStyle);
      });

      $scope.popupPositionAdjustment = function(){
        $timeout(function(){
          $scope.$apply();
        }).then(function(){
          $scope.currentTop = $('#popup-frame').offset().top;
          $timeout(function(){
            popupEvent.resize();
            $scope.popupFix();
          });
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
        $scope.radioStyle = custom.radioStyle ? custom.radioStyle : '1';
        $scope.radioEntireBackgroundColor = custom.radioEntireBackgroundColor ? custom.radioEntireBackgroundColor : $scope.getRawColor($scope.widget.settings.main_color, 0.5);
        $scope.radioEntireActiveColor = custom.radioEntireActiveColor ? custom.radioEntireActiveColor : $scope.widget.settings.main_color;
        $scope.radioTextColor = custom.radioTextColor ? custom.radioTextColor : $scope.widget.settings.re_text_color;
        $scope.radioActiveTextColor = custom.radioActiveTextColor ? custom.radioActiveTextColor : $scope.widget.settings.re_text_color;
        $scope.radioSelectionDistance = custom.radioSelectionDistance ? custom.radioSelectionDistance : 4;
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
        var isColorType = true;
        switch(colorType) {
          case "lh":
            targetValue = $scope.radioSelectionDistance = 4;
            isColorType = false;
            break;
          case "bg":
            targetValue = $scope.radioBackgroundColor = "#FFFFFF";
            break;
          case "button":
            targetValue = $scope.radioActiveColor = $scope.widget.settings.main_color;
            break;
          case "border":
            targetValue = $scope.radioBorderColor = $scope.widget.settings.main_color;
            break;
          case "b_bg":
            targetValue = $scope.radioEntireBackgroundColor = $scope.getRawColor($scope.widget.settings.main_color, 0.5);
            break;
          case "b_select_bg":
            targetValue = $scope.radioEntireActiveColor = $scope.widget.settings.main_color;
            break;
          case "b_text":
            targetValue = $scope.radioTextColor = $scope.widget.settings.re_text_color;
            break;
          case "b_select_text":
            targetValue = $scope.radioActiveTextColor = $scope.widget.settings.re_text_color;
            break;
          default:
            /* Do nothing */
        }
        if(isColorType) {
          target.css("background-color", targetValue);
        }
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


      $scope.changeTextTrigger = function(e, forceProcess, text, index){
        $scope.replaceTag(text, index);
        $scope.autoResize(e, forceProcess);
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

      $scope.replaceTag = function(text, index){
        var target = $(".preview_text_span_" + index);
        target.text("");
        var str = replaceVariable(text, false, $scope.widget.settings['widget_size_type']);
        target.append(str);
      };

      /** ==========================
       * Simulator Methods
       * =========================== */
      // シミュレーターの起動
      this.openSimulator = function() {
        $scope.diagramSimulatorService.actionListOrigin = graph.toJSON();
        $scope.$broadcast('openSimulator', $scope.diagramSimulatorService.actionListOrigin);
        // シミュレータ起動時、強制的に自由入力エリアを有効の状態で表示する
        $scope.$broadcast('switchSimulatorChatTextArea', true);
      };
      /* =========================== */

      // シナリオ設定の削除
      this.removeAct = function(lastPage) {
        // アラート表示を行わないように、フラグを戻す
        $scope.changeFlg = false;

        modalOpen.call(window, '削除します、よろしいですか？', 'p-confirm', 'チャットツリー設定', 'moment');
        popupEvent.closePopup = function() {
          $.ajax({
            type: 'post',
            data: {
              id: document.getElementById('TChatbotDiagramId').value
            },
            cache: false,
            url: "<?= $this->Html->url('/TChatbotDiagrams/remoteDeleteBy') ?>",
            success: function() {
              // 一覧ページへ遷移する
              var url = "<?= $this->Html->url('/TChatbotDiagrams/index') ?>";
              location.href = url + '/page:' + lastPage;
            }
          });
        };
      };

    }]);

  sincloApp.controller('DialogController', [
    '$scope',
    '$timeout',
    'SimulatorService',
    'ScenarioSimulatorService',
    'DiagramSimulatorService',
    function($scope, $timeout, SimulatorService, ScenarioSimulatorService, DiagramSimulatorService) {
      //thisを変数にいれておく
      var self = this;
      $scope.setActionList = [];
      $scope.widget = SimulatorService;
      $scope.diagramSimulatorService = DiagramSimulatorService;
      $scope.scenarioSimulatorService = ScenarioSimulatorService;
      $scope.scenarioSimulatorService.widget = SimulatorService;

      /**
       * シミュレーションの起動(ダイアログ表示)
       * @param Object activity 実行可能なシナリオ
       */
      $scope.$on('openSimulator', function(event, activity) {
        var diagrams = activity;
        var obj = {};
        for(var i=0; i < diagrams.cells.length; i++) {
          var cell = diagrams.cells[i];
          obj[cell['id']] = {
            id: cell['id'],
            parent: cell['parent'],
            type: cell['type'],
            embeds: cell['embeds'],
            attrs: cell['attrs']
          }
        }
        $scope.diagramSimulatorService.setActionList = obj;
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
          $scope.diagramSimulatorService.actionInit();
          $scope.diagramSimulatorService.doAction();
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

      // シミュレーションの終了(ダイアログ非表示)
      $scope.closeSimulator = function() {
        $scope.diagramSimulatorService.actionStop();
        $('#tchatbotdiagrams_simulator_wrapper').hide();
      };

      // アクションの開始
      $scope.actionInit = function() {
        $scope.diagramSimulatorService.actionInit();
        $scope.diagramSimulatorService.doAction();
      };

      // アクションのクリア(アクションを最初から実行し直す)
      $scope.actionClear = function() {
        $scope.diagramSimulatorService.actionStop();
        $scope.diagramSimulatorService.actionInit();
        $scope.diagramSimulatorService.doAction();
      };

      $scope.$on('receiveScenario', function(event, activity){
        $scope.scenarioSimulatorService.setActionList = activity.scenarios;
        $scope.scenarioSimulatorService.actionInit(true);
        $scope.scenarioSimulatorService.doAction();
      });
    }]);


  sincloApp.directive('branchModal', function(){
    return {
      restrict: 'E',
      replace: true,
      template: '<div id=\'branch_modal\'>' +
          '<div id=\'branch_modal_editor\'>' +
          '<h3>設定</h3>' +
          '<div class="scroll-wrapper">' +
          '<div id=\'branch_modal_head\'>' +
          '<label for=\'node_name\'>ノード名</label>' +
          '<input ng-model="branchTitle" id=\'my_node_name\' name=\'node_name\' type=\'text\' placeholder=\'ノード名を入力して下さい\'/>' +
          '</div>' +
          '<div class="node_name_valid_margin">' +
          '<span class="diagram_valid" ng-show="nodeNameIsEmpty">ノード名を入力して下さい</span>' +
          '<span class="diagram_valid" ng-show="nodeNameIsNotUnique">ノード名「{{branchTitle}}」は既に使用されています</span>' +
          '</div>' +
          '<div id=\'branch_modal_body\'>' +
          '<div class=\'branch_modal_setting_header\'>' +
          '<div class=\'flex_row_box\'>' +
          '<p>発言内容</p>' +
          '<resize-textarea ng-keyup="changeTextTrigger($event, true, branchText, \'branch\')" ng-keydown="changeTextTrigger($event, true, branchText, \'branch\')" ng-model="branchText"></resize-textarea>' +
          '</div>' +
          '<div class="mt20">' +
          '<div class=\'flex_row_box\'>' +
          '<label for=\'branch_button\'>タイプ</label>' +
          '<select name=\'branch_button\' id=\'branchBtnType\' ng-model="branchType" ng-change="handleButtonCSS()">' +
          '<option value="" selected>タイプを選択してください' +
          '<option value="1">ラジオボタン' +
          '<option value="2">ボタン' +
          '</select>' +
          '<div id="bulkRegister" class="btn-shadow disOffgreenBtn">選択肢を一括登録</div>'+
          '</div>' +
          '<radio-type-customize ng-show="branchType == 1"></radio-type-customize>' +
          '<div class="btn_valid_margin">' +
          '<span class="diagram_valid" ng-show="btnTypeIsEmpty">タイプを選択してください</span>' +
          '</div>' +
          '</div>' +
          '</div>' +
          '<div class=\'branch_modal_setting_content\'>' +
          '<div class=\'setting_row\' ng-repeat="selection in branchSelectionList track by $index">' +
          '<select name="contentType" ng-model="branchSelectionList[$index].type" ng-change="handleButtonCSS()">' +
          '<option value="1">選択肢' +
          '<option value="2">発言内容' +
          '</select>' +
          '<input type="text" ng-model="branchSelectionList[$index].value" ng-change="handleButtonCSS()" ng-if="branchSelectionList[$index].type == 1" />' +
          '<resize-textarea ng-if="branchSelectionList[$index].type == 2" ng-keyup="autoResize($event, true)" ng-keydown="autoResize($event, true)" ng-model="branchSelectionList[$index].value"></resize-textarea>' +
          '<img src=\'/img/add.png?1530001126\' width=\'20\' height=\'20\' class=\'btn-shadow disOffgreenBtn\' ng-hide="addBtnHide" ng-click="btnClick(\'add\', branchSelectionList, $index, {type: \'1\', value: \'\'})">' +
          '<img src=\'/img/dustbox.png?1530001127\' width=\'20\' height=\'20\' class=\'btn-shadow redBtn\' ng-hide="deleteBtnHide" ng-click="btnClick(\'delete\', branchSelectionList, $index)">' +
          '</div>' +
          '</div>' +
          '<radio-customize ng-show="branchType == 1"></radio-customize>' +
          '<button-customize ng-show="branchType == 2"></button-customize>' +
          '</div>' +
          '</div>' +
          '</div>' +
          '<div id=\'branch_modal_preview\'>' +
          '<h3>プレビュー</h3>' +
          '<div class="diagram_preview_area">' +
          '<preview-branch>' +
          '</preview-branch>' +
          '</div>' +
          '</div>' +
          '</div>'
    }
  }).directive('textModal', function(){
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
          '<span class="diagram_valid" ng-show="nodeNameIsNotUnique">ノード名「{{speakTextTitle}}」は既に使用されています</span>' +
          '<div id=\'text_modal_body\'>' +
          '<p>発言内容</p>' +
          '<div id="text_modal_contents" >' +
          '<div class=\'text_modal_setting\' ng-repeat="speakText in speakTextList track by $index" finisher>' +
          '<resize-textarea ng-keyup="changeTextTrigger($event, true, speakText, $index)" ng-keydown="changeTextTrigger($event, true, speakText, $index)" ng-model="speakTextList[$index]"></resize-textarea>' +
          '<img src=\'/img/add.png?1530001126\' width=\'20\' height=\'20\' class=\'btn-shadow disOffgreenBtn\' ng-hide="addBtnHide" ng-click="btnClick(\'add\', speakTextList, $index, \'\')">' +
          '<img src=\'/img/dustbox.png?1530001127\' width=\'20\' height=\'20\' class=\'btn-shadow redBtn\' ng-hide="deleteBtnHide" ng-click="btnClick(\'delete\', speakTextList, $index)">' +
          '</div>' +
          '</div>' +
          '</div>' +
          '</div>' +
          '<div id=\'text_modal_preview\'>' +
          '<h3>プレビュー</h3>' +
          '<div class="diagram_preview_area">' +
          '<preview-text ng-repeat="text in speakTextList track by $index">' +
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
          '<div class="callbackToDiagramWrap">' +
          '<label for="callbackToDiagram"><input type="checkbox" name=\'callbackToDiagram\' id=\'callbackToDiagram\'ng-model="callbackToDiagram">終了後、このチャットツリーに戻る</label>' +
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
  }).directive('radioTypeCustomize', function(){
    return {
      restrict: 'E',
      replace: true,
      require: '^ngModel',
      template: '<div class="customize_form">' +
          '<div class="radio_type">' +
          '<p>表示形式</p>' +
          '<div style="margin-left: 14px;">' +
          '<label class="pointer"><input type="radio" value="1" ng-model="radioStyle">ボタン型</label>' +
          '<label class="pointer"><input type="radio" value="2" ng-model="radioStyle">ラベル型</label>' +
          '</div>' +
          '</div>' +
          '<div ng-show="radioStyle == 1" class="customize_area radio_customize">' +
          '<span class="customize_row">' +
          '<label>ボタン背景色</label>' +
          '<input class="jscolor {hash:true}" type="text" ng-model="radioEntireBackgroundColor" maxlength="7">' +
          '<span class="greenBtn btn-shadow revert-button" ng-click=\'revertStandard("radio","b_bg",$event)\'>標準に戻す</span>' +
          '</span>' +
          '<span class="customize_row">' +
          '<label>選択時のボタン背景色</label>' +
          '<input class="jscolor {hash:true}" type="text" ng-model="radioEntireActiveColor" maxlength="7">' +
          '<span class="greenBtn btn-shadow revert-button" ng-click=\'revertStandard("radio","b_select_bg",$event)\'>標準に戻す</span>' +
          '</span>' +
          '<span class="customize_row">' +
          '<label>文字色</label>' +
          '<input class="jscolor {hash:true}" type="text" ng-model="radioTextColor" maxlength="7">' +
          '<span class="greenBtn btn-shadow revert-button" ng-click=\'revertStandard("radio","b_text",$event)\'>標準に戻す</span>' +
          '</span>' +
          '<span class="customize_row">' +
          '<label>選択時の文字色</label>' +
          '<input class="jscolor {hash:true}" type="text" ng-model="radioActiveTextColor" maxlength="7">' +
          '<span class="greenBtn btn-shadow revert-button" ng-click=\'revertStandard("radio","b_select_text",$event)\' >標準に戻す</span>' +
          '</span>' +
          '</div>' +
          '</div>'

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
          '<label>選択肢の行間</label>' +
          '<input class="line_setting" type="number" ng-model="radioSelectionDistance" max="100" min ="0">' +
          '<p>px</p>' +
          '<span class="greenBtn btn-shadow revert-button" ng-click=\'revertStandard("radio","lh",$event)\'>標準に戻す</span>' +
          '</span>' +
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
          '<label class="pointer" style="margin-left:158px; width: 100px;">' +
          '<input type="checkbox" style="margin-top: 5px; margin-bottom: 10px;" ng-model="radioNoneBorder">枠線なしにする' +
          '</label>' +
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
          '<div style="margin-left: 32px;">' +
          '<label class="pointer"><input type="radio" value="1" ng-model="buttonUITextAlign">左寄せ</label>' +
          '<label class="pointer"><input type="radio" value="2" ng-model="buttonUITextAlign">中央寄せ</label>' +
          '<label class="pointer"><input type="radio" value="3" ng-model="buttonUITextAlign">右寄せ</label>' +
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
          '<label class="pointer" style="margin-left:138px; width: 100px;">' +
          '<input type="checkbox" style="margin-top: 5px; margin-bottom: 10px;" ng-model="outButtonUINoneBorder">枠線なしにする' +
          '</label>' +
          '</div>' +
          '</div>'
    }
  }).directive('resizeTextarea', function() {
    return {
      restrict: 'E',
      replace: true,
      template: '<textarea class="resize for_modal" style="font-size: 13px; border-width: 1px; padding: 5px; line-height: 1.5;"></textarea>',
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
