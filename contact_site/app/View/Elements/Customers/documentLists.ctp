<script type="text/javascript">
var sincloApp = angular.module('sincloApp', []);
sincloApp.controller('MainCtrl', function($scope){
  $scope.documentList = [];
  $scope.tagList = {};
  $scope.searchName = "";
  $scope.selectList = {};
  $scope.searchFunc = function(documentList){
    var targetTagNum = Object.keys($scope.selectList).length;

    function check(elem, index, array){
      var flg = true;
      elem.tags = $scope.jParse(elem.tag);
      if ( $scope.searchName === "" && targetTagNum === 0 ) {
        return elem;
      }

      if ( $scope.searchName !== "" && (elem.name + elem.overview).indexOf($scope.searchName) < 0 ) {
        flg = false;
      }

      if ( flg && targetTagNum > 0 ) {
        var selectList = Object.keys($scope.selectList);
        flg = true;
        for ( var i = 0; selectList.length > i; i++ ) {
          if ( elem.tags.indexOf(Number(selectList[i])) === -1 ) {
            flg = false;
          }
        }
      }

      return ( flg ) ? elem : false;

    }

    return documentList.filter(check);
  };

  /**
   * openDocumentList
   *  ドキュメントリストの取得
   * @return void(0)
   */
  $scope.openDocumentList = function() {
    $.ajax({
      type: 'GET',
      url: '<?=$this->Html->url(["controller" => "Customers", "action" => "remoteOpenDocumentLists"])?>',
      dataType: 'json',
      success: function(json) {
        $("#ang-popup").addClass("show");
        var contHeight = $('#ang-popup-content').height();
        $('#ang-popup-frame').css('height', contHeight);
        $scope.tagList = ( json.hasOwnProperty('tagList') ) ? JSON.parse(json.tagList) : {};
        $scope.documentList = ( json.hasOwnProperty('documentList') ) ? JSON.parse(json.documentList) : {};
        $scope.$apply();
      }
    });
  };

  $scope.closeDocumentList = function() {
    $("#ang-popup").removeClass("show");
  };

  angular.element(document).on("click", function(evt){
    if ( evt.target.getAttribute('data-elem-type') !== 'selector' ) {
      var e = document.querySelector('ng-multi-selector');
      if ( e.classList.contains('show') ) {
        e.classList.remove('show');
      }
    }
  });
});

sincloApp.directive('ngOverView', function(){
  return {
    restrict: "E",
    scope: {
      text: "@",
      docid: "@"
    },
    template: '<span ng-mouseover="toggleOverView()" ng-mouseleave="toggleOverView()">{{::text}}</span>',
    link: function(scope, elem, attr){
      var ballons = angular.element('#ang-ballons');
      var ballon = document.createElement('div');
      ballon.classList.add("hide");
      ballon.textContent = scope.text;
      ballon.setAttribute('data-id', scope.docid);
      ballons.append(ballon);

      scope.toggleOverView = function(){
        var p = angular.element(elem).offset();
        ballon.style.top = p.top + "px";
        ballon.style.left = p.left + "px";
        ballon.classList.toggle("hide");
      }
    }
  }
});

sincloApp.directive('ngMultiSelector', function(){
  return {
    restrict: "E",
    template: '<selected data-elem-type="selector" ng-click="openMultiSelector()">{{selected}}</selected>' +
              '<ul>' +
              '  <li data-elem-type="selector" ng-repeat="(id, name) in tagList" ng-click="changAct(id)" ng-class="{selected: judgeSelect(id)}">{{name}}</li>' +
              '</ul>',
    link: function(scope, elem, attr){
      scope.openMultiSelector = function(){
        var e = angular.element(elem);
        if ( e.hasClass('show') ) {
          e.removeClass('show');
        }
        else {
          e.addClass('show');
        }
      };
      scope.selected = "-";
      scope.changAct = function(id){
        if ( scope.selectList.hasOwnProperty(id) ) {
          delete scope.selectList[id];
        }
        else {
          scope.selectList[id] = true;
        }
        var str = Object.keys(scope.selectList).map(function(item){
          return scope.tagList[item];
        }).join('、');
        scope.selected = ( str === "" ) ? "-" : str;
      };

      scope.judgeSelect = function(id){
        return (scope.selectList.hasOwnProperty(id));
      };

      scope.jParse = function(str){
        return JSON.parse(str);
      };
    }
  };
});
</script>
