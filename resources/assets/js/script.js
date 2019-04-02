// script.js
// create the module and name it scotchApp
// also include ngRoute for all our routing needs
var app = angular.module('SimpleInjection', ['ngRoute', 'ui.bootstrap']);

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

app.config(['$interpolateProvider', function($interpolateProvider) {
  $interpolateProvider.startSymbol('[[');
  $interpolateProvider.endSymbol(']]');
}]);

app.config(function($logProvider) {
  $logProvider.debugEnabled(true);
});

// configure our routes
app.config(function($routeProvider, $locationProvider) {
  $routeProvider
    // route for the home page
    .when('/manage/dashboard', {
      controller: 'homeController',
      templateUrl: BASEURL + '/resources/views/admin/dashboard.html',
    })
    .when('/manage/users', {
      templateUrl: BASEURL + '/resources/views/admin/users.html',
      controller: 'usersController'
    })
    .when('/manage/devices', {
      templateUrl: BASEURL + '/resources/views/admin/devices.html',
      controller: 'devicesController'
    })
    .when('/manage/editdevice/:id', {
      templateUrl: BASEURL + '/resources/views/admin/edit-devices.html',
      controller: 'editdevicesController'
    })
    .when('/manage/my-profile', {
      controller: 'MyprofileController',
      templateUrl: BASEURL + '/resources/views/admin/my-profile.html',
    })
    .when('/manage/add-device', {
      controller: 'adddeviceController',
      templateUrl: BASEURL + '/resources/views/admin/add-device.html',
    })
    .when('/manage/add-user', {
      controller: 'adduserController',
      templateUrl: BASEURL + '/resources/views/admin/add-user.html',
    })
    .when('/manage/addsubuser/:id', {
      controller: 'addsubuserController',
      templateUrl: BASEURL + '/resources/views/admin/add-sub-user.html',
    })
    .when('/manage/edituser/:id', {
      controller: 'edituserController',
      templateUrl: BASEURL + '/resources/views/admin/edit-user.html',
    })
    .when('/manage/feedback', {
      controller: 'FeedbackController',
      templateUrl: BASEURL + '/resources/views/admin/feedback.html',
    }).
    otherwise({
        templateUrl: BASEURL + '/resources/views/admin/404.html',
    });
 
  $locationProvider.html5Mode(true);
});

// Home controller
app.controller('homeController', function($scope, $http) {
  
  $http.get('manage/getdashboardstats', {
      cache: false
  }).
  success(function(response, status, headers, config) {
      $scope.dashboardInfo = response;
  }).
  error(function(data, status, headers, config) {});


  $(document).ready(function(){

     var chart = AmCharts.makeChart( "chartdiv", {
            "type": "serial",
            "theme": "light",
            //"dataProvider": jsonArray,
            "dataLoader": {
                "url": "manage/dashboardstateone"
            },
            "valueAxes": [ {
              "gridColor": "#3B3F51",
              "gridAlpha": 0.2,
              "dashLength": 0
            } ],
            "gridAboveGraphs": true,
            "startDuration": 1,
            "graphs": [ {
              "balloonText": "[[category]]: <b>[[value]]</b>",
              "fillAlphas": 0.8,
              "lineAlpha": 0.2,
              "type": "line",
              "valueField": "visits"
            } ],
            "chartCursor": {
              "categoryBalloonEnabled": false,
              "cursorAlpha": 0,
              "zoomable": false
            },
            "categoryField": "Month",
            "categoryAxis": {
              "gridPosition": "start",
              "gridAlpha": 0,
              "labelRotation": 45,
              "tickPosition": "start",
              "tickLength": 20
            },
            "export": {
              "enabled": true
            }

      } );

       

       var chart = AmCharts.makeChart( "chartdiv2", {
            "type": "serial",
            "theme": "patterns",
             "dataLoader": {
                "url": "manage/dashboardstatesec"
            },
            "valueAxes": [ {
              "gridColor": "#FFFFFF",
              "gridAlpha": 0.2,
              "dashLength": 0
            } ],
            "gridAboveGraphs": true,
            "startDuration": 1,
            "graphs": [ {
              "balloonText": "[[category]]: <b>[[value]]</b>",
              "fillAlphas": 0.8,
              "lineAlpha": 0.2,
              "type": "column",
              "valueField": "visits"
            } ],
            "chartCursor": {
              "categoryBalloonEnabled": false,
              "cursorAlpha": 0,
              "zoomable": false
            },
            "categoryField": "Month",
            "categoryAxis": {
              "gridPosition": "start",
              "gridAlpha": 0,
              "labelRotation": 45,
              "tickPosition": "start",
              "tickLength": 20
            },
            "export": {
              "enabled": true
            }

      } );

    function setDataSet(dataset_url) {
            AmCharts.loadFile(dataset_url, {}, function(data) {
              chart.dataProvider = AmCharts.parseJSON(data);
              chart.validateData();
            });
    }

  });

});

