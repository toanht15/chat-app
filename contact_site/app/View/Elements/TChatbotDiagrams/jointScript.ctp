<?php
/**
 * Created by PhpStorm.
 * User: ryo.hosokawa
 * Date: 2019/02/19
 * Time: 13:45
 */
?>
<script>
var nodeFactory = new NodeFactory();
var wasMoved = false;
var nodeTypeArray = [
    "branch",
    "text",
    "scenario",
    "jump",
    "link"
];

var currentEditCell = null;

  $(function(){
  //アイコンクリック時のイベント付与
  $("#node_list > i ").each(function(index, target) {
    $(target).draggable({
      helper: 'clone',
    });

    $(target).on('click', function(){
      console.log(paper.localToClientPoint());
    })
  });

  var canvas = document.getElementById("canvas");
  $(canvas).droppable({
    drop: function( e, ui ){
      if( ui.draggable.attr("id") === "popup-frame") return;
      var cursorPos = paper.clientToLocalPoint(ui.offset.left, ui.offset.top);
      nodeMaster(ui.draggable.attr("id"), cursorPos.x, cursorPos.y);
    }
  });

  graph = new joint.dia.Graph;

  var paper = new joint.dia.Paper({
    el: canvas,
    width: canvas.width,
    height: canvas.height,
    gridSize: 5,
    model: graph,
    linkPinning: false,
    defaultLink: new joint.dia.Link({
      attrs: {
        '.marker-target': {d: 'M 10 0 L 0 5 L 10 10 z'},
        '.marker-arrowhead[end="source"]': {d: 'M 0 0 z'},
        '.marker-arrowhead[end="target"]': {d: 'M 0 0 z'},
      },
    }),
    validateConnection: function(cellViewS, magnetS, cellViewT, magnetT, end, linkView) {
      wasMoved = true;
      // in portからは矢印を表示させない
      if (magnetS && magnetS.getAttribute('port-group') === 'in') return false;
      // 同一Elementのout → in portは許容しない
      if (cellViewS === cellViewT) return false;

      // 子供から親への接続は許容しない
      var parents = cellViewS.model.getAncestors();
      if(parents.length > 0 && parents[0].id === cellViewT.model.id){
        return false;
      }
      //既に他ポートに接続しているout portは線を出さない
      /*if(cellViewS.model.attributes.state.next_node){
        var sourceId = cellViewS.model.id;
        var links = graph.getLinks();
        var count = 0;
        links.forEach(function(l){
          if(l.get('source').id == sourceId)
            count++;
        });
        if(count > 1){
          linkView.model.remove();
          return false;
        }
      }*/
      console.log(cellViewS);

      // outPortには入力させない
      return magnetT && magnetT.getAttribute('port-group') === 'in';
    },
    validateMagnet: function(cellView, magnet) {
      return magnet.getAttribute('magnet') !== 'passive';
    }
  });

  var dragReferencePosition = null;


  paper.on('cell:pointerup',
      function (cellView, evt, x, y) {
      //init current edit cell to null;
        currentEditCell = null;
        if( !wasMoved && isNeedModalOpen(cellView) ) {
          currentEditCell = setViewElement(cellView);
          var modalData = processModalCreate(cellView);
          modalOpen.call(window, modalData.content, modalData.id , modalData.name, 'moment');
          initPopupCloseEvent();
          btnViewHandler.switcher();
        }
        wasMoved = false;
      });

  paper.on('blank:pointerdown',function(e, x, y) {
    dragReferencePosition = { x:x * paper.scale().sx, y:y * paper.scale().sy };
  });

  paper.on('blank:pointerup',function(cellView, x, y) {
    dragReferencePosition = null;
  });

  $('input[type=range]').on('input', function(e){
    paper.scale(e.target.value/5);
  });

  $(canvas).mousemove(function(e) {
    if( dragReferencePosition ){
      paper.translate(
          e.offsetX - dragReferencePosition.x,
          e.offsetY - dragReferencePosition.y
      );
    }
  });



  var nodeMaster = function(type, posX, posY){
    var node = nodeFactory.createNode(type, posX, posY);
    graph.addCell(node);
    initNodeEvent(node);
  };


});

  var addMoveFlg = function(){
    wasMoved = true;
  };

  function initNodeEvent(node){
    for(var i = 0; i < node.length; i++) {

      if(node[i].attr('nodeType') === "childViewNode"
      || node[i].attr('nodeType') === "childPortNode"){
        node[i].on('change:position', childMove);
      }

      if(nodeTypeArray.indexOf(node[i].attr('nodeType')) > -1
      || node[i].attr('nodeType') === "operator"
      || node[i].attr('nodeType') === "cv") {
        node[i].on('change:position', addMoveFlg);
      }
    }
  }

  function childMove(elm, pos, self){
    if (self.translateBy === elm.id) {
      var parent = elm.getAncestors()[0];
      if( parent ) {
        parent.unembed(elm);
        parent.translate(self.tx, self.ty);
        parent.embed(elm);
      }
    }
  }

  function isNeedModalOpen (cell){
    var type = cell.model.attr().nodeType;
    if( type != null ){
      return nodeTypeArray.indexOf(type) > -1
          || type === "childViewNode"
          || type === "childPortNode"
          || type === "operator"
          || type === "cv";
    }
    return false;
  }


  function processModalCreate(elm) {
    var type = elm.model.attr('nodeType');
    var htmlCreator,
        modalName,
        modalClass;
    if (elm.model.getAncestors()[0] != null) {
      type = elm.model.getAncestors()[0].attr('nodeType');
    }
    switch (type) {
      case "branch":
        htmlCreator = createBranchHtml;
        modalName = "分岐";
        modalClass = "p_diagrams_branch";
        break;
      case "text":
        htmlCreator = createTextHtml;
        modalName = "テキスト発言";
        modalClass = "p_diagrams_text";
        break;
      case "scenario":
        htmlCreator = createScenarioHtml;
        modalName = "シナリオ呼出";
        modalClass = "p_const_diagrams";
        break;
      case "jump":
        htmlCreator = createJumpHtml;
        modalName = "ジャンプ";
        modalClass = "p_const_diagrams";
        break;
      case "link":
        htmlCreator = createLinkHtml;
        modalName = "リンク";
        modalClass = "p_const_diagrams";
        break;
      case "operator":
        htmlCreator = createOperatorHtml;
        modalName = "オペレーター呼出";
        modalClass = "p_close_diagrams";
        break;
      case "cv":
        htmlCreator = createCvHtml;
        modalName = "CVポイント";
        modalClass = "p_close_diagrams";
        break;
      default:
        return null;
    }
    var nodeData = currentEditCell.attr('actionParam');
    if(currentEditCell.getAncestors()[0]){
      nodeData = currentEditCell.getAncestors()[0].attr('actionParam');
    }

    return {
      name: modalName,
      content: htmlCreator(nodeData),
      id: modalClass
    }
  }

  function initPopupCloseEvent (){
    popupEvent.closePopup = function(type) {
      switch(type){
        case 1:
          saveEditNode();
          popupEvent.closeNoPopup();
          //保存処理
          break;
        case 2:
          //削除処理
          if(confirm("現在のノードを削除します。よろしいですか？")){
            deleteEditNode();
            popupEvent.closeNoPopup();
          } else {
            //キャンセルの場合はモーダルを閉じない
          }
          break;
        default:
          //一応保存処理にしておく
          break;
      }
    }
  }

  function deleteEditNode() {
    if( currentEditCell == null ){
      //編集対象が無い場合は処理を実行しない
      return;
    }
    if(currentEditCell.getAncestors()[0]){
      currentEditCell.getAncestors()[0].remove();
    } else {
      currentEditCell.remove();
    }
    currentEditCell.remove();
    currentEditCell = null;
  }
  
  function saveEditNode() {
    if( currentEditCell == null ){
      //編集対象が無い場合は処理を実行しない
      return;
    }
    if(currentEditCell.getAncestors()[0]){
      //シナリオ呼出・ジャンプ・リンクの場合
      bindSingleView(currentEditCell.getAncestors()[0].attr('nodeType'));
    } else {

    }

  }

  function setViewElement(target) {
    //親エレメント直下の子エレメントを設定
    //
    var viewNode = target.model;
    var childList = viewNode.getEmbeddedCells();
    if(viewNode.attr('nodeType') === 'operator'
    || viewNode.attr('nodeType') === 'cv') {
      // オペレーター、 CVの場合は
      return viewNode;
    } else if(childList.length > 0) {
      //一番上のViewを基準にする
      //親の場合は、直下の子供
      viewNode = childList[0];
    } else {
      //一番上のViewを基準にする
      //子供の場合は、親を取得して子供に再設定する。
      viewNode = viewNode.getAncestors()[0].getEmbeddedCells()[0]
    }
    return viewNode;
  }

  function bindSingleView(type){
    var viewText = "";
    var nodeParam = {};
    var target,
        nodeName,
        speakTextContents,
        btnType,
        selectionList;
    switch(type){
      case 'text':
        nodeName = $('#my_node_name').val();
        speakTextContents = nodeEditHandler.typeText.convertContents($('.text_modal_setting'));
        currentEditCell.getAncestors()[0].attr('.label/text', convertTextForTitle(convertTextLength(nodeName, 4),"テキスト発言"));
        currentEditCell.attr('text/text', convertTextLength(speakTextContents[0], 8));
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
        currentEditCell.getAncestors()[0].attr('.label/text', convertTextForTitle(convertTextLength(nodeName, 6),"分岐"));
        currentEditCell.getAncestors()[0].attr('actionParam/selection', null);
        nodeParam = {
          nodeName: nodeName,
          text: speakTextContents,
          btnType: btnType,
          selection: selectionList
        };
        break;
      case 'scenario':
        target = $('#callTargetScenario option:selected');
        viewText = target.text();
        nodeParam = {
          scenarioId: target.val()
        };
        currentEditCell.attr('text/text', convertTextLength(viewText, 8));
        break;
      case 'jump':
        target = $('#jumpTargetNode option:selected');
        viewText = target.text();
        nodeParam = {
          targetId: target.val()
        };
        currentEditCell.attr('text/text', convertTextLength(viewText, 8));
        break;
      case 'link':
        viewText = $('#linkTarget').val();
        radio = $('input[name=link_type]:checked').val();
        nodeParam = {
          link: viewText,
          linkType: radio
        };
        currentEditCell.attr('text/text', convertTextLength(viewText, 16));
        break;
      default:
        break;
    }
    currentEditCell.getAncestors()[0].attr('actionParam' ,nodeParam);
  }

  function convertTextLength(text, regNum){
    if(text) {
      return text.length > regNum ? (text).slice(0, regNum) + "..." : text;
    } else {
      return text;
    }
  }

  function convertTextForTitle(text, basicTitle){
    if(text){
      return basicTitle + ":" + text;
    } else {
      return basicTitle;
    }
  }

  function createBranchHtml (nodeData){
    var html = $("<div id='branch_modal'>" +
        "<div id='branch_modal_editor'>" +
        "<div id='branch_modal_head'>" +
        "<label for='node_name'>ノード名</label>" +
        "<input id='my_node_name' name='node_name' type='text' placeholder='ノード名を入力して下さい'/>" +
        "</div>" +
        "<div id='branch_modal_body'>" +
        "<div class='branch_modal_setting_header'>" +
        "<div class='flex_row_box'>" +
        "<p>発言内容</p>" +
        "<textarea></textarea>" +
        "</div>" +
        "<div class='flex_row_box'>" +
        "<label for='branch_button'>表示形式</label>" +
        "<select name='branch_button' id='branchBtnType'>" +
        "<option>表示形式を選択して下さい</option>" +
        "<option value='1'>ラジオボタン</option>" +
        "<option value='2'>ボタン</option>" +
        "<select>" +
        "</div>" +
        "</div>" +
        "<div class='branch_modal_setting_content'>" +
        "<div class='setting_row'>" +
        "<p>選択肢</p>" +
        "<textarea></textarea>" +
        "<img src='/img/add.png?1530001126' width='20' height='20' class='btn-shadow disOffgreenBtn' onclick='addTextBox(this)'>" +
        "<img src='/img/dustbox.png?1530001127' width='20' height='20' class='btn-shadow redBtn' onclick='deleteTextBox(this)'>" +
        "</div>" +
        "</div>" +
        "</div>" +
        "</div>" +
        "<div id='branch_modal_preview'>" +
        "</div>" +
        "</div>");
    html.find('input[type=text]').val(nodeData.nodeName);
    html.find('.flex_row_box > textarea').val(nodeData.text);
    html.find('select').val(nodeData.btnType);
    html = nodeEditHandler.typeBranch.createContents(html, nodeData);
    return html;
  }

  function createTextHtml (nodeData){
    var html = $("<div id='text_modal'>" +
        "<div id='text_modal_editor'>" +
        "<div id='text_modal_head'>" +
        "<label for='node_name'>ノード名</label>" +
        "<input id='my_node_name' name='node_name' type='text' placeholder='ノード名を入力して下さい'/>" +
        "</div>" +
        "<p>発言内容</p>" +
        "<div id='text_modal_body'>" +
        "<div class='text_modal_setting'>" +
        "<textarea></textarea>" +
        "<img src='/img/add.png?1530001126' width='20' height='20' class='btn-shadow disOffgreenBtn' onclick='addTextBox(this)'>" +
        "<img src='/img/dustbox.png?1530001127' width='20' height='20' class='btn-shadow redBtn' onclick='deleteTextBox(this)'>" +
        "</div>" +
        "</div>" +
        "</div>" +
        "<div id='text_modal_preview'>" +
        "</div>" +
        "</div>");
    html.find('input[type=text]').val(nodeData.nodeName);
    html = nodeEditHandler.typeText.createContents(html, nodeData);
    return html;
  }

  function createLinkHtml (nodeData){
    var html = $("<div id='link_modal'>" +
        "<label for='link'>遷移先URL</label>" +
        "<input id='linkTarget' name='link' type='text' placeholder='URLを入力して下さい'/>" +
        "</div>" +
        "<div id='link_type_area'>" +
        "<label><input type='radio' name='link_type' value='same'>ページ遷移する</label>" +
        "<label><input type='radio' name='link_type' value='another'>別タブで開く</label>" +
        "</div>");
    html.find('input[type=text]').val(nodeData.link);
    html.find('input[type=radio]').val([nodeData.linkType]);
    return html;
  }

  function createScenarioHtml (nodeData){
    var html = $("<div id='scenario_modal'>" +
        "<label for='scenario'>シナリオ名</label>" +
        "<select name='scenario' id='callTargetScenario'>" +
        "<option>シナリオを選択してください</option>" +
        "<option>資料請求</option>" +
        "<option>メインメニュー</option>" +
        "<select>" +
        "</div>");
    html.find('select').val(nodeData.scenarioId);
    return html;
  }

  function createJumpHtml (nodeData){
    var html = $("<div id='jump_modal'>" +
        "<label for='jump'>ノード名</label>" +
        "<select name='jump' id='jumpTargetNode'>" +
        "<option>ノード名を選択してください</option>" +
        "<option>資料請求</option>" +
        "<option>メインメニュー</option>" +
        "<select>" +
        "</div>");
    html.find('select').val(nodeData.targetId);
    return html;
  }

  function createOperatorHtml (nodeData){
    return $("<p>このノードに到達した場合、オペレーターを呼び出します。</p>");
  }


  function createCvHtml (nodeData){
    return $("<p>このノードに到達した場合、CVに登録します。</p>");
  }

  function addTextBox (e){
    var cloneElm = $(e.parentNode).clone();
    cloneElm.children('textarea').val('');
    $(e.parentNode).after(cloneElm);
    btnViewHandler.switcher();
    popupEvent.resize();
  }

  function deleteTextBox (e){
    e.parentNode.parentNode.removeChild(e.parentNode);
    btnViewHandler.switcher();
    popupEvent.resize();
  }

  var btnViewHandler = {
    switcher: function(){
      var self = btnViewHandler;
      var textElm = document.getElementsByClassName('text_modal_setting');
      var branchElm = document.getElementsByClassName('setting_row');
      if(textElm.length > 0){
        self._controller(textElm, 1, 5);
      }
      if (branchElm.length > 0) {
        self._controller(branchElm, 1, 10)
      }

    },
    _controller: function(elm, min, max){
      if (elm.length >= max) {
        for (var i = 0; i < elm.length; i++) {
          $(elm[i]).children("img.disOffgreenBtn").hide();
        }
      } else
      if (elm.length <= min) {
        for (var j = 0; j < elm.length; j++) {
          $(elm[j]).children("img.redBtn").hide();
        }
      } else
      {
        for (var k = 0; k < elm.length; k++) {
          $(elm[k]).children("img.disOffgreenBtn").show();
          $(elm[k]).children("img.redBtn").show();
        }
      }
    }
  };

  var nodeEditHandler = {
    typeText: {
      convertContents: function(originContents) {
        return nodeEditHandler.textAreaToArray(originContents);
      },
      createContents: function(html, nodeData) {
        if(nodeData.text.length > 0) {
          html.find('.text_modal_setting > textarea').val(nodeData.text[0]);
          for(var i = 1; i < nodeData.text.length; i++) {
            tmpClone = html.find('.text_modal_setting:last-child').clone();
            tmpClone.find('textarea').val(nodeData.text[i]);
            html.find('#text_modal_body').append(tmpClone);
          }
        } else {

        }
        return html;
      }
    },
    typeBranch: {
      convertContents: function(originContents) {
        return nodeEditHandler.textAreaToArray(originContents);
      },
      createContents: function(html, nodeData) {
        if(nodeData.selection.length > 0) {
          html.find('.setting_row > textarea').val(nodeData.selection[0]);
          for(var i = 1; i < nodeData.selection.length; i++) {
            tmpClone = html.find('.setting_row:last-child').clone();
            tmpClone.find('textarea').val(nodeData.selection[i]);
            html.find('.branch_modal_setting_content').append(tmpClone);
          }
        } else {

        }
        return html
      },
      handleBranchPorts: function(additionalPortList) {
        var self = nodeEditHandler.typeBranch;
        var offsetMasterNodeHeight = 70;
        var addMasterNodeHeight = 0;
        var masterBranch = currentEditCell.getAncestors()[0];
        self.removeAllPortView(masterBranch);
        var masterViewData = masterBranch.attributes;
        for(var i = 0; i < additionalPortList.length; i++) {
          var port = self.portCreator(
              masterViewData.position.x,
              masterViewData.position.y,
              additionalPortList[i],
              70 + i * 35
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
      resizeParent: function(){

      },
      removeAllPortView: function(branch){
        var childList = branch.getEmbeddedCells();
        for(var i = 0; i < childList.length; i++) {
          if( childList[i].attr('nodeType') === 'childPortNode') {
            childList[i].remove();
          }
        }
      },
      portCreator: function(posX, posY, text, additionalY){
        return new joint.shapes.devs.Model({
          position: { x: posX + 5, y: posY + additionalY },
          size: { width: 140, height: 30 },
          outPorts: ['out'],
          ports: {
            groups: {
              'out': {
                attrs: {
                  '.port-body': {
                    fill: '#E6B3B3',
                    height: 30,
                    width: 30,
                    stroke: false
                  },
                  '.port-label': {
                    'font-size': 0
                  }
                },
                position: {
                  name: 'absolute',
                  args: {
                    x: 145,
                    y: 0,
                  }
                },
                markup: '<rect class="port-body"/>'
              }
            }
          },
          attrs: {
            '.label': {
              text: text,
              'ref-width': '70%',
              'ref-x': .5,
              'ref-y': .5,
              'font-size': "12px",
              fill: '#000'
            },
            rect: {
              fill: '#FFFFFF',
              stroke: false
            },
            button: {
              cursor: 'pointer',
              ref: 'buttonLabel',
              refWidth: '150%',
              refHeight:  '150%',
              refX: '-25%',
              refY: '-25%'
            },
            nodeType: 'childPortNode',
          }
        });
      }
    },
    textAreaToArray: function(contents){
      var contentArray = [];
      for(var i = 0; i < contents.length; i++) {
        if($(contents[i]).children('textarea').val()) {
          contentArray.push($(contents[i]).children('textarea').val());
        }
      }
      return contentArray;
    }
  }



</script>