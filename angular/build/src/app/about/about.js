angular.module('ngBoilerplate.about', [
  'ui.state',
  'placeholders',
  'ui.bootstrap'
]).config([
  '$stateProvider',
  function config($stateProvider) {
    $stateProvider.state('about', {
      url: '/about',
      views: {
        'main': {
          controller: 'AboutCtrl',
          templateUrl: 'about/about.tpl.html'
        }
      },
      data: { pageTitle: 'What is It?' }
    });
  }
]).controller('AboutCtrl', [
  '$scope',
  function AboutCtrl($scope) {
    $scope.dropdownDemoItems = [
      'The first choice!',
      'And another choice for you.',
      'but wait! A third!'
    ];
  }
]);
;