// My profile controller
app.controller('MyprofileController', function($scope, $http){
  
   
    $http.get('manage/getadmininfo', {
      cache: false
    }).
    success(function(response, status, headers, config) {
      console.log(response);
      $scope.adminInfo = response.data;
    }).
    error(function(data, status, headers, config) {});

      $('#profile-form').validate({
          errorElement: 'span', //default input error message container
          errorClass: 'help-block', // default input error message class
          focusInvalid: false, // do not focus the last invalid input
          rules: {
            name: {
              required: true
            },
            email: {
              required: true,
              email: true
            }
          },
          messages: {
            name: {
              required: "Name is required."
            },
            email :{
              required: 'UPC code is required'
            }
          },
          invalidHandler: function(event, validator) { //display error alert on form submit
            $('.alert-danger', $('#brand-form')).show();
          },
          highlight: function(element) { // hightlight error inputs
            $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
          },
          success: function(label) {
            label.closest('.form-group').removeClass('has-error');
            label.remove();
          },
          errorPlacement: function(error, element) {
            if (element.is(':checkbox')) {
              error.insertAfter(element.closest(".md-checkbox-list, .md-checkbox-inline, .checkbox-list, .checkbox-inline"));
            } else if (element.is(':radio')) {
              error.insertAfter(element.closest(".md-radio-list, .md-radio-inline, .radio-list,.radio-inline"));
            } else {
              error.insertAfter(element); // for other inputs, just perform default behavior
            }
          },
          submitHandler: function(form) {

            //define form data
            var fd = new FormData($('#profile-form')[0]);
            //return false;
            $.ajax({
              url: BASEURL + 'manage/updateprofile',
              type: "post",
              processData: false,
              contentType: false,
              data: fd,
              beforeSend: function() {},
              success: function(res) {
                
                if (res.success == '1') // in case genre added successfully
                {
                  swal({
                    title: "Success!!",
                    text: res.message,
                    type: "success",
                    showConfirmButton: true,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Okay!",
                  });
                  //return false;
                  $('#profile-form')[0].reset();
                  //redirect to dashboard
                } else { // in case error occuer
                  swal({
                    title: "Error!!",
                    text: res.message,
                    type: "error",
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Try Again!",
                  });
                  return false;
                }
              },
              error: function(e) {
                swal({
                  title: "Error!!",
                  text: e.statusText,
                  type: "error",
                  confirmButtonClass: "btn-danger",
                  confirmButtonText: "Try Again!",
                });
                return false;
              },
              complete: function() {}
            }, "json");
            return false;
          }
      });


      $('#password-form').validate({
        errorElement: 'span', //default input error message container
        errorClass: 'help-block', // default input error message class
        focusInvalid: false, // do not focus the last invalid input
        rules: {
          name: {
            required: true
          },
          upc_code: {
            required: true
          }
        },
        messages: {
          name: {
            required: "Name is required."
          },
          upc_code :{
            required: 'UPC code is required'
          }
        },
        invalidHandler: function(event, validator) { //display error alert on form submit
          $('.alert-danger', $('#brand-form')).show();
        },
        highlight: function(element) { // hightlight error inputs
          $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
        },
        success: function(label) {
          label.closest('.form-group').removeClass('has-error');
          label.remove();
        },
        errorPlacement: function(error, element) {
          if (element.is(':checkbox')) {
            error.insertAfter(element.closest(".md-checkbox-list, .md-checkbox-inline, .checkbox-list, .checkbox-inline"));
          } else if (element.is(':radio')) {
            error.insertAfter(element.closest(".md-radio-list, .md-radio-inline, .radio-list,.radio-inline"));
          } else {
            error.insertAfter(element); // for other inputs, just perform default behavior
          }
        },
        submitHandler: function(form) {
          //define form data
          var fd = new FormData($('#password-form')[0]);
          //return false;
          $.ajax({
            url: BASEURL + 'manage/updatepassword',
            type: "post",
            processData: false,
            contentType: false,
            data: fd,
            beforeSend: function() {},
            success: function(res) {
              
              if (res.success == '1') // in case genre added successfully
              {
                swal({
                  title: "Success!!",
                  text: res.message + ' Redirecting....',
                  type: "success",
                  showConfirmButton: true,
                   confirmButtonClass: "btn-success",
                    confirmButtonText: "Okay!",
                });
                //return false;
              } else { // in case error occuer
                swal({
                  title: "Error!!",
                  text: res.message,
                  type: "error",
                  confirmButtonClass: "btn-danger",
                  confirmButtonText: "Try Again!",
                });
                return false;
              }
            },
            error: function(e) {
              swal({
                title: "Error!!",
                text: e.statusText,
                type: "error",
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Try Again!",
              });
              return false;
            },
            complete: function() {}
          }, "json");
          return false;
        }
      });
});

