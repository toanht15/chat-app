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
  width: 200,
  height: 70,
  inPortX: -30,
  inPortY: 20,
  outPortX: 190,
  outPortY: 20,
  labelX: .3,
  labelY: .1,
  nodeType: "text"
};
var smallNode = {
  width: 140,
  height: 50,
  inPortX: -30,
  inPortY: 10,
  outPortX: 130,
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
      inColor = "#ee5253";
      outColor = "#F6ABAC";
      bgColor = "#ee5253";
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
      inColor = "#ff9f43";
      outColor = "#FFD2A8";
      bgColor = "#ff9f43";
      labelText = "テキスト発言";
      childNode = contentViewNode;
      masterNode = constantNode;
      actionParam = {
        nodeName: "",
        text:[],
      };
    } else if (type === "scenario") {
      inColor = "#0abde3";
      outColor = "#59DCF7";
      bgColor = "#0abde3";
      labelText = "シナリオ呼出";
      childNode = contentViewNode;
      masterNode = constantNodeOnlyInPort;
      actionParam = {
        scenarioId: ""
      };
    } else if (type === "jump") {
      inColor = "#10ac84";
      outColor = "#32EAB9";
      bgColor = "#10ac84";
      labelText = "ジャンプ";
      childNode = contentViewNode;
      masterNode = constantNodeOnlyInPort;
      actionParam = {
        targetId: ""
      };
    } else if (type === "link") {
      inColor = "#960C84";
      outColor = "#CA10B1";
      bgColor = "#960C84";
      labelText = "リンク";
      childNode = contentViewNode;
      masterNode = constantNode;
      actionParam = {
        link: "",
        linkType: "same"
      }
    } else if (type === "operator") {
      inColor = "#2e86de";
      outColor = "#84B7EB";
      bgColor = "#2e86de";
      labelText = "オペレータ呼出";
      childNode = contentViewNode;
      masterNode = constantNodeOnlyInPort;
      actionParam = {};
    } else if (type === "cv") {
      console.log("CVポイント");
      inColor = "#5f27cd";
      outColor = "#9771E4";
      bgColor = "#5f27cd";
      labelText = "CVポイント";
      childNode = contentViewNode;
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
    size: { width: 190, height: 40 },
    attrs: {
      rect: {
        fill: "#FFFFFF",
        stroke: false,
        rx: 3,
        ry: 3
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
              fill: "#BDC6CF",
              height: portSetting.outPortSize,
              width: portSetting.outPortSize + 10,
              stroke: false,
              rx: 5,
              ry: 5
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
          z: 0,
          markup: '<rect class="port-body"/>'
        }
      }
    },
    attrs: {
      '.label': {
        text: "START",
        'ref-width': '70%',
        'font-size': '12px',
        'font-weight': 'bold',
        fill: '#FFF',
        y: 19
      },
      rect: {
        fill: '#8395a7',
        stroke: false,
        rx: 5,
        ry: 5
      },
      nodeBasicInfo: {
        nodeType: 'start',
        nextNode: ''
      }
    },
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
              width: portSetting.inPortSize + 10,
              stroke: false,
              rx: 5,
              ry: 5
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
          z: 0,
          markup: '<rect class="port-body"/>'
        }
      }
    },
    attrs: {
      '.label': {
        text: labelText,
        'text-anchor': 'middle',
        'font-size': "12px",
        'font-weight': 'bold',
        fill: '#fff',
        y: 7
      },
      rect: {
        fill: bgColor,
        stroke: false,
        rx: 5,
        ry: 5
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
              width: portSetting.inPortSize + 10,
              stroke: false,
              rx: 5,
              ry: 5
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
          z: 0,
          markup: '<rect class="port-body"/>'
        },
        'out': {
          attrs: {
            '.port-body': {
              fill: outColor,
              height: portSetting.outPortSize,
              width: portSetting.outPortSize + 10,
              stroke: false,
              rx: 5,
              ry: 5
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
          z: 0,
          markup: '<rect class="port-body"/>'
        }
      }
    },
    attrs: {
      '.label': {
        text: labelText,
        'text-anchor': 'middle',
        'font-size': "12px",
        'font-weight': 'bold',
        fill: '#fff',
        y: 7
      },
      rect: {
        fill: bgColor,
        stroke: false,
        rx: 5,
        ry: 5
      },
      actionParam: actionParam,
      nodeBasicInfo: {
        nodeType: nodeType,
        nextNodeId: ""
      }
    },
  });
}
</script>
