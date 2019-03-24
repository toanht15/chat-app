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
    labelText,
    iconSVG;

var nodeSize = {};

var actionParam = {};

var iconParam = {};

var defaultIcon = {
  scale: "0.04",
  translate: "150, 150"
};

var portSetting = {
  inPortSize: 33,
  outPortSize: 33
};

var largeNode = {
  width: 250,
  height: 110,
  inPortX: -31,
  inPortY: 40,
  outPortX: 248,
  outPortY: 40,
  labelX: .3,
  labelY: .1,
  nodeType: "text",
};

var middleNode = {
  width: 250,
  height: 76,
  inPortX: -31,
  inPortY: 23,
  outPortX: 248,
  outPortY: 23,
  labelX: .3,
  labelY: .1,
  nodeType: "text",
};


var smallNode = {
  width: 100,
  height: 50,
  inPortX: -31,
  inPortY: 10,
  outPortX: 98,
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
    nodeSize = largeNode;
    nodeType = type;
    iconParam = defaultIcon;
    inColor = "#c0c0c0";
    outColor = "#c0c0c0";
    if(type === "branch") {
      //inColor = "#c73576";
      //outColor = "#DD82AB";
      bgColor = "#c73576";
      labelText = "分岐";
      iconSVG = "M592 0h-96c-26.51 0-48 21.49-48 48v32H192V48c0-26.51-21.49-48-48-48H48C21.49 0 0 21.49 0 48v96c0 26.51 21.49 48 48 48h94.86l88.76 150.21c-4.77 7.46-7.63 16.27-7.63 25.79v96c0 26.51 21.49 48 48 48h96c26.51 0 48-21.49 48-48v-96c0-26.51-21.49-48-48-48h-96c-5.2 0-10.11 1.04-14.8 2.57l-83.43-141.18C184.8 172.59 192 159.2 192 144v-32h256v32c0 26.51 21.49 48 48 48h96c26.51 0 48-21.49 48-48V48c0-26.51-21.49-48-48-48zM32 144V48c0-8.82 7.18-16 16-16h96c8.82 0 16 7.18 16 16v96c0 8.82-7.18 16-16 16H48c-8.82 0-16-7.18-16-16zm336 208c8.82 0 16 7.18 16 16v96c0 8.82-7.18 16-16 16h-96c-8.82 0-16-7.18-16-16v-96c0-8.82 7.18-16 16-16h96zm240-208c0 8.82-7.18 16-16 16h-96c-8.82 0-16-7.18-16-16V48c0-8.82 7.18-16 16-16h96c8.82 0 16 7.18 16 16v96z";
      iconParam = {
        scale: "0.035",
        translate: "200, 250"
      };
      childNode = contentViewNode;
      masterNode = constantNodeOnlyInPort;
      actionParam = {
        nodeName: "",
        text: "",
        btnType: "",
        selection: []
      };
    } else if (type === "text") {
      //inColor = "#D48BB3";
      //outColor = "#EFD6E4";
      bgColor = "#D48BB3";
      labelText = "テキスト発言";
      iconSVG = "M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 7.1 5.8 12 12 12 2.4 0 4.9-.7 7.1-2.4L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64zm32 352c0 17.6-14.4 32-32 32H293.3l-8.5 6.4L192 460v-76H64c-17.6 0-32-14.4-32-32V64c0-17.6 14.4-32 32-32h384c17.6 0 32 14.4 32 32v288z";
      iconParam = {
        scale: "0.035",
        translate: "200, 250"
      };
      childNode = contentViewNode;
      masterNode = constantNode;
      actionParam = {
        nodeName: "",
        text:[],
      };
    } else if (type === "scenario") {
      //inColor = "#82c0cd";
      //outColor = "#C8E3E8";
      bgColor = "#82c0cd";
      labelText = "シナリオ呼出";
      iconSVG = "M160 416h64v-32h-64v32zm32-192c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm192 0c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm160 0h-32v-32c0-53-43-96-96-96H304V16c0-8.8-7.2-16-16-16s-16 7.2-16 16v80H160c-53 0-96 43-96 96v32H32c-17.7 0-32 14.3-32 32v128c0 17.7 14.3 32 32 32h32v32c0 35.3 28.7 64 64 64h320c35.3 0 64-28.7 64-64v-32h32c17.7 0 32-14.3 32-32V256c0-17.7-14.3-32-32-32zM64 384H32V256h32v128zm416 64c0 17.6-14.4 32-32 32H128c-17.6 0-32-14.4-32-32V192c0-35.3 28.7-64 64-64h256c35.3 0 64 28.7 64 64v256zm64-64h-32V256h32v128zm-192 32h64v-32h-64v32zm-96 0h64v-32h-64v32z";
      childNode = contentViewNode;
      masterNode = constantNodeOnlyInPort;
      actionParam = {
        scenarioId: "",
        callbackToDiagram: false
      };
      nodeSize = middleNode;
    } else if (type === "jump") {
      //inColor = "#c8d627";
      //outColor = "#DFE679";
      bgColor = "#c8d627";
      labelText = "ジャンプ";
      iconSVG = "M0 80v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48H48C21.5 32 0 53.5 0 80zm400-16c8.8 0 16 7.2 16 16v352c0 8.8-7.2 16-16 16H48c-8.8 0-16-7.2-16-16V80c0-8.8 7.2-16 16-16h352zm-208 64v64H88c-13.2 0-24 10.8-24 24v80c0 13.2 10.8 24 24 24h104v64c0 28.4 34.5 42.8 54.6 22.6l128-128c12.5-12.5 12.5-32.8 0-45.3l-128-128c-20.1-20-54.6-5.8-54.6 22.7zm160 128L224 384v-96H96v-64h128v-96l128 128z";
      childNode = contentViewNode;
      masterNode = constantNodeOnlyInPort;
      actionParam = {
        targetId: ""
      };
      nodeSize = middleNode;
    } else if (type === "link") {
      //inColor = "#845d9e";
      //outColor = "#B39CC3";
      bgColor = "#845d9e";
      labelText = "リンク";
      iconSVG = "M301.148 394.702l-79.2 79.19c-50.778 50.799-133.037 50.824-183.84 0-50.799-50.778-50.824-133.037 0-183.84l79.19-79.2a132.833 132.833 0 0 1 3.532-3.403c7.55-7.005 19.795-2.004 20.208 8.286.193 4.807.598 9.607 1.216 14.384.481 3.717-.746 7.447-3.397 10.096-16.48 16.469-75.142 75.128-75.3 75.286-36.738 36.759-36.731 96.188 0 132.94 36.759 36.738 96.188 36.731 132.94 0l79.2-79.2.36-.36c36.301-36.672 36.14-96.07-.37-132.58-8.214-8.214-17.577-14.58-27.585-19.109-4.566-2.066-7.426-6.667-7.134-11.67a62.197 62.197 0 0 1 2.826-15.259c2.103-6.601 9.531-9.961 15.919-7.28 15.073 6.324 29.187 15.62 41.435 27.868 50.688 50.689 50.679 133.17 0 183.851zm-90.296-93.554c12.248 12.248 26.362 21.544 41.435 27.868 6.388 2.68 13.816-.68 15.919-7.28a62.197 62.197 0 0 0 2.826-15.259c.292-5.003-2.569-9.604-7.134-11.67-10.008-4.528-19.371-10.894-27.585-19.109-36.51-36.51-36.671-95.908-.37-132.58l.36-.36 79.2-79.2c36.752-36.731 96.181-36.738 132.94 0 36.731 36.752 36.738 96.181 0 132.94-.157.157-58.819 58.817-75.3 75.286-2.651 2.65-3.878 6.379-3.397 10.096a163.156 163.156 0 0 1 1.216 14.384c.413 10.291 12.659 15.291 20.208 8.286a131.324 131.324 0 0 0 3.532-3.403l79.19-79.2c50.824-50.803 50.799-133.062 0-183.84-50.802-50.824-133.062-50.799-183.84 0l-79.2 79.19c-50.679 50.682-50.688 133.163 0 183.851z";
      childNode = contentViewNode;
      masterNode = constantNode;
      actionParam = {
        link: "",
        linkType: "same"
      };
      nodeSize = middleNode;
    } else if (type === "operator") {
      //inColor = "#98B5E0";
      //outColor = "#E7EEF7";
      bgColor = "#98B5E0";
      labelText = "オペレータ呼出";
      iconSVG = "M313.6 288c-28.7 0-42.5 16-89.6 16-47.1 0-60.8-16-89.6-16C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4zM416 464c0 8.8-7.2 16-16 16H48c-8.8 0-16-7.2-16-16v-41.6C32 365.9 77.9 320 134.4 320c19.6 0 39.1 16 89.6 16 50.4 0 70-16 89.6-16 56.5 0 102.4 45.9 102.4 102.4V464zM224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm0-224c52.9 0 96 43.1 96 96s-43.1 96-96 96-96-43.1-96-96 43.1-96 96-96z";
      childNode = contentViewNode;
      masterNode = constantNodeOnlyInPort;
      actionParam = {};
      nodeSize = middleNode;
    } else if (type === "cv") {
      console.log("CVポイント");
      //inColor = "#A2CCBA";
      //outColor = "#E4F0EB";
      bgColor = "#A2CCBA";
      labelText = "CVポイント";
      iconSVG = "M192 0C86.4 0 0 86.4 0 192c0 76.8 25.6 99.2 172.8 310.4 4.8 6.4 12 9.6 19.2 9.6s14.4-3.2 19.2-9.6C358.4 291.2 384 268.8 384 192 384 86.4 297.6 0 192 0zm.01 474c-19.67-28.17-37.09-52.85-52.49-74.69C42.64 261.97 32 245.11 32 192c0-88.22 71.78-160 160-160s160 71.78 160 160c0 53.11-10.64 69.97-107.52 207.31-15.52 22.01-33.09 46.92-52.47 74.69zm89.33-339.54a7.98 7.98 0 0 0-5.66-2.34c-2.05 0-4.1.78-5.66 2.34L162.54 241.94l-48.57-48.57a7.98 7.98 0 0 0-5.66-2.34c-2.05 0-4.1.78-5.66 2.34l-11.31 11.31c-3.12 3.12-3.12 8.19 0 11.31l65.54 65.54c1.56 1.56 3.61 2.34 5.66 2.34s4.09-.78 5.65-2.34l124.45-124.45c3.12-3.12 3.12-8.19 0-11.31l-11.3-11.31z";
      childNode = contentViewNode;
      masterNode = constantNode;
      actionParam = {};
      nodeSize = middleNode;
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
    position: {x :posX + 5, y: posY + 35},
    size: { width: nodeSize.width - 10 , height: nodeSize.height - 40 },
    attrs: {
      rect: {
        fill: "#FFFFFF",
        stroke: false,
        rx: 3,
        ry: 3
      },
      text: {
        text: "",
        'font-size': "14px",
        y: 0
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
              fill: "#C0C0C0",
              height: portSetting.outPortSize,
              width: portSetting.outPortSize,
              stroke: false,
              rx: 5,
              ry: 5,
              'fill-opacity': "0.9"
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
        y: 19,
      },
      '.body': {
        fill: '#8395a7',
        stroke: false,
        rx: 5,
        ry: 5
      },
      '.inCover': {
        fill: '#BDC6CF',
        height: portSetting.inPortSize,
        width: 2,
        x: 100,
        y: 10
      },
      nodeBasicInfo: {
        nodeType: 'start',
        nextNode: ''
      }
    },
    markup: '<rect class="body"/><text class="label"/>'
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
              stroke: false,
              rx: 5,
              ry: 5,
              'fill-opacity': "0.9"
            },
            '.port-label': {
              'font-size': 0
            },
            type: nodeType
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
        'font-size': "14px",
        'font-weight': 'bold',
        fill: '#fff',
        y: 12,
      },
      '.icon' : {
        transform: "scale("+iconParam.scale+") translate("+iconParam.translate+")",
      },
      '.body': {
        fill: bgColor,
        stroke: false,
        rx: 5,
        ry: 5
      },
      '.inCover': {
        fill: bgColor,
        height: portSetting.inPortSize,
        width: 2,
        x: -2,
        y: nodeSize.inPortY,
      },
      nodeBasicInfo: {
        nodeType: nodeType
      },
      actionParam: actionParam
    },
    markup: '<rect class="body"/><path class="icon" d="' + iconSVG + '"></path><text class="label"/>'
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
              stroke: false,
              rx: 5,
              ry: 5,
              'fill-opacity': "0.9"
            },
            '.port-label': {
              'font-size': 0
            },
            type: nodeType
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
              width: portSetting.outPortSize,
              stroke: false,
              rx: 5,
              ry: 5,
              'fill-opacity': "0.9"
            },
            '.port-label': {
              'font-size': 0
            },
              type: nodeType
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
        'font-size': "14px",
        'font-weight': 'bold',
        fill: '#fff',
        y: 12,
      },
      '.icon' : {
        transform: "scale("+iconParam.scale+") translate("+iconParam.translate+")",
      },
      '.body': {
        fill: bgColor,
        stroke: false,
        rx: 5,
        ry: 5
      },
      '.inCover': {
        fill: bgColor,
        stroke: false,
        height: portSetting.inPortSize,
        width: 2,
        x: -2,
        y: nodeSize.inPortY
      },
      '.outCover': {
        fill: bgColor,
        stroke: false,
        height: portSetting.outPortSize,
        width: 2,
        x: 250,
        y: nodeSize.outPortY
      },
      actionParam: actionParam,
      nodeBasicInfo: {
        nodeType: nodeType,
        nextNodeId: ""
      }
    },
    markup: '<rect class="body"/><path class="icon" d="' + iconSVG + '"></path><text class="label"/>'
  });
}
</script>
