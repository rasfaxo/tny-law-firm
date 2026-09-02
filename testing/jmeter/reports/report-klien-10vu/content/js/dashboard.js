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
    createTable($("#apdexTable"), {"supportsControllersDiscrimination": true, "overall": {"data": [0.09230769230769231, 500, 1500, "Total"], "isController": false}, "titles": ["Apdex", "T (Toleration threshold)", "F (Frustration threshold)", "Label"], "items": [{"data": [0.0, 500, 1500, "PF-04 - POST Upload Dokumen-0"], "isController": false}, {"data": [0.0, 500, 1500, "PF-04 - POST Upload Dokumen-1"], "isController": false}, {"data": [0.15, 500, 1500, "PF-02 - GET Form Pra-Pendaftaran"], "isController": false}, {"data": [0.55, 500, 1500, "Step 1 - GET Login Page"], "isController": false}, {"data": [0.0, 500, 1500, "PF-04 - POST Upload Dokumen"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Klien-2"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Klien"], "isController": false}, {"data": [0.2, 500, 1500, "Step 2 - POST Login Klien-0"], "isController": false}, {"data": [0.0, 500, 1500, "PF-05 - GET Monitoring Status"], "isController": false}, {"data": [0.3, 500, 1500, "Step 2 - POST Login Klien-1"], "isController": false}, {"data": [0.0, 500, 1500, "PF-03 - POST Formulir Perkara-1"], "isController": false}, {"data": [0.0, 500, 1500, "PF-03 - POST Formulir Perkara-0"], "isController": false}, {"data": [0.0, 500, 1500, "PF-03 - POST Formulir Perkara"], "isController": false}]}, function(index, item){
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
    createTable($("#statisticsTable"), {"supportsControllersDiscrimination": true, "overall": {"data": ["Total", 130, 0, 0.0, 3668.5461538461536, 80, 9668, 3324.0, 7567.6, 8100.899999999999, 9546.169999999998, 3.4975382711399283, 87.44105786239878, 5.672517336355566], "isController": false}, "titles": ["Label", "#Samples", "FAIL", "Error %", "Average", "Min", "Max", "Median", "90th pct", "95th pct", "99th pct", "Transactions/s", "Received", "Sent"], "items": [{"data": ["PF-04 - POST Upload Dokumen-0", 10, 0, 0.0, 2804.9, 1913, 3835, 2767.0, 3811.6, 3835.0, 3835.0, 0.5519677650825191, 1.042485993817961, 1.1693738960644697], "isController": false}, {"data": ["PF-04 - POST Upload Dokumen-1", 10, 0, 0.0, 3915.2000000000003, 2483, 5005, 4349.0, 4998.7, 5005.0, 5005.0, 0.563888575617458, 19.532681748336525, 0.5088213319048156], "isController": false}, {"data": ["PF-02 - GET Form Pra-Pendaftaran", 10, 0, 0.0, 1960.8000000000002, 1019, 3367, 1994.0, 3304.5, 3367.0, 3367.0, 0.6178942165101335, 19.633407705913246, 0.5599666337123084], "isController": false}, {"data": ["Step 1 - GET Login Page", 10, 0, 0.0, 845.4000000000001, 80, 1749, 885.0, 1746.5, 1749.0, 1749.0, 0.9599692809830085, 8.466291578669482, 0.17061954017471442], "isController": false}, {"data": ["PF-04 - POST Upload Dokumen", 10, 0, 0.0, 6720.9, 4398, 8487, 7350.0, 8434.9, 8487.0, 8487.0, 0.4853897679836909, 17.7302833159402, 1.4663131916804193], "isController": false}, {"data": ["Step 2 - POST Login Klien-2", 10, 0, 0.0, 3302.5000000000005, 2630, 4300, 3236.0, 4263.1, 4300.0, 4300.0, 0.6416426050689765, 33.184702839268525, 0.5733427574590952], "isController": false}, {"data": ["Step 2 - POST Login Klien", 10, 0, 0.0, 6395.8, 4526, 9668, 5513.5, 9616.9, 9668.0, 9668.0, 0.5615453728661276, 31.097549205413298, 1.5968946540880504], "isController": false}, {"data": ["Step 2 - POST Login Klien-0", 10, 0, 0.0, 1612.2, 1041, 2122, 1646.0, 2118.8, 2122.0, 2122.0, 0.9310120100549297, 1.690186842472768, 0.9892002606833629], "isController": false}, {"data": ["PF-05 - GET Monitoring Status", 10, 0, 0.0, 3399.7, 2453, 4440, 3425.5, 4428.4, 4440.0, 4440.0, 0.6469142191745374, 22.099701610816407, 0.583739002458274], "isController": false}, {"data": ["Step 2 - POST Login Klien-1", 10, 0, 0.0, 1478.3, 791, 3244, 813.0, 3232.9, 3244.0, 3244.0, 0.8022462896109105, 1.4799250401123145, 0.7121502707581228], "isController": false}, {"data": ["PF-03 - POST Formulir Perkara-1", 10, 0, 0.0, 4210.0, 3510, 4995, 3919.5, 4989.9, 4995.0, 4995.0, 0.5063803929511849, 15.865827393913307, 0.45692918270204574], "isController": false}, {"data": ["PF-03 - POST Formulir Perkara-0", 10, 0, 0.0, 3417.2, 2776, 4279, 3246.5, 4272.8, 4279.0, 4279.0, 0.5350168530308704, 1.0104712829704137, 1.4883813566689852], "isController": false}, {"data": ["PF-03 - POST Formulir Perkara", 10, 0, 0.0, 7628.2, 6308, 9275, 7950.0, 9165.5, 9275.0, 9275.0, 0.4439511653718091, 14.748283157602664, 1.635639220310766], "isController": false}]}, function(index, item){
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
    createTable($("#top5ErrorsBySamplerTable"), {"supportsControllersDiscrimination": false, "overall": {"data": ["Total", 130, 0, "", "", "", "", "", "", "", "", "", ""], "isController": false}, "titles": ["Sample", "#Samples", "#Errors", "Error", "#Errors", "Error", "#Errors", "Error", "#Errors", "Error", "#Errors", "Error", "#Errors"], "items": [{"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}]}, function(index, item){
        return item;
    }, [[0, 0]], 0);

});