// User controller
app.controller('usersController', function($scope, $http) {

    $('#user_list').dataTable({
            "processing": false,
            "serverSide": true,
            "bAutoWidth": false,
            "filter": true,
            "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
            "ajax": {
              'type': 'POST',
              'url': BASEURL + "manage/userlist",
              'data': function(d) {
                d.search = $('#search_user_string').val()
              }
            },
            "language": {
              "aria": {
                "sortAscending": ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
              },
              "emptyTable": "No user(s) available.",
              "info": "Found _START_ to _END_ of _TOTAL_ users",
              "infoEmpty": "No Product request(s) found",
              "infoFiltered": "(filtered from _MAX_ total users",
              "lengthMenu": "View _MENU_ users",
              "search": "Search:",
              "zeroRecords": "No matching users found"
            },
            "dom": "<'row'<'col-md-6 col-sm-12'><'col-md-6 col-sm-12'>r>t<'row'<'col-md-3 col-sm-12'l><'col-md-4 col-sm-12'i><'col-md-5 col-sm-12 text-right'p>>",
            "aoColumns": [
              { "data": "id",  "name": "id", "searchable": true, "orderable": true },
              { "data": "name",  "name": "No of request",  "searchable": false, "orderable": false },
              { "data": "email",  "name": "Email",  "searchable": false, "orderable": false },
              { "data": "status",  "name": "No of request",  "searchable": false, "orderable": false , render : function(data){

                if(data == 1){
                  return '<p class="font-green-sharp"> Active </p>';
                }else{
                  return '<p class="font-red-mint"> Inactive </p>';
                }
              } },
              { "data": "created_on",  "name": "Created at",  "searchable": false, "orderable": false },
              { "data": "action",  "name": "Created at",  "searchable": false, "orderable": false }
            ],
            "lengthMenu": [
              [10, 30, 50, -1],
              [10, 30, 50, "All"] // change per page values here
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number"
        });

    oTable1 = $('#user_list').DataTable();

    $('#search_user_string').keyup(function() {
            oTable1.search($(this).val()).draw();
    });

    // To delete the user.
    $(document).on('click', '.delete-user', function() {

          // To delete the user.
          var currencyEditId = $(this).data('id');

          swal({
              title: "Are you sure?",
              text: "If you delete this User.",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: '#DD6B55',
              confirmButtonText: 'Yes, I am sure!',
              cancelButtonText: "No, cancel it!",
              closeOnConfirm: false,
              closeOnCancel: false
            },
            function(isConfirm) {
              // Is user delete to confirmed.
              if (isConfirm) {
                swal({
                  title: 'Deleted!',
                  text: 'User is successfully removed!',
                  type: 'success'
                }, function() {
                  console.log('inside');
                  // To Order details.
                  $http.get('manage/deleteuser?id=' + currencyEditId, {
                    cache: false
                  }).
                  success(function(data, status, headers, config) {
                    oTable1.draw();
                  }).
                  error(function(data, status, headers, config) {});
                });
              } else {
                swal("Cancelled", "User not removed :)", "error");
              }
            });
    });
});

// Device controller
app.controller('devicesController', function($scope, $http) {
   
    $('#device_listing').dataTable({
            "processing": false,
            "serverSide": true,
            "bAutoWidth": false,
            "filter": true,
            "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
            "ajax": {
              'type': 'POST',
              'url': BASEURL + "manage/devicelist",
              'data': function(d) {
                d.search = $('#device_string').val(),
                d.unique_id = $('#unique_id').val()
              }
            },
            "language": {
              "aria": {
                "sortAscending": ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
              },
              "emptyTable": "No device(s) available.",
              "info": "Found _START_ to _END_ of _TOTAL_ devices",
              "infoEmpty": "No Product request(s) found",
              "infoFiltered": "(filtered from _MAX_ total devices",
              "lengthMenu": "View _MENU_ devices",
              "search": "Search:",
              "zeroRecords": "No matching devices found"
            },
            "dom": "<'row'<'col-md-6 col-sm-12'><'col-md-6 col-sm-12'>r>t<'row'<'col-md-3 col-sm-12'l><'col-md-4 col-sm-12'i><'col-md-5 col-sm-12 text-right'p>>",
            "aoColumns": [
              { "data": "id",  "name": "id", "searchable": true, "orderable": true },
              { "data": "name",  "name": "No of request",  "searchable": false, "orderable": false },
              { "data": "unique_id",  "name": "No of request",  "searchable": false, "orderable": false },
              { "data": "status",  "name": "No of request",  "searchable": false, "orderable": false, render : function(data){
                if(data == 1){
                  return '<p class="font-green-sharp"> Active </p>';
                }else{
                  return '<p class="font-red-mint"> Inactive </p>';
                }
              } },
              { "data": "is_assigned",  "name": "No of request",  "searchable": false, "orderable": false, render : function(data){

                if(data == 1){
                  return '<p class="btn btn-xs blue"> Assigned </p>';
                }else{
                  return '<p class="btn btn-xs default"> Unassigned </p>';
                }
              } },
              { "data": "created_on",  "name": "Created at",  "searchable": false, "orderable": false },
              { "data": "action",  "name": "Created at",  "searchable": false, "orderable": false }
            ],
            "lengthMenu": [
              [10, 30, 50, -1],
              [10, 30, 50, "All"] // change per page values here
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number"
    });

    oTable1 = $('#device_listing').DataTable();

    // Device string
    $('#device_string').keyup(function() {
        oTable1.search($(this).val()).draw();
    });

    // Search string
    $('#unique_id').keyup(function() {
        oTable1.search($(this).val()).draw();
    });

    // To delete the user.
    $(document).on('click', '.delete-product', function() {

          // To delete the user.
          var currencyEditId = $(this).data('id');

          swal({
              title: "Are you sure?",
              text: "If you delete this Device.",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: '#DD6B55',
              confirmButtonText: 'Yes, I am sure!',
              cancelButtonText: "No, cancel it!",
              closeOnConfirm: false,
              closeOnCancel: false
            },
            function(isConfirm) {
              // Is user delete to confirmed.
              if (isConfirm) {
                swal({
                  title: 'Deleted!',
                  text: 'Device is successfully removed!',
                  type: 'success'
                }, function() {
                  console.log('inside');
                  // To Order details.
                  $http.get('manage/deletedevice?id=' + currencyEditId, {
                    cache: false
                  }).
                  success(function(data, status, headers, config) {
                    oTable1.draw();
                  }).
                  error(function(data, status, headers, config) {});
                });
              } else {
                swal("Cancelled", "Device not removed :)", "error");
              }
            });
    });
});

// Add device controller
app.controller('adddeviceController', function($scope, $http){


  function updateFirebase(){

      // Firebase create object.
      firebase.database().ref('devices/SDCKM-98948-KLJNM').set({
                            "battery_level":{
                            },
                            "solar_production":{
                            },
                            "tank_level":{
                            },
                            "temprature_level":{
                            }
      }, function(error) {
                        if (error) {
                         alert('We are unable to insert device id into firebase. Delete the device and create again.');
                        } else {
                          // Data saved successfully!
                          console.log('data inserted successfully');
                        }
      });
  }

  //updateFirebase();

    $('#add-device').validate({
          errorElement: 'span', //default input error message container
          errorClass: 'help-block', // default input error message class
          focusInvalid: false, // do not focus the last invalid input
          rules: {
            name: {
              required: true
            },
            unique_id: {
              required: true
            }
            // email: {
            //   required: true,
            //   email: true,
            //   remote: {
            //     url: BASEURL + 'manage/checkemailexist',
            //     type: "post"
            //   }
            // }
          },
          messages: {
            name: {
              required: "Name is required."
            },
            unique_id: {
              required: "Unique id is required."
            }
            // email :{
            //   required: 'email is  required.',
            //   remote : "Email is already exist in database. Please try other one."
            // }
          },
          invalidHandler: function(event, validator) { //display error alert on form submit
            $('.alert-danger', $('#brand-form')).show();
          },
          highlight: function(element) { // hightlight error inputs
            $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
          },
          success: function(label) {
            label.closest('.form-group').removeClass('has-error');
            label.remove();
          },
          errorPlacement: function(error, element) {
            if (element.is(':checkbox')) {
              error.insertAfter(element.closest(".md-checkbox-list, .md-checkbox-inline, .checkbox-list, .checkbox-inline"));
            } else if (element.is(':radio')) {
              error.insertAfter(element.closest(".md-radio-list, .md-radio-inline, .radio-list,.radio-inline"));
            } else {
              error.insertAfter(element); // for other inputs, just perform default behavior
            }
          },
          submitHandler: function(form) {

            //define form data
            var fd = new FormData($('#add-device')[0]);
            //return false;
            $.ajax({
              url: BASEURL + 'manage/adddevice',
              type: "post",
              processData: false,
              contentType: false,
              data: fd,
              beforeSend: function() {},
              success: function(res) {
                
                if (res.success == '1') // in case genre added successfully
                {
                  swal({
                    title: "Success!!",
                    text: res.message,
                    type: "success",
                    showConfirmButton: true,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Okay!",
                  });

                  // Firebase create object.
                  firebase.database().ref('devices/' + res.data).set({
                            "data": {
                              "batch_mode_date" : {},
                              "batch_mode_date_latest" : 0,
                              "batch_mode_month" : {},
                              "batch_mode_month_latest" : 0,
                              "batch_mode_quarts" : {},
                              "batch_mode_quarts_latest" : 0,
                              "batch_mode_repeat_day" : {},
                              "batch_mode_repeat_day_latest" : 0,
                              "batch_mode_time_hour" : {},
                              "batch_mode_time_hour_latest" : 0,
                              "batch_mode_time_minute" : {},
                              "batch_mode_time_minute_latest" : 0,
                              "batch_mode_year" : {},
                              "batch_mode_year_latest" : 0,
                              "battery_level" : 0,
                              "charge_mode" : 0,
                              "off_mode_second" : {},
                              "off_mode_second_latest" : 0,
                              "on_mode_second" : {},
                              "on_mode_second_latest" : 0,
                              "prime_mode_minutes" : {},
                              "prime_mode_minutes_latest" : 0,
                              "pump_status" : {},
                              "pump_status_latest" : 0,
                              "quarts" : { },
                              "quarts_latest" : "1.0",
                              "solar_volate_produced_today" : 345,
                              "tank_level" : 12,
                              "temprature_level" : 1
                            },
                            "battery_level":{"2018-08-07": 0},
                            "solar_production":{"2018-08-07": 0},
                            "tank_level":{"2018-08-07": 0},
                            "temprature_level":{"2018-08-07": 0}
                    }, function(error) {
                        if (error) {
                         alert('We are unable to insert device id into firebase. Delete the device and create again.');
                        } else {
                          // Data saved successfully!
                          console.log('data inserted successfully');
                        }
                  });

                  //return false;
                  $('#add-device')[0].reset();
                  //redirect to dashboard
                } else { // in case error occuer
                  swal({
                    title: "Error!!",
                    text: res.message,
                    type: "error",
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Try Again!",
                  });
                  return false;
                }
              },
              error: function(e) {
                swal({
                  title: "Error!!",
                  text: e.statusText,
                  type: "error",
                  confirmButtonClass: "btn-danger",
                  confirmButtonText: "Try Again!",
                });
                return false;
              },
              complete: function() {}
            }, "json");
            return false;
          }
      });
});

//editdevicesController
app.controller('editdevicesController', function($scope, $http, $routeParams) {

  console.log($routeParams);

  $http.get('manage/getdeviceinfo?id='+ $routeParams.id, { 
      cache: false
    }).
    success(function(response, status, headers, config) {
      $scope.deviceInfo = response.data.device;
    }).
    error(function(data, status, headers, config) {});

     // Edit device.
    $('#edit-device').validate({
          errorElement: 'span', //default input error message container
          errorClass: 'help-block', // default input error message class
          focusInvalid: false, // do not focus the last invalid input
          rules: {
            name: {
              required: true
            },
            unique_id: {
              required: true
            }
          },
          messages: {
            name: {
              required: "Name is required."
            },
            unique_id: {
              required: "Unique id is required."
            }
          },
          invalidHandler: function(event, validator) { //display error alert on form submit
            $('.alert-danger', $('#edit-device')).show();
          },
          highlight: function(element) { // hightlight error inputs
            $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
          },
          success: function(label) {
            label.closest('.form-group').removeClass('has-error');
            label.remove();
          },
          errorPlacement: function(error, element) {
            if (element.is(':checkbox')) {
              error.insertAfter(element.closest(".md-checkbox-list, .md-checkbox-inline, .checkbox-list, .checkbox-inline"));
            } else if (element.is(':radio')) {
              error.insertAfter(element.closest(".md-radio-list, .md-radio-inline, .radio-list,.radio-inline"));
            } else {
              error.insertAfter(element); // for other inputs, just perform default behavior
            }
          },
          submitHandler: function(form) {

            //define form data
            var fd = new FormData($('#edit-device')[0]);
            //return false;
            $.ajax({
              url: BASEURL + 'manage/editdevice',
              type: "post",
              processData: false,
              contentType: false,
              data: fd,
              beforeSend: function() {},
              success: function(res) {
                
                if (res.success == '1') // in case genre added successfully
                {
                  swal({
                    title: "Success!!",
                    text: res.message,
                    type: "success",
                    showConfirmButton: true,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Okay!",
                  });
                } else { // in case error occuer
                  swal({
                    title: "Error!!",
                    text: res.message,
                    type: "error",
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Try Again!",
                  });
                  return false;
                }
              },
              error: function(e) {
                swal({
                  title: "Error!!",
                  text: e.statusText,
                  type: "error",
                  confirmButtonClass: "btn-danger",
                  confirmButtonText: "Try Again!",
                });
                return false;
              },
              complete: function() {}
            }, "json");
            return false;
          }
    });
});

app.controller('adduserController', function($scope, $http) {

    $('#new-user-form').validate({
          errorElement: 'span', //default input error message container
          errorClass: 'help-block', // default input error message class
          focusInvalid: false, // do not focus the last invalid input
          rules: {
            name: {
              required: true
            },
            email: {
              required: true,
              email: true,
              remote: {
                url: BASEURL + 'manage/checkemailexist',
                type: "post"
              }
            }
          },
          messages: {
            name: {
              required: "Name is required."
            },
            email :{
              required: 'email is  required.',
              remote : "Email is already exist in database. Please try other one."
            }
          },
          invalidHandler: function(event, validator) { //display error alert on form submit
            $('.alert-danger', $('#new-user-form')).show();
          },
          highlight: function(element) { // hightlight error inputs
            $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
          },
          success: function(label) {
            label.closest('.form-group').removeClass('has-error');
            label.remove();
          },
          errorPlacement: function(error, element) {
            if (element.is(':checkbox')) {
              error.insertAfter(element.closest(".md-checkbox-list, .md-checkbox-inline, .checkbox-list, .checkbox-inline"));
            } else if (element.is(':radio')) {
              error.insertAfter(element.closest(".md-radio-list, .md-radio-inline, .radio-list,.radio-inline"));
            } else {
              error.insertAfter(element); // for other inputs, just perform default behavior
            }
          },
          submitHandler: function(form) {

            //define form data
            var fd = new FormData($('#new-user-form')[0]);
            //return false;
            $.ajax({
              url: BASEURL + 'manage/addnewuser',
              type: "post",
              processData: false,
              contentType: false,
              data: fd,
              beforeSend: function() {},
              success: function(res) {
                
                if (res.success == '1') // in case genre added successfully
                {
                  swal({
                    title: "Success!!",
                    text: res.message,
                    type: "success",
                    showConfirmButton: true,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Okay!",
                  });
                  //return false;
                  $('#new-user-form')[0].reset();
                  //redirect to dashboard
                } else { // in case error occuer
                  swal({
                    title: "Error!!",
                    text: res.message,
                    type: "error",
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Try Again!",
                  });
                  return false;
                }
              },
              error: function(e) {
                swal({
                  title: "Error!!",
                  text: e.statusText,
                  type: "error",
                  confirmButtonClass: "btn-danger",
                  confirmButtonText: "Try Again!",
                });
                return false;
              },
              complete: function() {}
            }, "json");
            return false;
          }
    });
});

//addsubuserController
app.controller('addsubuserController', function($scope, $http, $routeParams) {

  $scope.globalUserId = $routeParams.id;

   $('#new-user-form').validate({
          errorElement: 'span', //default input error message container
          errorClass: 'help-block', // default input error message class
          focusInvalid: false, // do not focus the last invalid input
          rules: {
            name: {
              required: true
            },
            email: {
              required: true,
              email: true,
              remote: {
                url: BASEURL + 'manage/checkemailexist',
                type: "post"
              }
            }
          },
          messages: {
            name: {
              required: "Name is required."
            },
            email :{
              required: 'email is  required.',
              remote : "Email is already exist in database. Please try other one."
            }
          },
          invalidHandler: function(event, validator) { //display error alert on form submit
            $('.alert-danger', $('#new-user-form')).show();
          },
          highlight: function(element) { // hightlight error inputs
            $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
          },
          success: function(label) {
            label.closest('.form-group').removeClass('has-error');
            label.remove();
          },
          errorPlacement: function(error, element) {
            if (element.is(':checkbox')) {
              error.insertAfter(element.closest(".md-checkbox-list, .md-checkbox-inline, .checkbox-list, .checkbox-inline"));
            } else if (element.is(':radio')) {
              error.insertAfter(element.closest(".md-radio-list, .md-radio-inline, .radio-list,.radio-inline"));
            } else {
              error.insertAfter(element); // for other inputs, just perform default behavior
            }
          },
          submitHandler: function(form) {

            //define form data
            var fd = new FormData($('#new-user-form')[0]);
            
            $.ajax({
              url: BASEURL + 'manage/addnewsubuser',
              type: "post",
              processData: false,
              contentType: false,
              data: fd,
              beforeSend: function() {},
              success: function(res) {
                
                if (res.success == '1') // in case genre added successfully
                {
                  swal({
                    title: "Success!!",
                    text: res.message,
                    type: "success",
                    showConfirmButton: true,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Okay!",
                  });
                  //return false;
                  $('#new-user-form')[0].reset();
                  //redirect to dashboard
                } else { // in case error occuer
                  swal({
                    title: "Error!!",
                    text: res.message,
                    type: "error",
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Try Again!",
                  });
                  return false;
                }
              },
              error: function(e) {
                swal({
                  title: "Error!!",
                  text: e.statusText,
                  type: "error",
                  confirmButtonClass: "btn-danger",
                  confirmButtonText: "Try Again!",
                });
                return false;
              },
              complete: function() {}
            }, "json");
            return false;
          }
    });

});

//edituserController 
app.controller('edituserController', function($scope, $http, $routeParams) {

    $('.mylink').click(function(event){
        event.preventDefault();
    });

    $http.get('manage/getuserinfo?id='+ $routeParams.id, { 
      cache: false
    }).
    success(function(response, status, headers, config) {
      $scope.userInfo = response.data.user;
      $scope.deviceList = response.data.devicelist;
    }).
    error(function(data, status, headers, config) {});


    // Edit user.
    $('#edit-user').validate({
          errorElement: 'span', //default input error message container
          errorClass: 'help-block', // default input error message class
          focusInvalid: false, // do not focus the last invalid input
          rules: {
            name: {
              required: true
            },
            email: {
              required: true,
              email: true,
              remote: {
                url: BASEURL + 'manage/checkemailexist',
                type: "post"
              }
            }
          },
          messages: {
            name: {
              required: "Name is required."
            },
            email :{
              required: 'email is  required.',
              remote : "Email is already exist in database. Please try other one."
            }
          },
          invalidHandler: function(event, validator) { //display error alert on form submit
            $('.alert-danger', $('#edit-user')).show();
          },
          highlight: function(element) { // hightlight error inputs
            $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
          },
          success: function(label) {
            label.closest('.form-group').removeClass('has-error');
            label.remove();
          },
          errorPlacement: function(error, element) {
            if (element.is(':checkbox')) {
              error.insertAfter(element.closest(".md-checkbox-list, .md-checkbox-inline, .checkbox-list, .checkbox-inline"));
            } else if (element.is(':radio')) {
              error.insertAfter(element.closest(".md-radio-list, .md-radio-inline, .radio-list,.radio-inline"));
            } else {
              error.insertAfter(element); // for other inputs, just perform default behavior
            }
          },
          submitHandler: function(form) {

            //define form data
            var fd = new FormData($('#edit-user')[0]);
            //return false;
            $.ajax({
              url: BASEURL + 'manage/edituser',
              type: "post",
              processData: false,
              contentType: false,
              data: fd,
              beforeSend: function() {},
              success: function(res) {
                
                if (res.success == '1') // in case genre added successfully
                {
                  swal({
                    title: "Success!!",
                    text: res.message,
                    type: "success",
                    showConfirmButton: true,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Okay!",
                  });
                } else { // in case error occuer
                  swal({
                    title: "Error!!",
                    text: res.message,
                    type: "error",
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Try Again!",
                  });
                  return false;
                }
              },
              error: function(e) {
                swal({
                  title: "Error!!",
                  text: e.statusText,
                  type: "error",
                  confirmButtonClass: "btn-danger",
                  confirmButtonText: "Try Again!",
                });
                return false;
              },
              complete: function() {}
            }, "json");
            return false;
          }
    });

    // Assigned device list
    $('#device_assigned_list').dataTable({
            "processing": false,
            "serverSide": true,
            "bAutoWidth": false,
            "filter": true,
            "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
            "ajax": {
              'type': 'POST',
              'url': BASEURL + "manage/assigneddevicelist?id="+ $routeParams.id,
              'data': function(d) {
                d.search = $('#search_user_string').val()
              }
            },
            "language": {
              "aria": {
                "sortAscending": ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
              },
              "emptyTable": "No device(s) available.",
              "info": "Found _START_ to _END_ of _TOTAL_ devices",
              "infoEmpty": "No Product request(s) found",
              "infoFiltered": "(filtered from _MAX_ total devices",
              "lengthMenu": "View _MENU_ devices",
              "search": "Search:",
              "zeroRecords": "No matching devices found"
            },
            "dom": "<'row'<'col-md-6 col-sm-12'><'col-md-6 col-sm-12'>r>t<'row'<'col-md-3 col-sm-12'l><'col-md-4 col-sm-12'i><'col-md-5 col-sm-12 text-right'p>>",
            "aoColumns": [
              { "data": "id",  "name": "id", "searchable": true, "orderable": true },
              { "data": "device_name",  "name": "No of request",  "searchable": false, "orderable": false },
              { "data": "created_on",  "name": "Created at",  "searchable": false, "orderable": false },
              { "data": "status",  "name": "No of request",  "searchable": false, "orderable": false , render : function(data){
                if(data == 1){
                  return '<p class="font-green-sharp"> Active </p>';
                }else{
                  return '<p class="font-red-mint"> Inactive </p>';
                }
              }},
              { "data": "action",  "name": "Created at",  "searchable": false, "orderable": false }
            ],
            "lengthMenu": [
              [10, 30, 50, -1],
              [10, 30, 50, "All"] // change per page values here
            ],
            "pageLength": 10,
            "pagingType": "bootstrap_full_number"
    });

    oTable1 = $('#device_assigned_list').DataTable();

    $('#search_user_string').keyup(function() {
        oTable1.search($(this).val()).draw();
    });

    // Assign device form validation
    $('#assign-device').validate({
          errorElement: 'span', //default input error message container
          errorClass: 'help-block', // default input error message class
          focusInvalid: false, // do not focus the last invalid input
          rules: {
            name: {
              required: true
            },
            email: {
              required: true,
            }
          },
          messages: {
            name: {
              required: "Name is required."
            },
            email :{
              required: 'email is  required.'
            }
          },
          invalidHandler: function(event, validator) { //display error alert on form submit
            $('.alert-danger', $('#assign-device')).show();
          },
          highlight: function(element) { // hightlight error inputs
            $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
          },
          success: function(label) {
            label.closest('.form-group').removeClass('has-error');
            label.remove();
          },
          errorPlacement: function(error, element) {
            if (element.is(':checkbox')) {
              error.insertAfter(element.closest(".md-checkbox-list, .md-checkbox-inline, .checkbox-list, .checkbox-inline"));
            } else if (element.is(':radio')) {
              error.insertAfter(element.closest(".md-radio-list, .md-radio-inline, .radio-list,.radio-inline"));
            } else {
              error.insertAfter(element); // for other inputs, just perform default behavior
            }
          },
          submitHandler: function(form) {

            //define form data
            var fd = new FormData($('#assign-device')[0]);
            //return false;
            $.ajax({
              url: BASEURL + 'manage/assigndevice',
              type: "post",
              processData: false,
              contentType: false,
              data: fd,
              beforeSend: function() {},
              success: function(res) {
                
                if (res.success == '1') // in case genre added successfully
                {
                  swal({
                    title: "Success!!",
                    text: res.message,
                    type: "success",
                    showConfirmButton: true,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Okay!",
                  });

                } else { // in case error occuer
                  swal({
                    title: "Error!!",
                    text: res.message,
                    type: "error",
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Try Again!",
                  });
                  return false;
                }
              },
              error: function(e) {
                swal({
                  title: "Error!!",
                  text: e.statusText,
                  type: "error",
                  confirmButtonClass: "btn-danger",
                  confirmButtonText: "Try Again!",
                });
                return false;
              },
              complete: function() {}
            }, "json");
            return false;
          }
    });
    // Revoke assigned devices

     // To delete the user.
    $(document).on('click', '.revoke_device', function() {

          // To delete the user.
          var currencyEditId = $(this).data('id');

          swal({
              title: "Are you sure?",
              text: "If you revoke this device from this user.",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: '#DD6B55',
              confirmButtonText: 'Yes, I am sure!',
              cancelButtonText: "No, cancel it!",
              closeOnConfirm: false,
              closeOnCancel: false
            },
            function(isConfirm) {
              // Is user delete to confirmed.
              if (isConfirm) {
                swal({
                  title: 'Deleted!',
                  text: 'Feedback is successfully removed!',
                  type: 'success'
                }, function() {
                  // To Order details.
                  $http.get('manage/revokeaccess?id=' + currencyEditId, {
                    cache: false
                  }).
                  success(function(data, status, headers, config) {
                    oTable1.draw();
                  }).
                  error(function(data, status, headers, config) {});
                });
              } else {
                swal("Cancelled", "Feedback not removed :)", "error");
              }
            });
    });

    $('.edit-user-device').select2({
        width : '308px',
        placeholder: "Select a device",
        allowClear: true
      });
});

// Main controller
app.controller('mainController', function($scope, $rootScope, $location, $http) {
  // create a message to display in our view
  
    $http.get('manage/getadmininfo', {
      cache: false
    }).
    success(function(response, status, headers, config) {
      $scope.adminInfo = response.data;
    }).
    error(function(data, status, headers, config) {});

    $scope.isActive = function (path) {
        return ($location.path().substr(1, $location.path().length) === path[0]) ? 'true' : '';
    }
});
