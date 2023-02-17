<?php

	require __DIR__ . "/FileStore.php";
    
	header('Access-Control-Allow-Credentials: true');
 	header('Access-Control-Allow-Origin: http://localhost:5173');
	header('Access-Control-Allow-Methods: POST, PUT, GET, DELETE, OPTIONS');
	
	$dbFile = "db.json";
	
	$out = [];
	$action = $_REQUEST["action"] ?? "";
	switch($action)
	{
		case "insert":
			if(!empty($_POST["values"]))
			{
				$values = json_decode($_POST["values"]);
				
				$f = new FileStore($dbFile);
				$uuid = $f->addMessage($values->message);
				$out = ["uuid" => $uuid];
			}
		break;
		
		case "update":
			if(!empty($_POST["key"]) && !empty($_POST["values"]))
			{
				$uuid = $_POST["key"];
				$values = json_decode($_POST["values"]);
				
				$f = new FileStore($dbFile);
				$f->updateMessage($uuid, $values->message);
			}
		break;
		
		case "delete":
			if(!empty($_POST["key"]))
			{
				$uuid = $_POST["key"];
				$f = new FileStore($dbFile);
				$f->deleteMessage($uuid);
			} 
		break;
			
		default:
			$offset = !empty($_GET["skip"]) && intval($_GET["skip"]) > 0 ? $_GET["skip"] : 0;
			$limit = !empty($_GET["take"]) && intval($_GET["take"]) > 0 ? $_GET["take"] : 20;
			
			$order = "id";
			$direction = "ASC";
			if(!empty($_GET["sort"]))
			{
				$sort = json_decode($_GET["sort"]);
				
				if(in_array($sort[0]->selector, ["uuid", "message", "date"]))
					$order = $sort[0]->selector;
				$direction = $sort[0]->desc ? "DESC" : "ASC";
			}
			
			$f = new FileStore($dbFile);
			$out = $f->getList($limit, $offset, [$order, $direction]);
			
			$out = [
				"data" => $out,
				"totalCount" => $f->getTotalRows(),
			];
	}
	
	echo json_encode($out);
