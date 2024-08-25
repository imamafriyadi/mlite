jQuery().ready(function () {
    var var_tbl_NAMA_TABLE = $('#tbl_NAMA_TABLE').DataTable({
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'dom': 'Bfrtip',
        'searching': false,
        'select': true,
        'colReorder': true,
        "bInfo" : false,
        "ajax": {
            "url": "{?=url(['MODULE_NAME','data'])?}",
            "dataType": "json",
            "type": "POST",
            "data": function (data) {

                // Read values
                var search_field_NAMA_TABLE = $('#search_field_NAMA_TABLE').val();
                var search_text_NAMA_TABLE = $('#search_text_NAMA_TABLE').val();
                
                data.search_field_NAMA_TABLE = search_field_NAMA_TABLE;
                data.search_text_NAMA_TABLE = search_text_NAMA_TABLE;
                
            }
        },
        "columns": [
COLUMNS_ISI
        ],
        "columnDefs": [
COLUMNDEFS_ISI
        ],
        order: [[1, 'DESC']], 
        buttons: [],
        "scrollCollapse": true,
        // "scrollY": '48vh', 
        // "pageLength":'25', 
        "lengthChange": true,
        "scrollX": true,
        dom: "<'row'<'col-sm-12'tr>><<'pmd-datatable-pagination' l i p>>"
    });


    $.contextMenu({
        selector: '#tbl_NAMA_TABLE tr', 
        trigger: 'right',
        callback: function(key, options) {
          var rowData = var_tbl_NAMA_TABLE.rows({ selected: true }).data()[0];
          if (rowData != null) {
DATA_ISI
            switch (key) {
                case 'detail' :
                    OpenModal(mlite.url + '/MODULE_NAME/detail/' + DEL_FIELD + '?t=' + mlite.token);
                break;
                default :
                break
            } 
          } else {
            bootbox.alert("Silakan pilih data atau klik baris data.");            
          }          
        },
        items: {
            "detail": {name: "View Detail", "icon": "edit", disabled:  {$disabled_menu.read}}
        }
    });

    // ==============================================================
    // FORM VALIDASI
    // ==============================================================

    $("form[name='form_NAMA_TABLE']").validate({
        rules: {
RULES_ISI
        },
        messages: {
MESSAGES_ISI
        },
        submitHandler: function (form) {
SUBMITHANDLER_ISI
var typeact = $('#typeact').val();

var formData = new FormData(form); // tambahan
formData.append('typeact', typeact); // tambahan

            $.ajax({
                url: "{?=url(['MODULE_NAME','aksi'])?}",
                method: "POST",
                contentType: false, // tambahan
                processData: false, // tambahan
                data: formData,
                success: function (data) {
                    data = JSON.parse(data);
                    var audio = new Audio('{?=url()?}/assets/sound/' + data.status + '.mp3');
                    audio.play();
                    if (typeact == "add") {
                        if(data.status === 'success') {
                            bootbox.alert('<span class="text-success">' + data.msg + '</span>');
                            $("#modal_NAMA_TABLE").modal('hide');
                        } else {
                            bootbox.alert('<span class="text-danger">' + data.msg + '</span>');
                        }    
                    }
                    else if (typeact == "edit") {
                        if(data.status === 'success') {
                            bootbox.alert('<span class="text-success">' + data.msg + '</span>');
                            $("#modal_NAMA_TABLE").modal('hide');
                        } else {
                            bootbox.alert('<span class="text-danger">' + data.msg + '</span>');
                        }    
                    }
                    var_tbl_NAMA_TABLE.draw();
                }
            })
        }
    });

    // ==============================================================
    // KETIKA TOMBOL SEARCH DITEKAN
    // ==============================================================
    $('#filter_search_NAMA_TABLE').click(function () {
        var_tbl_NAMA_TABLE.draw();
    });

    // ===========================================
    // KETIKA TOMBOL EDIT DITEKAN
    // ===========================================

    $("#edit_data_NAMA_TABLE").click(function () {
        var rowData = var_tbl_NAMA_TABLE.rows({ selected: true }).data()[0];
        if (rowData != null) {

            EDIT_ISI
            $("#typeact").val("edit");
  
            FORM_ISI
            $("#EDIT_FIELD").prop('readonly', true); // GA BISA DIEDIT KALI READONLY
            $('#modal-title').text("Edit Data NAMA_MODULE");
            $("#modal_NAMA_TABLE").modal('show');
        }
        else {
            bootbox.alert("Silakan pilih data yang akan di edit.");
        }

    });

    // ==============================================================
    // TOMBOL  DELETE DI CLICK
    // ==============================================================
    jQuery("#hapus_data_NAMA_TABLE").click(function () {
        var rowData = var_tbl_NAMA_TABLE.rows({ selected: true }).data()[0];


        if (rowData) {
DELETE_ISI
            bootbox.confirm('Anda yakin akan menghapus data dengan DEL_FIELD="' + DEL_FIELD, function(result) {
                if(result) {
                    $.ajax({
                        url: "{?=url(['MODULE_NAME','aksi'])?}",
                        method: "POST",
                        data: {
                            DEL_FIELD: DEL_FIELD,
                            typeact: 'del'
                        },
                        success: function (data) {
                            data = JSON.parse(data);
                            var audio = new Audio('{?=url()?}/assets/sound/' + data.status + '.mp3');
                            audio.play();
                            if(data.status === 'success') {
                                bootbox.alert('<span class="text-success">' + data.msg + '</span>');
                            } else {
                                bootbox.alert('<span class="text-danger">' + data.msg + '</span>');
                            }    
                            var_tbl_NAMA_TABLE.draw();
                        }
                    })    
                }
            });

        }
        else {
            bootbox.alert("Pilih satu baris untuk dihapus");
        }
    });

    // ==============================================================
    // TOMBOL TAMBAH DATA DI CLICK
    // ==============================================================
    jQuery("#tambah_data_NAMA_TABLE").click(function () {

        TAMBAH_ISI
        $("#typeact").val("add");
        $("#ADD_FIELD").prop('readonly', false);
        
        $('#modal-title').text("Tambah Data NAMA_MODULE");
        $("#modal_NAMA_TABLE").modal('show');
    });

    // ===========================================
    // Ketika tombol lihat data di tekan
    // ===========================================
    $("#lihat_data_NAMA_TABLE").click(function () {

        var search_field_NAMA_TABLE = $('#search_field_NAMA_TABLE').val();
        var search_text_NAMA_TABLE = $('#search_text_NAMA_TABLE').val();

        $.ajax({
            url: "{?=url(['MODULE_NAME','aksi'])?}",
            method: "POST",
            data: {
                typeact: 'lihat', 
                search_field_NAMA_TABLE: search_field_NAMA_TABLE, 
                search_text_NAMA_TABLE: search_text_NAMA_TABLE
            },
            dataType: 'json',
            success: function (res) {
                var eTable = "<div class='table-responsive'><table id='tbl_lihat_NAMA_TABLE' class='table display dataTable' style='width:100%'><thead>HEADER_ISI</thead>";
                for (var i = 0; i < res.length; i++) {
                    eTable += "<tr>";
                    ETABLE_ISI
                    eTable += "</tr>";
                }
                eTable += "</tbody></table></div>";
                $('#forTable_NAMA_TABLE').html(eTable);
            }
        });

        $('#modal-title').text("Lihat Data");
        $("#modal_lihat_NAMA_TABLE").modal('show');
    });
        
    // ===========================================
    // Ketika tombol export pdf di tekan
    // ===========================================
    $("#export_pdf").click(function () {

        var doc = new jsPDF('p', 'pt', 'A4'); /* pilih 'l' atau 'p' */
        var img = "{?=base64_encode(file_get_contents(url($settings['logo'])))?}";
        doc.addImage(img, 'JPEG', 20, 10, 50, 50);
        doc.setFontSize(20);
        doc.text("{$settings.nama_instansi}", 80, 35, null, null, null);
        doc.setFontSize(10);
        doc.text("{$settings.alamat} - {$settings.kota} - {$settings.propinsi}", 80, 46, null, null, null);
        doc.text("Telepon: {$settings.nomor_telepon} - Email: {$settings.email}", 80, 56, null, null, null);
        doc.line(20,70,572,70,null); /* doc.line(20,70,820,70,null); --> Jika landscape */
        doc.line(20,72,572,72,null); /* doc.line(20,72,820,72,null); --> Jika landscape */
        doc.setFontSize(14);
        doc.text("Tabel Data NAMA_TABLE_UPPER", 20, 95, null, null, null);
        const totalPagesExp = "{total_pages_count_string}";        
        doc.autoTable({
            html: '#tbl_lihat_NAMA_TABLE',
            startY: 105,
            margin: {
                left: 20, 
                right: 20
            }, 
            styles: {
                fontSize: 10,
                cellPadding: 5
            }, 
            didDrawPage: data => {
                let footerStr = "Page " + doc.internal.getNumberOfPages();
                if (typeof doc.putTotalPages === 'function') {
                footerStr = footerStr + " of " + totalPagesExp;
                }
                doc.setFontSize(10);
                doc.text(`© ${new Date().getFullYear()} {$settings.nama_instansi}.`, data.settings.margin.left, doc.internal.pageSize.height - 10);                
                doc.text(footerStr, data.settings.margin.left + 480, doc.internal.pageSize.height - 10);
           }
        });
        if (typeof doc.putTotalPages === 'function') {
            doc.putTotalPages(totalPagesExp);
        }
        // doc.save('table_data_NAMA_TABLE.pdf');
        window.open(doc.output('bloburl'), '_blank',"toolbar=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes");  
              
    })

    // ===========================================
    // Ketika tombol export xlsx di tekan
    // ===========================================
    $("#export_xlsx").click(function () {
        let tbl1 = document.getElementById("tbl_lihat_NAMA_TABLE");
        let worksheet_tmp1 = XLSX.utils.table_to_sheet(tbl1);
        let a = XLSX.utils.sheet_to_json(worksheet_tmp1, { header: 1 });
        let worksheet1 = XLSX.utils.json_to_sheet(a, { skipHeader: true });
        const new_workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(new_workbook, worksheet1, "Data NAMA_TABLE");
        XLSX.writeFile(new_workbook, 'tmp_file.xls');
    })

    $("#view_chart").click(function () {
        window.open(mlite.url + '/MODULE_NAME/chart?t=' + mlite.token, '_blank',"toolbar=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes");  
    })   

});