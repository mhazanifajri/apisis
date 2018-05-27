<?php

/**
 * Step 1: Require the Slim Framework using Composer's autoloader
 *
 * If you are not using Composer, you need to load Slim Framework with your own
 * PSR-4 autoloader.
 */
require 'vendor/autoload.php';
require 'helper/db.php'; 

/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
$app = new Slim\App();
date_default_timezone_set("Asia/Jakarta");

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 */
$app->get('/', function ($request, $response, $args) {
    // $response->write("Welcome to Slim!");
    $response->write("Welcome to the Jungle!");
    return $response;
});

$app->get('/hello[/{name}]', function ($request, $response, $args) {
    $response->write("Hello, " . $args['name']);
    return $response;
});

$app->post('/post',function ($request, $response,$args) {
    $input = $request->getParsedBody();
    $response->write("Hello, " . $input['name']);
    return $response;

});

$app->post('/login',function ($request, $response,$args) {
    $input = $request->getParsedBody();
    $username = $input['username'];
    $password = md5($input['password']);
    
    $query = "select username, nama_lengkap , level from tbl_user where username = '".$username."' and password = '".$password."'";
    $result = queryGet($query);
    $responseData = array('response_code' => 0,
                          'response_message' => 'username atau password salah'
                        );
    if($result != null){
         $responseData = array('response_code' => 1,
                          'response_message' => 'success',
                          'data' => $result[0]
                        );
    }

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->getBody()->write(json_encode($responseData));

});
$app->post('/change/password',function ($request, $response,$args) {
    $input = $request->getParsedBody();
    $username = $input['username'];
    $password = md5($input['password']);
    $newPassword = md5($input['newpassword']);
    
    $query = "select username, nama_lengkap , level from tbl_user where username = '".$username."' and password = '".$password."'";
    $result = queryGet($query);
    $responseData = array('response_code' => 0,
                          'response_message' => 'gagal password salah'
                        );
    if($result != null){
         $query = "update tbl_user set password = '".$newPassword."' where username = '".$username."' ";
         $result = queryExecute($query);
         if ($result) {
           $responseData = array('response_code' => 1,
                          'response_message' => 'success',
                        );
         }else{
            $responseData = array('response_code' => 0,
                          'response_message' => 'gagal update password'
                        );
         }
         
    }

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->getBody()->write(json_encode($responseData));

});
$app->get('/profile[/{level}/{id}]',function ($request, $response,$args) {
    $level = $args['level'];
    $username = $args['id'];
     $responseData = array('response_code' => 0,
                          'response_message' => 'Data null'
                        );
     $data = null;
    if ($level == "2") {
        $query = "SELECT (`nik`)username, (`nama_siswa`)nama , `tempat_lahir` , `tgl_lahir`,`jenis_kelamin` , `agama` , `alamat`, `no_telp` ,`email`,CONCAT('".BASE_URL."',foto)foto  FROM `tbl_siswa` where nik = '".$username."'";
        $result = queryGet($query);
        if($result != null){
            $data = $result[0];
        }
        
    }else if ($level == "1"){
        $query = "SELECT (`nip`)username , (`nama_lengkap`)nama , `tempat_lahir`, `tgl_lahir`, `jenis_kelamin`, `agama`, alamat, `no_telp`, `email` , CONCAT('".BASE_URL."',foto)foto  FROM `tbl_guru` WHERE `nip` = '".$username."'";
        $result = queryGet($query);
        if($result != null){
            $data = $result[0];
            
        }
    }else {
        $query = "select username , (nama_lengkap)nama , ('')tempat_lahir, ('')tgl_lahir, ('')jenis_kelamin, ('')agama, ('')alamat, ('')no_telp, ('')email , ('')foto  from tbl_user where username = '".$username."'";
        $result = queryGet($query);
        if($result != null){
            $data = $result[0];
        }
    }
   
    if($data != null){
         $responseData = array('response_code' => 1,
                          'response_message' => 'success',
                          'data' => $data
                        );
    }

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->getBody()->write(json_encode($responseData));

});

