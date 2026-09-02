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
    createTable($("#apdexTable"), {"supportsControllersDiscrimination": true, "overall": {"data": [0.05384615384615385, 500, 1500, "Total"], "isController": false}, "titles": ["Apdex", "T (Toleration threshold)", "F (Frustration threshold)", "Label"], "items": [{"data": [0.0, 500, 1500, "PF-04 - POST Upload Dokumen-0"], "isController": false}, {"data": [0.0, 500, 1500, "PF-04 - POST Upload Dokumen-1"], "isController": false}, {"data": [0.1, 500, 1500, "PF-02 - GET Form Pra-Pendaftaran"], "isController": false}, {"data": [0.325, 500, 1500, "Step 1 - GET Login Page"], "isController": false}, {"data": [0.0, 500, 1500, "PF-04 - POST Upload Dokumen"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Klien-2"], "isController": false}, {"data": [0.0, 500, 1500, "Step 2 - POST Login Klien"], "isController": false}, {"data": [0.125, 500, 1500, "Step 2 - POST Login Klien-0"], "isController": false}, {"data": [0.0, 500, 1500, "PF-05 - GET Monitoring Status"], "isController": false}, {"data": [0.15, 500, 1500, "Step 2 - POST Login Klien-1"], "isController": false}, {"data": [0.0, 500, 1500, "PF-03 - POST Formulir Perkara-1"], "isController": false}, {"data": [0.0, 500, 1500, "PF-03 - POST Formulir Perkara-0"], "isController": false}, {"data": [0.0, 500, 1500, "PF-03 - POST Formulir Perkara"], "isController": false}]}, function(index, item){
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
    createTable($("#statisticsTable"), {"supportsControllersDiscrimination": true, "overall": {"data": ["Total", 260, 0, 0.0, 5977.223076923077, 82, 20066, 5410.0, 11812.5, 13298.4, 19015.709999999995, 3.8212254376037977, 95.53647743180582, 6.1926701363883545], "isController": false}, "titles": ["Label", "#Samples", "FAIL", "Error %", "Average", "Min", "Max", "Median", "90th pct", "95th pct", "99th pct", "Transactions/s", "Received", "Sent"], "items": [{"data": ["PF-04 - POST Upload Dokumen-0", 20, 0, 0.0, 4701.400000000001, 3315, 6470, 4428.0, 5978.6, 6445.7, 6470.0, 0.4169011735768036, 0.7873895211890022, 0.8842050476309591], "isController": false}, {"data": ["PF-04 - POST Upload Dokumen-1", 20, 0, 0.0, 5688.450000000001, 3891, 8471, 5410.0, 7082.900000000001, 8402.449999999999, 8471.0, 0.41195493213042494, 14.2703763208305, 0.37172495828956315], "isController": false}, {"data": ["PF-02 - GET Form Pra-Pendaftaran", 20, 0, 0.0, 4094.7500000000005, 1002, 6935, 4881.5, 6219.100000000001, 6901.549999999999, 6935.0, 0.5024746878376002, 15.966329485214683, 0.4553676858528252], "isController": false}, {"data": ["Step 1 - GET Login Page", 20, 0, 0.0, 1735.85, 82, 4431, 1556.0, 3279.7000000000003, 4374.099999999999, 4431.0, 0.862775548940943, 7.609107404771149, 0.15334487295630042], "isController": false}, {"data": ["PF-04 - POST Upload Dokumen", 20, 0, 0.0, 10390.399999999998, 7541, 14368, 9663.0, 13184.2, 14310.8, 14368.0, 0.38562393953416624, 14.08657136935061, 1.1658345625096407], "isController": false}, {"data": ["Step 2 - POST Login Klien-2", 20, 0, 0.0, 5451.75, 2665, 8515, 5792.0, 8154.900000000001, 8497.449999999999, 8515.0, 0.5226298735235706, 27.03045278529058, 0.46699837331451866], "isController": false}, {"data": ["Step 2 - POST Login Klien", 20, 0, 0.0, 11890.350000000002, 4519, 20066, 11889.0, 19163.100000000002, 20022.3, 20066.0, 0.4941688080648349, 27.36717958557768, 1.4052925479343743], "isController": false}, {"data": ["Step 2 - POST Login Klien-0", 20, 0, 0.0, 3118.95, 1051, 6329, 3021.5, 5778.8, 6302.799999999999, 6329.0, 0.7306202966318405, 1.3263897767954995, 0.7762840651713305], "isController": false}, {"data": ["PF-05 - GET Monitoring Status", 20, 0, 0.0, 5408.549999999999, 2458, 7856, 6072.0, 7747.300000000002, 7855.3, 7856.0, 0.4394252317968098, 15.011971762809244, 0.39651261150415257], "isController": false}, {"data": ["Step 2 - POST Login Klien-1", 20, 0, 0.0, 3317.6, 799, 6855, 3183.0, 6039.6, 6814.4, 6855.0, 0.6269592476489029, 1.1565683777429467, 0.5565487852664577], "isController": false}, {"data": ["PF-03 - POST Formulir Perkara-1", 20, 0, 0.0, 5839.05, 3584, 7517, 6061.0, 7058.500000000001, 7495.45, 7517.0, 0.4145421382083489, 12.98885205509265, 0.3740595075239398], "isController": false}, {"data": ["PF-03 - POST Formulir Perkara-0", 20, 0, 0.0, 5113.45, 2225, 7173, 5673.5, 7015.400000000001, 7165.55, 7173.0, 0.45073469755701795, 0.851289946362571, 1.2491601544893176], "isController": false}, {"data": ["PF-03 - POST Formulir Perkara", 20, 0, 0.0, 10953.35, 6153, 13956, 11758.5, 13521.300000000001, 13935.5, 13956.0, 0.39615727443795185, 13.161010077250669, 1.4553750495196593], "isController": false}]}, function(index, item){
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
