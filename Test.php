<?php
header("Content-Type: application/json");

include_once "Models/BaseModel.php";
include_once __DIR__ . "../Connection/Connection.php";
Connection::GetConnect();
echo getenv('KEY');