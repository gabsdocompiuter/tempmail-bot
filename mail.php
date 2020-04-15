<?php
require_once 'htmlDOM.php';

class TempMail{
    function __construct(){
        $this->cookieFile = dirname(__FILE__) . '/mail.cookie';
        $this->url = 'https://10minutemail.net';
    }
    
    function getMail(){
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);

        $result = curl_exec($ch);

        $oDom = new simple_html_dom();
        $oDom->load($result);
        $email = $oDom->find('[id="fe_text"]', 0)->value;

        return $email;
    }

    function getNewMail(){
        if(file_exists($this->cookieFile)){
            unlink($this->cookieFile);
        }

        return $this->getMail();
    }

    function getLastMessage(){
        //-------- Descobre o link do último email
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);

        $result = curl_exec($ch);

        $oDom = new simple_html_dom();
        $oDom->load($result);
        $mailList = $oDom->find('[id="maillist"]', 0);

        $lastMailLocation = $mailList->find('tr', 1)->onclick;
        $lastMailCodeRead = explode("'", $lastMailLocation)[1];

        //-------- Busca o último email
        $ch = curl_init($this->url . "/$lastMailCodeRead");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);

        $result = curl_exec($ch);

        $oDom = new simple_html_dom();
        $oDom->load($result);
        $mailSended = $oDom->find('[id="tab1"] [class="mailinhtml"]', 0)->innertext;

        return $mailSended;
    }

    function refreshTime(){
        try{
            $ch = curl_init($this->url . '/more.html');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        
            curl_exec($ch);
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }
}