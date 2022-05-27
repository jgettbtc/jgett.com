<?php

/*
+------------------+--------------+------+-----+---------+----------------+
| Field            | Type         | Null | Key | Default | Extra          |
+------------------+--------------+------+-----+---------+----------------+
| bitcoin_price_id | int          | NO   | PRI | NULL    | auto_increment |
| price_date       | datetime     | NO   | MUL | NULL    |                |
| price            | double       | NO   |     | NULL    |                |
| volume           | double       | NO   |     | NULL    |                |
| exchange         | varchar(100) | NO   |     | NULL    |                |
| source           | varchar(100) | NO   |     | NULL    |                |
| import_date      | datetime     | NO   |     | NULL    |                |
+------------------+--------------+------+-----+---------+----------------+
*/

class Importer{
    private $mysqli;
    
    function __construct(){
        $this->mysqli = new mysqli("localhost", "webapp", "badkitty", "jgett_data");
    }

    function real_escape($val, $quote){
        if ($val === null)
            return "NULL";
    
        $esc = $this->mysqli->real_escape_string($val);
    
        $result = $quote ? "'".$esc."'" : $esc;
    
        return $result;
    }

    function where($exchange, $source, $price_date){
        return "WHERE exchange ".($exchange === "NULL" ? "IS NULL" : "= $exchange")." AND source = $source AND price_date = $price_date";
    }

    function get_price($price_date, $exchange, $source){
        $price_date = $this->real_escape($price_date, true);
        $exchange = $this->real_escape($exchange, true);
        $source = $this->real_escape($source, true);

        $sql = "SELECT * FROM bitcoin_price ".$this->where($exchange, $source, $price_date);
        
        $query = $this->mysqli->query($sql);
        
        if ($row = $query->fetch_assoc()){
            return $row;
        }

        return false;
    }

    function update_price($price_date, $price, $volume, $exchange, $source, $import_date){
        $price_date = $this->real_escape($price_date, true);
        $price = $this->real_escape($price, false);
        $volume = $this->real_escape($volume, false);
        $exchange = $this->real_escape($exchange, true);
        $source = $this->real_escape($source, true);
        $import_date = $this->real_escape($import_date, true);

        $sql = "UPDATE bitcoin_price SET price = $price, volume = $volume, import_date = $import_date ".$this->where($exchange, $source, $price_date);

        $this->mysqli->query($sql);
    
        return $this->mysqli->affected_rows;
    }

    function insert_price($price_date, $price, $volume, $exchange, $source, $import_date){
        $price_date = $this->real_escape($price_date, true);
        $price = $this->real_escape($price, false);
        $volume = $this->real_escape($volume, false);
        $exchange = $this->real_escape($exchange, true);
        $source = $this->real_escape($source, true);
        $import_date = $this->real_escape($import_date, true);

        $sql = "INSERT bitcoin_price (price_date, price, volume, exchange, source, import_date) VALUES ($price_date, $price, $volume, $exchange, $source, $import_date)";

        $this->mysqli->query($sql);
    
        return $this->mysqli->affected_rows;
    }
    
    function save_price($arr) {
        $result = array('inserts' => 0, 'updates' => 0);

        if ($row = $this->get_price($arr['price_date'], $arr['exchange'], $arr['source']))
            $result['updates'] += $this->update_price($arr['price_date'], $arr['price'], $arr['volume'], $arr['exchange'], $arr['source'], $arr['import_date']);
        else
            $result['inserts'] += $this->insert_price($arr['price_date'], $arr['price'], $arr['volume'], $arr['exchange'], $arr['source'], $arr['import_date']);

        return $result;
    }
    
    function parse_line($line, $exchange, $source) {
        $now = date('Y-m-d H:i:s');
        $jan3_2009 = 1230958800;
        $parts = explode(',', str_replace(array("\r", "\n"), '', $line));
        if (count($parts) >= 3) {
            if ($parts[0] > $jan3_2009) {
                $price_date = date('Y-m-d H:i:s', $parts[0]);
                $price = $parts[1];
                $volume = $parts[2];
    
                return array(
                    'price_date'    => $price_date,
                    'price'         => $price,
                    'volume'        => $volume,
                    'exchange'      => $exchange,
                    'source'        => $source,
                    'import_date'   => $now,
                );
            }
        }
    
        return false;
    }
    
    function find_index($arr, $key, $val) {
        $index = 0;
        foreach ($arr as $a){
            if (isset($a[$key]) && $a[$key] === $val)
                return $index;
            $index++;
        }
        return -1;
    }

    function get_bitbo_data($interval, $limit){
        /*
        Bitbo intervals:
    
        label   interval    limit
        ------- ----------- -----
        1H      1_min       60
        1D      5_min       288
        7D      30_min      336
        1M      3_hour      240
        YTD     1_day       338
        1Y      1_day       365
        5Y      5_day       366
        ALL     15_day      9999
        */

        $url = "https://api.bitbo.io/price-history?interval=$interval&limit=$limit";
        return $this->exec_url($url);
    }

    function diff_minutes($d1, $d2){
        $date1 = new DateTime($d1);
        $date2 = new DateTime($d2);
        $diff = $date1->diff($date2);
        $minutes = $diff->days * 24 * 60;
        $minutes += $diff->h * 60;
        $minutes += $diff->i;
        return $minutes;
    }

