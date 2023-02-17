<?php

    class FileStore
	{
		private $file;
		private $content = [];
        public function __construct($file)
		{
			if(file_exists($file))
			{
				$h = fopen($file, "r");
				$content = fread($h, filesize($file));
				$this->content = $content ? json_decode($content, true) : [];
				fclose($h);
			}
			$this->file = $file;
		}
		
		private function store()
		{
			$h = fopen($this->file, "w");
			fwrite($h, json_encode($this->content));
			fclose($h);
		}
		
		private function sort($content, $column, $desc)
		{
			uasort($content, function($a, $b) use($column, $desc)
            {
				$a = $a[$column];
				$b = $b[$column];
				
				if($column == "date")
                {
					$a = strtotime($a);
					$b = strtotime($b);
					if ($a == $b) return 0;
						return ($a < $b) ? ($desc ? 1 : -1) : ($desc ? -1 : 1);
				}
                elseif(in_array($column, ["message", "uuid"]))
                {
					$c = new Collator("pl_PL");
					$compare = $c->compare($a, $b);
					return $desc ? -$compare : $compare;
				}
			});
			return $content;
		}
		
		public function getList($limit, $offset, $order = null)
		{
			if(empty($order))
				$order = ["date", "desc"];
			$order = array_map("strtolower", $order);
            
			if(!in_array($order[0], ["uuid", "date", "message"]))
				$order[0] = "date";
				
			if(!in_array($order[1], ["asc", "desc"]))
				$order[1] = "desc";
			
            $list = [];
            foreach($this->content as $uuid => $item)
            {
                $item["uuid"] = $uuid;
                $list[] = $item;
            }
            
            $list = array_values($this->sort($list, $order[0], $order[1] == "desc"));
            $list = array_slice($list, $offset, $limit);
            
			return array_values($list);
		}
		
		public function addMessage($message)
		{
			$uuid = bin2hex(openssl_random_pseudo_bytes(16));
			$data = [
				"date" => date("Y-m-d H:i:s"),
				"message" => $message,
			];
			$this->content[$uuid] = $data;
			$this->store();
			
			return $uuid;
		}
		
		public function updateMessage($id, $message)
		{
			if(!empty($this->content[$id]))
			{
				$this->content[$id]["message"] = $message;
				$this->store();
			}
		}
		
		public function deleteMessage($id)
		{
			if(!empty($this->content[$id]))
			{
				unset($this->content[$id]);
				$this->store();
			}
		}
        
        public function getTotalRows()
        {
            return count($this->content);
        }
    }
	