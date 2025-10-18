<?php

namespace App\Controllers;

class SbController extends BaseController
{
    public function index(): string
    {
        return view('sb/sb_main');
    }


    // barcode test AJAX page //
    public function barcode_test_ajax(): string
    {
        return view('sb/barcode_test_ajax');
    }


    // plain text editor //
    public function text_editor(): string
    {

        $filePath = WRITEPATH . 'uploads/plain_text.txt';
        $content = '';

        if (is_file($filePath)) {
            $content = file_get_contents($filePath); // or file_get_contents($filePath)
        }

        return view('sb/text_editor', ['content' => $content]);
    }


    // save text editor content //
    public function save_text()
    {
        print_r($_POST);
        $content = $this->request->getPost('content');

        if (!empty($content)) {
            // Define file path
            $filePath = WRITEPATH . 'uploads/plain_text.txt';

            // Save text into file
            if (write_file($filePath, $content)) {
                return "Text saved successfully: " . $filePath;
            } else {
                return "Error: Unable to save file.";
            }
        }

        return "No content provided.";
    }


    // Parse function
    function parseGS1($data, $ais, $GS) {
        $pos = 0;
        $len = strlen($data);
        $results = [];

        while ($pos < $len) {
            $found = false;
            // Try to match any known AI at this position (longest first!)
            foreach (array_keys($ais) as $ai) {
                $aiLen = strlen($ai);
                if (substr($data, $pos, $aiLen) === $ai) {
                    $found = true;
                    $pos += $aiLen;
                    $fieldLen = $ais[$ai];
                    if ($fieldLen > 0) {
                        // Fixed-length field
                        $value = substr($data, $pos, $fieldLen);
                        $pos += $fieldLen;
                    } else {
                        // Variable-length field
                        $nextGS = strpos($data, $GS, $pos);
                        if ($nextGS === false) {
                            $value = substr($data, $pos); // until end
                            $pos = $len;
                        } else {
                            $value = substr($data, $pos, $nextGS - $pos);
                            $pos = $nextGS + 1; // skip GS
                        }
                    }
                    $results[$ai] = $value;
                    break;
                }
            }

            if (!$found) {
                // Skip unexpected characters
                $pos++;
            }
        }
        return $results;
    }


    // - barcode scan test - //
    function barcode_test(){
        $barcode = $this->request->getGet('barcode');
        $barcode = trim($barcode);
        print_r($barcode);

        echo view('sb/barcode_test', ['barcode' => $barcode]);
        echo "<pre>";
/*
        // ASCII 29 (Group Separator)
        $GS = chr(29);

        // Define GS1 Application Identifiers
        // key = AI, value = fixed length (0 = variable length)
        $ais = [
            "01"   => 14, // GTIN
            "10"   => 0,  // Batch/Lot
            "11"   => 6,  // Production date YYMMDD
            "17"   => 6,  // Expiry date YYMMDD
            "21"   => 0,  // Serial number
            "240"  => 0,  // Internal product ID
            "3100" => 6,  // Net weight (kg, 0 decimals)
            "41"   => 13  // Ship-to GLN
        ];

        

        // Run parser
        $decoded = parseGS1($barcode, $ais, $GS);

        // Show results
        echo "<pre>";
        print_r($decoded);
        echo "</pre>";
*/
        echo "- barcode: $barcode - ".strlen($barcode)." chars<hr/>";
        
        $parts = explode(chr(29), $barcode);
        print_r($parts);
        foreach ($parts as $group) {
            echo $group." - ";
            echo strlen($group)." chars";
            echo "<br/>";
        }
        echo " > extracting item code from group 0, item_code: ";
        $group0 = $parts[0];
        $item_code = substr($group0, 19);
        echo $item_code;
        echo "<br/>";

        echo " > extracting lot number from group 2, lot_no: ";
        $group2 = $parts[2];
        $lot_no = substr($group2, 2);
        echo $lot_no;
        echo "<br/>";

        echo " > extracting packing size from group 3, size: ";
        $group3 = $parts[3];
        $size = substr($group3, 7);
        echo $size;
        echo "<br/>";
        

        echo "</pre>";
        
        
        
    }



}
