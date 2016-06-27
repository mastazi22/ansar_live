
var GlobalApp = angular.module('GlobalApp', [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol('[[');
    $interpolateProvider.endSymbol(']]');
});
GlobalApp.controller('DivisionController', function ($scope, getNameService) {
    getNameService.getDivision().then(function (response) {
        $scope.division = response.data;
    });
    $scope.SelectedItemChanged = function () {
        getNameService.getDistric($scope.SelectedDivision).then(function (response) {
            $scope.district = response.data;
        })
    }
    $scope.SelectedDistrictChanged = function () {
        getNameService.getThana($scope.SelectedDistrict).then(function (response) {
            $scope.thana = response.data;
        })
    }
});
GlobalApp.factory('getNameService', function ($http) {
    return {
        getDivision: function () {
            return $http.get("{{action('FormSubmitHandler@DivisionName')}}");
        },
        getDistric: function (data) {

            return $http.get("{{action('FormSubmitHandler@DistrictName')}}", {params: {id: data}});
        },
        getThana: function (data) {
            return $http.get("{{action('FormSubmitHandler@ThanaName')}}", {params: {id: data}});
        }
    }

});
GlobalApp.factory('getBloodService', function ($http) {
    return {
        getAllBloodName: function () {
            return $http.get("{{url('getBloodName')}}")
        }
    }
});
GlobalApp.controller('addEduController', function ($scope) {
    $scope.rows = [];
    $scope.addEduinput = function () {

        $scope.rows.push({
            text: "Add new:",
            text1: "???????? ???????",
            text2: "?????? ???????????? ???",
            text3: "Passing year",
            text4: "????? / ??????",
            placeholder: "Enter a name"
        });
    };
    $scope.deleteRows = function (index) {
        $scope.rows.splice(index, 1)
    }
});
GlobalApp.controller('addTrainController', function ($scope) {
    $scope.rows = [];
    $scope.addTraininput = function () {

        $scope.rows.push({
            text: "Add new:",
            text1: "????",
            text2: "??????????",
            text3: "Training start date",
            text4: "Training end date",
            text5: "??? ??",
            placeholder: "Enter a name"
        });
    };
    $scope.deleteRows = function (index) {
        $scope.rows.splice(index, 1)
    }
});
GlobalApp.controller('addNomineeController', function ($scope) {
    $scope.rows = [];
    $scope.addNomineeinput = function () {

        $scope.rows.push({
            text: "Add new:",
            text1: "???",
            text2: "???????",
            text3: "Percentage",
            text4: "?????? ??",
            text5: "??? ??",
            placeholder: "Enter a name"
        });
    };
    $scope.deleteRows = function (index) {
        $scope.rows.splice(index, 1)
    }
});
GlobalApp.controller('bloodGroup', function ($scope, getBloodService) {
    getBloodService.getAllBloodName().then(function (response) {
        $scope.blood = response.data;
    })
});