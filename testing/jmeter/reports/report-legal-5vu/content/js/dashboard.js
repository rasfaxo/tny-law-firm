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
    createTable($("#apdexTable"), {"supportsControllersDiscrimination": true, "overall": {"data": [0.2818181818181818, 500, 1500, "Total"], "isController": false}, "titles": ["Apdex", "T (Toleration threshold)", "F (Frustration threshold)", "Label"], "items": [{"data": [0.0, 500, 1500, "Setup 2 - POST Login Klien-2"], "isController": false}, {"data": [0.5, 500, 1500, "Setup 2 - POST Login Klien-1"], "isController": false}, {"data": [0.5, 500, 1500, "Setup 2 - POST Login Klien-0"], "isController": false}, {"data": [0.5, 500, 1500, "Setup 3 - GET Form Pra-Pendaftaran"], "isController": false}, {"data": [1.0, 500, 1500, "Step 1 - GET Login Page (Legal)"], "isController": false}, {"data": [0.5, 500, 1500, "Setup 5 - POST Logout Klien-0"], "isController": false}, {"data": [0.0, 500, 1500, "PF-07 - POST Submit Verifikasi Berkas-1"], "isController": false}, {"data": [1.0, 500, 1500, "Setup 5 - POST Logout Klien-1"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 4 - POST Submit Formulir Perkara-1"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 4 - POST Submit Formulir Perkara-0"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 2 - POST Login Klien"], "isController": false}, {"data": [0.0, 500, 1500, "Step 3 - GET Form Verifikasi Berkas"], "isController": false}, {"data": [0.5, 500, 1500, "Setup 5 - POST Logout Klien"], "isController": false}, {"data": [0.0, 500, 1500, "PF-07 - POST Submit Verifikasi Berkas-0"], "isController": false}, {"data": [0.7, 500, 1500, "Setup 1 - GET Login Page (Klien)"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 4 - POST Submit Formulir Perkara"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Staf Legal"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Staf Legal-2"], "isController": false}, {"data": [0.0, 500, 1500, "PF-07 - POST Submit Verifikasi Berkas"], "isController": false}, {"data": [0.5, 500, 1500, "Step 2 - POST Login Staf Legal-1"], "isController": false}, {"data": [0.0, 500, 1500, "Step 5 - GET Riwayat Verifikasi"], "isController": false}, {"data": [0.5, 500, 1500, "Step 2 - POST Login Staf Legal-0"], "isController": false}]}, function(index, item){
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
    createTable($("#statisticsTable"), {"supportsControllersDiscrimination": true, "overall": {"data": ["Total", 110, 0, 0.0, 1977.0454545454547, 45, 5093, 1658.5, 4633.8, 4970.199999999999, 5092.67, 4.05799240048696, 99.38194153032427, 5.6329862904415835], "isController": false}, "titles": ["Label", "#Samples", "FAIL", "Error %", "Average", "Min", "Max", "Median", "90th pct", "95th pct", "99th pct", "Transactions/s", "Received", "Sent"], "items": [{"data": ["Setup 2 - POST Login Klien-2", 5, 0, 0.0, 2750.8, 2656, 2825, 2791.0, 2825.0, 2825.0, 2825.0, 1.1200716845878136, 57.89151755712365, 1.0008453040994623], "isController": false}, {"data": ["Setup 2 - POST Login Klien-1", 5, 0, 0.0, 813.8, 801, 829, 815.0, 829.0, 829.0, 829.0, 1.9062142584826536, 3.5164440764391913, 1.6921374618757148], "isController": false}, {"data": ["Setup 2 - POST Login Klien-0", 5, 0, 0.0, 1277.4, 1065, 1445, 1348.0, 1445.0, 1445.0, 1445.0, 1.538935056940597, 2.793828389504463, 1.6351184979993845], "isController": false}, {"data": ["Setup 3 - GET Form Pra-Pendaftaran", 5, 0, 0.0, 1035.4, 994, 1084, 1037.0, 1084.0, 1084.0, 1084.0, 1.8301610541727673, 58.15408240300146, 1.6585834553440701], "isController": false}, {"data": ["Step 1 - GET Login Page (Legal)", 5, 0, 0.0, 75.0, 45, 91, 82.0, 91.0, 91.0, 91.0, 3.604902667627974, 31.792847647801008, 3.1859735490266763], "isController": false}, {"data": ["Setup 5 - POST Logout Klien-0", 5, 0, 0.0, 828.6, 795, 905, 808.0, 905.0, 905.0, 905.0, 2.311604253351826, 4.083683685852982, 2.343208217753121], "isController": false}, {"data": ["PF-07 - POST Submit Verifikasi Berkas-1", 5, 0, 0.0, 1671.2, 1630, 1710, 1679.0, 1710.0, 1710.0, 1710.0, 1.7029972752043596, 80.40508717217303, 1.5450043639305178], "isController": false}, {"data": ["Setup 5 - POST Logout Klien-1", 5, 0, 0.0, 81.2, 51, 105, 89.0, 105.0, 105.0, 105.0, 3.5971223021582737, 93.83992805755396, 3.1615332733812953], "isController": false}, {"data": ["Setup 4 - POST Submit Formulir Perkara-1", 5, 0, 0.0, 2542.6, 2477, 2643, 2508.0, 2643.0, 2643.0, 2643.0, 1.2742099898063202, 39.88252381179919, 1.1497754204892967], "isController": false}, {"data": ["Setup 4 - POST Submit Formulir Perkara-0", 5, 0, 0.0, 1987.8, 1795, 2141, 2061.0, 2141.0, 2141.0, 2141.0, 1.4249073810202335, 2.691182495012824, 3.912650951125677], "isController": false}, {"data": ["Setup 2 - POST Login Klien", 5, 0, 0.0, 4846.0, 4545, 5090, 4945.0, 5090.0, 5090.0, 5090.0, 0.743052459503641, 41.1247608299896, 2.113055431713479], "isController": false}, {"data": ["Step 3 - GET Form Verifikasi Berkas", 5, 0, 0.0, 1672.6, 1620, 1742, 1648.0, 1742.0, 1742.0, 1742.0, 1.6744809109176155, 61.80208158908238, 1.5403916192230407], "isController": false}, {"data": ["Setup 5 - POST Logout Klien", 5, 0, 0.0, 911.2, 860, 972, 914.0, 972.0, 972.0, 972.0, 2.2036139268400174, 61.37968612274129, 4.170511513882768], "isController": false}, {"data": ["PF-07 - POST Submit Verifikasi Berkas-0", 5, 0, 0.0, 2292.4, 2282, 2311, 2286.0, 2311.0, 2311.0, 2311.0, 1.4132278123233466, 2.7036262542396834, 1.7320321332673827], "isController": false}, {"data": ["Setup 1 - GET Login Page (Klien)", 5, 0, 0.0, 594.8, 85, 1692, 176.0, 1692.0, 1692.0, 1692.0, 1.2843565373747752, 11.327171766632418, 0.22827430644746982], "isController": false}, {"data": ["Setup 4 - POST Submit Formulir Perkara", 5, 0, 0.0, 4530.8, 4272, 4726, 4641.0, 4726.0, 4726.0, 4726.0, 0.831946755407654, 27.611045393094845, 3.035143250831947], "isController": false}, {"data": ["Step 2 - POST Login Staf Legal", 5, 0, 0.0, 4976.8, 4864, 5093, 5001.0, 5093.0, 5093.0, 5093.0, 0.8114248620577734, 30.375182570593964, 2.3162059294871797], "isController": false}, {"data": ["Step 2 - POST Login Staf Legal-2", 5, 0, 0.0, 2982.8, 2956, 3012, 2978.0, 3012.0, 3012.0, 3012.0, 1.1775788977861517, 39.74305780440414, 1.0579810409797454], "isController": false}, {"data": ["PF-07 - POST Submit Verifikasi Berkas", 5, 0, 0.0, 3964.4, 3928, 3997, 3963.0, 3997.0, 3997.0, 3997.0, 0.9582215408202377, 47.07450471924109, 2.0437068800306633], "isController": false}, {"data": ["Step 2 - POST Login Staf Legal-1", 5, 0, 0.0, 820.2, 807, 835, 819.0, 835.0, 835.0, 835.0, 2.416626389560174, 4.5170145601739975, 2.1452279180763654], "isController": false}, {"data": ["Step 5 - GET Riwayat Verifikasi", 5, 0, 0.0, 1667.4, 1654, 1685, 1664.0, 1685.0, 1685.0, 1685.0, 1.7182130584192439, 80.0133564218213, 1.5588085266323024], "isController": false}, {"data": ["Step 2 - POST Login Staf Legal-0", 5, 0, 0.0, 1171.8, 1043, 1294, 1216.0, 1294.0, 1294.0, 1294.0, 2.1358393848782575, 3.877466227039726, 2.281844030328919], "isController": false}]}, function(index, item){
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
    createTable($("#top5ErrorsBySamplerTable"), {"supportsControllersDiscrimination": false, "overall": {"data": ["Total", 110, 0, "", "", "", "", "", "", "", "", "", ""], "isController": false}, "titles": ["Sample", "#Samples", "#Errors", "Error", "#Errors", "Error", "#Errors", "Error", "#Errors", "Error", "#Errors", "Error", "#Errors"], "items": [{"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}, {"data": [], "isController": false}]}, function(index, item){
        return item;
    }, [[0, 0]], 0);

});
