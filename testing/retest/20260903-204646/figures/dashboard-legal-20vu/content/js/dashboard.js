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
    createTable($("#apdexTable"), {"supportsControllersDiscrimination": true, "overall": {"data": [0.017045454545454544, 500, 1500, "Total"], "isController": false}, "titles": ["Apdex", "T (Toleration threshold)", "F (Frustration threshold)", "Label"], "items": [{"data": [0.0, 500, 1500, "Setup 2 - POST Login Klien-2"], "isController": false}, {"data": [0.1, 500, 1500, "Setup 2 - POST Login Klien-1"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 2 - POST Login Klien-0"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 3 - GET Form Pra-Pendaftaran"], "isController": false}, {"data": [0.0, 500, 1500, "Step 1 - GET Login Page (Legal)"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 5 - POST Logout Klien-0"], "isController": false}, {"data": [0.0, 500, 1500, "PF-07 - POST Submit Verifikasi Berkas-1"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 5 - POST Logout Klien-1"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 4 - POST Submit Formulir Perkara-1"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 4 - POST Submit Formulir Perkara-0"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 2 - POST Login Klien"], "isController": false}, {"data": [0.0, 500, 1500, "Step 3 - GET Form Verifikasi Berkas"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 5 - POST Logout Klien"], "isController": false}, {"data": [0.0, 500, 1500, "PF-07 - POST Submit Verifikasi Berkas-0"], "isController": false}, {"data": [0.275, 500, 1500, "Setup 1 - GET Login Page (Klien)"], "isController": false}, {"data": [0.0, 500, 1500, "Setup 4 - POST Submit Formulir Perkara"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Staf Legal"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Staf Legal-2"], "isController": false}, {"data": [0.0, 500, 1500, "PF-07 - POST Submit Verifikasi Berkas"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Staf Legal-1"], "isController": false}, {"data": [0.0, 500, 1500, "Step 5 - GET Riwayat Verifikasi"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Staf Legal-0"], "isController": false}]}, function(index, item){
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
    createTable($("#statisticsTable"), {"supportsControllersDiscrimination": true, "overall": {"data": ["Total", 440, 0, 0.0, 6723.313636363639, 101, 28997, 5417.0, 13587.8, 15504.649999999994, 23549.66999999995, 4.419179237892454, 118.43130632256494, 6.136260275847176], "isController": false}, "titles": ["Label", "#Samples", "FAIL", "Error %", "Average", "Min", "Max", "Median", "90th pct", "95th pct", "99th pct", "Transactions/s", "Received", "Sent"], "items": [{"data": ["Setup 2 - POST Login Klien-2", 20, 0, 0.0, 5088.05, 2435, 7996, 5297.0, 7522.800000000001, 7974.2, 7996.0, 0.7561722560399259, 39.61655863643238, 0.675681263941926], "isController": false}, {"data": ["Setup 2 - POST Login Klien-1", 20, 0, 0.0, 3128.0, 871, 6494, 2788.5, 5006.5, 6419.899999999999, 6494.0, 0.942462654917299, 2.6985356486499223, 0.8366196809763913], "isController": false}, {"data": ["Setup 2 - POST Login Klien-0", 20, 0, 0.0, 2926.0000000000005, 1518, 5619, 2763.5, 5252.300000000001, 5602.95, 5619.0, 1.2305420537746876, 3.487336953177875, 1.3074509321356058], "isController": false}, {"data": ["Setup 3 - GET Form Pra-Pendaftaran", 20, 0, 0.0, 4683.65, 2305, 6326, 4929.0, 6287.900000000001, 6325.25, 6326.0, 0.6929766813346732, 22.527156023699803, 0.6280101174595475], "isController": false}, {"data": ["Step 1 - GET Login Page (Legal)", 20, 0, 0.0, 3243.8500000000004, 2324, 4682, 3278.5, 3907.4, 4643.749999999999, 4682.0, 0.7170771933598652, 6.849347683840666, 0.6337449804596466], "isController": false}, {"data": ["Setup 5 - POST Logout Klien-0", 20, 0, 0.0, 4305.499999999999, 2658, 6309, 4080.5, 6199.4000000000015, 6306.25, 6309.0, 0.6714338469802262, 1.870048175378521, 0.6806136066069091], "isController": false}, {"data": ["PF-07 - POST Submit Verifikasi Berkas-1", 20, 0, 0.0, 7060.849999999999, 1702, 14133, 6431.0, 13450.6, 14099.449999999999, 14133.0, 0.5158893933140735, 29.1237951161396, 0.46802856092653733], "isController": false}, {"data": ["Setup 5 - POST Logout Klien-1", 20, 0, 0.0, 3196.5999999999995, 1905, 4605, 3417.0, 4406.3, 4596.25, 4605.0, 0.7108078331023208, 19.062248718324625, 0.6247334470625866], "isController": false}, {"data": ["Setup 4 - POST Submit Formulir Perkara-1", 20, 0, 0.0, 6217.25, 4427, 8290, 6208.5, 7873.100000000002, 8274.25, 8290.0, 0.6242586928022973, 19.997676099475626, 0.5639055574630127], "isController": false}, {"data": ["Setup 4 - POST Submit Formulir Perkara-0", 20, 0, 0.0, 5805.400000000001, 4366, 7005, 5876.5, 6971.3, 7004.05, 7005.0, 0.6119202056051891, 1.7819785674947988, 1.6816750455115654], "isController": false}, {"data": ["Setup 2 - POST Login Klien", 20, 0, 0.0, 11145.9, 4834, 18213, 10631.5, 17124.100000000002, 18161.3, 18213.0, 0.693312996152113, 40.27329260841682, 1.971608832807571], "isController": false}, {"data": ["Step 3 - GET Form Verifikasi Berkas", 20, 0, 0.0, 7682.6, 3987, 14528, 5981.5, 13870.900000000001, 14496.85, 14528.0, 0.4501766943525334, 16.94680054499516, 0.41456701442816307], "isController": false}, {"data": ["Setup 5 - POST Logout Klien", 20, 0, 0.0, 7502.799999999999, 4729, 10449, 7573.0, 10388.300000000001, 10447.35, 10449.0, 0.5815136801093246, 17.214480190373042, 1.1005600703631553], "isController": false}, {"data": ["PF-07 - POST Submit Verifikasi Berkas-0", 20, 0, 0.0, 8755.799999999997, 3907, 15023, 7242.0, 14686.5, 15006.35, 15023.0, 0.46358536924574656, 1.3590657016364562, 0.5686164294654861], "isController": false}, {"data": ["Setup 1 - GET Login Page (Klien)", 20, 0, 0.0, 1531.55, 101, 3707, 1577.0, 2327.5, 3638.249999999999, 3707.0, 1.5460729746444033, 14.767714614254793, 0.27479031385281383], "isController": false}, {"data": ["Setup 4 - POST Submit Formulir Perkara", 20, 0, 0.0, 12023.35, 10245, 13984, 11765.5, 13727.9, 13971.5, 13984.0, 0.5368118742786591, 18.759635353759027, 1.9601758981533672], "isController": false}, {"data": ["Step 2 - POST Login Staf Legal", 20, 0, 0.0, 16457.35, 11308, 28997, 13755.0, 27103.800000000003, 28912.5, 28997.0, 0.38771712158808935, 15.594747462876084, 1.1067354945331884], "isController": false}, {"data": ["Step 2 - POST Login Staf Legal-2", 20, 0, 0.0, 7086.2, 4144, 14689, 5898.5, 14202.900000000001, 14666.1, 14689.0, 0.4599287110497873, 15.867675275957227, 0.41321720133379325], "isController": false}, {"data": ["PF-07 - POST Submit Verifikasi Berkas", 20, 0, 0.0, 15816.899999999998, 5678, 22210, 16320.0, 21243.1, 22162.149999999998, 22210.0, 0.44530536815621313, 26.44455061034667, 0.9501877240442634], "isController": false}, {"data": ["Step 2 - POST Login Staf Legal-1", 20, 0, 0.0, 4469.65, 2586, 12167, 4092.0, 6255.200000000002, 11874.549999999996, 12167.0, 0.5079494082389394, 1.4668031251587341, 0.45090430868085535], "isController": false}, {"data": ["Step 5 - GET Riwayat Verifikasi", 20, 0, 0.0, 4884.9, 1649, 7401, 5260.5, 7188.1, 7392.15, 7401.0, 0.7098743522396536, 39.64024344253567, 0.6440168683892951], "isController": false}, {"data": ["Step 2 - POST Login Staf Legal-0", 20, 0, 0.0, 4900.749999999999, 2917, 13248, 4342.5, 8719.300000000008, 13040.249999999996, 13248.0, 0.5329780146568954, 1.5104513657561627, 0.5694120586275816], "isController": false}]}, function(index, item){
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
