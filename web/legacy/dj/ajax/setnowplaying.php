<?php namespace ChapmanRadio;define('PATH', '../../');require_once PATH."inc/global.php";/* This page can only be used by a currently-broadcasting DJ. */if(!DJLive::isActive()) die(json_encode(array("error"=>"Error 403: Permission Denied. Your DJ Live session has ended. If this isn't correct, contact webmaster"))); $showid = Request::GetInteger('showid', NULL);if(!$showid) die(json_encode(array("error" => "missing or invalid showid $showid")));$trackid = Request::Get('trackid');$text = Request::Get('text');NowPlaying::setNowPlaying($showid, $trackid, $text);$nowplaying = NowPlaying::getNowPlaying($showid);$tweet = "";if(isset($nowplaying['trackid']) && $nowplaying['trackid'] != ""){	$tweet = "Now Playing: ".$nowplaying['track']." by ".$nowplaying['artist'];	$tweetfull = $tweet.". ".$nowplaying['text'];	if(strlen($tweetfull) <= 140) $tweet = $tweetfull;	}else if(isset($nowplaying['text'])) {	$tweet = $nowplaying['text'];	}// if($tweet != "") Social::tweet($tweet);Icecast::metadata($tweet);die(json_encode(array("success"=>true)));