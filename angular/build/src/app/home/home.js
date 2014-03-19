angular.module('ngBoilerplate.home', [
  'ui.state',
  'plusOne'
]).config([
  '$stateProvider',
  function config($stateProvider) {
    $stateProvider.state('home', {
      url: '/home',
      views: {
        'main': {
          controller: 'HomeCtrl',
          templateUrl: 'home/home.tpl.html'
        }
      },
      data: { pageTitle: 'Home' }
    });
  }
]).controller('HomeCtrl', [
  '$scope',
  function HomeController($scope) {
  }
]);
;