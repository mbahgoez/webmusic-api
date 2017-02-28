<?php 

require "vendor/autoload.php";
// use Psr\Http\Message\ServerRequestInterface;
// use Psr\Http\Message\ResponseInterface;

$app = new \Slim\App;

// $app->add(function(ServerRequestInterface $request, ResponseInterface $response, callable $next){
// 	return $next($request, $response);
// });


$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});



// GET

$app->get("/api/terbaru", function($req, $res, $args){
	include "koneksi.php";
	$data = $db->query("SELECT * FROM vlistmusic ORDER BY idmusic DESC")->fetchAll(PDO::FETCH_ASSOC);
	echo json_encode($data);
});


$app->get("/api/category", function($req, $res, $args){

	include "koneksi.php";
	$data = $db->query("SELECT * FROM tbkategori")->fetchAll(PDO::FETCH_ASSOC);
	
	return $res->withJson($data);


});

$app->get("/api/category/{id}", function($req, $res, $args){
	include "koneksi.php";
	
	$id = $args["id"];
	
	$data['artist'] = $db->query("SELECT * FROM tbartist WHERE idkategori='$id'")->fetchAll(PDO::FETCH_ASSOC);

	$data['totalSongs'] = $db->query("SELECT * FROM vlistmusic WHERE idkategori='$id'")->fetchAll(PDO::FETCH_ASSOC);

	if(count($data['artist']) > 0){
		$result['status'] = "ok";	
		$result['idkategori'] = $id;
		$result['totalArtist'] = count($data['artist']);
		$result['totalSong'] = count($data['totalSongs']);
		$result['artist'] = $data['artist'];
	} else {
		$result['status'] = "not found";
	}

	$res->withHeader("Content-Type", "application/json");
	return $res->withJson($result);
	
});

$app->get("/api/artist", function($req, $res, $args){
	include "koneksi.php";

	$category = $db->query("SELECT * FROM vartist ORDER BY NamaArtist ASC")->fetchAll(PDO::FETCH_ASSOC);
	
	if(count($category) > 0 ){

		foreach($category as $item){
			$idartist = $item['idartist'];
				
			$datalagu = $db->query("SELECT * FROM tbmusic WHERE idartist='$idartist'")->fetchAll(PDO::FETCH_ASSOC);

			$item['totalSongs'] = count($datalagu);

			$kategori[] = $item;
		}

		$data['status'] = "ok";
		$data['totalArtist'] = count($category);
		$data['artist'] = $kategori;
	} else {
		$data['status'] = "not found";
	}
	
	$res->withHeader("Content-Type", "application/json");
	return $res->withJson($data);

});


$app->get("/api/artist/{id}", function($req, $res, $args){
	include "koneksi.php";

	$id = $args['id'];
	$data['data'] = $db->query("SELECT * FROM vlistmusic WHERE idartist='$id'")->fetchAll(PDO::FETCH_ASSOC);
	
	$result = null;

	if(count($data['data']) > 0){

		$result['status'] = "ok";
		$result['NamaArtist'] = $data['data'][0]['NamaArtist'];
		$result['totalSongs'] = count($data['data']);
	} else {
		$result['status'] = "not found";
	}

	$res->withHeader("Content-Type", "application/json");

	return $res->withJson($result);


});

$app->get("/api/songs", function(){
	include "koneksi.php";
	$data['status'] = "ok";
	$data['totalSongs'] = count($db->query("SELECT * FROM vlistmusic")->fetchAll());
	$data['songs'] = $db->query("SELECT * FROM vlistmusic LIMIT 0, 10")->fetchAll(PDO::FETCH_ASSOC);
	echo json_encode($data);
});


