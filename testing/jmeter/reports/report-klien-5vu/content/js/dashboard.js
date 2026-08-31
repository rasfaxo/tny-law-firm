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
    createTable($("#apdexTable"), {"supportsControllersDiscrimination": true, "overall": {"data": [0.14615384615384616, 500, 1500, "Total"], "isController": false}, "titles": ["Apdex", "T (Toleration threshold)", "F (Frustration threshold)", "Label"], "items": [{"data": [0.1, 500, 1500, "PF-04 - POST Upload Dokumen-0"], "isController": false}, {"data": [0.0, 500, 1500, "PF-04 - POST Upload Dokumen-1"], "isController": false}, {"data": [0.1, 500, 1500, "PF-02 - GET Form Pra-Pendaftaran"], "isController": false}, {"data": [0.7, 500, 1500, "Step 1 - GET Login Page"], "isController": false}, {"data": [0.0, 500, 1500, "PF-04 - POST Upload Dokumen"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Klien-2"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Klien"], "isController": false}, {"data": [0.5, 500, 1500, "Step 2 - POST Login Klien-0"], "isController": false}, {"data": [0.0, 500, 1500, "PF-05 - GET Monitoring Status"], "isController": false}, {"data": [0.5, 500, 1500, "Step 2 - POST Login Klien-1"], "isController": false}, {"data": [0.0, 500, 1500, "PF-03 - POST Formulir Perkara-1"], "isController": false}, {"data": [0.0, 500, 1500, "PF-03 - POST Formulir Perkara-0"], "isController": false}, {"data": [0.0, 500, 1500, "PF-03 - POST Formulir Perkara"], "isController": false}]}, function(index, item){
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
    createTable($("#statisticsTable"), {"supportsControllersDiscrimination": true, "overall": {"data": ["Total", 65, 0, 0.0, 2826.876923076923, 101, 7258, 2526.0, 5034.4, 6920.4, 7258.0, 2.811175503849148, 63.23746898246259, 4.556165394429548], "isController": false}, "titles": ["Label", "#Samples", "FAIL", "Error %", "Average", "Min", "Max", "Median", "90th pct", "95th pct", "99th pct", "Transactions/s", "Received", "Sent"], "items": [{"data": ["PF-04 - POST Upload Dokumen-0", 5, 0, 0.0, 1515.0, 1374, 1564, 1556.0, 1564.0, 1564.0, 1564.0, 2.4764735017335315, 4.677245851906885, 5.2523487803368], "isController": false}, {"data": ["PF-04 - POST Upload Dokumen-1", 5, 0, 0.0, 2526.8, 2480, 2576, 2526.0, 2576.0, 2576.0, 2576.0, 1.6874789065136686, 58.44642518140398, 1.522686044549443], "isController": false}, {"data": ["PF-02 - GET Form Pra-Pendaftaran", 5, 0, 0.0, 1848.4, 1021, 2307, 2094.0, 2307.0, 2307.0, 2307.0, 1.9171779141104295, 60.919077118481596, 1.7374424846625767], "isController": false}, {"data": ["Step 1 - GET Login Page", 5, 0, 0.0, 731.4, 101, 2015, 218.0, 2015.0, 2015.0, 2015.0, 1.2843565373747752, 11.327171766632418, 0.22827430644746982], "isController": false}, {"data": ["PF-04 - POST Upload Dokumen", 5, 0, 0.0, 4043.4, 3855, 4098, 4093.0, 4098.0, 4098.0, 4098.0, 1.1111111111111112, 40.582248263888886, 3.359157986111111], "isController": false}, {"data": ["Step 2 - POST Login Klien-2", 5, 0, 0.0, 2705.2, 2671, 2738, 2697.0, 2738.0, 2738.0, 2738.0, 1.1676786548341895, 41.3796123306866, 1.0433847355207846], "isController": false}, {"data": ["Step 2 - POST Login Klien", 5, 0, 0.0, 4811.8, 4569, 5038, 4848.0, 5038.0, 5038.0, 5038.0, 0.77724234416291, 30.38835399502565, 2.2102829162132753], "isController": false}, {"data": ["Step 2 - POST Login Klien-0", 5, 0, 0.0, 1283.8, 1045, 1499, 1327.0, 1499.0, 1499.0, 1499.0, 1.7053206002728514, 3.0958896444406547, 1.8119031377899046], "isController": false}, {"data": ["PF-05 - GET Monitoring Status", 5, 0, 0.0, 2498.0, 2472, 2517, 2505.0, 2517.0, 2517.0, 2517.0, 1.7105713308244954, 58.429374786178585, 1.5435233492986657], "isController": false}, {"data": ["Step 2 - POST Login Klien-1", 5, 0, 0.0, 819.4, 811, 828, 819.0, 828.0, 828.0, 828.0, 2.0584602717167555, 3.7972963410868674, 1.8272855341704406], "isController": false}, {"data": ["PF-03 - POST Formulir Perkara-1", 5, 0, 0.0, 2973.4, 2840, 3482, 2848.0, 3482.0, 3482.0, 3482.0, 1.4297969688304262, 44.79704702244781, 1.2901683585930799], "isController": false}, {"data": ["PF-03 - POST Formulir Perkara-0", 5, 0, 0.0, 4008.4, 3773, 4083, 4060.0, 4083.0, 4083.0, 4083.0, 1.2201073694485114, 2.304382473157638, 3.3824617191312836], "isController": false}, {"data": ["PF-03 - POST Formulir Perkara", 5, 0, 0.0, 6984.4, 6902, 7258, 6927.0, 7258.0, 7258.0, 7258.0, 0.6593696426216538, 21.90407923150468, 2.422925870367928], "isController": false}]}, function(index, item){
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
    createTable($("#top5ErrorsBySamplerTable"), {"supportsControllersDiscrimination": false, "overall": {"data": ["Total", 65, 0, "", "", "", "", "", "", "", "", "", ""], "isController": false}, "titles": ["Sample", "#Samples", "#Errors", "Error", "#Errors", "Error", "#Errors", "Error", "#Errors", "Error", "#Errors", "Error", "#Errors"], "items": [{"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}]}, function(index, item){
        return item;
    }, [[0, 0]], 0);

});
