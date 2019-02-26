<?php
/**
 * Created by PhpStorm.
 * User: ryo.hosokawa
 * Date: 2019/02/19
 * Time: 16:04
 */
?>

<script>
var inColor,
    outColor,
    bgColor,
    labelText;

var nodeSize = {};

var actionParam = {};

var portSetting = {
  inPortSize: 30,
  outPortSize: 30
};

var middleNode = {
  width: 150,
  height: 70,
  inPortX: -30,
  inPortY: 20,
  outPortX: 150,
  outPortY: 20,
  labelX: .3,
  labelY: .1,
  nodeType: "text"
};
var smallNode = {
  width: 150,
  height: 50,
  inPortX: -30,
  inPortY: 10,
  outPortX: 150,
  outPortY: 10,
  labelX: .5,
  labelY: .4,
  nodeType: "text"
};



function NodeFactory() {
  this.createNode = function(type, posX, posY) {
    var masterNode = null;
    var childNode = null;
    var returnNode = null;
    nodeSize = middleNode;
    nodeType = type;
    if(type === "branch") {
      inColor = "#993333";
      outColor = "#E6B3B3";
      bgColor = "#CC6666";
      labelText = "分岐";
      childNode = contentViewNode;
      masterNode = constantNodeOnlyInPort;
      actionParam = {
        nodeName: "",
        text: "",
        btnType: "",
        selection: []
      };
    } else if (type === "text") {
      inColor = "#996633";
      outColor = "#E6CCB3";
      bgColor = "#CC9966";
      labelText = "テキスト発言";
      childNode = contentViewNode;
      masterNode = constantNode;
      actionParam = {
        nodeName: "",
        text:[],
      };
    } else if (type === "scenario") {
      inColor = "#999933";
      outColor = "#E6E6B3";
      bgColor = "#CCCC66";
      labelText = "シナリオ";
      childNode = contentViewNode;
      masterNode = constantNodeOnlyInPort;
      actionParam = {
        scenarioId: ""
      };
    } else if (type === "jump") {
      inColor = "#669933";
      outColor = "#CCE6B3";
      bgColor = "#99CC66";
      labelText = "ジャンプ";
      childNode = contentViewNode;
      masterNode = constantNodeOnlyInPort;
      actionParam = {
        targetId: ""
      };
    } else if (type === "link") {
      inColor = "#339933";
      outColor = "#B3E6B3";
      bgColor = "#66CC66";
      labelText = "リンク";
      childNode = contentViewNode;
      masterNode = constantNode;
      actionParam = {
        link: "",
        linkType: "same"
      }
    } else if (type === "operator") {
      inColor = "#339966";
      outColor = "#B3E6CC";
      bgColor = "#66CC99";
      labelText = "オペレータ呼出";
      nodeSize = smallNode;
      masterNode = constantNodeOnlyInPort;
      actionParam = {};
    } else if (type === "cv") {
      console.log("CVポイント");
      inColor = "#339999";
      outColor = "#B3E6E6";
      bgColor = "#66CCCC";
      labelText = "CVポイント";
      nodeSize = smallNode;
      masterNode = constantNode;
      actionParam = {};
    }

    if ( masterNode === null ){
      return false;
    } else {
      masterNode = new masterNode(posX, posY);
      returnNode = [masterNode];
    }

    if ( childNode !== null ){
      childNode = new childNode(posX, posY);
      masterNode.embed(childNode);
      returnNode = [masterNode, childNode];
    }

    return returnNode;
  };
}

function contentViewNode(posX, posY) {
  return new joint.shapes.basic.Rect({
    position: {x :posX + 5, y: posY + 25},
    size: { width: 140, height: 40 },
    attrs: {
      rect: {
        fill: "#FFFFFF",
        stroke: false
      },
      text: {
        text: ""
      },
      nodeType: "childViewNode"
    }
  });
}

function constantNodeOnlyInPort(posX, posY) {
  return new joint.shapes.devs.Model({
    position: {x: posX, y: posY},
    size: {width: nodeSize.width, height: nodeSize.height},
    inPorts: ['in'],
    ports: {
      groups: {
        'in': {
          attrs: {
            '.port-body': {
              fill: inColor,
              magnet: 'passive',
              height: portSetting.inPortSize,
              width: portSetting.inPortSize,
              stroke: false
            },
            '.port-label': {
              'font-size': 0
            }
          },
          position: {
            name: 'absolute',
            args: {
              x: nodeSize.inPortX,
              y: nodeSize.inPortY,
            }
          },
          markup: '<rect class="port-body"/>'
        }
      }
    },
    attrs: {
      '.label': {
        text: labelText,
        'text-anchor': 'middle',
        'font-size': "12px",
        fill: '#fff'
      },
      rect: {fill: bgColor, stroke: false},
      button: {
        cursor: 'pointer',
        ref: 'buttonLabel',
        refWidth: '150%',
        refHeight: '150%',
        refX: '-25%',
        refY: '-25%'
      },
      nodeType: nodeType,
      actionParam: actionParam
    },
  });
}

function constantNode(posX, posY) {
  return new joint.shapes.devs.Model({
    position: { x: posX, y: posY },
    size: { width: nodeSize.width, height: nodeSize.height },
    inPorts: ['in'],
    outPorts: ['out'],
    ports: {
      groups: {
        'in': {
          attrs: {
            '.port-body': {
              fill: inColor,
              magnet: 'passive',
              height: portSetting.inPortSize,
              width: portSetting.inPortSize,
              stroke: false
            },
            '.port-label': {
              'font-size': 0
            }
          },
          position: {
            name: 'absolute',
            args: {
              x: nodeSize.inPortX,
              y: nodeSize.inPortY,
            }
          },
          markup: '<rect class="port-body"/>'
        },
        'out': {
          attrs: {
            '.port-body': {
              fill: outColor,
              height: portSetting.outPortSize,
              width: portSetting.outPortSize,
              stroke: false
            },
            '.port-label': {
              'font-size': 0
            }
          },
          position: {
            name: 'absolute',
            args: {
              x: nodeSize.outPortX,
              y: nodeSize.outPortY,
            }
          },
          markup: '<rect class="port-body"/>'
        }
      }
    },
    attrs: {
      '.label': {
        text: labelText,
        'text-anchor': 'middle',
        'font-size': "12px",
        fill: '#fff'
      },
      rect: { fill: bgColor, stroke: false },
      button: {
        cursor: 'pointer',
        ref: 'buttonLabel',
        refWidth: '150%',
        refHeight:  '150%',
        refX: '-25%',
        refY: '-25%'
      },
      nodeType: nodeType,
      actionParam: actionParam
    }
  });
}
</script>
