<?php
/**
 * Copyright 2016 David Rodal
 * User: David Markarian Rodal
 * Date: 2/1/16
 * Time: 4:02 PM
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */?>
<!DOCTYPE html>
<html lang="en" ng-app="analyticsApp">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="https://code.highcharts.com/modules/series-label.js"></script>
  <script src="https://code.highcharts.com/modules/exporting.js"></script>
  <link href="{{ mix('css/app.css')}}" rel="stylesheet" type="text/css">
  <style type="text/css">

    body{
      background: url("{{ asset('images/armoredKnight.jpg')}}") #fff;
      background-repeat: no-repeat;
      background-position: center top;
    }
    pre{
      font-size:15px;
    }

    .top-header{
      font-size:1.2em;
      color: black;

    }



  </style>
</head>
  <body>


  <div class="tabbable">
    <ul class="nav nav-tabs">
      <li><a href="{{URL::to('wargame/play')}}">Home</a></li>
      <li class="{{$indexTab or ''}}"><a href="{{URL::to('messages')}}">Analytics @include('messenger.unread-count')</a></li>
      <li ><a class="logout" href="{{url('/logout')}}">Logout</a></li>
    </ul>
  </div>
  <div class="coolBox">
    <h1 class="top-header">Analytics</h1>
    @yield('content')

  </div>
  <div id="graph" class="coolBox">

  </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.4/lodash.min.js"></script>
  <script>
    if(displayData) {
        var displayObj = JSON.parse(displayData);
        var xaxis = Object.keys(displayObj);
        var ties = _.map(displayObj, (obj) => {
            return obj[0];
        })
        var p1Win = _.map(displayObj, (obj) => {
            return obj[1];
        })
        var p2Win = _.map(displayObj, (obj) => {
            return obj[2];
        })
        var total = _.map(displayObj, (obj) => {
            return obj[0] + obj[1] + obj[2];
        })

        Highcharts.chart('graph', {

            title: {
                text: 'Wins vs Losses vs Ties'
            },

            subtitle: {
                text: ''
            },

            yAxis: {
                title: {
                    text: 'Games played'
                }
            },
            xAxis: {
                categories: xaxis
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },

            series: [{
                name: 'Ties',
                data: ties
            }, {
                name: 'P1 Win',
                data: p1Win
            }, {
                name: 'P2 Winn',
                data: p2Win
            }, {
                name: 'Total',
                data: total
            }],

            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }

        });
    }
  </script>
  <script src="{{ mix('javascripts/analytics.js')}}"></script>

  </body>
</html>