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
    createTable($("#apdexTable"), {"supportsControllersDiscrimination": true, "overall": {"data": [0.04230769230769231, 500, 1500, "Total"], "isController": false}, "titles": ["Apdex", "T (Toleration threshold)", "F (Frustration threshold)", "Label"], "items": [{"data": [0.0, 500, 1500, "PF-04 - POST Upload Dokumen-0"], "isController": false}, {"data": [0.0, 500, 1500, "PF-04 - POST Upload Dokumen-1"], "isController": false}, {"data": [0.0, 500, 1500, "PF-02 - GET Form Pra-Pendaftaran"], "isController": false}, {"data": [0.3, 500, 1500, "Step 1 - GET Login Page"], "isController": false}, {"data": [0.0, 500, 1500, "PF-04 - POST Upload Dokumen"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Klien-2"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Klien"], "isController": false}, {"data": [0.125, 500, 1500, "Step 2 - POST Login Klien-0"], "isController": false}, {"data": [0.0, 500, 1500, "PF-05 - GET Monitoring Status"], "isController": false}, {"data": [0.125, 500, 1500, "Step 2 - POST Login Klien-1"], "isController": false}, {"data": [0.0, 500, 1500, "PF-03 - POST Formulir Perkara-1"], "isController": false}, {"data": [0.0, 500, 1500, "PF-03 - POST Formulir Perkara-0"], "isController": false}, {"data": [0.0, 500, 1500, "PF-03 - POST Formulir Perkara"], "isController": false}]}, function(index, item){
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
    createTable($("#statisticsTable"), {"supportsControllersDiscrimination": true, "overall": {"data": ["Total", 260, 0, 0.0, 7091.957692307688, 232, 18653, 6621.5, 14641.0, 15945.5, 17675.47999999998, 3.8378083162354053, 100.31841239279969, 6.218621765539433], "isController": false}, "titles": ["Label", "#Samples", "FAIL", "Error %", "Average", "Min", "Max", "Median", "90th pct", "95th pct", "99th pct", "Transactions/s", "Received", "Sent"], "items": [{"data": ["PF-04 - POST Upload Dokumen-0", 20, 0, 0.0, 6554.35, 4506, 8793, 6690.5, 8116.9000000000015, 8763.15, 8793.0, 0.49813200498132004, 1.4506148816936488, 1.0584332191780823], "isController": false}, {"data": ["PF-04 - POST Upload Dokumen-1", 20, 0, 0.0, 7481.500000000002, 5190, 9623, 7724.0, 9299.7, 9607.85, 9623.0, 0.5045663252434532, 17.848713474128363, 0.4557850105958928], "isController": false}, {"data": ["PF-02 - GET Form Pra-Pendaftaran", 20, 0, 0.0, 5021.15, 1971, 6888, 5582.5, 6801.6, 6883.95, 6888.0, 0.6254104255917946, 20.33072485068326, 0.5667781981925639], "isController": false}, {"data": ["Step 1 - GET Login Page", 20, 0, 0.0, 1505.0000000000002, 232, 3651, 1505.0, 2739.600000000001, 3607.3499999999995, 3651.0, 1.5421389467190993, 14.73013773228468, 0.27409110185827745], "isController": false}, {"data": ["PF-04 - POST Upload Dokumen", 20, 0, 0.0, 14036.25, 10189, 16537, 14299.0, 16443.4, 16533.25, 16537.0, 0.4312482480539923, 16.510975099456626, 1.3058736011384953], "isController": false}, {"data": ["Step 2 - POST Login Klien-2", 20, 0, 0.0, 5108.849999999999, 2175, 7767, 5566.0, 7365.000000000001, 7749.45, 7767.0, 0.731288164101064, 38.36813331840287, 0.653445966945775], "isController": false}, {"data": ["Step 2 - POST Login Klien", 20, 0, 0.0, 11271.55, 4409, 18653, 11909.0, 18303.800000000003, 18641.7, 18653.0, 0.6760868095463458, 39.323757373571766, 1.9226218646474207], "isController": false}, {"data": ["Step 2 - POST Login Klien-0", 20, 0, 0.0, 2769.1000000000004, 1393, 5615, 2487.0, 4839.300000000001, 5577.799999999999, 5615.0, 1.2917393270038107, 3.6607690693018147, 1.3724730349415488], "isController": false}, {"data": ["PF-05 - GET Monitoring Status", 20, 0, 0.0, 6531.849999999999, 2390, 9352, 6674.0, 8147.1, 9292.55, 9352.0, 0.5860462390482609, 20.45095342397515, 0.5293874717965247], "isController": false}, {"data": ["Step 2 - POST Login Klien-1", 20, 0, 0.0, 3392.35, 838, 7305, 3605.0, 5893.1, 7234.999999999999, 7305.0, 0.9348852428364418, 2.6768393867152804, 0.8298932477913336], "isController": false}, {"data": ["PF-03 - POST Formulir Perkara-1", 20, 0, 0.0, 7557.55, 6374, 9144, 7479.5, 8802.7, 9126.949999999999, 9144.0, 0.47594117367093425, 15.261723026033982, 0.4299273297320451], "isController": false}, {"data": ["PF-03 - POST Formulir Perkara-0", 20, 0, 0.0, 6703.9000000000015, 4093, 9200, 6766.0, 8148.7, 9147.75, 9200.0, 0.5101260011222772, 1.4855427102994438, 1.4097212480232617], "isController": false}, {"data": ["PF-03 - POST Formulir Perkara", 20, 0, 0.0, 14262.05, 10468, 16479, 14815.5, 16093.800000000001, 16460.55, 16479.0, 0.43368895827912224, 15.16979600355625, 1.5902493169398908], "isController": false}]}, function(index, item){
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
    createTable($("#top5ErrorsBySamplerTable"), {"supportsControllersDiscrimination": false, "overall": {"data": ["Total", 260, 0, "", "", "", "", "", "", "", "", "", ""], "isController": false}, "titles": ["Sample", "#Samples", "#Errors", "Error", "#Errors", "Error", "#Errors", "Error", "#Errors", "Error", "#Errors", "Error", "#Errors"], "items": [{"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}]}, function(index, item){
        return item;
    }, [[0, 0]], 0);

});
