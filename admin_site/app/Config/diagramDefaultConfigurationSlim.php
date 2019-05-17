<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/08/08
 * Time: 12:55
 */
$config['default'] = array(
  'diagrams_slim' => array(
    0 => array(
      'name' => 'サンプルツリー',
      'activity' => array(
        "cells" => array(
          array(
            "type" => "devs.Model",
            "inPorts" => array(),
            "outPorts" => array(
              "out"
            ),
            "size" => array(
              "width" => 100,
              "height" => 50
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "left"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 98,
                      "y" => 10
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#BDC6CF",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 100,
              "y" => 150
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><text class=\"label\"/>",
            "id" => "f1ff0714-7729-430e-a8a8-fecb4a6cf897",
            "z" => 1,
            "attrs" => array(
              ".label" => array(
                "text" => "START",
                "font-size" => "12px",
                "fill" => "#FFF",
                "ref-width" => "70%",
                "font-weight" => "bold",
                "y" => 19
              ),
              ".body" => array(
                "stroke" => false,
                "fill" => "#8395a7",
                "rx" => 5,
                "ry" => 5
              ),
              ".inCover" => array(
                "fill" => "#BDC6CF",
                "height" => 33,
                "width" => 2,
                "x" => 100,
                "y" => 10
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "start",
                "nextNode" => "",
                "nextNodeId" => "b938dc97-5042-4c97-b5a7-c319538362be",
                "messageIntervalSec" => 2
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(
              "in"
            ),
            "outPorts" => array(),
            "size" => array(
              "width" => 250,
              "height" => 470
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => -31,
                      "y" => 40
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#c73576",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => "passive",
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "branch"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                ),
                "out" => array(
                  "position" => array(
                    "name" => "right"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                )
              ),
              "items" => array(
                array(
                  "id" => "in",
                  "group" => "in",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "in"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 335,
              "y" => 120
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><path class=\"icon\" d=\"M592 0h-96c-26.51 0-48 21.49-48 48v32H192V48c0-26.51-21.49-48-48-48H48C21.49 0 0 21.49 0 48v96c0 26.51 21.49 48 48 48h94.86l88.76 150.21c-4.77 7.46-7.63 16.27-7.63 25.79v96c0 26.51 21.49 48 48 48h96c26.51 0 48-21.49 48-48v-96c0-26.51-21.49-48-48-48h-96c-5.2 0-10.11 1.04-14.8 2.57l-83.43-141.18C184.8 172.59 192 159.2 192 144v-32h256v32c0 26.51 21.49 48 48 48h96c26.51 0 48-21.49 48-48V48c0-26.51-21.49-48-48-48zM32 144V48c0-8.82 7.18-16 16-16h96c8.82 0 16 7.18 16 16v96c0 8.82-7.18 16-16 16H48c-8.82 0-16-7.18-16-16zm336 208c8.82 0 16 7.18 16 16v96c0 8.82-7.18 16-16 16h-96c-8.82 0-16-7.18-16-16v-96c0-8.82 7.18-16 16-16h96zm240-208c0 8.82-7.18 16-16 16h-96c-8.82 0-16-7.18-16-16V48c0-8.82 7.18-16 16-16h96c8.82 0 16 7.18 16 16v96z\"></path><text class=\"label\"/>",
            "id" => "b938dc97-5042-4c97-b5a7-c319538362be",
            "embeds" => array(
              "ad1b0bf4-5883-422e-b07f-2fe9e387f45c",
              "39c262c5-a9fa-41ff-905b-35a4f2cb5fb0",
              "5b6b3bb9-d4f3-4bab-b4b4-f22a4d4837d2",
              "22166176-b4c5-407c-a3e0-167b0eed3165",
              "d73b1efc-bdf2-40bb-8efa-b89d6f61e276",
              "2fb4d49c-a483-4440-9862-9c43a360818f",
              "80f421f5-5e1b-401d-b5a4-03bc4ba7b899",
              "2b2bf14a-e520-42de-8cdf-99e461567888",
              "6161f4d6-2f26-4d13-8e38-2edf178fb360",
              "39f817ff-ba79-4533-873a-6a1eccfac756"
            ),
            "z" => 2,
            "attrs" => array(
              ".label" => array(
                "text" => "メインメニュー",
                "font-size" => "14px",
                "fill" => "#fff",
                "font-weight" => "bold",
                "y" => 12
              ),
              ".body" => array(
                "stroke" => false,
                "fill" => "#c73576",
                "rx" => 5,
                "ry" => 5
              ),
              ".icon" => array(
                "transform" => "scale(0.035) translate(200, 250)"
              ),
              ".inCover" => array(
                "fill" => "#c73576",
                "height" => 33,
                "width" => 2,
                "x" => -2,
                "y" => 40
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "branch"
              ),
              "actionParam" => array(
                "nodeName" => "メインメニュー",
                "text" => "（メインメニューのサンプルです。）\n\nここでsincloの設定方法を学習することができます。",
                "btnType" => "1",
                "selection" => array(
                  array(
                    "type" => "1",
                    "value" => "基本を覚える",
                    "uuid" => "39c262c5-a9fa-41ff-905b-35a4f2cb5fb0"
                  ),
                  array(
                    "type" => "1",
                    "value" => "画像を使った設定サンプル",
                    "uuid" => "5b6b3bb9-d4f3-4bab-b4b4-f22a4d4837d2"
                  ),
                  array(
                    "type" => "1",
                    "value" => "リンクを使った設定サンプル",
                    "uuid" => "22166176-b4c5-407c-a3e0-167b0eed3165"
                  ),
                  array(
                    "type" => "2",
                    "value" => "以下は「シナリオ」を使ったサンプルです。",
                    "uuid" => "6161f4d6-2f26-4d13-8e38-2edf178fb360"
                  ),
                  array(
                    "type" => "1",
                    "value" => "資料請求のサンプル",
                    "uuid" => "d73b1efc-bdf2-40bb-8efa-b89d6f61e276"
                  ),
                  array(
                    "type" => "1",
                    "value" => "来店予約のサンプル",
                    "uuid" => "2fb4d49c-a483-4440-9862-9c43a360818f"
                  ),
                  array(
                    "type" => "1",
                    "value" => "会員登録・入会のサンプル",
                    "uuid" => "80f421f5-5e1b-401d-b5a4-03bc4ba7b899"
                  ),
                  array(
                    "type" => "1",
                    "value" => "アンケートのサンプル",
                    "uuid" => "2b2bf14a-e520-42de-8cdf-99e461567888"
                  ),
                  array(
                    "type" => "1",
                    "value" => "問い合わせフォームのサンプル",
                    "uuid" => "39f817ff-ba79-4533-873a-6a1eccfac756"
                  )
                ),
                "customizeDesign" => array(
                  "isCustomize" => false,
                  "radioStyle" => "1",
                  "radioEntireBackgroundColor" => "#D5E682",
                  "radioEntireActiveColor" => "#ABCD05",
                  "radioTextColor" => "#333333",
                  "radioActiveTextColor" => "#333333",
                  "radioSelectionDistance" => 4,
                  "radioBackgroundColor" => "#FFFFFF",
                  "radioActiveColor" => "#ABCD05",
                  "radioBorderColor" => "#ABCD05",
                  "radioNoneBorder" => false
                )
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 340,
              "y" => 155
            ),
            "size" => array(
              "width" => 240,
              "height" => 70
            ),
            "angle" => 0,
            "id" => "ad1b0bf4-5883-422e-b07f-2fe9e387f45c",
            "z" => 3,
            "parent" => "b938dc97-5042-4c97-b5a7-c319538362be",
            "attrs" => array(
              "rect" => array(
                "fill" => "#FFFFFF",
                "stroke" => false,
                "rx" => 3,
                "ry" => 3
              ),
              "text" => array(
                "text" => "（メインメニューのサンプルです。\n）\n",
                "font-size" => "14px",
                "y" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childViewNode",
                "tooltip" => "（メインメニューのサンプルです。）\n\nここでsincloの設定方法を学習することができます。"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(),
            "outPorts" => array(
              "out"
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "left"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 235,
                      "y" => 3
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#DD82AB",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "fill-opacity" => "0.9",
                      "height" => 30,
                      "width" => 30,
                      "rx" => 3,
                      "ry" => 3
                    ),
                    "type" => "branch"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 4,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 340,
              "y" => 235
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><text class=\"label\"/><rect class=\"cover_top\"/><rect class=\"cover_bottom\"/>",
            "id" => "39c262c5-a9fa-41ff-905b-35a4f2cb5fb0",
            "parent" => "b938dc97-5042-4c97-b5a7-c319538362be",
            "z" => 4,
            "attrs" => array(
              ".label" => array(
                "text" => "基本を覚える",
                "font-size" => "14px",
                "ref-width" => "70%",
                "y" => 12
              ),
              "text" => array(
                "text" => "基本を覚える",
                "ref-width" => "70%",
                "font-size" => "14px",
                "fill" => "#000",
                "y" => 12
              ),
              "rect.body" => array(
                "fill" => "#FFF",
                "stroke" => false,
                "rx" => 10,
                "ry" => 10
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childPortNode",
                "nextNode" => "",
                "tooltip" => "基本を覚える",
                "nextNodeId" => "260cc4d3-d816-47c3-830f-23e62092549e"
              ),
              ".cover_top" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "fill-opacity" => 0
              ),
              ".cover_bottom" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "transform" => "translate(0 26)",
                "fill-opacity" => 1
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(),
            "outPorts" => array(
              "out"
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "left"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 235,
                      "y" => 3
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#DD82AB",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "fill-opacity" => "0.9",
                      "height" => 30,
                      "width" => 30,
                      "rx" => 3,
                      "ry" => 3
                    ),
                    "type" => "branch"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 4,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 340,
              "y" => 275
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><text class=\"label\"/><rect class=\"cover_top\"/><rect class=\"cover_bottom\"/>",
            "id" => "5b6b3bb9-d4f3-4bab-b4b4-f22a4d4837d2",
            "parent" => "b938dc97-5042-4c97-b5a7-c319538362be",
            "z" => 5,
            "attrs" => array(
              ".label" => array(
                "text" => "画像を使った設定サンプ...",
                "font-size" => "14px",
                "ref-width" => "70%",
                "y" => 12
              ),
              "text" => array(
                "text" => "画像を使った設定サンプ...",
                "ref-width" => "70%",
                "font-size" => "14px",
                "fill" => "#000",
                "y" => 12
              ),
              "rect.body" => array(
                "fill" => "#FFF",
                "stroke" => false,
                "rx" => 10,
                "ry" => 10
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childPortNode",
                "nextNode" => "",
                "tooltip" => "画像を使った設定サンプル",
                "nextNodeId" => "e6ddcffa-918c-401a-bd36-ecca27d17132"
              ),
              ".cover_top" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "fill-opacity" => 1
              ),
              ".cover_bottom" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "transform" => "translate(0 26)",
                "fill-opacity" => 1
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(),
            "outPorts" => array(
              "out"
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "left"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 235,
                      "y" => 3
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#DD82AB",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "fill-opacity" => "0.9",
                      "height" => 30,
                      "width" => 30,
                      "rx" => 3,
                      "ry" => 3
                    ),
                    "type" => "branch"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 4,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 340,
              "y" => 315
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><text class=\"label\"/><rect class=\"cover_top\"/><rect class=\"cover_bottom\"/>",
            "id" => "22166176-b4c5-407c-a3e0-167b0eed3165",
            "parent" => "b938dc97-5042-4c97-b5a7-c319538362be",
            "z" => 6,
            "attrs" => array(
              ".label" => array(
                "text" => "リンクを使った設定サン...",
                "font-size" => "14px",
                "ref-width" => "70%",
                "y" => 12
              ),
              "text" => array(
                "text" => "リンクを使った設定サン...",
                "ref-width" => "70%",
                "font-size" => "14px",
                "fill" => "#000",
                "y" => 12
              ),
              "rect.body" => array(
                "fill" => "#FFF",
                "stroke" => false,
                "rx" => 10,
                "ry" => 10
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childPortNode",
                "nextNode" => "",
                "tooltip" => "リンクを使った設定サンプル",
                "nextNodeId" => "fd060639-35ff-42e3-ad6a-6db1bb43c835"
              ),
              ".cover_top" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "fill-opacity" => 1
              ),
              ".cover_bottom" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "transform" => "translate(0 26)",
                "fill-opacity" => 0
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(),
            "outPorts" => array(
              "out"
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "left"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 235,
                      "y" => 3
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#DD82AB",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "fill-opacity" => "0.9",
                      "height" => 30,
                      "width" => 30,
                      "rx" => 3,
                      "ry" => 3
                    ),
                    "type" => "branch"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 4,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 340,
              "y" => 385
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><text class=\"label\"/><rect class=\"cover_top\"/><rect class=\"cover_bottom\"/>",
            "id" => "d73b1efc-bdf2-40bb-8efa-b89d6f61e276",
            "parent" => "b938dc97-5042-4c97-b5a7-c319538362be",
            "z" => 7,
            "attrs" => array(
              ".label" => array(
                "text" => "資料請求のサンプル",
                "font-size" => "14px",
                "ref-width" => "70%",
                "y" => 12
              ),
              "text" => array(
                "text" => "資料請求のサンプル",
                "ref-width" => "70%",
                "font-size" => "14px",
                "fill" => "#000",
                "y" => 12
              ),
              "rect.body" => array(
                "fill" => "#FFF",
                "stroke" => false,
                "rx" => 10,
                "ry" => 10
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childPortNode",
                "nextNode" => "",
                "tooltip" => "資料請求のサンプル",
                "nextNodeId" => "2424f263-4685-4c7d-b43b-265b2e290042"
              ),
              ".cover_top" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "fill-opacity" => 0
              ),
              ".cover_bottom" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "transform" => "translate(0 26)",
                "fill-opacity" => 1
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(),
            "outPorts" => array(
              "out"
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "left"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 235,
                      "y" => 3
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#DD82AB",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "fill-opacity" => "0.9",
                      "height" => 30,
                      "width" => 30,
                      "rx" => 3,
                      "ry" => 3
                    ),
                    "type" => "branch"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 4,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 340,
              "y" => 425
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><text class=\"label\"/><rect class=\"cover_top\"/><rect class=\"cover_bottom\"/>",
            "id" => "2fb4d49c-a483-4440-9862-9c43a360818f",
            "parent" => "b938dc97-5042-4c97-b5a7-c319538362be",
            "z" => 8,
            "attrs" => array(
              ".label" => array(
                "text" => "来店予約のサンプル",
                "font-size" => "14px",
                "ref-width" => "70%",
                "y" => 12
              ),
              "text" => array(
                "text" => "来店予約のサンプル",
                "ref-width" => "70%",
                "font-size" => "14px",
                "fill" => "#000",
                "y" => 12
              ),
              "rect.body" => array(
                "fill" => "#FFF",
                "stroke" => false,
                "rx" => 10,
                "ry" => 10
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childPortNode",
                "nextNode" => "",
                "tooltip" => "来店予約のサンプル",
                "nextNodeId" => "ad98461f-7201-46d8-b335-93c56d7e1f65"
              ),
              ".cover_top" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "fill-opacity" => 1
              ),
              ".cover_bottom" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "transform" => "translate(0 26)",
                "fill-opacity" => 1
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(),
            "outPorts" => array(
              "out"
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "left"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 235,
                      "y" => 3
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#DD82AB",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "fill-opacity" => "0.9",
                      "height" => 30,
                      "width" => 30,
                      "rx" => 3,
                      "ry" => 3
                    ),
                    "type" => "branch"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 4,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 340,
              "y" => 465
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><text class=\"label\"/><rect class=\"cover_top\"/><rect class=\"cover_bottom\"/>",
            "id" => "80f421f5-5e1b-401d-b5a4-03bc4ba7b899",
            "parent" => "b938dc97-5042-4c97-b5a7-c319538362be",
            "z" => 9,
            "attrs" => array(
              ".label" => array(
                "text" => "会員登録・入会のサンプ...",
                "font-size" => "14px",
                "ref-width" => "70%",
                "y" => 12
              ),
              "text" => array(
                "text" => "会員登録・入会のサンプ...",
                "ref-width" => "70%",
                "font-size" => "14px",
                "fill" => "#000",
                "y" => 12
              ),
              "rect.body" => array(
                "fill" => "#FFF",
                "stroke" => false,
                "rx" => 10,
                "ry" => 10
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childPortNode",
                "nextNode" => "",
                "tooltip" => "会員登録・入会のサンプル",
                "nextNodeId" => "ce587fe5-3d35-40ba-b9ef-abbccc37cf4d"
              ),
              ".cover_top" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "fill-opacity" => 1
              ),
              ".cover_bottom" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "transform" => "translate(0 26)",
                "fill-opacity" => 1
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(),
            "outPorts" => array(
              "out"
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "left"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 235,
                      "y" => 3
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#DD82AB",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "fill-opacity" => "0.9",
                      "height" => 30,
                      "width" => 30,
                      "rx" => 3,
                      "ry" => 3
                    ),
                    "type" => "branch"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 4,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 340,
              "y" => 505
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><text class=\"label\"/><rect class=\"cover_top\"/><rect class=\"cover_bottom\"/>",
            "id" => "2b2bf14a-e520-42de-8cdf-99e461567888",
            "parent" => "b938dc97-5042-4c97-b5a7-c319538362be",
            "z" => 10,
            "attrs" => array(
              ".label" => array(
                "text" => "アンケートのサンプル",
                "font-size" => "14px",
                "ref-width" => "70%",
                "y" => 12
              ),
              "text" => array(
                "text" => "アンケートのサンプル",
                "ref-width" => "70%",
                "font-size" => "14px",
                "fill" => "#000",
                "y" => 12
              ),
              "rect.body" => array(
                "fill" => "#FFF",
                "stroke" => false,
                "rx" => 10,
                "ry" => 10
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childPortNode",
                "nextNode" => "",
                "tooltip" => "アンケートのサンプル",
                "nextNodeId" => "a5558797-2c50-4fae-9c7b-312a211a00ba"
              ),
              ".cover_top" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "fill-opacity" => 1
              ),
              ".cover_bottom" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "transform" => "translate(0 26)",
                "fill-opacity" => 1
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "f1ff0714-7729-430e-a8a8-fecb4a6cf897",
              "port" => "out"
            ),
            "target" => array(
              "id" => "b938dc97-5042-4c97-b5a7-c319538362be",
              "port" => "in"
            ),
            "id" => "1cc2144a-0026-4ca7-addd-4d6baa0e27b8",
            "z" => 11,
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(
              "in"
            ),
            "outPorts" => array(
              "out"
            ),
            "size" => array(
              "width" => 250,
              "height" => 110
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => -31,
                      "y" => 40
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#D48BB3",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => "passive",
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "text"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 248,
                      "y" => 40
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#EFD6E4",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "text"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "in",
                  "group" => "in",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "in"
                    )
                  )
                ),
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 780,
              "y" => 40
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><path class=\"icon\" d=\"M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 7.1 5.8 12 12 12 2.4 0 4.9-.7 7.1-2.4L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64zm32 352c0 17.6-14.4 32-32 32H293.3l-8.5 6.4L192 460v-76H64c-17.6 0-32-14.4-32-32V64c0-17.6 14.4-32 32-32h384c17.6 0 32 14.4 32 32v288z\"></path><text class=\"label\"/>",
            "id" => "260cc4d3-d816-47c3-830f-23e62092549e",
            "embeds" => array(
              "6f3ab5cf-0107-4417-8aa3-0bfc2a2b69c6"
            ),
            "z" => 12,
            "attrs" => array(
              ".label" => array(
                "text" => "基本を覚える",
                "font-size" => "14px",
                "fill" => "#fff",
                "font-weight" => "bold",
                "y" => 12
              ),
              ".body" => array(
                "stroke" => false,
                "fill" => "#D48BB3",
                "rx" => 5,
                "ry" => 5
              ),
              ".icon" => array(
                "transform" => "scale(0.035) translate(200, 250)"
              ),
              ".inCover" => array(
                "fill" => "#D48BB3",
                "stroke" => false,
                "height" => 33,
                "width" => 2,
                "x" => -2,
                "y" => 40
              ),
              ".outCover" => array(
                "fill" => "#D48BB3",
                "stroke" => false,
                "height" => 33,
                "width" => 2,
                "x" => 250,
                "y" => 40
              ),
              "actionParam" => array(
                "nodeName" => "基本を覚える",
                "text" => array(
                  "（基本を覚える）\n\nユーザー操作（ユーザーの発言）に応じて自動返信（オートリプライ）することが可能です。\n\n設定は非常にシンプルで、「分岐」で用意した選択肢と「テキスト発言」を線でつなぐだけです。\n\nノードは左メニューからドラッグ＆ドロップで配置でき、各ノードにある右の四角をドラッグし、\n繋ぎたいノードの左の四角にドロップすると線が表示されます。",
                  "テキスト発言は１つのノードで複数個の発言を設定する事ができます。"
                )
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "text",
                "nextNodeId" => "d837f4fa-338e-4431-b8dc-5f8962d0b75c"
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 785,
              "y" => 75
            ),
            "size" => array(
              "width" => 240,
              "height" => 70
            ),
            "angle" => 0,
            "id" => "6f3ab5cf-0107-4417-8aa3-0bfc2a2b69c6",
            "z" => 13,
            "parent" => "260cc4d3-d816-47c3-830f-23e62092549e",
            "attrs" => array(
              "rect" => array(
                "fill" => "#FFFFFF",
                "stroke" => false,
                "rx" => 3,
                "ry" => 3
              ),
              "text" => array(
                "text" => "（基本を覚える）\n\nユーザー操作（ユーザーの発言）...",
                "font-size" => "14px",
                "y" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childViewNode",
                "tooltip" => "（基本を覚える）\n\nユーザー操作（ユーザーの発言）に応じて自動返信（オートリプライ）することが可能です。\n\n設定は非常にシンプルで、「分岐」で用意した選択肢と「テキスト発言」を線でつなぐだけです。\n\nノードは左メニューからドラッグ＆ドロップで配置でき、各ノードにある右の四角をドラッグし、\n繋ぎたいノードの左の四角にドロップすると線が表示されます。"
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "39c262c5-a9fa-41ff-905b-35a4f2cb5fb0",
              "port" => "out"
            ),
            "target" => array(
              "id" => "260cc4d3-d816-47c3-830f-23e62092549e",
              "port" => "in"
            ),
            "id" => "8a82365c-19b0-4cf0-b4f9-90088a89031e",
            "z" => 14,
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(
              "in"
            ),
            "outPorts" => array(),
            "size" => array(
              "width" => 250,
              "height" => 200
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => -31,
                      "y" => 40
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#c73576",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => "passive",
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "branch"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                ),
                "out" => array(
                  "position" => array(
                    "name" => "right"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                )
              ),
              "items" => array(
                array(
                  "id" => "in",
                  "group" => "in",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "in"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 1165,
              "y" => 40
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><path class=\"icon\" d=\"M592 0h-96c-26.51 0-48 21.49-48 48v32H192V48c0-26.51-21.49-48-48-48H48C21.49 0 0 21.49 0 48v96c0 26.51 21.49 48 48 48h94.86l88.76 150.21c-4.77 7.46-7.63 16.27-7.63 25.79v96c0 26.51 21.49 48 48 48h96c26.51 0 48-21.49 48-48v-96c0-26.51-21.49-48-48-48h-96c-5.2 0-10.11 1.04-14.8 2.57l-83.43-141.18C184.8 172.59 192 159.2 192 144v-32h256v32c0 26.51 21.49 48 48 48h96c26.51 0 48-21.49 48-48V48c0-26.51-21.49-48-48-48zM32 144V48c0-8.82 7.18-16 16-16h96c8.82 0 16 7.18 16 16v96c0 8.82-7.18 16-16 16H48c-8.82 0-16-7.18-16-16zm336 208c8.82 0 16 7.18 16 16v96c0 8.82-7.18 16-16 16h-96c-8.82 0-16-7.18-16-16v-96c0-8.82 7.18-16 16-16h96zm240-208c0 8.82-7.18 16-16 16h-96c-8.82 0-16-7.18-16-16V48c0-8.82 7.18-16 16-16h96c8.82 0 16 7.18 16 16v96z\"></path><text class=\"label\"/>",
            "id" => "d837f4fa-338e-4431-b8dc-5f8962d0b75c",
            "embeds" => array(
              "e979e907-4706-49be-857e-b404932f344a",
              "6e4300c1-fd2d-490d-8a06-0986a1c49964",
              "648eca8b-0d1f-49a2-af75-a3c4d512a4af"
            ),
            "z" => 15,
            "attrs" => array(
              ".label" => array(
                "text" => "基本を覚える：メニュー...",
                "font-size" => "14px",
                "fill" => "#fff",
                "font-weight" => "bold",
                "y" => 12
              ),
              ".body" => array(
                "stroke" => false,
                "fill" => "#c73576",
                "rx" => 5,
                "ry" => 5
              ),
              ".icon" => array(
                "transform" => "scale(0.035) translate(200, 250)"
              ),
              ".inCover" => array(
                "fill" => "#c73576",
                "height" => 33,
                "width" => 2,
                "x" => -2,
                "y" => 40
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "branch"
              ),
              "actionParam" => array(
                "nodeName" => "基本を覚える：メニューの作り方",
                "text" => "メニューは「分岐」ノードを利用し、作成します。\n\n分岐で更に詳細なメニューに遷移させるような組み合わせも可能ですし、選択肢に「メニューに戻る」を用意し「ジャンプ」ノードを用意して選択肢と線で繋ぐ事で簡単にメニューに戻る動作を設定することができます。\n\n選択肢と選択肢の間に「発言内容」を入れる事もできるので、区切り等を表現することもできます。",
                "btnType" => "1",
                "selection" => array(
                  array(
                    "type" => "2",
                    "value" => "------------------------------------------------"
                  ),
                  array(
                    "type" => "1",
                    "value" => "メニューに戻る"
                  )
                ),
                "customizeDesign" => array(
                  "isCustomize" => false,
                  "radioStyle" => "1",
                  "radioEntireBackgroundColor" => "#D5E682",
                  "radioEntireActiveColor" => "#ABCD05",
                  "radioTextColor" => "#333333",
                  "radioActiveTextColor" => "#333333",
                  "radioSelectionDistance" => 4,
                  "radioBackgroundColor" => "#FFFFFF",
                  "radioActiveColor" => "#ABCD05",
                  "radioBorderColor" => "#ABCD05",
                  "radioNoneBorder" => false
                )
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 1170,
              "y" => 75
            ),
            "size" => array(
              "width" => 240,
              "height" => 70
            ),
            "angle" => 0,
            "id" => "6e4300c1-fd2d-490d-8a06-0986a1c49964",
            "z" => 16,
            "parent" => "d837f4fa-338e-4431-b8dc-5f8962d0b75c",
            "attrs" => array(
              "rect" => array(
                "fill" => "#FFFFFF",
                "stroke" => false,
                "rx" => 3,
                "ry" => 3
              ),
              "text" => array(
                "text" => "メニューは「分岐」ノードを利用し\n、作成します。\n",
                "font-size" => "14px",
                "y" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childViewNode",
                "tooltip" => "メニューは「分岐」ノードを利用し、作成します。\n\n分岐で更に詳細なメニューに遷移させるような組み合わせも可能ですし、選択肢に「メニューに戻る」を用意し「ジャンプ」ノードを用意して選択肢と線で繋ぐ事で簡単にメニューに戻る動作を設定することができます。\n\n選択肢と選択肢の間に「発言内容」を入れる事もできるので、区切り等を表現することもできます。"
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "260cc4d3-d816-47c3-830f-23e62092549e",
              "port" => "out"
            ),
            "target" => array(
              "id" => "d837f4fa-338e-4431-b8dc-5f8962d0b75c",
              "port" => "in"
            ),
            "id" => "b128f3dc-9655-4612-bcf7-72cded51fda3",
            "z" => 17,
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(),
            "outPorts" => array(
              "out"
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "left"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 235,
                      "y" => 3
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#DD82AB",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "fill-opacity" => "0.9",
                      "height" => 30,
                      "width" => 30,
                      "rx" => 3,
                      "ry" => 3
                    ),
                    "type" => "branch"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 4,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 1170,
              "y" => 185
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><text class=\"label\"/><rect class=\"cover_top\"/><rect class=\"cover_bottom\"/>",
            "id" => "e979e907-4706-49be-857e-b404932f344a",
            "parent" => "d837f4fa-338e-4431-b8dc-5f8962d0b75c",
            "z" => 19,
            "attrs" => array(
              ".label" => array(
                "text" => "メニューに戻る",
                "font-size" => "14px",
                "ref-width" => "70%",
                "y" => 12
              ),
              "text" => array(
                "text" => "メニューに戻る",
                "ref-width" => "70%",
                "font-size" => "14px",
                "fill" => "#000",
                "y" => 12
              ),
              "rect.body" => array(
                "fill" => "#FFF",
                "stroke" => false,
                "rx" => 10,
                "ry" => 10
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childPortNode",
                "nextNode" => "",
                "tooltip" => "メニューに戻る",
                "nextNodeId" => "250bfb04-432e-4039-ae48-b18bd114e7b3"
              ),
              ".cover_top" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "fill-opacity" => 0
              ),
              ".cover_bottom" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "transform" => "translate(0 26)",
                "fill-opacity" => 0
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(
              "in"
            ),
            "outPorts" => array(),
            "size" => array(
              "width" => 250,
              "height" => 76
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => -31,
                      "y" => 23
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#c8d627",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => "passive",
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "jump"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                ),
                "out" => array(
                  "position" => array(
                    "name" => "right"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                )
              ),
              "items" => array(
                array(
                  "id" => "in",
                  "group" => "in",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "in"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 1520,
              "y" => 450
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><path class=\"icon\" d=\"M0 80v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48H48C21.5 32 0 53.5 0 80zm400-16c8.8 0 16 7.2 16 16v352c0 8.8-7.2 16-16 16H48c-8.8 0-16-7.2-16-16V80c0-8.8 7.2-16 16-16h352zm-208 64v64H88c-13.2 0-24 10.8-24 24v80c0 13.2 10.8 24 24 24h104v64c0 28.4 34.5 42.8 54.6 22.6l128-128c12.5-12.5 12.5-32.8 0-45.3l-128-128c-20.1-20-54.6-5.8-54.6 22.7zm160 128L224 384v-96H96v-64h128v-96l128 128z\"></path><text class=\"label\"/>",
            "id" => "250bfb04-432e-4039-ae48-b18bd114e7b3",
            "embeds" => array(
              "5dafa364-d962-422c-af5b-34366a28ecce"
            ),
            "z" => 20,
            "attrs" => array(
              ".label" => array(
                "text" => "ジャンプ",
                "font-size" => "14px",
                "fill" => "#fff",
                "font-weight" => "bold",
                "y" => 12
              ),
              ".body" => array(
                "stroke" => false,
                "fill" => "#c8d627",
                "rx" => 5,
                "ry" => 5
              ),
              ".icon" => array(
                "transform" => "scale(0.04) translate(150, 150)"
              ),
              ".inCover" => array(
                "fill" => "#c8d627",
                "height" => 33,
                "width" => 2,
                "x" => -2,
                "y" => 23
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "jump"
              ),
              "actionParam" => array(
                "targetId" => "b938dc97-5042-4c97-b5a7-c319538362be"
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 1525,
              "y" => 485
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "angle" => 0,
            "id" => "5dafa364-d962-422c-af5b-34366a28ecce",
            "z" => 21,
            "parent" => "250bfb04-432e-4039-ae48-b18bd114e7b3",
            "attrs" => array(
              "rect" => array(
                "fill" => "#FFFFFF",
                "stroke" => false,
                "rx" => 3,
                "ry" => 3
              ),
              "text" => array(
                "text" => "メインメニュー",
                "font-size" => "14px",
                "y" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childViewNode",
                "tooltip" => "メインメニュー"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(
              "in"
            ),
            "outPorts" => array(),
            "size" => array(
              "width" => 250,
              "height" => 270
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => -31,
                      "y" => 40
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#c73576",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => "passive",
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "branch"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                ),
                "out" => array(
                  "position" => array(
                    "name" => "right"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                )
              ),
              "items" => array(
                array(
                  "id" => "in",
                  "group" => "in",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "in"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 780,
              "y" => 165
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><path class=\"icon\" d=\"M592 0h-96c-26.51 0-48 21.49-48 48v32H192V48c0-26.51-21.49-48-48-48H48C21.49 0 0 21.49 0 48v96c0 26.51 21.49 48 48 48h94.86l88.76 150.21c-4.77 7.46-7.63 16.27-7.63 25.79v96c0 26.51 21.49 48 48 48h96c26.51 0 48-21.49 48-48v-96c0-26.51-21.49-48-48-48h-96c-5.2 0-10.11 1.04-14.8 2.57l-83.43-141.18C184.8 172.59 192 159.2 192 144v-32h256v32c0 26.51 21.49 48 48 48h96c26.51 0 48-21.49 48-48V48c0-26.51-21.49-48-48-48zM32 144V48c0-8.82 7.18-16 16-16h96c8.82 0 16 7.18 16 16v96c0 8.82-7.18 16-16 16H48c-8.82 0-16-7.18-16-16zm336 208c8.82 0 16 7.18 16 16v96c0 8.82-7.18 16-16 16h-96c-8.82 0-16-7.18-16-16v-96c0-8.82 7.18-16 16-16h96zm240-208c0 8.82-7.18 16-16 16h-96c-8.82 0-16-7.18-16-16V48c0-8.82 7.18-16 16-16h96c8.82 0 16 7.18 16 16v96z\"></path><text class=\"label\"/>",
            "id" => "e6ddcffa-918c-401a-bd36-ecca27d17132",
            "embeds" => array(
              "bdf6cd9a-dc7b-4293-b9c6-5847c0fbe29f",
              "2e0b3f9a-e84e-4bbd-b56c-81312dc55284",
              "9f5c2074-ca6f-4826-87d0-4b859f68381c",
              "68aa8e35-7d03-4029-8a18-afb87023c501",
              "1fc092f3-65d4-49b4-9bb7-2743b0498b80"
            ),
            "z" => 23,
            "attrs" => array(
              ".label" => array(
                "text" => "画像を使ったサンプル",
                "font-size" => "14px",
                "fill" => "#fff",
                "font-weight" => "bold",
                "y" => 12
              ),
              ".body" => array(
                "stroke" => false,
                "fill" => "#c73576",
                "rx" => 5,
                "ry" => 5
              ),
              ".icon" => array(
                "transform" => "scale(0.035) translate(200, 250)"
              ),
              ".inCover" => array(
                "fill" => "#c73576",
                "height" => 33,
                "width" => 2,
                "x" => -2,
                "y" => 40
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "branch"
              ),
              "actionParam" => array(
                "nodeName" => "画像を使ったサンプル",
                "text" => "（画像を使った設定サンプルです。）\n\nsinclo（シンクロ）はコンタクトセンターシステムメーカーであるメディアリンクが長年培った技術力とノウハウを活かした100%自社開発（国産）のチャットボットツール（特許取得済み）です。\n<img src=\"https://sinclo.medialink-ml.co.jp/lp/images/index/features_photo01.jpg\" alt=\"sinclo（シンクロ）\" style=\"display:block;margin-left:auto;margin-right:auto;width:250px;height:auto;margin-top:10px;margin-bottom:10px\">\n「売上にインパクトを与えるコミュニケーションのあり方」を熟知している当社だからこそ、本当に効果のあるチャットボットツールを自信をもってご提供いたします。",
                "btnType" => "1",
                "selection" => array(
                  array(
                    "type" => "1",
                    "value" => "リンクを使った設定サンプル"
                  ),
                  array(
                    "type" => "1",
                    "value" => "資料請求のサンプル"
                  ),
                  array(
                    "type" => "2",
                    "value" => "------------------------------------------------"
                  ),
                  array(
                    "type" => "1",
                    "value" => "メニューに戻る"
                  )
                ),
                "customizeDesign" => array(
                  "isCustomize" => false,
                  "radioStyle" => "1",
                  "radioEntireBackgroundColor" => "#D5E682",
                  "radioEntireActiveColor" => "#ABCD05",
                  "radioTextColor" => "#333333",
                  "radioActiveTextColor" => "#333333",
                  "radioSelectionDistance" => 4,
                  "radioBackgroundColor" => "#FFFFFF",
                  "radioActiveColor" => "#ABCD05",
                  "radioBorderColor" => "#ABCD05",
                  "radioNoneBorder" => false
                )
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 785,
              "y" => 200
            ),
            "size" => array(
              "width" => 240,
              "height" => 70
            ),
            "angle" => 0,
            "id" => "68aa8e35-7d03-4029-8a18-afb87023c501",
            "z" => 24,
            "parent" => "e6ddcffa-918c-401a-bd36-ecca27d17132",
            "attrs" => array(
              "rect" => array(
                "fill" => "#FFFFFF",
                "stroke" => false,
                "rx" => 3,
                "ry" => 3
              ),
              "text" => array(
                "text" => "（画像を使った設定サンプルです。\n）\n",
                "font-size" => "14px",
                "y" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childViewNode",
                "tooltip" => "（画像を使った設定サンプルです。）\n\nsinclo（シンクロ）はコンタクトセンターシステムメーカーであるメディアリンクが長年培った技術力とノウハウを活かした100%自社開発（国産）のチャットボットツール（特許取得済み）です。\n<img src=\"https://sinclo.medialink-ml.co.jp/lp/images/index/features_photo01.jpg\" alt=\"sinclo（シンクロ）\" style=\"display:block;margin-left:auto;margin-right:auto;width:250px;height:auto;margin-top:10px;margin-bottom:10px\">\n「売上にインパクトを与えるコミュニケーションのあり方」を熟知している当社だからこそ、本当に効果のあるチャットボットツールを自信をもってご提供いたします。"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(),
            "outPorts" => array(
              "out"
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "left"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 235,
                      "y" => 3
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#DD82AB",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "fill-opacity" => "0.9",
                      "height" => 30,
                      "width" => 30,
                      "rx" => 3,
                      "ry" => 3
                    ),
                    "type" => "branch"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 4,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 785,
              "y" => 280
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><text class=\"label\"/><rect class=\"cover_top\"/><rect class=\"cover_bottom\"/>",
            "id" => "bdf6cd9a-dc7b-4293-b9c6-5847c0fbe29f",
            "parent" => "e6ddcffa-918c-401a-bd36-ecca27d17132",
            "z" => 25,
            "attrs" => array(
              ".label" => array(
                "text" => "リンクを使った設定サン...",
                "font-size" => "14px",
                "ref-width" => "70%",
                "y" => 12
              ),
              "text" => array(
                "text" => "リンクを使った設定サン...",
                "ref-width" => "70%",
                "font-size" => "14px",
                "fill" => "#000",
                "y" => 12
              ),
              "rect.body" => array(
                "fill" => "#FFF",
                "stroke" => false,
                "rx" => 10,
                "ry" => 10
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childPortNode",
                "nextNode" => "",
                "tooltip" => "リンクを使った設定サンプル",
                "nextNodeId" => "290f3354-76b8-46ed-bd97-3776db8b41d9"
              ),
              ".cover_top" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "fill-opacity" => 0
              ),
              ".cover_bottom" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "transform" => "translate(0 26)",
                "fill-opacity" => 1
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(),
            "outPorts" => array(
              "out"
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "left"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 235,
                      "y" => 3
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#DD82AB",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "fill-opacity" => "0.9",
                      "height" => 30,
                      "width" => 30,
                      "rx" => 3,
                      "ry" => 3
                    ),
                    "type" => "branch"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 4,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 785,
              "y" => 320
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><text class=\"label\"/><rect class=\"cover_top\"/><rect class=\"cover_bottom\"/>",
            "id" => "2e0b3f9a-e84e-4bbd-b56c-81312dc55284",
            "parent" => "e6ddcffa-918c-401a-bd36-ecca27d17132",
            "z" => 26,
            "attrs" => array(
              ".label" => array(
                "text" => "資料請求のサンプル",
                "font-size" => "14px",
                "ref-width" => "70%",
                "y" => 12
              ),
              "text" => array(
                "text" => "資料請求のサンプル",
                "ref-width" => "70%",
                "font-size" => "14px",
                "fill" => "#000",
                "y" => 12
              ),
              "rect.body" => array(
                "fill" => "#FFF",
                "stroke" => false,
                "rx" => 10,
                "ry" => 10
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childPortNode",
                "nextNode" => "",
                "tooltip" => "資料請求のサンプル",
                "nextNodeId" => "b1ce3e27-3612-4a59-a48e-e4417f3a771e"
              ),
              ".cover_top" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "fill-opacity" => 1
              ),
              ".cover_bottom" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "transform" => "translate(0 26)",
                "fill-opacity" => 0
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(),
            "outPorts" => array(
              "out"
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "left"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 235,
                      "y" => 3
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#DD82AB",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "fill-opacity" => "0.9",
                      "height" => 30,
                      "width" => 30,
                      "rx" => 3,
                      "ry" => 3
                    ),
                    "type" => "branch"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 4,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 785,
              "y" => 390
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><text class=\"label\"/><rect class=\"cover_top\"/><rect class=\"cover_bottom\"/>",
            "id" => "9f5c2074-ca6f-4826-87d0-4b859f68381c",
            "parent" => "e6ddcffa-918c-401a-bd36-ecca27d17132",
            "z" => 28,
            "attrs" => array(
              ".label" => array(
                "text" => "メニューに戻る",
                "font-size" => "14px",
                "ref-width" => "70%",
                "y" => 12
              ),
              "text" => array(
                "text" => "メニューに戻る",
                "ref-width" => "70%",
                "font-size" => "14px",
                "fill" => "#000",
                "y" => 12
              ),
              "rect.body" => array(
                "fill" => "#FFF",
                "stroke" => false,
                "rx" => 10,
                "ry" => 10
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childPortNode",
                "nextNode" => "",
                "tooltip" => "メニューに戻る",
                "nextNodeId" => "250bfb04-432e-4039-ae48-b18bd114e7b3"
              ),
              ".cover_top" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "fill-opacity" => 0
              ),
              ".cover_bottom" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "transform" => "translate(0 26)",
                "fill-opacity" => 0
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "5b6b3bb9-d4f3-4bab-b4b4-f22a4d4837d2",
              "port" => "out"
            ),
            "target" => array(
              "id" => "e6ddcffa-918c-401a-bd36-ecca27d17132",
              "port" => "in"
            ),
            "id" => "f0364faf-4525-4779-b773-95101e29af86",
            "z" => 29,
            "vertices" => array(),
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(
              "in"
            ),
            "outPorts" => array(),
            "size" => array(
              "width" => 250,
              "height" => 200
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => -31,
                      "y" => 40
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#c73576",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => "passive",
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "branch"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                ),
                "out" => array(
                  "position" => array(
                    "name" => "right"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                )
              ),
              "items" => array(
                array(
                  "id" => "in",
                  "group" => "in",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "in"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 1165,
              "y" => 635
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><path class=\"icon\" d=\"M592 0h-96c-26.51 0-48 21.49-48 48v32H192V48c0-26.51-21.49-48-48-48H48C21.49 0 0 21.49 0 48v96c0 26.51 21.49 48 48 48h94.86l88.76 150.21c-4.77 7.46-7.63 16.27-7.63 25.79v96c0 26.51 21.49 48 48 48h96c26.51 0 48-21.49 48-48v-96c0-26.51-21.49-48-48-48h-96c-5.2 0-10.11 1.04-14.8 2.57l-83.43-141.18C184.8 172.59 192 159.2 192 144v-32h256v32c0 26.51 21.49 48 48 48h96c26.51 0 48-21.49 48-48V48c0-26.51-21.49-48-48-48zM32 144V48c0-8.82 7.18-16 16-16h96c8.82 0 16 7.18 16 16v96c0 8.82-7.18 16-16 16H48c-8.82 0-16-7.18-16-16zm336 208c8.82 0 16 7.18 16 16v96c0 8.82-7.18 16-16 16h-96c-8.82 0-16-7.18-16-16v-96c0-8.82 7.18-16 16-16h96zm240-208c0 8.82-7.18 16-16 16h-96c-8.82 0-16-7.18-16-16V48c0-8.82 7.18-16 16-16h96c8.82 0 16 7.18 16 16v96z\"></path><text class=\"label\"/>",
            "id" => "1a5cc34d-c31b-4205-9a1d-e8afc9ed927f",
            "embeds" => array(
              "543c4e3f-be30-4251-9775-5b6e51d4f6a1",
              "f865fa81-960b-4765-8482-241dba4500ad",
              "606401cd-10b3-40e2-92e6-9cb18a8bda80"
            ),
            "z" => 33,
            "attrs" => array(
              ".label" => array(
                "text" => "資料請求サンプル",
                "font-size" => "14px",
                "fill" => "#fff",
                "font-weight" => "bold",
                "y" => 12
              ),
              ".body" => array(
                "stroke" => false,
                "fill" => "#c73576",
                "rx" => 5,
                "ry" => 5
              ),
              ".icon" => array(
                "transform" => "scale(0.035) translate(200, 250)"
              ),
              ".inCover" => array(
                "fill" => "#c73576",
                "height" => 33,
                "width" => 2,
                "x" => -2,
                "y" => 40
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "branch"
              ),
              "actionParam" => array(
                "nodeName" => "資料請求サンプル",
                "text" => "（資料請求のサンプル）\n\n資料請求ですね。\n\nそれでは、こちらにお客様のご連絡先（会社名やお名前、メールアドレスなど）を入力していただきます。\n\nまずは入力方法を下記からお選びください。\n※普段お使いのメール署名をそのままコピー＆ペーストする場合は【一括入力】をお選びください。",
                "btnType" => "1",
                "selection" => array(
                  array(
                    "type" => "1",
                    "value" => "連絡先をまとめて入力する（一括入力）"
                  ),
                  array(
                    "type" => "1",
                    "value" => "連絡先を１つずつ入力する（個別入力）"
                  )
                ),
                "customizeDesign" => array(
                  "isCustomize" => false,
                  "radioStyle" => "1",
                  "radioEntireBackgroundColor" => "#D5E682",
                  "radioEntireActiveColor" => "#ABCD05",
                  "radioTextColor" => "#333333",
                  "radioActiveTextColor" => "#333333",
                  "radioSelectionDistance" => 4,
                  "radioBackgroundColor" => "#FFFFFF",
                  "radioActiveColor" => "#ABCD05",
                  "radioBorderColor" => "#ABCD05",
                  "radioNoneBorder" => false
                )
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 1170,
              "y" => 670
            ),
            "size" => array(
              "width" => 240,
              "height" => 70
            ),
            "angle" => 0,
            "id" => "606401cd-10b3-40e2-92e6-9cb18a8bda80",
            "z" => 34,
            "parent" => "1a5cc34d-c31b-4205-9a1d-e8afc9ed927f",
            "attrs" => array(
              "rect" => array(
                "fill" => "#FFFFFF",
                "stroke" => false,
                "rx" => 3,
                "ry" => 3
              ),
              "text" => array(
                "text" => "（資料請求のサンプル）\n\n資料請求ですね。",
                "font-size" => "14px",
                "y" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childViewNode",
                "tooltip" => "（資料請求のサンプル）\n\n資料請求ですね。\n\nそれでは、こちらにお客様のご連絡先（会社名やお名前、メールアドレスなど）を入力していただきます。\n\nまずは入力方法を下記からお選びください。\n※普段お使いのメール署名をそのままコピー＆ペーストする場合は【一括入力】をお選びください。"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(
              "in"
            ),
            "outPorts" => array(
              "out"
            ),
            "size" => array(
              "width" => 250,
              "height" => 110
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => -31,
                      "y" => 40
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#D48BB3",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => "passive",
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "text"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 248,
                      "y" => 40
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#EFD6E4",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "text"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "in",
                  "group" => "in",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "in"
                    )
                  )
                ),
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 785,
              "y" => 660
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><path class=\"icon\" d=\"M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 7.1 5.8 12 12 12 2.4 0 4.9-.7 7.1-2.4L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64zm32 352c0 17.6-14.4 32-32 32H293.3l-8.5 6.4L192 460v-76H64c-17.6 0-32-14.4-32-32V64c0-17.6 14.4-32 32-32h384c17.6 0 32 14.4 32 32v288z\"></path><text class=\"label\"/>",
            "id" => "2424f263-4685-4c7d-b43b-265b2e290042",
            "embeds" => array(
              "4069bdc7-47ec-4d0f-9ed6-5de573bf63c4"
            ),
            "z" => 35,
            "attrs" => array(
              ".label" => array(
                "text" => "資料請求サンプル説明",
                "font-size" => "14px",
                "fill" => "#fff",
                "font-weight" => "bold",
                "y" => 12
              ),
              ".body" => array(
                "stroke" => false,
                "fill" => "#D48BB3",
                "rx" => 5,
                "ry" => 5
              ),
              ".icon" => array(
                "transform" => "scale(0.035) translate(200, 250)"
              ),
              ".inCover" => array(
                "fill" => "#D48BB3",
                "stroke" => false,
                "height" => 33,
                "width" => 2,
                "x" => -2,
                "y" => 40
              ),
              ".outCover" => array(
                "fill" => "#D48BB3",
                "stroke" => false,
                "height" => 33,
                "width" => 2,
                "x" => 250,
                "y" => 40
              ),
              "actionParam" => array(
                "nodeName" => "資料請求サンプル説明",
                "text" => array(
                  "サイト訪問者から連絡先をヒアリングする場合のサンプルです。ここでは「資料請求」を設定したい場合のサンプルを用意しています。\n\nサイト訪問者から連絡先やリード情報を獲得するためのフローを作成するには「シナリオ」機能を利用します。\n\n（シナリオ機能の詳細は「チャットボット設定」「シナリオ設定」のサンプルをご覧下さい）\n\nチャットツリーでは設定したシナリオを呼び出すことも簡単に設定できます。"
                )
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "text",
                "nextNodeId" => "1a5cc34d-c31b-4205-9a1d-e8afc9ed927f"
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 790,
              "y" => 695
            ),
            "size" => array(
              "width" => 240,
              "height" => 70
            ),
            "angle" => 0,
            "id" => "4069bdc7-47ec-4d0f-9ed6-5de573bf63c4",
            "z" => 36,
            "parent" => "2424f263-4685-4c7d-b43b-265b2e290042",
            "attrs" => array(
              "rect" => array(
                "fill" => "#FFFFFF",
                "stroke" => false,
                "rx" => 3,
                "ry" => 3
              ),
              "text" => array(
                "text" => "サイト訪問者から連絡先をヒアリン\nグする場合のサンプルです。ここで\nは「資料請求」を設定したい場合...",
                "font-size" => "14px",
                "y" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childViewNode",
                "tooltip" => "サイト訪問者から連絡先をヒアリングする場合のサンプルです。ここでは「資料請求」を設定したい場合のサンプルを用意しています。\n\nサイト訪問者から連絡先やリード情報を獲得するためのフローを作成するには「シナリオ」機能を利用します。\n\n（シナリオ機能の詳細は「チャットボット設定」「シナリオ設定」のサンプルをご覧下さい）\n\nチャットツリーでは設定したシナリオを呼び出すことも簡単に設定できます。"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(),
            "outPorts" => array(
              "out"
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "left"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 235,
                      "y" => 3
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#DD82AB",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "fill-opacity" => "0.9",
                      "height" => 30,
                      "width" => 30,
                      "rx" => 3,
                      "ry" => 3
                    ),
                    "type" => "branch"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 4,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 1170,
              "y" => 750
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><text class=\"label\"/><rect class=\"cover_top\"/><rect class=\"cover_bottom\"/>",
            "id" => "543c4e3f-be30-4251-9775-5b6e51d4f6a1",
            "parent" => "1a5cc34d-c31b-4205-9a1d-e8afc9ed927f",
            "z" => 37,
            "attrs" => array(
              ".label" => array(
                "text" => "連絡先をまとめて入力す...",
                "font-size" => "14px",
                "ref-width" => "70%",
                "y" => 12
              ),
              "text" => array(
                "text" => "連絡先をまとめて入力す...",
                "ref-width" => "70%",
                "font-size" => "14px",
                "fill" => "#000",
                "y" => 12
              ),
              "rect.body" => array(
                "fill" => "#FFF",
                "stroke" => false,
                "rx" => 10,
                "ry" => 10
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childPortNode",
                "nextNode" => "",
                "tooltip" => "連絡先をまとめて入力する（一括入力）",
                "nextNodeId" => "bb8515ea-6dc0-46ee-84f4-a938620e3933"
              ),
              ".cover_top" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "fill-opacity" => "0"
              ),
              ".cover_bottom" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "transform" => "translate(0 26)",
                "fill-opacity" => "1"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(),
            "outPorts" => array(
              "out"
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "left"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 235,
                      "y" => 3
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#DD82AB",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "fill-opacity" => "0.9",
                      "height" => 30,
                      "width" => 30,
                      "rx" => 3,
                      "ry" => 3
                    ),
                    "type" => "branch"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 4,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 1170,
              "y" => 790
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><text class=\"label\"/><rect class=\"cover_top\"/><rect class=\"cover_bottom\"/>",
            "id" => "f865fa81-960b-4765-8482-241dba4500ad",
            "parent" => "1a5cc34d-c31b-4205-9a1d-e8afc9ed927f",
            "z" => 38,
            "attrs" => array(
              ".label" => array(
                "text" => "連絡先を１つずつ入力す...",
                "font-size" => "14px",
                "ref-width" => "70%",
                "y" => 12
              ),
              "text" => array(
                "text" => "連絡先を１つずつ入力す...",
                "ref-width" => "70%",
                "font-size" => "14px",
                "fill" => "#000",
                "y" => 12
              ),
              "rect.body" => array(
                "fill" => "#FFF",
                "stroke" => false,
                "rx" => 10,
                "ry" => 10
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childPortNode",
                "nextNode" => "",
                "tooltip" => "連絡先を１つずつ入力する（個別入力）",
                "nextNodeId" => "a74a249f-9f7b-44dd-9e93-c6e1915d305c"
              ),
              ".cover_top" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "fill-opacity" => "1"
              ),
              ".cover_bottom" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "transform" => "translate(0 26)",
                "fill-opacity" => "0"
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "d73b1efc-bdf2-40bb-8efa-b89d6f61e276",
              "port" => "out"
            ),
            "target" => array(
              "id" => "2424f263-4685-4c7d-b43b-265b2e290042",
              "port" => "in"
            ),
            "id" => "c971e29f-77fd-418f-91de-9dfcc14bc539",
            "z" => 39,
            "vertices" => array(),
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(
              "in"
            ),
            "outPorts" => array(),
            "size" => array(
              "width" => 250,
              "height" => 76
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => -31,
                      "y" => 23
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#c8d627",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => "passive",
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "jump"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                ),
                "out" => array(
                  "position" => array(
                    "name" => "right"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                )
              ),
              "items" => array(
                array(
                  "id" => "in",
                  "group" => "in",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "in"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 1165,
              "y" => 255
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><path class=\"icon\" d=\"M0 80v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48H48C21.5 32 0 53.5 0 80zm400-16c8.8 0 16 7.2 16 16v352c0 8.8-7.2 16-16 16H48c-8.8 0-16-7.2-16-16V80c0-8.8 7.2-16 16-16h352zm-208 64v64H88c-13.2 0-24 10.8-24 24v80c0 13.2 10.8 24 24 24h104v64c0 28.4 34.5 42.8 54.6 22.6l128-128c12.5-12.5 12.5-32.8 0-45.3l-128-128c-20.1-20-54.6-5.8-54.6 22.7zm160 128L224 384v-96H96v-64h128v-96l128 128z\"></path><text class=\"label\"/>",
            "id" => "290f3354-76b8-46ed-bd97-3776db8b41d9",
            "embeds" => array(
              "4d5018bd-b1d5-4738-8889-ebff5a8f8f7d"
            ),
            "z" => 41,
            "attrs" => array(
              ".label" => array(
                "text" => "ジャンプ",
                "font-size" => "14px",
                "fill" => "#fff",
                "font-weight" => "bold",
                "y" => 12
              ),
              ".body" => array(
                "stroke" => false,
                "fill" => "#c8d627",
                "rx" => 5,
                "ry" => 5
              ),
              ".icon" => array(
                "transform" => "scale(0.04) translate(150, 150)"
              ),
              ".inCover" => array(
                "fill" => "#c8d627",
                "height" => 33,
                "width" => 2,
                "x" => -2,
                "y" => 23
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "jump"
              ),
              "actionParam" => array(
                "targetId" => "fd060639-35ff-42e3-ad6a-6db1bb43c835"
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 1170,
              "y" => 290
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "angle" => 0,
            "id" => "4d5018bd-b1d5-4738-8889-ebff5a8f8f7d",
            "z" => 42,
            "parent" => "290f3354-76b8-46ed-bd97-3776db8b41d9",
            "attrs" => array(
              "rect" => array(
                "fill" => "#FFFFFF",
                "stroke" => false,
                "rx" => 3,
                "ry" => 3
              ),
              "text" => array(
                "text" => "リンクを使った...",
                "font-size" => "14px",
                "y" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childViewNode",
                "tooltip" => "リンクを使った設定サンプル"
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "bdf6cd9a-dc7b-4293-b9c6-5847c0fbe29f",
              "port" => "out"
            ),
            "target" => array(
              "id" => "290f3354-76b8-46ed-bd97-3776db8b41d9",
              "port" => "in"
            ),
            "id" => "50dcc20b-0146-4535-b8ae-f753f66240a3",
            "z" => 43,
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "9f5c2074-ca6f-4826-87d0-4b859f68381c",
              "port" => "out"
            ),
            "target" => array(
              "id" => "250bfb04-432e-4039-ae48-b18bd114e7b3",
              "port" => "in"
            ),
            "id" => "6c4a9c80-e9fb-4089-85d3-f48c458328f4",
            "z" => 44,
            "vertices" => array(),
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(
              "in"
            ),
            "outPorts" => array(),
            "size" => array(
              "width" => 250,
              "height" => 76
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => -31,
                      "y" => 23
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#c8d627",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => "passive",
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "jump"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                ),
                "out" => array(
                  "position" => array(
                    "name" => "right"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                )
              ),
              "items" => array(
                array(
                  "id" => "in",
                  "group" => "in",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "in"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 1165,
              "y" => 340
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><path class=\"icon\" d=\"M0 80v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48H48C21.5 32 0 53.5 0 80zm400-16c8.8 0 16 7.2 16 16v352c0 8.8-7.2 16-16 16H48c-8.8 0-16-7.2-16-16V80c0-8.8 7.2-16 16-16h352zm-208 64v64H88c-13.2 0-24 10.8-24 24v80c0 13.2 10.8 24 24 24h104v64c0 28.4 34.5 42.8 54.6 22.6l128-128c12.5-12.5 12.5-32.8 0-45.3l-128-128c-20.1-20-54.6-5.8-54.6 22.7zm160 128L224 384v-96H96v-64h128v-96l128 128z\"></path><text class=\"label\"/>",
            "id" => "b1ce3e27-3612-4a59-a48e-e4417f3a771e",
            "embeds" => array(
              "f56fee07-0be1-4bd8-b5f4-c4376f6450b4"
            ),
            "z" => 45,
            "attrs" => array(
              ".label" => array(
                "text" => "ジャンプ",
                "font-size" => "14px",
                "fill" => "#fff",
                "font-weight" => "bold",
                "y" => 12
              ),
              ".body" => array(
                "stroke" => false,
                "fill" => "#c8d627",
                "rx" => 5,
                "ry" => 5
              ),
              ".icon" => array(
                "transform" => "scale(0.04) translate(150, 150)"
              ),
              ".inCover" => array(
                "fill" => "#c8d627",
                "height" => 33,
                "width" => 2,
                "x" => -2,
                "y" => 23
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "jump"
              ),
              "actionParam" => array(
                "targetId" => "2424f263-4685-4c7d-b43b-265b2e290042"
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 1170,
              "y" => 375
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "angle" => 0,
            "id" => "f56fee07-0be1-4bd8-b5f4-c4376f6450b4",
            "z" => 46,
            "parent" => "b1ce3e27-3612-4a59-a48e-e4417f3a771e",
            "attrs" => array(
              "rect" => array(
                "fill" => "#FFFFFF",
                "stroke" => false,
                "rx" => 3,
                "ry" => 3
              ),
              "text" => array(
                "text" => "資料請求サンプル説明",
                "font-size" => "14px",
                "y" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childViewNode",
                "tooltip" => "資料請求サンプル説明"
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "2e0b3f9a-e84e-4bbd-b56c-81312dc55284",
              "port" => "out"
            ),
            "target" => array(
              "id" => "b1ce3e27-3612-4a59-a48e-e4417f3a771e",
              "port" => "in"
            ),
            "id" => "bdce364b-0e6c-446e-9496-cf21402e2367",
            "z" => 47,
            "vertices" => array(),
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "2424f263-4685-4c7d-b43b-265b2e290042",
              "port" => "out"
            ),
            "target" => array(
              "id" => "1a5cc34d-c31b-4205-9a1d-e8afc9ed927f",
              "port" => "in"
            ),
            "id" => "ae1a15bc-42f2-497a-9ad9-40e84049ca34",
            "z" => 48,
            "vertices" => array(),
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(
              "in"
            ),
            "outPorts" => array(
              "out",
              "out",
              "out"
            ),
            "size" => array(
              "width" => 250,
              "height" => 76
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => -31,
                      "y" => 23
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#82c0cd",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => "passive",
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "scenario"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 248,
                      "y" => 23
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#C8E3E8",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "scenario"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "in",
                  "group" => "in",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "in"
                    )
                  )
                ),
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 1520,
              "y" => 680
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><path class=\"icon\" d=\"M160 416h64v-32h-64v32zm32-192c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm192 0c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm160 0h-32v-32c0-53-43-96-96-96H304V16c0-8.8-7.2-16-16-16s-16 7.2-16 16v80H160c-53 0-96 43-96 96v32H32c-17.7 0-32 14.3-32 32v128c0 17.7 14.3 32 32 32h32v32c0 35.3 28.7 64 64 64h320c35.3 0 64-28.7 64-64v-32h32c17.7 0 32-14.3 32-32V256c0-17.7-14.3-32-32-32zM64 384H32V256h32v128zm416 64c0 17.6-14.4 32-32 32H128c-17.6 0-32-14.4-32-32V192c0-35.3 28.7-64 64-64h256c35.3 0 64 28.7 64 64v256zm64-64h-32V256h32v128zm-192 32h64v-32h-64v32zm-96 0h64v-32h-64v32z\"></path><text class=\"label\"/>",
            "id" => "bb8515ea-6dc0-46ee-84f4-a938620e3933",
            "embeds" => array(
              "9e1e0f35-8194-4354-86bd-02128f5e4517"
            ),
            "z" => 49,
            "attrs" => array(
              ".label" => array(
                "text" => "シナリオ呼出",
                "font-size" => "14px",
                "fill" => "#fff",
                "font-weight" => "bold",
                "y" => 12
              ),
              ".body" => array(
                "stroke" => false,
                "fill" => "#82c0cd",
                "rx" => 5,
                "ry" => 5
              ),
              ".icon" => array(
                "transform" => "scale(0.04) translate(150, 150)"
              ),
              ".inCover" => array(
                "fill" => "#82c0cd",
                "height" => 33,
                "width" => 2,
                "x" => -2,
                "y" => 23
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "scenario",
                "nextNodeId" => "b3026516-4383-472c-8824-f7e3a8810887"
              ),
              "actionParam" => array(
                "targetScenarioIndex" => 1,
                "callbackToDiagram" => true,
                "value" => "【サンプル】資料請求（一括ヒアリング）"
              ),
              ".outCover" => array(
                "fill" => "#82c0cd",
                "stroke" => false,
                "height" => 33,
                "width" => 2,
                "x" => 250,
                "y" => 40
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 1525,
              "y" => 715
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "angle" => 0,
            "id" => "9e1e0f35-8194-4354-86bd-02128f5e4517",
            "z" => 50,
            "parent" => "bb8515ea-6dc0-46ee-84f4-a938620e3933",
            "attrs" => array(
              "rect" => array(
                "fill" => "#FFFFFF",
                "stroke" => false,
                "rx" => 3,
                "ry" => 3
              ),
              "text" => array(
                "text" => "【サンプル】資料請求（一括ヒア...",
                "font-size" => "14px",
                "y" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childViewNode",
                "tooltip" => "【サンプル】資料請求（一括ヒアリング）"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(
              "in"
            ),
            "outPorts" => array(
              "out",
              "out",
              "out",
              "out",
              "out"
            ),
            "size" => array(
              "width" => 250,
              "height" => 76
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => -31,
                      "y" => 23
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#82c0cd",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => "passive",
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "scenario"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 248,
                      "y" => 23
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#C8E3E8",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "scenario"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "in",
                  "group" => "in",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "in"
                    )
                  )
                ),
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 1520,
              "y" => 770
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><path class=\"icon\" d=\"M160 416h64v-32h-64v32zm32-192c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm192 0c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm160 0h-32v-32c0-53-43-96-96-96H304V16c0-8.8-7.2-16-16-16s-16 7.2-16 16v80H160c-53 0-96 43-96 96v32H32c-17.7 0-32 14.3-32 32v128c0 17.7 14.3 32 32 32h32v32c0 35.3 28.7 64 64 64h320c35.3 0 64-28.7 64-64v-32h32c17.7 0 32-14.3 32-32V256c0-17.7-14.3-32-32-32zM64 384H32V256h32v128zm416 64c0 17.6-14.4 32-32 32H128c-17.6 0-32-14.4-32-32V192c0-35.3 28.7-64 64-64h256c35.3 0 64 28.7 64 64v256zm64-64h-32V256h32v128zm-192 32h64v-32h-64v32zm-96 0h64v-32h-64v32z\"></path><text class=\"label\"/>",
            "id" => "a74a249f-9f7b-44dd-9e93-c6e1915d305c",
            "embeds" => array(
              "1b724d17-c213-45c3-884c-9211b972bf43"
            ),
            "z" => 51,
            "attrs" => array(
              ".label" => array(
                "text" => "シナリオ呼出",
                "font-size" => "14px",
                "fill" => "#fff",
                "font-weight" => "bold",
                "y" => 12
              ),
              ".body" => array(
                "stroke" => false,
                "fill" => "#82c0cd",
                "rx" => 5,
                "ry" => 5
              ),
              ".icon" => array(
                "transform" => "scale(0.04) translate(150, 150)"
              ),
              ".inCover" => array(
                "fill" => "#82c0cd",
                "height" => 33,
                "width" => 2,
                "x" => -2,
                "y" => 23
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "scenario",
                "nextNodeId" => "b3026516-4383-472c-8824-f7e3a8810887"
              ),
              "actionParam" => array(
                "targetScenarioIndex" => 2,
                "callbackToDiagram" => true,
                "value" => "【サンプル】資料請求（個別ヒアリング）"
              ),
              ".outCover" => array(
                "fill" => "#82c0cd",
                "stroke" => false,
                "height" => 33,
                "width" => 2,
                "x" => 250,
                "y" => 40
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 1525,
              "y" => 805
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "angle" => 0,
            "id" => "1b724d17-c213-45c3-884c-9211b972bf43",
            "z" => 52,
            "parent" => "a74a249f-9f7b-44dd-9e93-c6e1915d305c",
            "attrs" => array(
              "rect" => array(
                "fill" => "#FFFFFF",
                "stroke" => false,
                "rx" => 3,
                "ry" => 3
              ),
              "text" => array(
                "text" => "【サンプル】資料請求（個別ヒア...",
                "font-size" => "14px",
                "y" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childViewNode",
                "tooltip" => "【サンプル】資料請求（個別ヒアリング）"
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "543c4e3f-be30-4251-9775-5b6e51d4f6a1",
              "port" => "out"
            ),
            "target" => array(
              "id" => "bb8515ea-6dc0-46ee-84f4-a938620e3933",
              "port" => "in"
            ),
            "id" => "e2c90822-357e-48ef-a605-8e9c9dfda035",
            "z" => 53,
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "f865fa81-960b-4765-8482-241dba4500ad",
              "port" => "out"
            ),
            "target" => array(
              "id" => "a74a249f-9f7b-44dd-9e93-c6e1915d305c",
              "port" => "in"
            ),
            "id" => "523b5730-9d5b-4417-804b-820c9cf6027c",
            "z" => 54,
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(
              "in"
            ),
            "outPorts" => array(
              "out",
              "out",
              "out",
              "out",
              "out",
              "out"
            ),
            "size" => array(
              "width" => 250,
              "height" => 76
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => -31,
                      "y" => 23
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#82c0cd",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => "passive",
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "scenario"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 248,
                      "y" => 23
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#C8E3E8",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "scenario"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "in",
                  "group" => "in",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "in"
                    )
                  )
                ),
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 785,
              "y" => 785
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><path class=\"icon\" d=\"M160 416h64v-32h-64v32zm32-192c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm192 0c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm160 0h-32v-32c0-53-43-96-96-96H304V16c0-8.8-7.2-16-16-16s-16 7.2-16 16v80H160c-53 0-96 43-96 96v32H32c-17.7 0-32 14.3-32 32v128c0 17.7 14.3 32 32 32h32v32c0 35.3 28.7 64 64 64h320c35.3 0 64-28.7 64-64v-32h32c17.7 0 32-14.3 32-32V256c0-17.7-14.3-32-32-32zM64 384H32V256h32v128zm416 64c0 17.6-14.4 32-32 32H128c-17.6 0-32-14.4-32-32V192c0-35.3 28.7-64 64-64h256c35.3 0 64 28.7 64 64v256zm64-64h-32V256h32v128zm-192 32h64v-32h-64v32zm-96 0h64v-32h-64v32z\"></path><text class=\"label\"/>",
            "id" => "ad98461f-7201-46d8-b335-93c56d7e1f65",
            "embeds" => array(
              "9a0df9f9-3068-49ab-87c6-9559b137ea3f"
            ),
            "z" => 55,
            "attrs" => array(
              ".label" => array(
                "text" => "シナリオ呼出",
                "font-size" => "14px",
                "fill" => "#fff",
                "font-weight" => "bold",
                "y" => 12
              ),
              ".body" => array(
                "stroke" => false,
                "fill" => "#82c0cd",
                "rx" => 5,
                "ry" => 5
              ),
              ".icon" => array(
                "transform" => "scale(0.04) translate(150, 150)"
              ),
              ".inCover" => array(
                "fill" => "#82c0cd",
                "height" => 33,
                "width" => 2,
                "x" => -2,
                "y" => 23
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "scenario",
                "nextNodeId" => "841927a8-4303-45ae-a45a-f905a3a6c58f"
              ),
              "actionParam" => array(
                "targetScenarioIndex" => 3,
                "callbackToDiagram" => true,
                "value" => "【サンプル】来店予約 "
              ),
              ".outCover" => array(
                "fill" => "#82c0cd",
                "stroke" => false,
                "height" => 33,
                "width" => 2,
                "x" => 250,
                "y" => 40
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 790,
              "y" => 820
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "angle" => 0,
            "id" => "9a0df9f9-3068-49ab-87c6-9559b137ea3f",
            "z" => 56,
            "parent" => "ad98461f-7201-46d8-b335-93c56d7e1f65",
            "attrs" => array(
              "rect" => array(
                "fill" => "#FFFFFF",
                "stroke" => false,
                "rx" => 3,
                "ry" => 3
              ),
              "text" => array(
                "text" => "【サンプル】来店予約 ",
                "font-size" => "14px",
                "y" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childViewNode",
                "tooltip" => "【サンプル】来店予約 "
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(
              "in"
            ),
            "outPorts" => array(
              "out",
              "out"
            ),
            "size" => array(
              "width" => 250,
              "height" => 76
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => -31,
                      "y" => 23
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#82c0cd",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => "passive",
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "scenario"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 248,
                      "y" => 23
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#C8E3E8",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "scenario"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "in",
                  "group" => "in",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "in"
                    )
                  )
                ),
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 785,
              "y" => 875
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><path class=\"icon\" d=\"M160 416h64v-32h-64v32zm32-192c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm192 0c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm160 0h-32v-32c0-53-43-96-96-96H304V16c0-8.8-7.2-16-16-16s-16 7.2-16 16v80H160c-53 0-96 43-96 96v32H32c-17.7 0-32 14.3-32 32v128c0 17.7 14.3 32 32 32h32v32c0 35.3 28.7 64 64 64h320c35.3 0 64-28.7 64-64v-32h32c17.7 0 32-14.3 32-32V256c0-17.7-14.3-32-32-32zM64 384H32V256h32v128zm416 64c0 17.6-14.4 32-32 32H128c-17.6 0-32-14.4-32-32V192c0-35.3 28.7-64 64-64h256c35.3 0 64 28.7 64 64v256zm64-64h-32V256h32v128zm-192 32h64v-32h-64v32zm-96 0h64v-32h-64v32z\"></path><text class=\"label\"/>",
            "id" => "ce587fe5-3d35-40ba-b9ef-abbccc37cf4d",
            "embeds" => array(
              "a03d6f9c-d061-41fa-9212-844f5037602a"
            ),
            "z" => 57,
            "attrs" => array(
              ".label" => array(
                "text" => "シナリオ呼出",
                "font-size" => "14px",
                "fill" => "#fff",
                "font-weight" => "bold",
                "y" => 12
              ),
              ".body" => array(
                "stroke" => false,
                "fill" => "#82c0cd",
                "rx" => 5,
                "ry" => 5
              ),
              ".icon" => array(
                "transform" => "scale(0.04) translate(150, 150)"
              ),
              ".inCover" => array(
                "fill" => "#82c0cd",
                "height" => 33,
                "width" => 2,
                "x" => -2,
                "y" => 23
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "scenario",
                "nextNodeId" => "841927a8-4303-45ae-a45a-f905a3a6c58f"
              ),
              "actionParam" => array(
                "targetScenarioIndex" => 4,
                "callbackToDiagram" => true,
                "value" => "【サンプル】会員登録・入会"
              ),
              ".outCover" => array(
                "fill" => "#82c0cd",
                "stroke" => false,
                "height" => 33,
                "width" => 2,
                "x" => 250,
                "y" => 40
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 790,
              "y" => 910
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "angle" => 0,
            "id" => "a03d6f9c-d061-41fa-9212-844f5037602a",
            "z" => 58,
            "parent" => "ce587fe5-3d35-40ba-b9ef-abbccc37cf4d",
            "attrs" => array(
              "rect" => array(
                "fill" => "#FFFFFF",
                "stroke" => false,
                "rx" => 3,
                "ry" => 3
              ),
              "text" => array(
                "text" => "【サンプル】会員登録・入会",
                "font-size" => "14px",
                "y" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childViewNode",
                "tooltip" => "【サンプル】会員登録・入会"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(
              "in"
            ),
            "outPorts" => array(
              "out",
              "out"
            ),
            "size" => array(
              "width" => 250,
              "height" => 76
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => -31,
                      "y" => 23
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#82c0cd",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => "passive",
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "scenario"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 248,
                      "y" => 23
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#C8E3E8",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "scenario"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "in",
                  "group" => "in",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "in"
                    )
                  )
                ),
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 785,
              "y" => 965
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><path class=\"icon\" d=\"M160 416h64v-32h-64v32zm32-192c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm192 0c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm160 0h-32v-32c0-53-43-96-96-96H304V16c0-8.8-7.2-16-16-16s-16 7.2-16 16v80H160c-53 0-96 43-96 96v32H32c-17.7 0-32 14.3-32 32v128c0 17.7 14.3 32 32 32h32v32c0 35.3 28.7 64 64 64h320c35.3 0 64-28.7 64-64v-32h32c17.7 0 32-14.3 32-32V256c0-17.7-14.3-32-32-32zM64 384H32V256h32v128zm416 64c0 17.6-14.4 32-32 32H128c-17.6 0-32-14.4-32-32V192c0-35.3 28.7-64 64-64h256c35.3 0 64 28.7 64 64v256zm64-64h-32V256h32v128zm-192 32h64v-32h-64v32zm-96 0h64v-32h-64v32z\"></path><text class=\"label\"/>",
            "id" => "a5558797-2c50-4fae-9c7b-312a211a00ba",
            "embeds" => array(
              "9645f396-44e5-4faa-9411-b5624d650671"
            ),
            "z" => 59,
            "attrs" => array(
              ".label" => array(
                "text" => "シナリオ呼出",
                "font-size" => "14px",
                "fill" => "#fff",
                "font-weight" => "bold",
                "y" => 12
              ),
              ".body" => array(
                "stroke" => false,
                "fill" => "#82c0cd",
                "rx" => 5,
                "ry" => 5
              ),
              ".icon" => array(
                "transform" => "scale(0.04) translate(150, 150)"
              ),
              ".inCover" => array(
                "fill" => "#82c0cd",
                "height" => 33,
                "width" => 2,
                "x" => -2,
                "y" => 23
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "scenario",
                "nextNodeId" => "841927a8-4303-45ae-a45a-f905a3a6c58f"
              ),
              "actionParam" => array(
                "targetScenarioIndex" => 5,
                "callbackToDiagram" => true,
                "value" => "【サンプル】アンケート"
              ),
              ".outCover" => array(
                "fill" => "#82c0cd",
                "stroke" => false,
                "height" => 33,
                "width" => 2,
                "x" => 250,
                "y" => 40
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 790,
              "y" => 1000
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "angle" => 0,
            "id" => "9645f396-44e5-4faa-9411-b5624d650671",
            "z" => 60,
            "parent" => "a5558797-2c50-4fae-9c7b-312a211a00ba",
            "attrs" => array(
              "rect" => array(
                "fill" => "#FFFFFF",
                "stroke" => false,
                "rx" => 3,
                "ry" => 3
              ),
              "text" => array(
                "text" => "【サンプル】アンケート",
                "font-size" => "14px",
                "y" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childViewNode",
                "tooltip" => "【サンプル】アンケート"
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "2fb4d49c-a483-4440-9862-9c43a360818f",
              "port" => "out"
            ),
            "target" => array(
              "id" => "ad98461f-7201-46d8-b335-93c56d7e1f65",
              "port" => "in"
            ),
            "id" => "f04997b0-029e-4c7f-99f7-934b59d07c6e",
            "z" => 61,
            "vertices" => array(),
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "80f421f5-5e1b-401d-b5a4-03bc4ba7b899",
              "port" => "out"
            ),
            "target" => array(
              "id" => "ce587fe5-3d35-40ba-b9ef-abbccc37cf4d",
              "port" => "in"
            ),
            "id" => "c25c955a-e882-43c9-948d-c92c7a19c7af",
            "z" => 62,
            "vertices" => array(),
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "2b2bf14a-e520-42de-8cdf-99e461567888",
              "port" => "out"
            ),
            "target" => array(
              "id" => "a5558797-2c50-4fae-9c7b-312a211a00ba",
              "port" => "in"
            ),
            "id" => "76742d72-eea4-4c8e-90cb-0c388fd0aa9f",
            "z" => 63,
            "vertices" => array(),
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(
              "in"
            ),
            "outPorts" => array(),
            "size" => array(
              "width" => 250,
              "height" => 76
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => -31,
                      "y" => 23
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#c8d627",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => "passive",
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "jump"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                ),
                "out" => array(
                  "position" => array(
                    "name" => "right"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                )
              ),
              "items" => array(
                array(
                  "id" => "in",
                  "group" => "in",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "in"
                    ),
                    ".port-body" => array(
                      "fill" => "#c8d627"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 1165,
              "y" => 875
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><path class=\"icon\" d=\"M0 80v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48H48C21.5 32 0 53.5 0 80zm400-16c8.8 0 16 7.2 16 16v352c0 8.8-7.2 16-16 16H48c-8.8 0-16-7.2-16-16V80c0-8.8 7.2-16 16-16h352zm-208 64v64H88c-13.2 0-24 10.8-24 24v80c0 13.2 10.8 24 24 24h104v64c0 28.4 34.5 42.8 54.6 22.6l128-128c12.5-12.5 12.5-32.8 0-45.3l-128-128c-20.1-20-54.6-5.8-54.6 22.7zm160 128L224 384v-96H96v-64h128v-96l128 128z\"></path><text class=\"label\"/>",
            "id" => "841927a8-4303-45ae-a45a-f905a3a6c58f",
            "embeds" => array(
              "dc1032df-667e-4bb0-9ae1-05d4099b2cd7"
            ),
            "z" => 64,
            "attrs" => array(
              ".label" => array(
                "text" => "ジャンプ",
                "font-size" => "14px",
                "fill" => "#fff",
                "font-weight" => "bold",
                "y" => 12
              ),
              ".body" => array(
                "stroke" => false,
                "fill" => "#c8d627",
                "rx" => 5,
                "ry" => 5
              ),
              ".icon" => array(
                "transform" => "scale(0.04) translate(150, 150)"
              ),
              ".inCover" => array(
                "fill" => "#c8d627",
                "height" => 33,
                "width" => 2,
                "x" => -2,
                "y" => 23
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "jump"
              ),
              "actionParam" => array(
                "targetId" => "b938dc97-5042-4c97-b5a7-c319538362be"
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 1170,
              "y" => 910
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "angle" => 0,
            "id" => "dc1032df-667e-4bb0-9ae1-05d4099b2cd7",
            "z" => 65,
            "parent" => "841927a8-4303-45ae-a45a-f905a3a6c58f",
            "attrs" => array(
              "rect" => array(
                "fill" => "#FFFFFF",
                "stroke" => false,
                "rx" => 3,
                "ry" => 3
              ),
              "text" => array(
                "text" => "メインメニュー",
                "font-size" => "14px",
                "y" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childViewNode",
                "tooltip" => "メインメニュー"
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "a5558797-2c50-4fae-9c7b-312a211a00ba",
              "port" => "out"
            ),
            "target" => array(
              "id" => "841927a8-4303-45ae-a45a-f905a3a6c58f",
              "port" => "in"
            ),
            "id" => "92cd1ed2-2d3d-4849-ae41-2365435e4b14",
            "z" => 66,
            "vertices" => array(),
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "ce587fe5-3d35-40ba-b9ef-abbccc37cf4d",
              "port" => "out"
            ),
            "target" => array(
              "id" => "841927a8-4303-45ae-a45a-f905a3a6c58f",
              "port" => "in"
            ),
            "id" => "ed761e47-7976-4863-9c96-f66e237f0e21",
            "z" => 67,
            "vertices" => array(),
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(
              "in"
            ),
            "outPorts" => array(),
            "size" => array(
              "width" => 250,
              "height" => 76
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => -31,
                      "y" => 23
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#c8d627",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => "passive",
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "jump"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                ),
                "out" => array(
                  "position" => array(
                    "name" => "right"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                )
              ),
              "items" => array(
                array(
                  "id" => "in",
                  "group" => "in",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "in"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 1875,
              "y" => 720
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><path class=\"icon\" d=\"M0 80v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48H48C21.5 32 0 53.5 0 80zm400-16c8.8 0 16 7.2 16 16v352c0 8.8-7.2 16-16 16H48c-8.8 0-16-7.2-16-16V80c0-8.8 7.2-16 16-16h352zm-208 64v64H88c-13.2 0-24 10.8-24 24v80c0 13.2 10.8 24 24 24h104v64c0 28.4 34.5 42.8 54.6 22.6l128-128c12.5-12.5 12.5-32.8 0-45.3l-128-128c-20.1-20-54.6-5.8-54.6 22.7zm160 128L224 384v-96H96v-64h128v-96l128 128z\"></path><text class=\"label\"/>",
            "id" => "b3026516-4383-472c-8824-f7e3a8810887",
            "embeds" => array(
              "c8c6182d-d290-44d6-a357-3b8e509fb60c"
            ),
            "z" => 69,
            "attrs" => array(
              ".label" => array(
                "text" => "ジャンプ",
                "font-size" => "14px",
                "fill" => "#fff",
                "font-weight" => "bold",
                "y" => 12
              ),
              ".body" => array(
                "stroke" => false,
                "fill" => "#c8d627",
                "rx" => 5,
                "ry" => 5
              ),
              ".icon" => array(
                "transform" => "scale(0.04) translate(150, 150)"
              ),
              ".inCover" => array(
                "fill" => "#c8d627",
                "height" => 33,
                "width" => 2,
                "x" => -2,
                "y" => 23
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "jump"
              ),
              "actionParam" => array(
                "targetId" => "b938dc97-5042-4c97-b5a7-c319538362be"
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 1880,
              "y" => 755
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "angle" => 0,
            "id" => "c8c6182d-d290-44d6-a357-3b8e509fb60c",
            "z" => 70,
            "parent" => "b3026516-4383-472c-8824-f7e3a8810887",
            "attrs" => array(
              "rect" => array(
                "fill" => "#FFFFFF",
                "stroke" => false,
                "rx" => 3,
                "ry" => 3
              ),
              "text" => array(
                "text" => "メインメニュー",
                "font-size" => "14px",
                "y" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childViewNode",
                "tooltip" => "メインメニュー"
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "bb8515ea-6dc0-46ee-84f4-a938620e3933",
              "port" => "out"
            ),
            "target" => array(
              "id" => "b3026516-4383-472c-8824-f7e3a8810887",
              "port" => "in"
            ),
            "id" => "108fa485-65c2-4047-a23d-2f81340a499a",
            "z" => 71,
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "a74a249f-9f7b-44dd-9e93-c6e1915d305c",
              "port" => "out"
            ),
            "target" => array(
              "id" => "b3026516-4383-472c-8824-f7e3a8810887",
              "port" => "in"
            ),
            "id" => "1ee0ac37-3f34-43c1-8944-b72fb9705611",
            "z" => 72,
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(
              "in"
            ),
            "outPorts" => array(),
            "size" => array(
              "width" => 250,
              "height" => 200
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => -31,
                      "y" => 40
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#c73576",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => "passive",
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "branch"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                ),
                "out" => array(
                  "position" => array(
                    "name" => "right"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                )
              ),
              "items" => array(
                array(
                  "id" => "in",
                  "group" => "in",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "in"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 785,
              "y" => 445
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><path class=\"icon\" d=\"M592 0h-96c-26.51 0-48 21.49-48 48v32H192V48c0-26.51-21.49-48-48-48H48C21.49 0 0 21.49 0 48v96c0 26.51 21.49 48 48 48h94.86l88.76 150.21c-4.77 7.46-7.63 16.27-7.63 25.79v96c0 26.51 21.49 48 48 48h96c26.51 0 48-21.49 48-48v-96c0-26.51-21.49-48-48-48h-96c-5.2 0-10.11 1.04-14.8 2.57l-83.43-141.18C184.8 172.59 192 159.2 192 144v-32h256v32c0 26.51 21.49 48 48 48h96c26.51 0 48-21.49 48-48V48c0-26.51-21.49-48-48-48zM32 144V48c0-8.82 7.18-16 16-16h96c8.82 0 16 7.18 16 16v96c0 8.82-7.18 16-16 16H48c-8.82 0-16-7.18-16-16zm336 208c8.82 0 16 7.18 16 16v96c0 8.82-7.18 16-16 16h-96c-8.82 0-16-7.18-16-16v-96c0-8.82 7.18-16 16-16h96zm240-208c0 8.82-7.18 16-16 16h-96c-8.82 0-16-7.18-16-16V48c0-8.82 7.18-16 16-16h96c8.82 0 16 7.18 16 16v96z\"></path><text class=\"label\"/>",
            "id" => "fd060639-35ff-42e3-ad6a-6db1bb43c835",
            "embeds" => array(
              "036548d6-b104-4568-b540-ada45999bb84",
              "90e86a22-9de1-45a8-a8f5-3b98953dbc20",
              "3c53f4c3-6aa5-43f9-bc1d-f6d764ea126c"
            ),
            "z" => 73,
            "attrs" => array(
              ".label" => array(
                "text" => "リンクを使った設定サン...",
                "font-size" => "14px",
                "fill" => "#fff",
                "font-weight" => "bold",
                "y" => 12
              ),
              ".body" => array(
                "stroke" => false,
                "fill" => "#c73576",
                "rx" => 5,
                "ry" => 5
              ),
              ".icon" => array(
                "transform" => "scale(0.035) translate(200, 250)"
              ),
              ".inCover" => array(
                "fill" => "#c73576",
                "height" => 33,
                "width" => 2,
                "x" => -2,
                "y" => 40
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "branch"
              ),
              "actionParam" => array(
                "nodeName" => "リンクを使った設定サンプル",
                "text" => "リンクは「リンク」ノードを利用することで、自動でページ遷移するような設定をすることができます。\n\nまた、以下のようにメッセージ内にリンクを配置することもできます。\n\n<a href=\"https://sinclo.medialink-ml.co.jp/lp/trial.php\" target=\"_blank\"style=\"display:inline-block;width:290px;text-align:center;font-weight:bold;text-decoration:none;background:#ABCD05;color:#FFFFFF;padding:10px;border-radius:5px;\">無料トライアル申し込み</a>",
                "btnType" => "1",
                "selection" => array(
                  array(
                    "type" => "2",
                    "value" => "------------------------------------------------"
                  ),
                  array(
                    "type" => "1",
                    "value" => "メニューに戻る"
                  )
                ),
                "customizeDesign" => array(
                  "isCustomize" => false,
                  "radioStyle" => "2",
                  "radioEntireBackgroundColor" => "#D5E682",
                  "radioEntireActiveColor" => "#ABCD05",
                  "radioTextColor" => "#333333",
                  "radioActiveTextColor" => "#333333",
                  "radioSelectionDistance" => 4,
                  "radioBackgroundColor" => "#FFFFFF",
                  "radioActiveColor" => "#ABCD05",
                  "radioBorderColor" => "#ABCD05",
                  "radioNoneBorder" => false
                )
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 790,
              "y" => 480
            ),
            "size" => array(
              "width" => 240,
              "height" => 70
            ),
            "angle" => 0,
            "id" => "90e86a22-9de1-45a8-a8f5-3b98953dbc20",
            "z" => 74,
            "parent" => "fd060639-35ff-42e3-ad6a-6db1bb43c835",
            "attrs" => array(
              "rect" => array(
                "fill" => "#FFFFFF",
                "stroke" => false,
                "rx" => 3,
                "ry" => 3
              ),
              "text" => array(
                "text" => "リンクは「リンク」ノードを利用す\nることで、自動でページ遷移するよ\nうな設定をすることができます。",
                "font-size" => "14px",
                "y" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childViewNode",
                "tooltip" => "リンクは「リンク」ノードを利用することで、自動でページ遷移するような設定をすることができます。\n\nまた、以下のようにメッセージ内にリンクを配置することもできます。\n\n<a href=\"https://sinclo.medialink-ml.co.jp/lp/trial.php\" target=\"_blank\"style=\"display:inline-block;width:290px;text-align:center;font-weight:bold;text-decoration:none;background:#ABCD05;color:#FFFFFF;padding:10px;border-radius:5px;\">無料トライアル申し込み</a>"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(),
            "outPorts" => array(
              "out"
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "left"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 235,
                      "y" => 3
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#DD82AB",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "fill-opacity" => "0.9",
                      "height" => 30,
                      "width" => 30,
                      "rx" => 3,
                      "ry" => 3
                    ),
                    "type" => "branch"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 4,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 790,
              "y" => 590
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><text class=\"label\"/><rect class=\"cover_top\"/><rect class=\"cover_bottom\"/>",
            "id" => "036548d6-b104-4568-b540-ada45999bb84",
            "parent" => "fd060639-35ff-42e3-ad6a-6db1bb43c835",
            "z" => 76,
            "attrs" => array(
              ".label" => array(
                "text" => "メニューに戻る",
                "font-size" => "14px",
                "ref-width" => "70%",
                "y" => 12
              ),
              "text" => array(
                "text" => "メニューに戻る",
                "ref-width" => "70%",
                "font-size" => "14px",
                "fill" => "#000",
                "y" => 12
              ),
              "rect.body" => array(
                "fill" => "#FFF",
                "stroke" => false,
                "rx" => 10,
                "ry" => 10
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childPortNode",
                "nextNode" => "",
                "tooltip" => "メニューに戻る",
                "nextNodeId" => "250bfb04-432e-4039-ae48-b18bd114e7b3"
              ),
              ".cover_top" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "fill-opacity" => 0
              ),
              ".cover_bottom" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "transform" => "translate(0 26)",
                "fill-opacity" => 0
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "22166176-b4c5-407c-a3e0-167b0eed3165",
              "port" => "out"
            ),
            "target" => array(
              "id" => "fd060639-35ff-42e3-ad6a-6db1bb43c835",
              "port" => "in"
            ),
            "id" => "63e510fd-7eaf-4d06-9f65-d3e078831f6b",
            "z" => 77,
            "vertices" => array(),
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "036548d6-b104-4568-b540-ada45999bb84",
              "port" => "out"
            ),
            "target" => array(
              "id" => "250bfb04-432e-4039-ae48-b18bd114e7b3",
              "port" => "in"
            ),
            "id" => "62566428-8736-47fd-ac74-68ad1605208c",
            "z" => 78,
            "vertices" => array(),
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "e979e907-4706-49be-857e-b404932f344a",
              "port" => "out"
            ),
            "target" => array(
              "id" => "250bfb04-432e-4039-ae48-b18bd114e7b3",
              "port" => "in"
            ),
            "id" => "9c9ff367-5352-4c25-bebe-f8028bc1f280",
            "z" => 79,
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 340,
              "y" => 355
            ),
            "size" => array(
              "width" => 240,
              "height" => 26
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><text class=\"label\"/><rect class=\"cover_top\"/><rect class=\"cover_bottom\"/>",
            "id" => "6161f4d6-2f26-4d13-8e38-2edf178fb360",
            "parent" => "b938dc97-5042-4c97-b5a7-c319538362be",
            "z" => 80,
            "attrs" => array(
              "text" => array(
                "fill" => "#FFF",
                "text" => "以下は「シナリオ」を使...",
                "font-size" => "14px",
                "ref-width" => "70%",
                "y" => 10
              ),
              "rect.body" => array(
                "fill" => "#c73576",
                "stroke" => false,
                "width" => 240,
                "height" => 26,
                "rx" => 0,
                "ry" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childTextNode",
                "tooltip" => "以下は「シナリオ」を使ったサンプルです。"
              ),
              ".cover_top" => array(
                "fill" => "#c73576",
                "width" => 240,
                "height" => 5,
                "fill-opacity" => 0,
                "stroke" => false
              ),
              ".cover_bottom" => array(
                "fill" => "#c73576",
                "width" => 240,
                "height" => 5,
                "transform" => "translate(0 21)",
                "fill-opacity" => 1,
                "stroke" => false
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "ad98461f-7201-46d8-b335-93c56d7e1f65",
              "port" => "out"
            ),
            "target" => array(
              "id" => "841927a8-4303-45ae-a45a-f905a3a6c58f",
              "port" => "in"
            ),
            "id" => "371f7c98-aa5e-4480-870c-e0b2260accea",
            "z" => 81,
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 785,
              "y" => 360
            ),
            "size" => array(
              "width" => 240,
              "height" => 26
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><text class=\"label\"/><rect class=\"cover_top\"/><rect class=\"cover_bottom\"/>",
            "id" => "1fc092f3-65d4-49b4-9bb7-2743b0498b80",
            "parent" => "e6ddcffa-918c-401a-bd36-ecca27d17132",
            "z" => 82,
            "attrs" => array(
              "text" => array(
                "fill" => "#FFF",
                "text" => "----------------------...",
                "font-size" => "14px",
                "ref-width" => "70%",
                "y" => 10
              ),
              "rect.body" => array(
                "fill" => "#c73576",
                "stroke" => false,
                "width" => 240,
                "height" => 26,
                "rx" => 0,
                "ry" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childTextNode",
                "tooltip" => "------------------------------------------------"
              ),
              ".cover_top" => array(
                "fill" => "#c73576",
                "width" => 240,
                "height" => 5,
                "fill-opacity" => 0,
                "stroke" => false
              ),
              ".cover_bottom" => array(
                "fill" => "#c73576",
                "width" => 240,
                "height" => 5,
                "transform" => "translate(0 21)",
                "fill-opacity" => 0,
                "stroke" => false
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 790,
              "y" => 560
            ),
            "size" => array(
              "width" => 240,
              "height" => 26
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><text class=\"label\"/><rect class=\"cover_top\"/><rect class=\"cover_bottom\"/>",
            "id" => "3c53f4c3-6aa5-43f9-bc1d-f6d764ea126c",
            "parent" => "fd060639-35ff-42e3-ad6a-6db1bb43c835",
            "z" => 83,
            "attrs" => array(
              "text" => array(
                "fill" => "#FFF",
                "text" => "----------------------...",
                "font-size" => "14px",
                "ref-width" => "70%",
                "y" => 10
              ),
              "rect.body" => array(
                "fill" => "#c73576",
                "stroke" => false,
                "width" => 240,
                "height" => 26,
                "rx" => 0,
                "ry" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childTextNode",
                "tooltip" => "------------------------------------------------"
              ),
              ".cover_top" => array(
                "fill" => "#c73576",
                "width" => 240,
                "height" => 5,
                "fill-opacity" => "0",
                "stroke" => false
              ),
              ".cover_bottom" => array(
                "fill" => "#c73576",
                "width" => 240,
                "height" => 5,
                "transform" => "translate(0 21)",
                "fill-opacity" => "1",
                "stroke" => false
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 1170,
              "y" => 155
            ),
            "size" => array(
              "width" => 240,
              "height" => 26
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><text class=\"label\"/><rect class=\"cover_top\"/><rect class=\"cover_bottom\"/>",
            "id" => "648eca8b-0d1f-49a2-af75-a3c4d512a4af",
            "parent" => "d837f4fa-338e-4431-b8dc-5f8962d0b75c",
            "z" => 84,
            "attrs" => array(
              "text" => array(
                "fill" => "#FFF",
                "text" => "----------------------...",
                "font-size" => "14px",
                "ref-width" => "70%",
                "y" => 10
              ),
              "rect.body" => array(
                "fill" => "#c73576",
                "stroke" => false,
                "width" => 240,
                "height" => 26,
                "rx" => 0,
                "ry" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childTextNode",
                "tooltip" => "------------------------------------------------"
              ),
              ".cover_top" => array(
                "fill" => "#c73576",
                "width" => 240,
                "height" => 5,
                "fill-opacity" => "0",
                "stroke" => false
              ),
              ".cover_bottom" => array(
                "fill" => "#c73576",
                "width" => 240,
                "height" => 5,
                "transform" => "translate(0 21)",
                "fill-opacity" => "1",
                "stroke" => false
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(),
            "outPorts" => array(
              "out"
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "left"
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000"
                    ),
                    ".port-body" => array(
                      "fill" => "#fff",
                      "stroke" => "#000",
                      "r" => 10,
                      "magnet" => true
                    )
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  )
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 235,
                      "y" => 3
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#DD82AB",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "fill-opacity" => "0.9",
                      "height" => 30,
                      "width" => 30,
                      "rx" => 3,
                      "ry" => 3
                    ),
                    "type" => "branch"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 4,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    ),
                    ".port-body" => array(
                      "fill" => "#DD82AB"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 340,
              "y" => 545
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><text class=\"label\"/><rect class=\"cover_top\"/><rect class=\"cover_bottom\"/>",
            "id" => "39f817ff-ba79-4533-873a-6a1eccfac756",
            "parent" => "b938dc97-5042-4c97-b5a7-c319538362be",
            "z" => 85,
            "attrs" => array(
              ".label" => array(
                "text" => "問い合わせフォームのサ...",
                "font-size" => "14px",
                "ref-width" => "70%",
                "y" => 12
              ),
              "text" => array(
                "text" => "問い合わせフォームのサ...",
                "ref-width" => "70%",
                "font-size" => "14px",
                "fill" => "#000",
                "y" => 12
              ),
              "rect.body" => array(
                "fill" => "#FFF",
                "stroke" => false,
                "rx" => 10,
                "ry" => 10
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childPortNode",
                "nextNode" => "",
                "tooltip" => "問い合わせフォームのサンプル",
                "nextNodeId" => "0c73ac1d-3cd4-4271-919b-11eb90729c2f"
              ),
              ".cover_top" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "fill-opacity" => "1"
              ),
              ".cover_bottom" => array(
                "fill" => "#FFFFFF",
                "width" => 240,
                "height" => 10,
                "transform" => "translate(0 26)",
                "fill-opacity" => "0"
              )
            )
          ),
          array(
            "type" => "devs.Model",
            "inPorts" => array(
              "in"
            ),
            "outPorts" => array(
              "out"
            ),
            "size" => array(
              "width" => 250,
              "height" => 76
            ),
            "ports" => array(
              "groups" => array(
                "in" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => -31,
                      "y" => 23
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#82c0cd",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => "passive",
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "scenario"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "left",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                ),
                "out" => array(
                  "position" => array(
                    "name" => "absolute",
                    "args" => array(
                      "x" => 248,
                      "y" => 23
                    )
                  ),
                  "attrs" => array(
                    ".port-label" => array(
                      "fill" => "#000",
                      "font-size" => 0
                    ),
                    ".port-body" => array(
                      "fill" => "#C8E3E8",
                      "stroke" => false,
                      "r" => 10,
                      "magnet" => true,
                      "height" => 33,
                      "width" => 33,
                      "rx" => 5,
                      "ry" => 5,
                      "fill-opacity" => "0.9"
                    ),
                    "type" => "scenario"
                  ),
                  "label" => array(
                    "position" => array(
                      "name" => "right",
                      "args" => array(
                        "y" => 10
                      )
                    )
                  ),
                  "z" => 0,
                  "markup" => "<rect class=\"port-body\"/>"
                )
              ),
              "items" => array(
                array(
                  "id" => "in",
                  "group" => "in",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "in"
                    )
                  )
                ),
                array(
                  "id" => "out",
                  "group" => "out",
                  "attrs" => array(
                    ".port-label" => array(
                      "text" => "out"
                    ),
                    ".port-body" => array(
                      "fill" => "#C8E3E8"
                    )
                  )
                )
              )
            ),
            "position" => array(
              "x" => 785,
              "y" => 1060
            ),
            "angle" => 0,
            "markup" => "<rect class=\"body\"/><path class=\"icon\" d=\"M160 416h64v-32h-64v32zm32-192c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm192 0c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm160 0h-32v-32c0-53-43-96-96-96H304V16c0-8.8-7.2-16-16-16s-16 7.2-16 16v80H160c-53 0-96 43-96 96v32H32c-17.7 0-32 14.3-32 32v128c0 17.7 14.3 32 32 32h32v32c0 35.3 28.7 64 64 64h320c35.3 0 64-28.7 64-64v-32h32c17.7 0 32-14.3 32-32V256c0-17.7-14.3-32-32-32zM64 384H32V256h32v128zm416 64c0 17.6-14.4 32-32 32H128c-17.6 0-32-14.4-32-32V192c0-35.3 28.7-64 64-64h256c35.3 0 64 28.7 64 64v256zm64-64h-32V256h32v128zm-192 32h64v-32h-64v32zm-96 0h64v-32h-64v32z\"></path><text class=\"label\"/>",
            "id" => "0c73ac1d-3cd4-4271-919b-11eb90729c2f",
            "embeds" => array(
              "bbd3c479-5115-4d2a-b5d9-906901b08a70"
            ),
            "z" => 86,
            "attrs" => array(
              ".label" => array(
                "text" => "シナリオ呼出",
                "font-size" => "14px",
                "fill" => "#fff",
                "font-weight" => "bold",
                "y" => 12
              ),
              ".body" => array(
                "stroke" => false,
                "fill" => "#82c0cd",
                "rx" => 5,
                "ry" => 5
              ),
              ".icon" => array(
                "transform" => "scale(0.04) translate(150, 150)"
              ),
              ".inCover" => array(
                "fill" => "#82c0cd",
                "height" => 33,
                "width" => 2,
                "x" => -2,
                "y" => 23
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "scenario",
                "nextNodeId" => "841927a8-4303-45ae-a45a-f905a3a6c58f"
              ),
              "actionParam" => array(
                "targetScenarioIndex" => 6,
                "callbackToDiagram" => true,
                "value" => "【サンプル】問い合わせフォーム"
              ),
              ".outCover" => array(
                "fill" => "#82c0cd",
                "stroke" => false,
                "height" => 33,
                "width" => 2,
                "x" => 250,
                "y" => 40
              )
            )
          ),
          array(
            "type" => "basic.Rect",
            "position" => array(
              "x" => 790,
              "y" => 1095
            ),
            "size" => array(
              "width" => 240,
              "height" => 36
            ),
            "angle" => 0,
            "id" => "bbd3c479-5115-4d2a-b5d9-906901b08a70",
            "parent" => "0c73ac1d-3cd4-4271-919b-11eb90729c2f",
            "z" => 87,
            "attrs" => array(
              "rect" => array(
                "fill" => "#FFFFFF",
                "stroke" => false,
                "rx" => 3,
                "ry" => 3
              ),
              "text" => array(
                "text" => "【サンプル】問い合わせフォーム",
                "font-size" => "14px",
                "y" => 0
              ),
              "nodeBasicInfo" => array(
                "nodeType" => "childViewNode",
                "tooltip" => "【サンプル】問い合わせフォーム"
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "39f817ff-ba79-4533-873a-6a1eccfac756",
              "port" => "out"
            ),
            "target" => array(
              "id" => "0c73ac1d-3cd4-4271-919b-11eb90729c2f",
              "port" => "in"
            ),
            "id" => "d1b5a16b-0dbb-49ff-9092-56ca9170acaa",
            "z" => 88,
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          ),
          array(
            "type" => "link",
            "source" => array(
              "id" => "0c73ac1d-3cd4-4271-919b-11eb90729c2f",
              "port" => "out"
            ),
            "target" => array(
              "id" => "841927a8-4303-45ae-a45a-f905a3a6c58f",
              "port" => "in"
            ),
            "id" => "b3c6095a-45a8-46e3-a800-25231795724a",
            "z" => 89,
            "attrs" => array(
              ".connection" => array(
                "stroke" => "#AAAAAA",
                "stroke-width" => 2
              ),
              ".marker-target" => array(
                "stroke" => "#AAAAAA",
                "fill" => "#AAAAAA",
                "d" => "M 14 0 L 0 7 L 14 14 z"
              ),
              ".link-tools .link-tool .tool-remove circle" => array(
                "class" => "diagram"
              ),
              ".marker-arrowhead[end=\"source\"]" => array(
                "d" => "M 0 0 z"
              ),
              ".marker-arrowhead[end=\"target\"]" => array(
                "d" => "M 0 0 z"
              )
            )
          )
        ),
        'del_flg' => 0,
        'sort' => 1
      )
    )
  )
);
