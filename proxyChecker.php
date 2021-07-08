<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//--------------------------------------------------------------------------------------------------
//Get all proxy IP's section
//--------------------------------------------------------------------------------------------------

    //Initiating curl
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/'.rand(111,999).'.36 (KHTML, like Gecko) Chrome/88.0.'.rand(1111,9999).'.104 Safari/'.rand(111,999).'.36');

    //get 10 pages from my-proxy.com
        $proxies = array();
        $firstcount = 1;
        $endcound = 10;
        for ($i = $firstcount; $i <= $endcound; $i++){
            curl_setopt($ch, CURLOPT_URL, "https://www.my-proxy.com/free-proxy-list-$i.html"); 
            $result =curl_exec($ch);
        
            ///Get Proxy 
            // >102.64.122.214:8085#U
            preg_match_all("!\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}:.\d{2,4}!", $result, $matches);
            $proxies = array_merge($proxies, $matches[0]);
        }
        curl_close($ch);
        print_r($proxies);


//--------------------------------------------------------------------------------------------------
//Checking all availabilty of each proxy IP's sections
//--------------------------------------------------------------------------------------------------
    $listProxies = [
        'good'=>[],
        'bad'=>[]
    ];
    foreach($proxies as $index=>$ipProxy)
    {
        checker($ipProxy);
    }
    print_r($listProxies);


function checker($ipProxy)
{
    global $listProxies;

    //defining starting time counter
        $time_pre = microtime(true);

    //setting up get_content options
        $options = array(
            'http'=>array(
                'proxy' => 'tcp://' . $ipProxy,   //IP:PORT info. ie: 8.8.8.8:2222
                'timeout' => 2,
                'request_fulluri' => true,
                'method'=>"GET",
                'header'=>"Accept-language: en\r\n" .
                "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.76 Safari/537.36\r\n"
            )
        );

    //Testing proxy to get content
        $context = stream_context_create($options);
        $base_url='http://lotsofrandomstuff.com/1.php'; //url that simply returns '1' each time
        $web=@file_get_contents($base_url,false,$context); 
    
    //calculating time execution
        $time_post = microtime(true);
        $exec_time = $time_post - $time_pre;

    //defining the result
        if($web=='1')
        {
            echo $ipProxy. " proxy is GOOD with execution time : ".($exec_time/1000000)."\n";
            array_push($listProxies['good'], $ipProxy);
        }else{
            echo $ipProxy. " proxy is dead with execution time : ".($exec_time/1000000)."\n";
            array_push($listProxies['bad'], $ipProxy);
        }
}
?>