$app->get("/api/songs/category/{id}", function($req, $res, $args){
	include "koneksi.php";
	
	$id = $args["id"];
	
	$data['artist'] = $db->query("SELECT * FROM tbkategori WHERE idkategori='$id'")->fetchAll(PDO::FETCH_ASSOC);

	$data['songs'] = $db->query("SELECT * FROM vlistmusic WHERE idkategori='$id'")->fetchAll(PDO::FETCH_ASSOC);

	if(count($data['artist']) > 0){
		$result['status'] = "ok";
		$result['idkategori'] = $id;
		$result['NamaKategori'] = $data['artist'][0]['NamaKategori'];
		$result['SlugKategori'] = $data['artist'][0]['SlugKategori'];
		$result['totalSongs'] = count($data['songs']);
		$result['songs'] = $data['songs'];
		
	} else {
		$result['status'] = "not found";
	}
	
    
	
	$res->withHeader("Content-Type", "application/json");
	return $res->withJson($result);
});

$app->get("/api/songs/artist/{id}", function($req, $res, $args){
	include "koneksi.php";
	$id = $args['id'];
	$data['data'] = $db->query("SELECT * FROM vlistmusic WHERE idartist='$id'")->fetchAll(PDO::FETCH_ASSOC);
	
	$result = null;

	if(count($data['data']) > 0){

		$result['status'] = "ok";
		$result['NamaArtist'] = $data['data'][0]['NamaArtist'];
		$result['totalSongs'] = count($data['data']);
		$result['songs'] = $data['data'];
	} else {
		$result['status'] = "not found";
	}

	$res->withHeader("Content-Type", "application/json");

	return $res->withJson($result);
});

$app->get("/api/songs/detail/{idyoutube}", function($req, $res, $args){
	include "koneksi.php";
	
	$id = $args['idyoutube'];

	$data['lagu'] = $db->query("SELECT * FROM vlistmusic WHERE idyoutube='$id'")->fetchAll(PDO::FETCH_ASSOC);
	
	if(count($data) > 0){
		$data['status'] = "Ok";
		print_r(json_encode($data));
	} else {
		$data['status'] = "Not Found";
		print_r(json_encode($data));
	}
	
});

$app->get("/api/songs/page/{page}", function($req, $res, $args){
	include "koneksi.php";
	$perpage = 10;
	$start = ($args['page']-1)*$perpage;


	$data = $db->query("SELECT * FROM vlistmusic LIMIT $start, $perpage")->fetchAll(PDO::FETCH_ASSOC);
	
	if(count($data) > 0){
		$response["status"] = "ok";
		$response['totalSongs'] = count($db->query("SELECT * FROM vlistmusic")->fetchAll());
		$response['songs'] = $data;
		echo json_encode($response);
	} else {
		$response['status'] = "empty";
		echo json_encode($response);
	}
});



$app->get("/api/songs/search/{q}", function($req, $res, $args){
	include "koneksi.php";

	$q = $args['q'];

	$data = $db->query("SELECT * FROM vlistmusic WHERE Title LIKE '%$q%'")->fetchAll(PDO::FETCH_ASSOC);

	if(count($data) > 0){
		$result['status'] = "Ok";
		$result['total'] = count($data);
		$result['data'] = $data;
	} else {
		$result["status"] = "Not Found";
		$result['total'] = count($data);
	}

	echo json_encode($result);
});


//===============================

$app->post("/api", function($req, $res, $args){


	include "functions.php";

	$submited = $req->getParsedBody();

	$url = $submited['urlyoutube'];


	if(getHost($url) == 'youtube'){
		
		$urlparse = parse_url($url);
		parse_str($urlparse['query'], $query);
		$toget = $query['v'];

	} else if(getHost($url) == 'youtu'){
		
		$youtuurl = explode("/", parse_url($url)['path']);
		$toget = $youtuurl[1];

	}



	$fetch = "https://www.youtubeinmp3.com/fetch/?format=JSON&video=https://www.youtube.com/watch?v={$toget}&bitrate=1&filesize=1";

	$file = file_get_contents($fetch);

	$decode = json_decode($file);

	$data['idyoutube'] = $toget;
	$data['title'] = $decode->title;
	$data['duration'] = sec_to_time($decode->length);
	$data['filesize'] = byte_to_mb($decode->filesize);
	$data['url'] = "//www.youtubeinmp3.com/fetch/?video=https://www.youtube.com/watch?v={$toget}&autostart=1";

	$res->withHeader("Content-Type", "application/json");
	return $res->withJson($data);

});