$app->post('/profile/update',function ($request, $response,$args) {
    $input = $request->getParsedBody();
    $username = $input['username'];
    $level = $input['level'];
    $nama = $input['nama'];
    $tempat_lahir = $input['tempat_lahir'];
    $tgl_lahir = $input['tgl_lahir'];
    $jenis_kelamin = $input['jenis_kelamin'];
    $agama = $input['agama'];
    $no_telp = $input['no_telp'];
    $email = $input['email'];
    $alamat = $input['alamat'];

    if ($level == "2") {
        $query = "Update tbl_siswa set  `nama_siswa` = '".$nama."', `tempat_lahir` = '".$tempat_lahir."' , `tgl_lahir` = '".$tgl_lahir."',`jenis_kelamin`= '".$jenis_kelamin."' , `agama` = '".$agama."' , `alamat` = '".$alamat."', `no_telp` = '".$no_telp."',`email` = '".$email."' where nik = '".$username."'";
        $result = queryExecute($query);        
    }else if ($level == "1"){
        $query = "Update tbl_guru set `nama_lengkap` = '".$nama."', `tempat_lahir` = '".$tempat_lahir."' , `tgl_lahir` = '".$tgl_lahir."',`jenis_kelamin`= '".$jenis_kelamin."' , `agama` = '".$agama."' , `alamat` = '".$alamat."', `no_telp` = '".$no_telp."',`email` = '".$email."' WHERE `nip` = '".$username."'";
        $result = queryExecute($query);
    }
    $responseData = array('response_code' => 0,
                          'response_message' => 'gagal update'
                        );
    if($result){
         $responseData = array('response_code' => 1,
                          'response_message' => 'success',
                        );
    }

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->getBody()->write(json_encode($responseData));

});

$app->get('/siswa',function ($request, $response,$args) {
    
    $query = "SELECT `nik`, `nama_siswa` , CONCAT('".BASE_URL."',foto)foto , nama_kelas FROM `tbl_siswa` LEFT JOIN tbl_kelas on tbl_siswa.id_kelas_siswa = tbl_kelas.id_kelas";
    $result = queryGet($query);
    $responseData = array('response_code' => 0,
                          'response_message' => 'error'
                        );
    if($result != null){
         $responseData = array('response_code' => 1,
                          'response_message' => 'success',
                          'data' => $result
                        );
    }

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->getBody()->write(json_encode($responseData));

});
$app->get('/allmatapelajaran',function ($request, $response,$args) {
    
    $query = "select id_matapelajaran,kode_matapelajaran, matapelajaran , (id_kelas_matapelajaran)id_kelas , tbl_kelas.`nama_kelas`
,(id_guru_matapelajaran)id_guru,(nama_lengkap)nama_guru from tbl_matapelajaran
left join tbl_kelas on tbl_kelas.`id_kelas` = id_kelas_matapelajaran
left join tbl_guru on tbl_guru.`id_guru` = id_guru_matapelajaran";
    $result = queryGet($query);
    $responseData = array('response_code' => 0,
                          'response_message' => 'error'
                        );
    if($result != null){
         $responseData = array('response_code' => 1,
                          'response_message' => 'success',
                          'data' => $result
                        );
    }

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->getBody()->write(json_encode($responseData));

});
$app->get('/matapelajaransiswa[/{id}]',function ($request, $response,$args) {
    $nik = $args['id'];
    $query = "select id_matapelajaran,kode_matapelajaran, matapelajaran , (id_kelas_matapelajaran)id_kelas , tbl_kelas.`nama_kelas`
,(id_guru_matapelajaran)id_guru,(nama_lengkap)nama_guru from tbl_matapelajaran
left join tbl_kelas on tbl_kelas.`id_kelas` = id_kelas_matapelajaran
left join tbl_guru on tbl_guru.`id_guru` = id_guru_matapelajaran
left join tbl_siswa on tbl_siswa.`id_kelas_siswa` = tbl_kelas.`id_kelas`
where tbl_siswa.`nik` = '".$nik."'";
    $result = queryGet($query);
    $responseData = array('response_code' => 0,
                          'response_message' => 'error'
                        );
    if($result != null){
         $responseData = array('response_code' => 1,
                          'response_message' => 'success',
                          'data' => $result
                        );
    }

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->getBody()->write(json_encode($responseData));

});
$app->get('/kelassiswa[/{id}]',function ($request, $response,$args) {
    $nik = $args['id'];
    $query = "SELECT `id_kelas_siswa` FROM `tbl_siswa` where nik = '".$nik."'";
    $result = queryGet($query);
    if ($result == null) {
       $responseData = array('response_code' => 0,
                          'response_message' => 'data kosong'
                        );
      return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->getBody()->write(json_encode($responseData));
    }
    $query = "SELECT `nik`, `nama_siswa` , CONCAT('".BASE_URL."',foto)foto  , nama_kelas FROM `tbl_siswa` LEFT JOIN tbl_kelas on tbl_siswa.id_kelas_siswa = tbl_kelas.id_kelas WHERE id_kelas_siswa = '".$result[0]['id_kelas_siswa']."'";
    $result = queryGet($query);
    $responseData = array('response_code' => 0,
                          'response_message' => 'error'
                        );
    if($result != null){
         $responseData = array('response_code' => 1,
                          'response_message' => 'success',
                          'data' => $result
                        );
    }

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->getBody()->write(json_encode($responseData));

});
$app->get('/detailssiswa[/{id}]',function ($request, $response,$args) {
    $nik = $args['id'];
    $query = "SELECT `nik`, `nama_siswa` , `tempat_lahir` , `tgl_lahir`,`jenis_kelamin` , `agama` , `alamat`, `no_telp` ,`email`,`id_kelas_siswa`, CONCAT('".BASE_URL."',foto)foto  , nama_kelas FROM `tbl_siswa` LEFT JOIN tbl_kelas on tbl_siswa.id_kelas_siswa = tbl_kelas.id_kelas where nik = '".$nik."'";
    $result = queryGet($query);
    $responseData = array('response_code' => 0,
                          'response_message' => 'error'
                        );
    if($result != null){
         $responseData = array('response_code' => 1,
                          'response_message' => 'success',
                          'data' => $result[0]
                        );
    }

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->getBody()->write(json_encode($responseData));

});

