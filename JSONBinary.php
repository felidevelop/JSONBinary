<?php

class JSONBinary implements JsonSerializable{
   private $bin;

   public function __construct($bin){
      $this->bin = $bin;
   }

   public function getBinary(){
      return $this->bin;
   }

   public function jsonSerialize(): string {
      return "SlNPTkJJTjowMDAwMDAwMA=="; // base 64 de "JSONBIN:00000000"
   }
}

function sendJSONBinary($json){
   $_BINS = [];
   $jsonlen = strlen(json_encode($json));
   $offset = 4 + $jsonlen;

   function packData(&$data, &$offset, &$bins){
      if ($data instanceof JSONBinary){
         $bins[] = $data->getBinary();
         $d = base64_encode("JSONBIN:".pack('N', $offset).pack('N', strlen($data->getBinary())));
         $offset += strlen($data->getBinary());
         $data = $d;
      }else if (is_array($data) || is_object($data)) {
         foreach ($data as &$subdata) {
            packData($subdata, $offset, $bins);
         }
      }
   };

   packData($json, $offset, $_BINS);

   header('Content-Type: application/octet-stream');
   header('Cache-Control: no-cache');
   echo pack('N', $jsonlen);
   echo json_encode($json, JSON_UNESCAPED_SLASHES);
   foreach ($_BINS as $_b) echo $_b;
   exit;

}