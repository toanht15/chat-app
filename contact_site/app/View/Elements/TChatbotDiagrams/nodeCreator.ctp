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
  inPortSize: 31,
  outPortSize: 31
};

var middleNode = {
  width: 180,
  height: 70,
  inPortX: -30,
  inPortY: 20,
  outPortX: 180,
  outPortY: 20,
  labelX: .3,
  labelY: .1,
  nodeType: "text"
};
var smallNode = {
  width: 180,
  height: 50,
  inPortX: -30,
  inPortY: 10,
  outPortX: 180,
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
      inColor = "#fd9644";
      outColor = "#FDCBA4";
      bgColor = "#fd9644";
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
      inColor = "#fed330";
      outColor = "#FEE794";
      bgColor = "#fed330";
      labelText = "テキスト発言";
      childNode = contentViewNode;
      masterNode = constantNode;
      actionParam = {
        nodeName: "",
        text:[],
      };
    } else if (type === "scenario") {
      inColor = "#26de81";
      outColor = "#7AEAB0";
      bgColor = "#26de81";
      labelText = "シナリオ呼出";
      childNode = contentViewNode;
      masterNode = constantNodeOnlyInPort;
      actionParam = {
        scenarioId: ""
      };
    } else if (type === "jump") {
      inColor = "#2bcbba";
      outColor = "#78E2D6";
      bgColor = "#2bcbba";
      labelText = "ジャンプ";
      childNode = contentViewNode;
      masterNode = constantNodeOnlyInPort;
      actionParam = {
        targetId: ""
      };
    } else if (type === "link") {
      inColor = "#45aaf2";
      outColor = "#A0D4F7";
      bgColor = "#45aaf2";
      labelText = "リンク";
      childNode = contentViewNode;
      masterNode = constantNode;
      actionParam = {
        link: "",
        linkType: "same"
      }
    } else if (type === "operator") {
      inColor = "#4b7bec";
      outColor = "#A3BBF4";
      bgColor = "#4b7bec";
      labelText = "オペレータ呼出";
      nodeSize = smallNode;
      masterNode = constantNodeOnlyInPort;
      actionParam = {};
    } else if (type === "cv") {
      console.log("CVポイント");
      inColor = "#a55eea";
      outColor = "#D6B7F5";
      bgColor = "#a55eea";
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
    size: { width: 170, height: 40 },
    attrs: {
      rect: {
        fill: "#FFFFFF",
        stroke: false
      },
      text: {
        text: "",
        'font-size': "12px"
      },
      nodeBasicInfo: {
        nodeType: "childViewNode"
      }
    }
  });
}

function startNode() {
  return new joint.shapes.devs.Model({
    position: {x: 100, y: 150},
    size: {width: smallNode.width, height: smallNode.height},
    outPorts: ['out'],
    ports: {
      groups: {
        'out': {
          attrs: {
            '.port-body': {
              fill: "#F27D82",
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
              x: smallNode.outPortX,
              y: smallNode.outPortY
            }
          },
          markup: '<rect class="port-body"/>'
        }
      }
    },
    attrs: {
      '.label': {
        text: "START",
        'ref-width': '70%',
        'font-size': '12px',
        fill: '#FFF'
      },
      rect: {
        fill: '#EA2027',
        stroke: false
      },
      button: {
        cursor: 'pointer',
        ref: 'buttonLabel',
        refWidth: '150%',
        refHeight: '150%',
        refX: '-25%',
        refY: '-25%'
      },
      nodeBasicInfo: {
        nodeType: 'start',
        nextNode: ''
      }
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
      nodeBasicInfo: {
        nodeType: nodeType
      },
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
        fill: '#fff',
      },
      rect: {
        fill: bgColor,
        stroke: false,
      },
      button: {
        cursor: 'pointer',
        ref: 'buttonLabel',
        refWidth: '150%',
        refHeight:  '150%',
        refX: '-25%',
        refY: '-25%'
      },
      actionParam: actionParam,
      nodeBasicInfo: {
        nodeType: nodeType,
        nextNodeId: ""
      }
    }
  });
}
</script>
