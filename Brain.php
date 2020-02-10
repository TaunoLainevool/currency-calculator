<?php
    class Brain{
        
        var $data;
        var $important_data;
        
        function readFile($file){
            /* Reading file */
            if (file_exists($file)){
               $xml = simplexml_load_file($file);
            } else {
                   exit('Failed to open file.');
            }
            
            $this->data = $xml;
        }
       
        public function getdata($date) {
           
            $data = $this->data;
            $sortable = array();

            foreach($data -> Cube -> Cube as $node) {
              $sortable[] = $node;
            }
           /*Finding currency rate of our envoice */
            foreach($sortable as $data){
                if($data['time'] <= $date){
                    $this->important_data  =$data[0];
                    
                   break;
                }
            }
       }
       
       public function calculator($amount,$from_currency,$to_currency){
           
           $important_data = $this ->important_data;
           $from_currency_rate = 0; 
           $to_currency_rate = 1;
           
           foreach($important_data as $data){
              if($data['currency'] == $from_currency ){
                  $from_currency_rate = $data['rate']->__toString();
              }elseif($data['currency'] == $to_currency and $to_currency != 'EUR'){
                  $to_currency_rate = $data['rate']->__toString();
              }
           }
          
          $the_amount_from = $amount/$from_currency_rate;
          $the_amount_to = $the_amount_from*$to_currency_rate;

          $my_json = [
                            'reimbursements' =>[[
                              'currency' => $from_currency,
                              'amount' => floatval($amount)
                           ],[
                              'currency' => $to_currency,
                              'amount' => round($the_amount_to,3)
                             ]
                           ]];
          $at_json = json_encode($my_json,JSON_PRETTY_PRINT);
          echo $at_json;

          }
       
    }
    
$testbrain =  new Brain();
$testbrain ->readFile('eurofxref-hist.xml');
$testbrain ->getdata('2020-01-26');
$testbrain ->calculator('108.89','GBP','EUR');
$testbrain ->calculator('108.89','GBP','USD');
