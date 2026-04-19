<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <!-- Datepicker -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="shortcut icon" href="images/favicon.ico">

    <style>
        .text-wrap {
            white-space: normal !important;
            word-wrap: break-word;
        }

        .text-center {
            text-align: center;
        }
    </style>

    <!-- Datatables -->
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-flash-1.6.1/b-html5-1.6.1/b-print-1.6.1/r-2.2.3/datatables.min.css" />

    <title>Sales Report</title>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-5">
                <h1 class="text-center">Sales Report</h1>
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-info text-white" id="basic-addon1"><i
                                        class="fas fa-calendar-alt"></i></span>
                            </div>
                            <input type="text" class="form-control" id="start_date" placeholder="Start Date" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-info text-white" id="basic-addon1"><i
                                        class="fas fa-calendar-alt"></i></span>
                            </div>
                            <input type="text" class="form-control" id="end_date" placeholder="End Date" readonly>
                        </div>
                    </div>
                </div>
                <div>
                    <button id="filter" class="btn btn-outline-info btn-sm">Filter</button>
                    <button id="reset" class="btn btn-outline-warning btn-sm">Reset</button>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-borderless display nowrap" id="records" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product Details</th>
                                        <th>Order Date</th>
                                        <th>Order Status</th>
                                        <th>Subtotal</th>
                                        <th>Shipping Charge</th>
                                        <th>Grand Total</th>
                                    </tr>
                                </thead>
                                <!-- <p id="total"></p> -->
                                <tfoot>
                                    <tr>
                                        <th colspan="6" style="text-align:center"> <label id="total"></label></th>
                                        <th></th> <!-- Leave this empty to dynamically populate the total  -->
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.0.min.js"
        integrity="sha256-xNzN2a4ltkB44Mc/Jz3pT4iU1cmeR0FkXs4pru/JxaQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
        </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
        </script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"></script>
    <!-- Datepicker -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <!-- Datatables -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript"
        src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-flash-1.6.1/b-html5-1.6.1/b-print-1.6.1/r-2.2.3/datatables.min.js">
        </script>
    <!-- Momentjs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>

    <script>
        $(function () {
            $("#start_date").datepicker({
                "dateFormat": "yy-mm-dd"
            });
            $("#end_date").datepicker({
                "dateFormat": "yy-mm-dd"
            });
        });
    </script>

    <script>
        // Fetch records

        function fetch(start_date, end_date) {
            $.ajax({
                url: "records.php",
                type: "POST",
                data: {
                    start_date: start_date,
                    end_date: end_date
                },
                dataType: "json",
                success: function (data) {
                    // Datatables
                    // Calculate grand total
                    // console.log(data);
                    // console.log(data[1]);
                    // console.log(data[0]);
                    var total = 0;

                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        total = total + parseFloat(data[i]["grand"]);
                    }
                    var integerTotal = parseFloat(total);
                    console.log(integerTotal);
                    // var i = "1";

                    $('#records').DataTable({

                        "data": data,
                        // buttons
                        "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
                            "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                        "buttons": [
                            // 'copy', 'csv', 'excel', 'pdf', 'print',
                            { extend: 'copyHtml5', footer: true },
                            { extend: 'excelHtml5', footer: true },
                            { extend: 'csvHtml5', footer: true },
                            {
                                extend: 'pdfHtml5', footer: true,
                                customize: function (doc) {
                                    doc.content[0].margin = [20, 0, 0, 0];// Adjust left margin
                                    // Set the text color to orange
                                    var orangeColor = '#FFA500'; // Hex color for orange 
                                    // Apply orange background color to all headers
                                    // Apply orange background color to the table header (thead)
                                    var headerRows = doc.content[1].table.body[0]; // Assuming the first row is the header
                                    headerRows.forEach(function (cell) {
                                        cell.fillColor = orangeColor;
                                    });

                                    // Apply orange background color to the table footer (tfoot)
                                    var footer = doc.content[1].table.body[doc.content[1].table.body.length - 1]; // Assuming the last row is the footer
                                    footer.forEach(function (cell) {
                                        cell.fillColor = orangeColor;
                                    });

                                }
                            }
                        ],
                        // responsive
                        "responsive": false,
                        "columns": [{
                            "data": "id",
                            // "render": function(data, type, row, meta) {
                            //     return i++;
                            // }
                        },
                        {
                            "data": "productdetails"
                        },
                        {
                            "data": "orderDate",
                            "render": function (data, type, row, meta) {
                                return moment(row.orderDate).format('DD-MM-YYYY');
                            }
                        },
                        {
                            "data": "ostatus", "className": "text-center"
                        },
                        {
                            "data": "subtotal",
                            "render": function (data, type, row, meta) {
                                return `${row.subtotal}`;
                            }, "className": "text-center"
                        },
                        {
                            "data": "charge",
                            "render": function (data, type, row, meta) {
                                return `${row.charge}`;
                            }, "className": "text-center"
                        },
                        {
                            "data": "grand",
                            "render": function (data, type, row, meta) {
                                return `${row.grand}`;

                            }, "className": "text-center"
                        }

                        ],
                        "createdRow": function (row, data, dataIndex) {
                            $('td:eq(1)', row).addClass('text-wrap'); // Apply text-wrap class to the Product Details column
                        }, "columnDefs": [
                            { "width": "10%", "targets": 0 },
                            { "width": "40%", "targets": 1 },
                            { "width": "10%", "targets": 2 },
                            { "width": "10%", "targets": 3 },
                            { "width": "10%", "targets": 4 },
                            { "width": "10%", "targets": 5 },
                            { "width": "10%", "targets": 6 }
                        ],

                        footerCallback: function (row, data, start, end, display) {
                            let api = this.api();
                            // Remove the formatting to get integer data for summation
                            let intVal = function (i) {
                                return typeof i === 'string'
                                    ? i.replace(/[\$,]/g, '') * 1
                                    : typeof i === 'number'
                                        ? i
                                        : 0;
                            };

                            // // Total over all pages
                            // total = api
                            //     .column(6)
                            //     .data()
                            //     .reduce((a, b) => intVal(a) + intVal(b), 0);

                            // // Total over this page
                            // pageTotal = api
                            //     .column(6, { page: 'current' })
                            //     .data()
                            //     .reduce((a, b) => intVal(a) + intVal(b), 0);
                            // Total over all pages
                            let total = api
                                .column(6)
                                .data()
                                .reduce((a, b, index) => {
                                    let rowData = api.row(index).data();
                                    return intVal(a) + (parseFloat(rowData.grand));
                                }, 0);

                            // Total over this page
                            let pageTotal = api
                                .column(6, { page: 'current' })
                                .data()
                                .reduce((a, b, index) => {
                                    let rowData = api.row(api.rows({ page: 'current' }).indexes()[index]).data();
                                    return intVal(a) + (parseFloat(rowData.grand));
                                }, 0);
                            // console.log(total);
                            // console.log(pageTotal);

                            $(api.column(6).footer()).html('RM ' + pageTotal.toFixed(2) + ' ( ' + total.toFixed(2) + ' Total)');

                        }

                        // "footerCallback": function (row, data, start, end, display) {
                        //     var api = this.api();
                        //     var integerTotal = api.column(6, { search: 'applied' }).data().reduce(function (a, b) {
                        //         return parseFloat(a) + parseFloat(b);
                        //     }, 0);

                        // $(api.column(6).footer()).html('Grand Total: ' + integerTotal.toFixed(2)  );
                        // document.getElementById("total").innerHTML = "RM "+ integerTotal.toFixed(2) ;
                        // }

                    });

                    //                 require_once('include/config.php');
                    //                 $price = $["price"];
                    //                 var total = 
                    // // total += parseFloat($(row.price)); // Assuming ".price" is the class containing the price value
                }
            });
        }

        fetch();


        // Filter

        $(document).on("click", "#filter", function (e) {
            e.preventDefault();

            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();

            if (start_date == "" || end_date == "") {
                alert("both date required");
            } else {
                $('#records').DataTable().destroy();
                fetch(start_date, end_date);
            }
        });

        // Reset

        $(document).on("click", "#reset", function (e) {
            e.preventDefault();

            $("#start_date").val(''); // empty value
            $("#end_date").val('');

            $('#records').DataTable().destroy();
            fetch();
        });
    </script>
</body>

</html>