$app->post("/api/songs", function($req, $res) use($app){
	include "koneksi.php";
	$submited = $req->getParsedBody();

	$idyoutube = $submited['idyoutube'];
	$judulvideo = $submited['judulvideo'];
	$judullagu = $submited['judullagu'];
	$filesize = $submited['filesize'];
	$duration = $submited['duration'];
	$artist = $submited['artist'];
	$album = $submited['album'];
	$tahun = $submited['tahun'];
	$tag = $submited['tag'];
	$genre = $submited['genre'];
	$link = $submited['url'];

	try{
		$stmt = $db->prepare("INSERT INTO tbmusic(idyoutube, Title, Track, Duration, Filesize, Album, Tahun, Genre, Tag, Link, idartist) VALUES(:idyoutube, :Title, :Track, :Duration, :Filesize, :Album, :Tahun, :Genre, :Tag, :Link, :idartist)");

		$stmt->bindParam(":idyoutube", $idyoutube);
		$stmt->bindParam(":Title", $judulvideo);
		$stmt->bindParam(":Track", $judullagu);
		$stmt->bindParam(":Duration", $duration);
		$stmt->bindParam(":Filesize", $filesize);
		$stmt->bindParam(":Album", $album);
		$stmt->bindParam(":Tahun", $tahun);
		$stmt->bindParam(":Genre", $genre);
		$stmt->bindParam(":Tag", $tag);
		$stmt->bindParam(":Link", $link);
		$stmt->bindParam(":idartist", $artist);

		$stmt->execute();

		$d = array("Status"=>"Insert OK");
		echo json_encode($d);
	} catch(PDOException $e){
		echo $e->getMessage();
	}
});

$app->put("/api/songs/{id}", function(){
});

$app->delete("/api/songs/{id}", function(){
});





$app->post("/api/category", function(){
});
$app->put("/api/category/{id}", function(){
});
$app->delete("/api/category/{id}", function(){
});









//  PSB



$app->post("/psb/jurusan", function(){
	include "koneksi.php";
	$data = $db->query("SELECT * FROM psb_jurusan")->fetchAll();
	print_r(json_encode($data));
});

$app->post("/psb/agama", function(){
	include "koneksi.php";
	$data = $db->query("SELECT * FROM psb_agama")->fetchAll();
	print_r(json_encode($data));
});

$app->post("/psb/asalsekolah", function(){
	include "koneksi.php";
	$data = $db->query("SELECT * FROM psb_asalsekolah")->fetchAll();
	print_r(json_encode($data));
});
$app->post("/psb/siswa", function(){
	include "koneksi.php";

	// SELECT psb_siswa.nisn, psb_siswa.nama_siswa AS nama, psb_siswa.jenis_kelamin, psb_agama.nama_agama as agama, psb_asalsekolah.nama_sekolah as asal_sekolah, psb_jurusan.nama_jurusan as jurusan, psb_siswa.alamat, psb_siswa.telp
	// FROM psb_siswa
	// LEFT JOIN psb_agama
	// ON psb_siswa.id_agama=psb_agama.id_agama
	// LEFT JOIN psb_asalsekolah
	// ON psb_siswa.id_sekolah=psb_asalsekolah.id_sekolah
	// LEFT JOIN psb_jurusan
	// ON psb_siswa.id_jurusan=psb_jurusan.id_jurusan
	$data = $db->query("SELECT psb_siswa.nisn, psb_siswa.nama_siswa AS nama, psb_siswa.jenis_kelamin, psb_agama.nama_agama as agama, psb_asalsekolah.nama_sekolah as asal_sekolah, psb_jurusan.nama_jurusan as jurusan, psb_siswa.alamat, psb_siswa.telp
FROM psb_siswa
LEFT JOIN psb_agama
ON psb_siswa.id_agama=psb_agama.id_agama
LEFT JOIN psb_asalsekolah
ON psb_siswa.id_sekolah=psb_asalsekolah.id_sekolah
LEFT JOIN psb_jurusan
ON psb_siswa.id_jurusan=psb_jurusan.id_jurusan")->fetchAll();
	print_r(json_encode($data));
});


$app->run();