    function get_bitbo_price($d){
        $now = date('Y-m-d H:i:s');
        $today = date('Y-m-d');

        if ($d < '2010-09-16') return array(
            'source'        => 'n/a',
            'price'         => 0,
            'import_date'   => 'n/a',
        );

        if ($d > $today) return array(
            'source'        => 'n/a',
            'price'         => 0,
            'import_date'   => 'n/a',
        );
        
        $price = null;
        $source = null;
        $import_date = null;

        if ($d === $today){
            $do_import = false;
            
            // check for recent today price in database
            if ($row = $this->get_price($d, null, 'bitbo')){
                $minutes = $this->diff_minutes($now, $row['import_date']);
                if ($minutes > 5){
                    $do_import = true;
                }else{
                    $price = floatval($row['price']);
                    $source = 'database';
                    $import_date = $row['import_date'];
                }
            }else{
                $do_import = true;
            }
            
            if ($do_import){    
                // import the most recent price
                $import = $this->import_bitbo("1_min", "1440", true);
                $i = $this->find_index($import['prices'], 'price_date', $d);
                if ($i >= 0){
                    $price = floatval($import['prices'][$i]['price']);
                    $source = 'api';
                    $import_date = $import['now'];
                }
            }
        }else{            
            if ($row = $this->get_price($d, null, 'bitbo')){
                $price = floatval($row['price']);
                $source = 'database';
                $import_date = $row['import_date'];
            }
        }

        return array(
            'source'        => $source,
            'price'         => $price,
            'import_date'   => $import_date,
        );
    }
    
    function import_bitbo($interval, $limit, $insert){
        $data = $this->get_bitbo_data($interval, $limit);
    
        $prices = array();
    
        $now = date('Y-m-d H:i:s');
        $jan3_2009 = 1230958800;
    
        $prices = array();
    
        $found = count($data[$interval]);
    
        // find the last price for each day in the bitbo data
        foreach ($data[$interval] as $item) {
            if ($item['t'] >= $jan3_2009){
                $price_date = date('Y-m-d', $item['t']);
                $price = $item['p'];
    
                $i = $this->find_index($prices, 'price_date', $price_date);
    
                if ($i >= 0){
                    $prices[$i]['price'] = $price;
                }else{
                    $prices[] = array(
                        'price_date'    => $price_date,
                        'price'         => $price,
                        'volume'        => null,
                        'exchange'      => null,
                        'source'        => 'bitbo',
                        'import_date'   => $now,
                    );
                }
            }
        }
    
        $result = array('inserts' => 0, 'updates' => 0);

        if ($insert){
            foreach ($prices as $p){
                $x = $this->save_price($p);
                $result['inserts'] += $x['inserts'];
                $result['updates'] += $x['updates'];
            }
        }
    
        return array(
            "now"       => $now,
            "found"     => $found,
            "inserts"   => $result['inserts'],
            "updates"   => $result['updates'],
            "prices"    => $prices,
        );
    }

    function import_yahoo($p1, $p2, $interval){
        //interval: 1d
        $period1 = strtotime($p1);
        $period2 = strtotime($p2);
        $url = "https://query1.finance.yahoo.com/v7/finance/download/BTC-USD?period1=$period1&period2=$period2&interval=$interval&events=history&includeAdjustedClose=true";
        $csv = $this->exec_url($url, 'csv');
        $data = array();

        if (count($csv) > 1){
            for ($i = 1; $i < count($csv); $i++){
                $row = $csv[$i];
                $item = array();
                for ($j = 0; $j < count($csv[0]); $j++){
                    $key = $csv[0][$j];
                    $val = $this->convert_to_number($row[$j]);
                    $item[$key] = $val;
                }
                $data[] = $item;
            }
        }

        return $data;
    }

    function dumpvar($v){
        header('content-type: text/plain');
        print_r($v);
        die();
    }
    
    function convert_to_number($val){
        if (is_numeric($val))
            return floatval($val);
        else
            return $val === "null" ? null : $val;
    }

    function exec_url($url, $format = "json") {
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $content = curl_exec($ch);
        //die($content);      
        
        curl_close($ch);

        if ($format === "json"){
            $data = json_decode($content, true);
        }else if ($format = "csv"){
            $data = str_getcsv($content, "\n"); //parse the rows
            foreach($data as &$row) $row = str_getcsv($row, ',');
        }else{
            $data = $content;
        }

        return $data;
    }
    
    function import_file($command, $exchange, $source){
        $file = "./{$exchange}USD.csv";
    
        $handle = fopen($file, "r") or die("Couldn't get handle");
        $count = 0;
    
        if ($handle) {
            while (!feof($handle)) {
                $line = fgets($handle);
                if ($parsed = $this->parse_line($line, $exchange, $source)) {
                    if ($command === 'import')
                        $count += $this->save_price($parsed);
                    else if ($command === 'count')
                        $count++;
                    else
                        die('unknown command');
                }
            }
            fclose($handle);
        }
    
        return array('ok' => $count);
    }

    function close(){
        $this->mysqli->close();
    }
}