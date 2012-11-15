<?php

define('MODULE_SHIPPING_TIRAMIZOO_APIKEY', '71caabeb34e39780d59b879ad2d57c34');



$data = new stdClass();
$data->pickup_postal_code = "8059";
$data->delivery_postal_code = "81925";
$data->items =  array(array
    (
      "width"=> 120,
      "height"=> 82,
      "length"=> 50,
      "weight"=> 2,
      "quantity"=> 1

    ),
    array(
      "width"=> 40,
      "height"=> 40,
      "length"=> 120,
      "weight"=> 5.4,
      "quantity"=> 3
    ));






$api = new tiramizoo_api(null, null);


print_r($api->request('quotes', $data, $result));


print_r($result);

    class tiramizoo_api {
    
        private $api_url = 'https://api.tiramizoo.com/v1';
    
        public function tiramizoo_api($api_userid, $api_key) {}
        
        public function request($method, $data = array(), &$result = false) {
                                
            $c = curl_init();

            curl_setopt($c, CURLOPT_URL, $this->api_url.'/'.$method.'?api_key='.MODULE_SHIPPING_TIRAMIZOO_APIKEY);
            curl_setopt($c, CURLOPT_POST, true);
            curl_setopt($c, CURLOPT_POSTFIELDS, preg_replace_callback('/(\\\u[0-9a-f]{4})/', array($this, "json_unescape"), json_encode($data)));

            curl_setopt($c, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                "Accept: application/json"
            ));

            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

            $result = curl_exec($c);
            $status = curl_getinfo($c, CURLINFO_HTTP_CODE);

            curl_close($c);

            switch ($status) {
                case 200:
                case 201:
                    $result = json_decode($result,true);
                    return true;
                break;
                default:
                    return false;
                break;
            }
    
        }   
    
        private function json_unescape($m) {
    
            return json_decode('"'.$m[1].'"');
    
        }
      
    }
    

    class payment_proxy {
        
        /*
            an instance of this class replaces the original payment class for one purpose:
            to execute code when the order is completed. it should act as a proxy for all
            methods and properties of the original payment class. however: if your payment
            method is unexpectedly broken, you may check this.
        */
    
        var $code, $title, $description, $enabled;
    
        function payment_proxy() {
        
            $this->code = $GLOBALS["payment_backup"]->code;
            $this->title = $GLOBALS["payment_backup"]->title;
            $this->description = $GLOBALS["payment_backup"]->description;
            $this->sort_order = $GLOBALS["payment_backup"]->sort_order;
            $this->enabled = $GLOBALS["payment_backup"]->enabled;
            $this->order_status = $GLOBALS["payment_backup"]->order_status;

        }
    
        function update_status() { 
        
            $GLOBALS["payment_backup"]->update_status(); 
            $this->enabled = $GLOBALS["payment_backup"]->enabled;
        
        }
   
        function javascript_validation() {
            return $GLOBALS["payment_backup"]->javascript_validation(); 
        }
   
        function selection() {
            return $GLOBALS["payment_backup"]->selection();     
        }

        function pre_confirmation_check() {
            return $GLOBALS["payment_backup"]->pre_confirmation_check();    
        }
    
        function confirmation() {
            return $GLOBALS["payment_backup"]->confirmation();  
        }

        function process_button() {
            return $GLOBALS["payment_backup"]->process_button();    
        }

        function before_process() {
            return $GLOBALS["payment_backup"]->before_process();    
        }

        function after_process() {

            /* code injection */
            if (preg_match('/^tiramizoo/', $_SESSION["shipping"]["id"])) {
                $GLOBALS["tiramizoo"]->submit();
            }

            return $GLOBALS["payment_backup"]->after_process(); 
    
        }

        function get_error() {
            return $GLOBALS["payment_backup"]->get_error();     
        }
    
        function check() {
            return $GLOBALS["payment_backup"]->check();     
        }
    
        function install() {
            $GLOBALS["payment_backup"]->install();  
        }
    
        function remove() {
            $GLOBALS["payment_backup"]->remove();   
        }
    
        function keys() {
            return $GLOBALS["payment_backup"]->keys();  
        }
    
    }
    
