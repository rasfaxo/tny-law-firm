/*
   Licensed to the Apache Software Foundation (ASF) under one or more
   contributor license agreements.  See the NOTICE file distributed with
   this work for additional information regarding copyright ownership.
   The ASF licenses this file to You under the Apache License, Version 2.0
   (the "License"); you may not use this file except in compliance with
   the License.  You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/
var showControllersOnly = false;
var seriesFilter = "";
var filtersOnlySampleSeries = true;

/*
 * Add header in statistics table to group metrics by category
 * format
 *
 */
function summaryTableHeader(header) {
    var newRow = header.insertRow(-1);
    newRow.className = "tablesorter-no-sort";
    var cell = document.createElement('th');
    cell.setAttribute("data-sorter", false);
    cell.colSpan = 1;
    cell.innerHTML = "Requests";
    newRow.appendChild(cell);

    cell = document.createElement('th');
    cell.setAttribute("data-sorter", false);
    cell.colSpan = 3;
    cell.innerHTML = "Executions";
    newRow.appendChild(cell);

    cell = document.createElement('th');
    cell.setAttribute("data-sorter", false);
    cell.colSpan = 7;
    cell.innerHTML = "Response Times (ms)";
    newRow.appendChild(cell);

    cell = document.createElement('th');
    cell.setAttribute("data-sorter", false);
    cell.colSpan = 1;
    cell.innerHTML = "Throughput";
    newRow.appendChild(cell);

    cell = document.createElement('th');
    cell.setAttribute("data-sorter", false);
    cell.colSpan = 2;
    cell.innerHTML = "Network (KB/sec)";
    newRow.appendChild(cell);
}

/*
 * Populates the table identified by id parameter with the specified data and
 * format
 *
 */
function createTable(table, info, formatter, defaultSorts, seriesIndex, headerCreator) {
    var tableRef = table[0];

    // Create header and populate it with data.titles array
    var header = tableRef.createTHead();

    // Call callback is available
    if(headerCreator) {
        headerCreator(header);
    }

    var newRow = header.insertRow(-1);
    for (var index = 0; index < info.titles.length; index++) {
        var cell = document.createElement('th');
        cell.innerHTML = info.titles[index];
        newRow.appendChild(cell);
    }

    var tBody;

    // Create overall body if defined
    if(info.overall){
        tBody = document.createElement('tbody');
        tBody.className = "tablesorter-no-sort";
        tableRef.appendChild(tBody);
        var newRow = tBody.insertRow(-1);
        var data = info.overall.data;
        for(var index=0;index < data.length; index++){
            var cell = newRow.insertCell(-1);
            cell.innerHTML = formatter ? formatter(index, data[index]): data[index];
        }
    }

    // Create regular body
    tBody = document.createElement('tbody');
    tableRef.appendChild(tBody);

    var regexp;
    if(seriesFilter) {
        regexp = new RegExp(seriesFilter, 'i');
    }
    // Populate body with data.items array
    for(var index=0; index < info.items.length; index++){
        var item = info.items[index];
        if((!regexp || filtersOnlySampleSeries && !info.supportsControllersDiscrimination || regexp.test(item.data[seriesIndex]))
                &&
                (!showControllersOnly || !info.supportsControllersDiscrimination || item.isController)){
            if(item.data.length > 0) {
                var newRow = tBody.insertRow(-1);
                for(var col=0; col < item.data.length; col++){
                    var cell = newRow.insertCell(-1);
                    cell.innerHTML = formatter ? formatter(col, item.data[col]) : item.data[col];
                }
            }
        }
    }

    // Add support of columns sort
    table.tablesorter({sortList : defaultSorts});
}