$app->get('/guru',function ($request, $response,$args) {
    
    $query = "SELECT `nip` , `nama_lengkap` , CONCAT('".BASE_URL."',foto)foto  FROM `tbl_guru`";
    $result = queryGet($query);
    $responseData = array('response_code' => 0,
                          'response_message' => 'error'
                        );
    if($result != null){
         $responseData = array('response_code' => 1,
                          'response_message' => 'success',
                          'data' => $result
                        );
    }

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->getBody()->write(json_encode($responseData));

});

$app->get('/detailsguru[/{id}]',function ($request, $response,$args) {
    $nip = $args['id'];
    $query = "SELECT `nip` , `nama_lengkap` ,`alamat` , `tempat_lahir`, `tgl_lahir`, `jenis_kelamin`, `agama`, `no_telp`, `email` , CONCAT('".BASE_URL."',foto)foto  FROM `tbl_guru` WHERE `nip` = '".$nip."'";
    $result = queryGet($query);
    $responseData = array('response_code' => 0,
                          'response_message' => 'error'
                        );
    if($result != null){
         $responseData = array('response_code' => 1,
                          'response_message' => 'success',
                          'data' => $result[0]
                        );
    }

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->getBody()->write(json_encode($responseData));

});

$app->get('/topic/modul[/{id}]',function ($request, $response,$args) {
    $idmapel = $args['id'];
    $query = "SELECT tbl_topicmodul.*,(nama_lengkap)nama_guru FROM `tbl_topicmodul` 
              LEFT JOIN tbl_guru on id_gurumodul = id_guru WHERE `id_matapelajaranmodul` = $idmapel";
    $result = queryGet($query);
    $responseData = array('response_code' => 0,
                          'response_message' => 'error'
                        );
    if($result != null){
         $responseData = array('response_code' => 1,
                          'response_message' => 'success',
                          'data' => $result
                        );
    }

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->getBody()->write(json_encode($responseData));

});
$app->get('/modul[/{id}]',function ($request, $response,$args) {
    $idtopic = $args['id'];
    $query = "SELECT id_modul , id_topic_modul , nama_modul ,   tgl_dibuat , CONCAT('".BASE_URL."',link_file)link_file  FROM `tbl_modul` WHERE `id_topic_modul` = $idtopic";
    $result = queryGet($query);
    $responseData = array('response_code' => 0,
                          'response_message' => 'error'
                        );
    if($result != null){
         $responseData = array('response_code' => 1,
                          'response_message' => 'success',
                          'data' => $result
                        );
    }

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->getBody()->write(json_encode($responseData));

});

$app->get('/topic/kuis[/{id}/{username}]',function ($request, $response,$args) {
    $idmapel = $args['id'];
    $username = $args['username'];
//     $query = "SELECT tbl_topickuis.* , (nama_lengkap)nama_guru FROM `tbl_topickuis` 
//               LEFT JOIN tbl_guru on id_guru_kuis = id_guru 
//               WHERE `id_matapelajaran_kuis` = $idmapel";
        $query = "SELECT * FROM (
                    SELECT a.*, IFNULL(c.nilai, "-")nilai, c.tgl_post, IFNULL(c.username, $username) username, (nama_lengkap)nama_guru 
                    FROM tbl_topickuis a
                    LEFT JOIN tbl_kuis b ON b.id_topkuis = a.id_topkuis
                    LEFT JOIN tbl_guru on id_guru_kuis = id_guru 
                    LEFT JOIN (
                        SELECT c1.* FROM tbl_nilai c1
                        LEFT JOIN tbl_siswa c2 ON c2.nik = c1.username
                    ) c ON c.id_topic_kuis=b.id_topkuis
                    WHERE a.id_matapelajaran_kuis=$idmapel
                ) t
                WHERE t.username = $username
                GROUP BY t.id_topkuis
                ORDER BY t.tgl_post DESC ";
    
    $result = queryGet($query);
    
    $responseData = array('response_code' => 0,
                          'response_message' => 'error'
                        );
    if($result != null){
         $responseData = array('response_code' => 1,
                          'response_message' => 'success',
                          'data' => $result
                        );
    }

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->getBody()->write(json_encode($responseData));

});

