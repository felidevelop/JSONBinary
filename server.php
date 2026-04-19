<?php
require_once 'JSONBinary.php';

$data = [
   (object)["nombre"=>"pdf.pdf","content"=>new JSONBinary(file_get_contents("pdf.pdf"))],
];

sendJSONBinary($data);