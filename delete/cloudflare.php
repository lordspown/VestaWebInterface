<?php

    session_start();

    if (file_exists( '../includes/config.php' )) { require( '../includes/config.php'); }  else { header( 'Location: ../install' );};
    if(base64_decode($_SESSION['loggedin']) == 'true') {}
    else { header('Location: ../login.php'); }

    $v_1 = $_GET['domain'];


    if (CLOUDFLARE_EMAIL != '' && CLOUDFLARE_API_KEY != ''){
        $cfenabled = curl_init();

        curl_setopt($cfenabled, CURLOPT_URL, "https://api.cloudflare.com/client/v4/zones?name=" . $v_1);
        curl_setopt($cfenabled, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($cfenabled, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cfenabled, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($cfenabled, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($cfenabled, CURLOPT_HTTPHEADER, array(
        "X-Auth-Email: " . CLOUDFLARE_EMAIL,
        "X-Auth-Key: " . CLOUDFLARE_API_KEY));

        $cfdata = array_values(json_decode(curl_exec($cfenabled), true));
        $cfid = $cfdata[0][0]['id'];
        $cfname = $cfdata[0][0]['name'];

        if ($cfdata[0][0]['name'] != '' && isset($cfdata[0][0]['name']) && $cfdata[0][0]['name'] == $v_1){ 

            $curl0 = curl_init(); 

            curl_setopt($curl0, CURLOPT_URL, $vst_url);
            curl_setopt($curl0, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl0, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl0, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl0, CURLOPT_POST, true);
            curl_setopt($curl0, CURLOPT_POSTFIELDS, http_build_query( array('user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-dns-records','arg1' => $username,'arg2' => $v_1, 'arg3' => 'json')));

            $dnsdata = array_values(json_decode(curl_exec($curl0), true));
            $keys = array_keys(array_column(json_decode(curl_exec($curl0), true), 'TYPE'), 'NS');

            foreach ($keys as &$value) {
                $value = $dnsdata[$value]['ID'];
            }  
            $requestArr = array_column(json_decode(curl_exec($curl0), true), 'TYPE');
            $requestrecord = array_search('NS', $requestArr);
            $requesta = array_search('A', $requestArr);
            $requestrecord = $dnsdata[$requestrecord]['ID'];
            foreach($dnsdata as $val) {

                ${'del0' . $val} = curl_init(); 

                curl_setopt(${'del0' . $val}, CURLOPT_URL, $vst_url);
                curl_setopt(${'del0' . $val}, CURLOPT_RETURNTRANSFER,true);
                curl_setopt(${'del0' . $val}, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt(${'del0' . $val}, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt(${'del0' . $val}, CURLOPT_POST, true);
                curl_setopt(${'del0' . $val}, CURLOPT_POSTFIELDS, http_build_query(array('user' => $vst_username,'password' => $vst_password,'returncode' => 'yes','cmd' => 'v-delete-dns-record','arg1' => $username,'arg2' => $v_1, 'arg3' => $val['ID'])));

                curl_exec( ${'del0' . $val});
                curl_close( ${'del0' . $val});
            } 
            
            $cfrecords = curl_init();

            curl_setopt($cfenabled, CURLOPT_URL, "https://api.cloudflare.com/client/v4/zones/" . $cfid . "/dns_records&per_page=100");
            curl_setopt($cfenabled, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($cfenabled, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($cfenabled, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($cfenabled, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($cfenabled, CURLOPT_HTTPHEADER, array(
            "X-Auth-Email: " . CLOUDFLARE_EMAIL,
            "X-Auth-Key: " . CLOUDFLARE_API_KEY));

            $recorddata = array_values(json_decode(curl_exec($cfrecords), true));
            $records = $recorddata[0];
            
            foreach ($records as &$val1) {
                
                ${'add' . $val1} = curl_init(); 

                curl_setopt(${'add' . $val1}, CURLOPT_URL, $vst_url);
                curl_setopt(${'add' . $val1}, CURLOPT_RETURNTRANSFER,true);
                curl_setopt(${'add' . $val1}, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt(${'add' . $val1}, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt(${'add' . $val1}, CURLOPT_POST, true);
                curl_setopt(${'add' . $val1}, CURLOPT_POSTFIELDS, http_build_query(array('user' => $vst_username,'password' => $vst_password,'cmd' => 'v-add-dns-record','arg1' => $username,'arg2' => $v_1, 'arg3' => $val1['name'], 'arg4' => $val1['type'], 'arg5' => $val1['content'])));

                curl_exec( ${'add' . $val1});
                curl_close( ${'add' . $val1});
            }
            $del2 = curl_init(); 

            curl_setopt($del2, CURLOPT_URL, $vst_url);
            curl_setopt($del2, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($del2, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($del2, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($del2, CURLOPT_POST, true);
            curl_setopt($del2, CURLOPT_POSTFIELDS, http_build_query(array('user' => $vst_username,'password' => $vst_password,'returncode' => 'yes','cmd' => 'v-delete-dns-record','arg1' => $username,'arg2' => $v_1, 'arg3' => $dnsdata[$requesta]['ID'])));

            curl_exec($del2);
            curl_close($del2);
            
            
            $ns = curl_init(); 
            curl_setopt($ns, CURLOPT_URL, $vst_url);
            curl_setopt($ns, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ns, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ns, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ns, CURLOPT_POST, true);
            curl_setopt($ns, CURLOPT_POSTFIELDS, http_build_query( array('user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-user-ns','arg1' => $username,'arg2' => 'json')));

            $nsdata = array_values(json_decode(curl_exec($ns), true));
            
            $curl1 = curl_init(); 

            curl_setopt($curl1, CURLOPT_URL, $vst_url);
            curl_setopt($curl1, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl1, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl1, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl1, CURLOPT_POST, true);
            curl_setopt($curl1, CURLOPT_POSTFIELDS, http_build_query(array('user' => $vst_username,'password' => $vst_password,'returncode' => 'yes','cmd' => 'v-add-dns-record','arg1' => $username,'arg2' => $v_1, 'arg3' => '@', 'arg4' => 'NS', 'arg5' => $nsdata[0])));

            curl_exec($curl1);
            curl_close($curl1);

            $curl2 = curl_init(); 

            curl_setopt($curl2, CURLOPT_URL, $vst_url);
            curl_setopt($curl2, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl2, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl2, CURLOPT_POST, true);
            curl_setopt($curl2, CURLOPT_POSTFIELDS, http_build_query(array('user' => $vst_username,'password' => $vst_password,'returncode' => 'yes','cmd' => 'v-add-dns-record','arg1' => $username,'arg2' => $v_1, 'arg3' => '@', 'arg4' => 'NS', 'arg5' => $nsdata[1])));

            curl_exec($curl2);
            curl_close($curl2);

            foreach($keys as $val) {
    
                ${'del1' . $val} = curl_init(); 

                curl_setopt(${'del1' . $val}, CURLOPT_URL, $vst_url);
                curl_setopt(${'del1' . $val}, CURLOPT_RETURNTRANSFER,true);
                curl_setopt(${'del1' . $val}, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt(${'del1' . $val}, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt(${'del1' . $val}, CURLOPT_POST, true);
                curl_setopt(${'del1' . $val}, CURLOPT_POSTFIELDS, http_build_query(array('user' => $vst_username,'password' => $vst_password,'returncode' => 'yes','cmd' => 'v-delete-dns-record','arg1' => $username,'arg2' => $v_1, 'arg3' => $val)));

                curl_exec( ${'del1' . $val});
                curl_close( ${'del1' . $val});
            } 
            
            $cfdelete = curl_init();

            curl_setopt_array($cfdelete, array(
            CURLOPT_URL => "https://api.cloudflare.com/client/v4/zones/" . $cfid,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "X-Auth-Email: " . CLOUDFLARE_EMAIL,
            "X-Auth-Key: " . CLOUDFLARE_API_KEY
            ),
            ));

            curl_exec($cfdelete);
        }  
    }
?>
<!DOCTYPE html>
<html lang="en" >

<head>
  <meta charset="UTF-8">
      <style>
    @import url(https://fonts.googleapis.com/css?family=Lato:300);


body{
	text-align: center;
	background-color: darkorange;
	overflow: hidden;
  height: 100%;
  line-height: 100%;
}
.outer {
    display: table;
    position: absolute;
    height: 100%;
    width: 100%;
}

.middle {
    display: table-cell;
    vertical-align: middle;
}
.box{
  margin-left: auto;
  margin-right: auto; 
	display: inline-block;
	height: 200px;
	position: relative;
	/*margin:0 -4px -5px -2px;*/
	transition: all .2s ease;
}

/* MEDIA QUERIES */
@media (max-width: 700px){
	.box{
		width: 50%;
	}

	.box:nth-child(2n-1){
		background-color: inherit;
	}

	.box:nth-child(4n),.box:nth-child(4n-3) {
		background-color: rgba(0,0,0,0.05);
	}

}

@media (max-width: 420px){
	.box{
		width: 100%;
	}

	.box:nth-child(4n),.box:nth-child(4n-3){
		background-color: inherit;
	}

	.box:nth-child(2n-1){
		background-color:rgba(0,0,0,0.05);
	}

}

.loader{
	position: relative;
	width: 150px;
	height: 20px;
	top: 45%;
	top: -webkit-calc(50% - 10px);
	top: calc(50% - 10px);
	left: 25%;
	left: -webkit-calc(50% - 75px);
	left: calc(50% - 75px);
}

.loader:after{
	content: "CHECKING DOMAIN ...";
	color: #fff;
	font-family:  Lato,"Helvetica Neue" ;
	font-weight: 200;
	font-size: 14px;
	position: absolute;
	width: 100%;
	height: 20px;
	line-height: 20px;
	left: 0;
	top: 0;
	background-color: darkorange;
	z-index: 1;
}
.special:after{
	content: "EXPORTING RECORDS ...";
}
.update:after{
	content: "UPDATING NS ...";
}
.update1:after{
	content: "IMPORTING RECORDS ...";
}
.update2:after{
	content: "DELETING CLOUDFLARE ...";
}
.process:after{
	content: "PROCESSING ...";
}
.loader:before{
	content: "";
	position: absolute;
	background-color: #fff;
	top: -5px;
	left: 0px;
	height: 30px;
	width: 0px;
	z-index: 0;
	opacity: 1;
	-webkit-transform-origin:  100% 0%;
			transform-origin:  100% 0% ;
	-webkit-animation: loader 10s ease-in-out infinite;
			animation: loader 10s ease-in-out infinite;
}



@-webkit-keyframes loader{
    0%{width: 0px;}
    70%{width: 100%; opacity: 1;}
    90%{opacity: 0; width: 100%;}
    100%{opacity: 0;width: 0px;}
}

@keyframes loader{
    0%{width: 0px;}
    70%{width: 100%; opacity: 1;}
    90%{opacity: 0; width: 100%;}
    100%{opacity: 0;width: 0px;}
}
    </style>
</head>
<body>
<div class="outer">
  <div class="middle">
		<div class="box">
			<div class="loader"></div>
		</div>
 </div>
</div>
    <script src="https://cdn.jsdelivr.net/gh/cdgco/VestaWebInterface@v0.4.0-Beta/plugins/bower_components/jquery/dist/jquery.min.js"></script>
    <script>
    setTimeout( function(){ 
       $('.loader').addClass("special"); 
      }  , 2000 );
    setTimeout( function(){ 
       $('.loader').addClass("update"); 
      }  , 5000 );
        setTimeout( function(){ 
       $('.loader').addClass("update1"); 
      }  , 7000 );
    setTimeout( function(){ 
       $('.loader').addClass("process"); 
      }  , 9000 );
    </script>
</body>
</html>
<?php header("Location: ../list/dns.php?delcf=0"); ?>