$app->get('/kuis[/{id}]',function ($request, $response,$args) {
    $idtopic = $args['id'];
    $query = "SELECT * FROM `tbl_kuis` WHERE `id_topkuis` = $idtopic";
    $result = queryGet($query);
    $responseData = array('response_code' => 0,
                          'response_message' => 'error'
                        );
    if($result != null){
         $responseData = array('response_code' => 1,
                          'response_message' => 'success',
                          'data' => $result
                        );
    }

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->getBody()->write(json_encode($responseData));

});

$app->post('/add/pengaduan',function ($request, $response,$args) {
    $input = $request->getParsedBody();
    $username = $input['username'];
    $level = $input['level'];
    $deskripsi = $input['deskripsi'];
    $date = date('y-m-d');

    $query = "INSERT INTO `tbl_pengaduan` (`username`, `level`, `deskripsi`, `tgl_dibuat`) VALUES ('".$username."', '".$level."', '".$deskripsi."', '".$date."');";
    $result = queryExecute($query);        

    $responseData = array('response_code' => 0,
                          'response_message' => 'gagal insert pengaduan'
                        );
    if($result){
         $responseData = array('response_code' => 1,
                          'response_message' => 'success',
                        );
    }

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->getBody()->write(json_encode($responseData));

});
$app->post('/nilai/add',function ($request, $response,$args) {
    $input = $request->getParsedBody();
    $username = $input['username'];
    $topic_kuis = $input['topic_kuis'];
    $benar = $input['benar'];
    $salah = $input['salah'];
    $tidak_dikerjakan = $input['tidak_dikerjakan'];
    $nilai = $input['nilai'];
    $date = date('d-m-y');

    $query = "select * from tbl_nilai where username = '".$username."' and id_topic_kuis = '".$topic_kuis."'";
    $result = queryGet($query);
    $responseData = array('response_code' => 3,
                          'response_message' => 'user sudah mengerjakan soal tersebut'
                        );
    if($result == null){
        $query = "INSERT INTO `tbl_nilai` (`username`, `id_topic_kuis`, `benar`, `salah`, `tidak_dikerjakan`, `nilai`, `tgl_post`) VALUES ( '".$username."', '".$topic_kuis."', '".$benar."', '".$salah."', '".$tidak_dikerjakan."', '".$nilai."', '".$date."');";
        $result = queryExecute($query);
        if($result){
            $responseData = array('response_code' => 1,
                          'response_message' => 'success',
                        );
        }else{
            $responseData = array('response_code' => 0,
                          'response_message' => 'Gagak Insert Nilai',
                        );
        }   
    }


    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->getBody()->write(json_encode($responseData));

});

$app->post('/topicmodul/add',function ($request, $response,$args) {
    $input = $request->getParsedBody();
    $username = $input['username'];
    $id_matapelajaran = $input['id_matapelajaran'];
    $judul = $input['judul'];
    $date = $input['date'];

    $query = "select id_guru from tbl_guru where nip = '".$username."'";
    $result = queryGet($query);

    $id_guru = $result[0]['id_guru'];
    if($result == null){
        $responseData = array('response_code' => 3,
                            'response_message' => 'user tidak ditemukan'
                            );
    }
    else{
        $query = "INSERT INTO `tbl_topicmodul` (`id_matapelajaranmodul`, `judul`, `id_gurumodul`, `tgl_dibuat`) VALUES ( '".$id_matapelajaran."', '".$judul."', '".$id_guru."', '".$date."');";
        $result = queryExecute($query);
        if($result){
            $responseData = array('response_code' => 1,
                          'response_message' => 'success',
                        );
        }else{
            $responseData = array('response_code' => 0,
                          'response_message' => 'Gagal Insert ',
                        );
        }   
    }


    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->getBody()->write(json_encode($responseData));

});

$app->get('/nilai[/{username}]',function ($request, $response,$args) {
    $username = $args['username'];
    $query = "SELECT *, judul FROM `tbl_nilai` LEFT JOIN tbl_topickuis on id_topkuis = id_topic_kuis
                where username = '$username'";
    $result = queryGet($query);
    $responseData = array('response_code' => 0,
                          'response_message' => 'error'
                        );
    if($result != null){
         $responseData = array('response_code' => 1,
                          'response_message' => 'success',
                          'data' => $result
                        );
    }

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->getBody()->write(json_encode($responseData));

});

$app->run();



?>
