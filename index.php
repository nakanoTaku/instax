<?php//クライアントID・クライアントシークレット・リダイレクトURI・スコープ$client_id = "76415ab5df2849e89a263849ed84df91";$client_secret = "c911604148e8453e8961feb0b03725b5";$redirect_uri = "http://instax.herokuapp.com/";$scope = "basic+comments+relationships+likes"; //セッションスタートsession_start(); //[ステップ1] アプリ認証画面でリクエストトークンを取得if(!isset($_GET['code']) || !is_string($_GET['code']) || empty($_GET['code'])){  //CSRF対策  session_regenerate_id(true);  $_SESSION['state'] = $state = sha1(uniqid(mt_rand(),true));   //ユーザーをアプリ認証画面に飛ばす  header("Location: https://api.instagram.com/oauth/authorize/?client_id={$client_id}&redirect_uri=".rawurlencode($redirect_uri)."&scope={$scope}&response_type=code&state={$state}");  exit;} //[ステップ2] リクエストトークン($_GET["code"])とアクセストークンの交換if(!isset($_SESSION['state']) || empty($_SESSION['state']) || !isset($_GET['state']) || empty($_GET['state']) || $_SESSION['state'] != $_GET['state']) exit; //セッション終了$_SESSION = array();session_destroy(); //アクセストークンを取得し、JSONをオブジェクト形式に変換$obj = json_decode(@file_get_contents(  'https://api.instagram.com/oauth/access_token',  false,  stream_context_create(    array('http' => array(      'method' => 'POST',      'content' => http_build_query(array(        'client_id' => $client_id,        'client_secret' => $client_secret,        'grant_type' => 'authorization_code',        'redirect_uri' => $redirect_uri,        'code' => $_GET['code'],      )),    ))  ))); //ユーザーID・ユーザーネーム・ユーザーアイコン・アクセストークン$user_id = $obj->user->id;$user_name = $obj->user->username;$user_picture = $obj->user->profile_picture;$access_token = $obj->access_token; //出力echo "<img src=\"{$user_picture}\" width=\"100\" height=\"100\"><br/>@{$user_name}(ID:{$user_id})さんのアクセストークンは<mark>{$access_token}</mark>です！";