$(document).ready(function() {

    // Customize table sorter default options
    $.extend( $.tablesorter.defaults, {
        theme: 'blue',
        cssInfoBlock: "tablesorter-no-sort",
        widthFixed: true,
        widgets: ['zebra']
    });

    var data = {"OkPercent": 100.0, "KoPercent": 0.0};
    var dataset = [
        {
            "label" : "FAIL",
            "data" : data.KoPercent,
            "color" : "#FF6347"
        },
        {
            "label" : "PASS",
            "data" : data.OkPercent,
            "color" : "#9ACD32"
        }];
    $.plot($("#flot-requests-summary"), dataset, {
        series : {
            pie : {
                show : true,
                radius : 1,
                label : {
                    show : true,
                    radius : 3 / 4,
                    formatter : function(label, series) {
                        return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">'
                            + label
                            + '<br/>'
                            + Math.round10(series.percent, -2)
                            + '%</div>';
                    },
                    background : {
                        opacity : 0.5,
                        color : '#000'
                    }
                }
            }
        },
        legend : {
            show : true
        }
    });

    // Creates APDEX table
    createTable($("#apdexTable"), {"supportsControllersDiscrimination": true, "overall": {"data": [0.0375, 500, 1500, "Total"], "isController": false}, "titles": ["Apdex", "T (Toleration threshold)", "F (Frustration threshold)", "Label"], "items": [{"data": [0.0, 500, 1500, "Setup 2 - POST Login Klien-2"], "isController": false}, {"data": [0.125, 500, 1500, "Setup 2 - POST Login Klien-1"], "isController": false}, {"data": [0.175, 500, 1500, "Setup 2 - POST Login Klien-0"], "isController": false}, {"data": [0.15, 500, 1500, "Setup 3 - GET Form Pra-Pendaftaran"], "isController": false}, {"data": [0.0, 500, 1500, "Step 1 - GET Login Page (Legal)"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 5 - POST Logout Klien-0"], "isController": false}, {"data": [0.0, 500, 1500, "PF-07 - POST Submit Verifikasi Berkas-1"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 5 - POST Logout Klien-1"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 4 - POST Submit Formulir Perkara-1"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 4 - POST Submit Formulir Perkara-0"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 2 - POST Login Klien"], "isController": false}, {"data": [0.0, 500, 1500, "Step 3 - GET Form Verifikasi Berkas"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 5 - POST Logout Klien"], "isController": false}, {"data": [0.0, 500, 1500, "PF-07 - POST Submit Verifikasi Berkas-0"], "isController": false}, {"data": [0.375, 500, 1500, "Setup 1 - GET Login Page (Klien)"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 4 - POST Submit Formulir Perkara"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Staf Legal"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Staf Legal-2"], "isController": false}, {"data": [0.0, 500, 1500, "PF-07 - POST Submit Verifikasi Berkas"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Staf Legal-1"], "isController": false}, {"data": [0.0, 500, 1500, "Step 5 - GET Riwayat Verifikasi"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Staf Legal-0"], "isController": false}]}, function(index, item){
        switch(index){
            case 0:
                item = item.toFixed(3);
                break;
            case 1:
            case 2:
                item = formatDuration(item);
                break;
        }
        return item;
    }, [[0, 0]], 3);

    // Create statistics table
    createTable($("#statisticsTable"), {"supportsControllersDiscrimination": true, "overall": {"data": ["Total", 440, 0, 0.0, 5929.2750000000015, 118, 18290, 5056.0, 11495.700000000003, 14378.849999999999, 17499.479999999985, 4.514116874589626, 111.3644435228835, 6.264198789139446], "isController": false}, "titles": ["Label", "#Samples", "FAIL", "Error %", "Average", "Min", "Max", "Median", "90th pct", "95th pct", "99th pct", "Transactions/s", "Received", "Sent"], "items": [{"data": ["Setup 2 - POST Login Klien-2", 20, 0, 0.0, 4720.25, 2627, 6603, 5004.0, 6543.100000000001, 6602.5, 6603.0, 0.6247266820765915, 32.27524725510714, 0.5582274551758605], "isController": false}, {"data": ["Setup 2 - POST Login Klien-1", 20, 0, 0.0, 2831.4, 827, 4904, 3164.5, 4749.3, 4896.3, 4904.0, 0.7367025195226168, 1.359014706424046, 0.6539673732871667], "isController": false}, {"data": ["Setup 2 - POST Login Klien-0", 20, 0, 0.0, 2608.5, 1043, 4243, 2916.0, 4179.800000000001, 4241.9, 4243.0, 0.8422825858075385, 1.5291048115392714, 0.8949252474205095], "isController": false}, {"data": ["Setup 3 - GET Form Pra-Pendaftaran", 20, 0, 0.0, 3468.35, 1002, 5972, 3712.0, 5656.500000000001, 5958.349999999999, 5972.0, 0.5709228968627786, 18.141102924909365, 0.5173988752818932], "isController": false}, {"data": ["Step 1 - GET Login Page (Legal)", 20, 0, 0.0, 3747.1499999999996, 2563, 5017, 3891.5, 4944.200000000001, 5015.75, 5017.0, 0.44545414049623594, 3.928609709786627, 0.3936874972159116], "isController": false}, {"data": ["Setup 5 - POST Logout Klien-0", 20, 0, 0.0, 4315.85, 2743, 5885, 4299.0, 5702.700000000001, 5876.55, 5885.0, 0.46164854696119845, 0.8155490443875077, 0.4679601481891836], "isController": false}, {"data": ["PF-07 - POST Submit Verifikasi Berkas-1", 20, 0, 0.0, 4911.8, 4025, 6040, 4896.0, 5881.1, 6032.9, 6040.0, 0.4506331395610833, 21.845739650928305, 0.4088263541525844], "isController": false}, {"data": ["Setup 5 - POST Logout Klien-1", 20, 0, 0.0, 3701.95, 2587, 5259, 3517.0, 4999.000000000001, 5247.95, 5259.0, 0.45586123583981036, 11.892747425808585, 0.40065928931233336], "isController": false}, {"data": ["Setup 4 - POST Submit Formulir Perkara-1", 20, 0, 0.0, 5777.699999999999, 3345, 7588, 6143.0, 7166.1, 7567.4, 7588.0, 0.4699910701696668, 14.711294215878647, 0.42409350472341023], "isController": false}, {"data": ["Setup 4 - POST Submit Formulir Perkara-0", 20, 0, 0.0, 5062.85, 2700, 6792, 5457.5, 6599.3, 6783.75, 6792.0, 0.5107513151846366, 0.9646416441084835, 1.400052152497574], "isController": false}, {"data": ["Setup 2 - POST Login Klien", 20, 0, 0.0, 10162.199999999999, 4546, 14802, 11293.5, 14464.1, 14785.55, 14802.0, 0.5839586557271744, 32.30642911106894, 1.6606324272241524], "isController": false}, {"data": ["Step 3 - GET Form Verifikasi Berkas", 20, 0, 0.0, 5376.6500000000015, 4638, 6579, 5307.0, 6245.6, 6564.05, 6579.0, 0.4248268830451591, 15.67974209689239, 0.3908075428013084], "isController": false}, {"data": ["Setup 5 - POST Logout Klien", 20, 0, 0.0, 8018.299999999997, 5901, 9802, 7653.0, 9681.7, 9796.25, 9802.0, 0.42894522369493415, 11.948323661958993, 0.8118123471882641], "isController": false}, {"data": ["PF-07 - POST Submit Verifikasi Berkas-0", 20, 0, 0.0, 5778.800000000001, 4746, 6732, 5812.5, 6649.5, 6728.1, 6732.0, 0.4285867352405443, 0.819923256187721, 0.5252698757098468], "isController": false}, {"data": ["Setup 1 - GET Login Page (Klien)", 20, 0, 0.0, 1598.95, 118, 3467, 1294.0, 3270.8, 3457.2999999999997, 3467.0, 0.9111617312072894, 8.035841400911162, 0.16194476082004555], "isController": false}, {"data": ["Setup 4 - POST Submit Formulir Perkara", 20, 0, 0.0, 10841.2, 6211, 13914, 11541.0, 13347.0, 13886.75, 13914.0, 0.4393094056143742, 14.580631980901023, 1.6006263934345208], "isController": false}, {"data": ["Step 2 - POST Login Staf Legal", 20, 0, 0.0, 16227.349999999999, 14281, 18290, 16154.0, 18207.8, 18286.35, 18290.0, 0.3429002503171827, 12.83819876641635, 0.9788060856221925], "isController": false}, {"data": ["Step 2 - POST Login Staf Legal-2", 20, 0, 0.0, 6780.5, 5457, 8910, 6637.5, 8128.000000000002, 8874.099999999999, 8910.0, 0.40333151834150077, 13.61464446326658, 0.3623681610099421], "isController": false}, {"data": ["PF-07 - POST Submit Verifikasi Berkas", 20, 0, 0.0, 10691.099999999997, 9088, 12773, 10621.0, 12305.300000000001, 12750.4, 12773.0, 0.39235688782516576, 19.77124290692314, 0.8368236748146114], "isController": false}, {"data": ["Step 2 - POST Login Staf Legal-1", 20, 0, 0.0, 4763.500000000001, 3553, 6407, 4731.5, 5847.700000000001, 6381.2, 6407.0, 0.42707666026051677, 0.798266335682255, 0.3791139493914158], "isController": false}, {"data": ["Step 5 - GET Riwayat Verifikasi", 20, 0, 0.0, 4377.599999999999, 2266, 6053, 4398.0, 5896.900000000001, 6046.5, 6053.0, 0.488770497812752, 23.47327475469831, 0.4434255785820768], "isController": false}, {"data": ["Step 2 - POST Login Staf Legal-0", 20, 0, 0.0, 4682.1, 3558, 5664, 4718.5, 5627.3, 5662.9, 5664.0, 0.4352936055369347, 0.7902449342706656, 0.4650500043529361], "isController": false}]}, function(index, item){
        switch(index){
            // Errors pct
            case 3:
                item = item.toFixed(2) + '%';
                break;
            // Mean
            case 4:
            // Mean
            case 7:
            // Median
            case 8:
            // Percentile 1
            case 9:
            // Percentile 2
            case 10:
            // Percentile 3
            case 11:
            // Throughput
            case 12:
            // Kbytes/s
            case 13:
            // Sent Kbytes/s
                item = item.toFixed(2);
                break;
        }
        return item;
    }, [[0, 0]], 0, summaryTableHeader);

    // Create error table
    createTable($("#errorsTable"), {"supportsControllersDiscrimination": false, "titles": ["Type of error", "Number of errors", "% in errors", "% in all samples"], "items": []}, function(index, item){
        switch(index){
            case 2:
            case 3:
                item = item.toFixed(2) + '%';
                break;
        }
        return item;
    }, [[1, 1]]);

        // Create top5 errors by sampler
    createTable($("#top5ErrorsBySamplerTable"), {"supportsControllersDiscrimination": false, "overall": {"data": ["Total", 440, 0, "", "", "", "", "", "", "", "", "", ""], "isController": false}, "titles": ["Sample", "#Samples", "#Errors", "Error", "#Errors", "Error", "#Errors", "Error", "#Errors", "Error", "#Errors", "Error", "#Errors"], "items": [{"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}]}, function(index, item){
        return item;
    }, [[0, 0]], 0);

});
