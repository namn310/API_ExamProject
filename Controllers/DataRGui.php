<?php
class DataRGui
{
    public function getData()
    {
        // $data = json_decode(file_get_contents("php://input"), true);
        $data1 =
            [
                [
                    1,
                    1,
                    1,
                    0,
                    0,
                    0,
                    0,
                    1,
                    0
                ],
                [
                    0,
                    0,
                    1,
                    0,
                    0,
                    0,
                    0,
                    1,
                    1
                ],
                [
                    0,
                    0,
                    0,
                    1,
                    0,
                    0,
                    1,
                    0,
                    0
                ],
                [
                    0,
                    0,
                    1,
                    0,
                    0,
                    0,
                    0,
                    0,
                    1
                ],
                [
                    0,
                    1,
                    1,
                    0,
                    0,
                    0,
                    0,
                    1,
                    0
                ],
                [
                    0,
                    0,
                    1,
                    0,
                    0,
                    0,
                    0,
                    1,
                    0
                ],
                [
                    1,
                    0,
                    0,
                    1,
                    0,
                    0,
                    0,
                    1,
                    1
                ],
                [
                    1,
                    1,
                    1,
                    1,
                    1,
                    1,
                    0,
                    1,
                    1
                ],
                [
                    1,
                    0,
                    1,
                    1,
                    0,
                    0,
                    0,
                    1,
                    0
                ],
                [
                    0,
                    1,
                    1,
                    1,
                    1,
                    0,
                    1,
                    1,
                    1
                ]
            ];
        echo json_encode($data1);
    }
    public function sendData()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $data1 =
            [
                [
                    1,
                    1,
                    1,
                    0,
                    0,
                    0,
                    0,
                    1,
                    0
                ],
                [
                    0,
                    0,
                    1,
                    0,
                    0,
                    0,
                    0,
                    1,
                    1
                ],
                [
                    0,
                    0,
                    0,
                    1,
                    0,
                    0,
                    0,
                    1,
                    0
                ],
                [
                    0,
                    0,
                    1,
                    0,
                    0,
                    0,
                    0,
                    0,
                    1
                ],
                [
                    0,
                    1,
                    1,
                    0,
                    0,
                    0,
                    1,
                    0,
                    0
                ],
                [
                    0,
                    1,
                    1,
                    0,
                    0,
                    1,
                    0,
                    1,
                    0
                ],
                [
                    1,
                    0,
                    0,
                    1,
                    0,
                    0,
                    0,
                    1,
                    1
                ],
                [
                    1,
                    1,
                    1,
                    1,
                    1,
                    1,
                    0,
                    1,
                    1
                ],
                [
                    1,
                    0,
                    1,
                    1,
                    0,
                    0,
                    1,
                    0,
                    0
                ],
                [
                    0,
                    1,
                    1,
                    1,
                    0,
                    1,
                    1,
                    1,
                    1
                ]
            ];
        echo json_encode($data);
    }
}
