<?php
namespace Plugins\Resep_Dokter_Racikan;

use Systems\AdminModule;

class Admin extends AdminModule
{

    public function navigation()
    {
        return [
            'Kelola'   => 'manage',
        ];
    }

    public function getManage()
    {
        $this->_addHeaderFiles();
        $disabled_menu = $this->core->loadDisabledMenu('resep_dokter_racikan'); 
        foreach ($disabled_menu as &$row) { 
          if ($row == "true" ) $row = "disabled"; 
        } 
        unset($row);
        return $this->draw('manage.html', ['disabled_menu' => $disabled_menu]);
    }

    public function postData()
    {
        $column_name = isset_or($_POST['column_name'], 'no_resep');
        $column_order = isset_or($_POST['column_order'], 'asc');
        $draw = isset_or($_POST['draw'], '0');
        $row1 = isset_or($_POST['start'], '0');
        $rowperpage = isset_or($_POST['length'], '10'); // Rows display per page
        $columnIndex = isset_or($_POST['order'][0]['column']); // Column index
        $columnName = isset_or($_POST['columns'][$columnIndex]['data'], $column_name); // Column name
        $columnSortOrder = isset_or($_POST['order'][0]['dir'], $column_order); // asc or desc
        $searchValue = isset_or($_POST['search']['value']); // Search value

        ## Custom Field value
        $search_field_resep_dokter_racikan= isset_or($_POST['search_field_resep_dokter_racikan']);
        $search_text_resep_dokter_racikan = isset_or($_POST['search_text_resep_dokter_racikan']);

        if ($search_text_resep_dokter_racikan != '') {
          $where[$search_field_resep_dokter_racikan.'[~]'] = $search_text_resep_dokter_racikan;
          $where = ["AND" => $where];
        } else {
          $where = [];
        }

        ## Total number of records without filtering
        $totalRecords = $this->core->db->count('resep_dokter_racikan', '*');

        ## Total number of records with filtering
        $totalRecordwithFilter = $this->core->db->count('resep_dokter_racikan', '*', $where);

        ## Fetch records
        $where['ORDER'] = [$columnName => strtoupper($columnSortOrder)];
        $where['LIMIT'] = [$row1, $rowperpage];
        $result = $this->core->db->select('resep_dokter_racikan', '*', $where);

        $data = array();
        foreach($result as $row) {
            $data[] = array(
                'no_resep'=>$row['no_resep'],
'no_racik'=>$row['no_racik'],
'nama_racik'=>$row['nama_racik'],
'kd_racik'=>$row['kd_racik'],
'jml_dr'=>$row['jml_dr'],
'aturan_pakai'=>$row['aturan_pakai'],
'keterangan'=>$row['keterangan']

            );
        }

        ## Response
        http_response_code(200);
        $response = array(
            "draw" => intval($draw), 
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        if($this->settings('settings', 'logquery') == true) {
          $this->core->LogQuery('resep_dokter_racikan => postData');
        }

        echo json_encode($response);
        exit();
    }

    public function postAksi()
    {
        if(isset($_POST['typeact'])){ 
            $act = $_POST['typeact']; 
        }else{ 
            $act = ''; 
        }

        if ($act=='add') {

            if($this->core->loadDisabledMenu('resep_dokter_racikan')['create'] == 'true') {
              http_response_code(403);
              $data = array(
                'code' => '403', 
                'status' => 'error', 
                'msg' => 'Maaf, akses dibatasi!'
              );
              echo json_encode($data);    
              exit();
            }

            $no_resep = $_POST['no_resep'];
            $no_racik = '1';
            $resep_dokter_racikan = $this->core->db->get('resep_dokter_racikan', '*', ['no_resep' => $no_resep]);
            if(!empty($resep_dokter_racikan['no_racik'])) {
              $no_racik = $resep_dokter_racikan['no_racik'] + 1;
            }
            $kd_racik = $_POST['kd_racik'];

            $nama_racik = $this->core->db->get('metode_racik', 'nm_racik', ['kd_racik' => $kd_racik]);

            $jml_dr = $_POST['jml_dr'];
            $aturan_pakai = $_POST['aturan_pakai'];
            $keterangan = $_POST['keterangan'];


            $tgl_perawatan = $this->core->db->get('reg_periksa', 'tgl_registrasi', [
              'no_rawat' => $_POST['no_rawat']
            ]);
            // $jam = $_POST['jam'];
            $jam = $this->core->db->get('reg_periksa', 'jam_reg', [
              'no_rawat' => $_POST['no_rawat']
            ]);
            $no_rawat = $_POST['no_rawat'];
            $kd_dokter = $_POST['kd_dokter'];
            $tgl_peresepan = $_POST['tgl_peresepan'];
            $jam_peresepan = $_POST['jam_peresepan'];
            $status = $_POST['status'];
            $tgl_penyerahan = isset_or($_POST['tgl_penyerahan'], '0000-00-00');
            $jam_penyerahan = isset_or($_POST['jam_penyerahan'], '00:00:00');

            $kode_brng = $_POST['kode_brng'];

            $p1 = $_POST['p1'];
            $p2 = $_POST['p2'];

            $kandungan = $_POST['kandungan'];

            // $jml = $_POST['jml'];

            $isNoResep = $this->core->db->has('resep_obat', ['no_resep' => $no_resep]);
            if($isNoResep) {
              $result = true;
            } else {
              $result = $this->core->db->insert('resep_obat', [
                'no_resep'=>$no_resep, 'tgl_perawatan'=>$tgl_perawatan, 'jam'=>$jam, 'no_rawat'=>$no_rawat, 'kd_dokter'=>$kd_dokter, 'tgl_peresepan'=>$tgl_peresepan, 'jam_peresepan'=>$jam_peresepan, 'status'=>$status, 'tgl_penyerahan'=>$tgl_penyerahan, 'jam_penyerahan'=>$jam_penyerahan
              ]);                
            }

            if (!empty($result)){

              $resep_dokter_racikan = $this->core->db->insert('resep_dokter_racikan', [
                'no_resep'=>$no_resep, 'no_racik'=>$no_racik, 'nama_racik'=>$nama_racik, 'kd_racik'=>$kd_racik, 'jml_dr'=>$jml_dr, 'aturan_pakai'=>$aturan_pakai, 'keterangan'=>$keterangan
              ]);

              if (!empty($resep_dokter_racikan)){
                for($l=0; $l < count($kode_brng); $l++){
                  $kapasitas = $this->core->db->get('databarang', 'kapasitas', ['kode_brng' => $kode_brng[$l]]);
                  if(isset($kandungan[$l]) && $kandungan[$l] !='') {
                    $p1[$l] = '1';
                    $p2[$l] = '1';
                  } else {
                    $kandungan[$l] = ($p1[$l]/$p2[$l])*$kapasitas;
                  }
                  $jml = ($kandungan[$l]*$jml_dr)/$kapasitas;
                  $resep_dokter_racikan_detail = $this->core->db->insert('resep_dokter_racikan_detail', [
                    'no_resep' => $no_resep, 'no_racik'=>$no_racik, 'kode_brng' => $kode_brng[$l], 'p1' =>$p1[$l], 'p2' => $p2[$l], 'kandungan'=>$kandungan[$l], 'jml' =>$jml
                  ]); 
                  if(!$resep_dokter_racikan_detail) {
                    $data = array(
                      'status' => 'error', 
                      'msg' => $this->core->db->errorInfo[2]
                    );    
                  }
                }  
              }
              
              http_response_code(200);
              $data = array(
                'code' => '200', 
                'status' => 'success', 
                'msg' => 'Data telah ditambah'
              );
            } else {
              http_response_code(201);
              $data = array(
                'code' => '201', 
                'status' => 'error', 
                'msg' => $this->core->db->errorInfo[2]
              );
            }

            if($this->settings('settings', 'logquery') == true) {
              $this->core->LogQuery('resep_dokter_racikan => postAksi => add');
            }

            echo json_encode($data);    
        }
        if ($act=="edit") {

            if($this->core->loadDisabledMenu('resep_dokter_racikan')['update'] == 'true') {
              http_response_code(403);
              $data = array(
                'code' => '403', 
                'status' => 'error', 
                'msg' => 'Maaf, akses dibatasi!'
              );
              echo json_encode($data);    
              exit();
            }

        $no_resep = $_POST['no_resep'];
$no_racik = $_POST['no_racik'];
$nama_racik = $_POST['nama_racik'];
$kd_racik = $_POST['kd_racik'];
$jml_dr = $_POST['jml_dr'];
$aturan_pakai = $_POST['aturan_pakai'];
$keterangan = $_POST['keterangan'];


        // BUANG FIELD PERTAMA

            $result = $this->core->db->update('resep_dokter_racikan', [
'no_resep'=>$no_resep, 'no_racik'=>$no_racik, 'nama_racik'=>$nama_racik, 'kd_racik'=>$kd_racik, 'jml_dr'=>$jml_dr, 'aturan_pakai'=>$aturan_pakai, 'keterangan'=>$keterangan
            ], [
              'no_resep'=>$no_resep
            ]);


            if (!empty($result)){
              http_response_code(200);
              $data = array(
                'code' => '200', 
                'status' => 'success', 
                'msg' => 'Data telah diubah'
              );
            } else {
              http_response_code(201);
              $data = array(
                'code' => '201', 
                'status' => 'error', 
                'msg' => $this->core->db->errorInfo[2]
              );
            }

            if($this->settings('settings', 'logquery') == true) {
              $this->core->LogQuery('resep_dokter_racikan => postAksi => edit');
            }

            echo json_encode($data);             
        }

        if ($act=="del") {

            if($this->core->loadDisabledMenu('resep_dokter_racikan')['delete'] == 'true') {
              http_response_code(403);
              $data = array(
                'code' => '403', 
                'status' => 'error', 
                'msg' => 'Maaf, akses dibatasi!'
              );
              echo json_encode($data);    
              exit();
            }

            $no_resep= $_POST['no_resep'];
            $result = $this->core->db->delete('resep_dokter_racikan', [
              'AND' => [
                'no_resep'=>$no_resep
              ]
            ]);

            if (!empty($result)){
              http_response_code(200);
              $data = array(
                'code' => '200', 
                'status' => 'success', 
                'msg' => 'Data telah dihapus'
              );
            } else {
              http_response_code(201);
              $data = array(
                'code' => '201', 
                'status' => 'error', 
                'msg' => $this->core->db->errorInfo[2]
              );
            }

            if($this->settings('settings', 'logquery') == true) {
              $this->core->LogQuery('resep_dokter_racikan => postAksi => del');
            }

            echo json_encode($data);                    
        }

        if ($act=="lihat") {

            if($this->core->loadDisabledMenu('resep_dokter_racikan')['read'] == 'true') {
              http_response_code(403);
              $data = array(
                'code' => '403', 
                'status' => 'error', 
                'msg' => 'Maaf, akses dibatasi!'
              );
              echo json_encode($data);    
              exit();
            }

            $search_field_resep_dokter_racikan= $_POST['search_field_resep_dokter_racikan'];
            $search_text_resep_dokter_racikan = $_POST['search_text_resep_dokter_racikan'];

            if ($search_text_resep_dokter_racikan != '') {
              $where[$search_field_resep_dokter_racikan.'[~]'] = $search_text_resep_dokter_racikan;
              $where = ["AND" => $where];
            } else {
              $where = [];
            }

            ## Fetch records
            $result = $this->core->db->select('resep_dokter_racikan', '*', $where);

            $data = array();
            foreach($result as $row) {
                $data[] = array(
                    'no_resep'=>$row['no_resep'],
'no_racik'=>$row['no_racik'],
'nama_racik'=>$row['nama_racik'],
'kd_racik'=>$row['kd_racik'],
'jml_dr'=>$row['jml_dr'],
'aturan_pakai'=>$row['aturan_pakai'],
'keterangan'=>$row['keterangan']
                );
            }

            if($this->settings('settings', 'logquery') == true) {
              $this->core->LogQuery('resep_dokter_racikan => postAksi => lihat');
            }
            
            echo json_encode($data);
        }
        exit();
    }

    public function getRead($no_resep)
    {

        if($this->core->loadDisabledMenu('resep_dokter_racikan')['read'] == 'true') {
          http_response_code(403);
          $data = array(
            'code' => '403', 
            'status' => 'error', 
            'msg' => 'Maaf, akses dibatasi!'
          );
          echo json_encode($data);    
          exit();
        }

        $result =  $this->core->db->get('resep_dokter_racikan', '*', ['no_resep' => $no_resep]);

        if (!empty($result)){
          http_response_code(200);
          $data = array(
            'code' => '200', 
            'status' => 'success', 
            'msg' => $result
          );
        } else {
          http_response_code(201);
          $data = array(
            'code' => '201', 
            'status' => 'error', 
            'msg' => 'Data tidak ditemukan'
          );
        }

        if($this->settings('settings', 'logquery') == true) {
          $this->core->LogQuery('resep_dokter_racikan => getRead');
        }

        echo json_encode($data);        
        exit();
    }

    public function getDetail($no_resep)
    {

        if($this->core->loadDisabledMenu('resep_dokter_racikan')['read'] == 'true') {
          http_response_code(403);
          $data = array(
            'code' => '403', 
            'status' => 'error', 
            'msg' => 'Maaf, akses dibatasi!'
          );
          echo json_encode($data);    
          exit();
        }

        $settings =  $this->settings('settings');

        if($this->settings('settings', 'logquery') == true) {
          $this->core->LogQuery('resep_dokter_racikan => getDetail');
        }

        echo $this->draw('detail.html', ['settings' => $settings, 'no_resep' => $no_resep]);
        exit();
    }

    public function getChart($type = '', $column = '')
    {
      if($type == ''){
        $type = 'pie';
      }

      $labels = $this->core->db->select('resep_dokter_racikan', 'no_resep', ['GROUP' => 'no_resep']);
      $datasets = $this->core->db->select('resep_dokter_racikan', ['count' => \Medoo\Medoo::raw('COUNT(<no_resep>)')], ['GROUP' => 'no_resep']);

      if(isset_or($column)) {
        $labels = $this->core->db->select('resep_dokter_racikan', ''.$column.'', ['GROUP' => ''.$column.'']);
        $datasets = $this->core->db->select('resep_dokter_racikan', ['count' => \Medoo\Medoo::raw('COUNT(<'.$column.'>)')], ['GROUP' => ''.$column.'']);          
      }

      $database = DBNAME;
      $nama_table = 'resep_dokter_racikan';

      $get_table = $this->core->db->pdo->prepare("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='$database' AND TABLE_NAME='$nama_table'");
	    $get_table->execute();
	    $result = $get_table->fetchAll();

      if($this->settings('settings', 'logquery') == true) {
        $this->core->LogQuery('resep_dokter_racikan => getChart');
      }

      echo $this->draw('chart.html', ['type' => $type, 'column' => $result, 'labels' => json_encode($labels), 'datasets' => json_encode(array_column($datasets, 'count'))]);
      exit();
    }

    public function getCss()
    {
        header('Content-type: text/css');
        echo $this->draw(MODULES.'/resep_dokter_racikan/css/styles.css');
        exit();
    }

    public function getJavascript()
    {
        header('Content-type: text/javascript');
        $settings = $this->settings('settings');
        echo $this->draw(MODULES.'/resep_dokter_racikan/js/scripts.js', ['settings' => $settings, 'disabled_menu' => $this->core->loadDisabledMenu('resep_dokter_racikan')]);
        exit();
    }

    private function _addHeaderFiles()
    {
        $this->core->addCSS(url('assets/vendor/datatables/datatables.min.css'));
        $this->core->addCSS(url('assets/css/jquery.contextMenu.css'));
        $this->core->addJS(url('assets/js/jqueryvalidation.js'), 'footer');
        $this->core->addJS(url('assets/vendor/jspdf/xlsx.js'), 'footer');
        $this->core->addJS(url('assets/vendor/jspdf/jspdf.min.js'), 'footer');
        $this->core->addJS(url('assets/vendor/jspdf/jspdf.plugin.autotable.min.js'), 'footer');
        $this->core->addJS(url('assets/vendor/datatables/datatables.min.js'), 'footer');
        $this->core->addJS(url('assets/js/jquery.contextMenu.js'), 'footer');

        $this->core->addCSS(url([ 'resep_dokter_racikan', 'css']));
        $this->core->addJS(url([ 'resep_dokter_racikan', 'javascript']), 'footer');
    }

}
