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
    createTable($("#apdexTable"), {"supportsControllersDiscrimination": true, "overall": {"data": [0.09318181818181819, 500, 1500, "Total"], "isController": false}, "titles": ["Apdex", "T (Toleration threshold)", "F (Frustration threshold)", "Label"], "items": [{"data": [0.0, 500, 1500, "Setup 2 - POST Login Klien-2"], "isController": false}, {"data": [0.25, 500, 1500, "Setup 2 - POST Login Klien-1"], "isController": false}, {"data": [0.2, 500, 1500, "Setup 2 - POST Login Klien-0"], "isController": false}, {"data": [0.25, 500, 1500, "Setup 3 - GET Form Pra-Pendaftaran"], "isController": false}, {"data": [0.25, 500, 1500, "Step 1 - GET Login Page (Legal)"], "isController": false}, {"data": [0.1, 500, 1500, "Setup 5 - POST Logout Klien-0"], "isController": false}, {"data": [0.0, 500, 1500, "PF-07 - POST Submit Verifikasi Berkas-1"], "isController": false}, {"data": [0.35, 500, 1500, "Setup 5 - POST Logout Klien-1"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 4 - POST Submit Formulir Perkara-1"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 4 - POST Submit Formulir Perkara-0"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 2 - POST Login Klien"], "isController": false}, {"data": [0.0, 500, 1500, "Step 3 - GET Form Verifikasi Berkas"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 5 - POST Logout Klien"], "isController": false}, {"data": [0.0, 500, 1500, "PF-07 - POST Submit Verifikasi Berkas-0"], "isController": false}, {"data": [0.6, 500, 1500, "Setup 1 - GET Login Page (Klien)"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 4 - POST Submit Formulir Perkara"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Staf Legal"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Staf Legal-2"], "isController": false}, {"data": [0.0, 500, 1500, "PF-07 - POST Submit Verifikasi Berkas"], "isController": false}, {"data": [0.05, 500, 1500, "Step 2 - POST Login Staf Legal-1"], "isController": false}, {"data": [0.0, 500, 1500, "Step 5 - GET Riwayat Verifikasi"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Staf Legal-0"], "isController": false}]}, function(index, item){
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
    createTable($("#statisticsTable"), {"supportsControllersDiscrimination": true, "overall": {"data": ["Total", 220, 0, 0.0, 3281.354545454545, 88, 11416, 2794.0, 7180.8, 8321.399999999994, 9389.569999999998, 4.252275934051067, 104.09914265902545, 5.904072218624968], "isController": false}, "titles": ["Label", "#Samples", "FAIL", "Error %", "Average", "Min", "Max", "Median", "90th pct", "95th pct", "99th pct", "Transactions/s", "Received", "Sent"], "items": [{"data": ["Setup 2 - POST Login Klien-2", 10, 0, 0.0, 3462.4999999999995, 2697, 4462, 3549.5, 4424.2, 4462.0, 4462.0, 0.6671559143371806, 34.45417264327173, 0.5961402945493361], "isController": false}, {"data": ["Setup 2 - POST Login Klien-1", 10, 0, 0.0, 1541.0, 800, 3071, 1207.5, 3038.0, 3071.0, 3071.0, 0.8281573498964804, 1.527723861283644, 0.735151397515528], "isController": false}, {"data": ["Setup 2 - POST Login Klien-0", 10, 0, 0.0, 1658.7999999999997, 1040, 2520, 1661.0, 2472.5, 2520.0, 2520.0, 0.929195316855603, 1.686888763705631, 0.9872700241590782], "isController": false}, {"data": ["Setup 3 - GET Form Pra-Pendaftaran", 10, 0, 0.0, 1774.1000000000001, 1008, 3016, 1632.0, 2969.7000000000003, 3016.0, 3016.0, 0.7037297677691766, 22.361288265306122, 0.6377551020408163], "isController": false}, {"data": ["Step 1 - GET Login Page (Legal)", 10, 0, 0.0, 1441.0, 925, 1978, 1403.0, 1977.8, 1978.0, 1978.0, 0.6645401382243488, 5.860802722953216, 0.5873133057549176], "isController": false}, {"data": ["Setup 5 - POST Logout Klien-0", 10, 0, 0.0, 1968.1, 1135, 3010, 1903.5, 3009.3, 3010.0, 3010.0, 0.5774338838203026, 1.0200956013973899, 0.5853284877006583], "isController": false}, {"data": ["PF-07 - POST Submit Verifikasi Berkas-1", 10, 0, 0.0, 2731.4, 1667, 3966, 2881.5, 3929.7000000000003, 3966.0, 3966.0, 0.5697681043815167, 26.84353366617287, 0.5169087587601846], "isController": false}, {"data": ["Setup 5 - POST Logout Klien-1", 10, 0, 0.0, 1278.0, 684, 2093, 1099.5, 2089.5, 2093.0, 2093.0, 0.647920176234288, 16.903503425877933, 0.5694610923934171], "isController": false}, {"data": ["Setup 4 - POST Submit Formulir Perkara-1", 10, 0, 0.0, 3357.9999999999995, 2962, 4133, 3320.0, 4087.7000000000003, 4133.0, 4133.0, 0.5446919766871834, 17.0484865222779, 0.4914994008388256], "isController": false}, {"data": ["Setup 4 - POST Submit Formulir Perkara-0", 10, 0, 0.0, 2938.5, 2071, 3916, 2712.0, 3907.5, 3916.0, 3916.0, 0.5970149253731344, 1.1275652985074627, 1.6414995335820894], "isController": false}, {"data": ["Setup 2 - POST Login Klien", 10, 0, 0.0, 6665.400000000001, 4864, 9016, 6569.0, 8967.9, 9016.0, 9016.0, 0.5794414184725923, 32.04514754027118, 1.6477865337814348], "isController": false}, {"data": ["Step 3 - GET Form Verifikasi Berkas", 10, 0, 0.0, 2780.8, 1678, 3504, 2884.0, 3497.8, 3504.0, 3504.0, 0.4996252810392206, 18.440271358980763, 0.4596162253310017], "isController": false}, {"data": ["Setup 5 - POST Logout Klien", 10, 0, 0.0, 3247.0, 1819, 5097, 3026.0, 5094.1, 5097.0, 5097.0, 0.5423581733376722, 15.107641148714611, 1.0264552147738366], "isController": false}, {"data": ["PF-07 - POST Submit Verifikasi Berkas-0", 10, 0, 0.0, 3477.8999999999996, 2317, 4340, 3489.0, 4310.3, 4340.0, 4340.0, 0.5088799552185639, 0.9735310862042644, 0.6236761169915017], "isController": false}, {"data": ["Setup 1 - GET Login Page (Klien)", 10, 0, 0.0, 860.9, 88, 2170, 975.0, 2121.3, 2170.0, 2170.0, 1.0055304172951232, 8.868110545500251, 0.1787173202614379], "isController": false}, {"data": ["Setup 4 - POST Submit Formulir Perkara", 10, 0, 0.0, 6297.6, 5034, 7283, 6399.5, 7282.7, 7283.0, 7283.0, 0.48945230287308505, 16.24393729198277, 1.7874090689393567], "isController": false}, {"data": ["Step 2 - POST Login Staf Legal", 10, 0, 0.0, 8978.8, 7192, 11416, 9211.0, 11217.2, 11416.0, 11416.0, 0.3917113870500215, 14.6637004729915, 1.1181370940890751], "isController": false}, {"data": ["Step 2 - POST Login Staf Legal-2", 10, 0, 0.0, 4503.8, 3929, 5033, 4599.5, 5009.0, 5033.0, 5033.0, 0.44089766765133814, 14.880468508884087, 0.39611899828049907], "isController": false}, {"data": ["PF-07 - POST Submit Verifikasi Berkas", 10, 0, 0.0, 6209.8, 4016, 7503, 6907.0, 7490.5, 7503.0, 7503.0, 0.468384074941452, 22.963078161592506, 0.9989754098360655], "isController": false}, {"data": ["Step 2 - POST Login Staf Legal-1", 10, 0, 0.0, 2170.9999999999995, 1178, 3303, 2130.0, 3243.9, 3303.0, 3303.0, 0.5184570717544588, 0.9690691751347988, 0.46023191232890914], "isController": false}, {"data": ["Step 5 - GET Riwayat Verifikasi", 10, 0, 0.0, 2542.2999999999997, 1653, 3908, 2249.5, 3881.5, 3908.0, 3908.0, 0.6356874960269532, 29.647993015383637, 0.5767125818447651], "isController": false}, {"data": ["Step 2 - POST Login Staf Legal-0", 10, 0, 0.0, 2303.1, 1670, 3541, 2096.5, 3485.7000000000003, 3541.0, 3541.0, 0.5664438654129377, 1.0283390095729013, 0.6051656140251501], "isController": false}]}, function(index, item){
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
    createTable($("#top5ErrorsBySamplerTable"), {"supportsControllersDiscrimination": false, "overall": {"data": ["Total", 220, 0, "", "", "", "", "", "", "", "", "", ""], "isController": false}, "titles": ["Sample", "#Samples", "#Errors", "Error", "#Errors", "Error", "#Errors", "Error", "#Errors", "Error", "#Errors", "Error", "#Errors"], "items": [{"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}]}, function(index, item){
        return item;
    }, [[0, 0]], 0);

});
