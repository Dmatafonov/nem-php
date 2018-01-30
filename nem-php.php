<?php
/**
 * Created Denis Matafonov dmatafonov@gmail.com
 * Date: 07.01.2018
 * Time: 20:08
 */

require_once dirname(__FILE__) . '/nem-api.php';
Class NemPhp extends NemApi{

    public $private        = null; //you can set it here or in construction call
    public $public         = null; //you can set it here or in construction call
    public $nis_address    = null; //you can set it here or in construction call, example  http://94.130.167.236:7890
    public $net            = 'testnet';
    public $version        = -1744830463; // mainnet -1744830465、testnet -1744830463 https://nemproject.github.io/#version-2-transfer-transactions
    public $securityCheck  = true; //do you trust your NIS server id it's remote?

    //Transaction object gets filled in createTransaction method
    public $transaction = [
        'mosaics' => null
    ];

    public function __construct($config)
    {

        $this->net          = $config['net'];
        $this->private      = $config['private'];
        $this->public       = $config['public'];
        $this->nis_address  = rtrim($config['nis_address'], '/'); //no need for backslash at the end
        $this->securityCheck = (isset($config['security_check']) && $config['security_check'] === false) ? false : true;

        if (!$this->private)      throw new Exception('No private key set');
        if (!$this->public)       throw new Exception('No public key set');
        if (!$this->nis_address)  throw new Exception('No NIS address is set');

        if (!in_array($this->net, ['mainnet', 'testnet', 'mijin'])) {
            throw new Exception('Incorrect net is set, available values are: mainnet, testnet or mijin');
        }

        if ($this->net == 'mainnet') $this->version = 1744830466;
        if ($this->net == 'testnet') $this->version = -1744830462;
        if ($this->net == 'mijin') $this->version = 1610612738 ;
    }

    /**
     * Fetch NEM server based on url/params and get/set property. Expects json in response
     * @param $url
     * @param array $data
     * @param string $type
     * @return array
     */
    public function fetch_nis($url, $data = [], $type = 'GET'){

        if ($type == 'POST') {
            $url = $this->nis_address . $url;
        } else {
            $url = $this->nis_address . $url . '?' . http_build_query($data);
        }

        $curl = curl_init($url);
        if ($type == 'POST') {
            curl_setopt($curl,CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, Array("Content-Type: application/json"));
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl,CURLOPT_COOKIEJAR,      'cookie');
        curl_setopt($curl,CURLOPT_COOKIEFILE,     'tmp');
        curl_setopt($curl,CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt( $curl , CURLOPT_TIMEOUT , 20 ) ;

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
        ]);

        $json = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return ['result' => json_decode($json, true), 'url' => $url, 'code' => $code, 'type' => $type, 'data' => $data];
    }


    /**
     * Some transactions require private key send. Hence, you should never try sending it other then to your own local NIS server
     * @throws Exception
     */
    public function checkIfSecure(){

        if ($this->nis_address != 'http://127.0.0.1' && $this->nis_address != 'http://localhost' && $this->securityCheck) {
            throw new Exception ('Insecure request try: Use requests that use this structure only when NIS is running locally');
        }
    }


    /**
     * @param $amount int           The amount of micro XEM that is transferred from sender to recipient.
     * @param $fee int              The fee for the transaction. The higher the fee, the higher the priority of the transaction. Transactions with high priority get included in a block before transactions with lower priority.
     * @param $recipient string     Address of recepient
     * @param array|null $mosaics   Array of mosaics attached to transaction - in case mosaics are attached, it multiplies all mosaics amount in transfer
     * @param null|string $message  UTF8 encoded string.
     * @param bool $secure          Should be message encoded or not
     * @param bool $extractFeeFromTransaction Should be fee extracted from amount or not
     * @return array
     * @throws Exception
     */

    public function prepareTransaction($amount, $fee, $recipient, $mosaics = null, $message = null, $secure = false, $extractFeeFromTransaction = false){

        $this->transaction = [
            'timeStamp'     => (time() - 1427587585),           // The number of seconds elapsed since the creation of the nemesis block.
            'amount'        => round($amount * 1000000),        //The amount of micro NEM that is transferred from sender to recipient.
            'fee'           => round($fee    *1000000),         //The fee for the transaction. The higher the fee, the higher the priority of the transaction. Transactions with high priority get included in a block before transactions with lower priority.
            'recipient'     => str_replace('-', '', $recipient),
            'type'          => 257 ,
            'deadline'      => (time() - 1427587585 + 43200), // Remittance deadline
            'message'       => !isset($message) ? null : [ //If message is set then encode message to HEX
                'payload' => bin2hex($message) ,
                'type'    => $secure ? 2 : 1 //encoded message or not
            ],
            'version'       => $this->version ,
            'signer'        => $this->public
        ];

        if (is_array($mosaics)) {
            $this->transaction['mosaics'] = $mosaics;
        } else {
            $this->transaction['mosaics'] = [];
        }

        $this->estimateTransactionFee();

        if ($extractFeeFromTransaction === true) {
            $this->transaction['amount'] -= $this->transaction['fee'];
        }

        return $this->transaction;
    }


    /**
     * Add some mosaics to prepared transaction
     * @param $namespace
     * @param $name
     * @param $amount
     * @throws Exception
     */
    public function addMosaicToTransaction($namespace, $name, $amount){

        //First check if that mosaic is already added to transaction
        if (is_array($mosaics = $this->transaction['mosaics'])) {
            foreach ($mosaics as $mosaic) {
                if ($mosaic['mosaicId']['name'] == $name && $mosaic['mosaicId']['namespaceId'] == $namespace) {
                    throw new Exception('Can\'t add this mosaic in transaction since its already there. First remove it if you want to change amount.');
                }
            }
        }

        //Now check if that mosaic actially exists on the earth
        if (!$this->ifMosaicExists($namespace, $name)) {
            throw new Exception("Mosaic with name $name does not exist in namespace $namespace.");
        }

        //Add new mosaic
        $this->transaction['mosaics'][] = [
            'mosaicId' => [
                'namespaceId'   => $namespace,
                'name'          => $name
            ],
            'quantity'  => $amount
        ];

        //Fees
        $this->estimateTransactionFee();
    }


    /**
     * Get address string from public key
     * @return mixed
     */
    public function getAddressFromPublicKey($public = null){
        $accountInfo = $this->accountGetFromPublicKey($public);
        return $accountInfo['result']['account']['address'];
    }


    /**
     * Start transaction
     * @return array
     */
    public function commitTransaction(){
        return $this->requestPrepareAnnounce();
    }


    /**
     * Get mosaic's info
     * @param $namespace
     * @param $name
     * @return array
     */
    public function fetchMosaicInfo($namespace, $name){

        if($namespace !== 'nem' && $name !== 'xem') {

            $result = $this->namespaceMosaicDefinitionPage($namespace);
            $mosaics = (isset($result['result']) && isset($result['result']['data']))
                ? $result['result']['data']
                : [];

            return $this->_mosaicsToObject($mosaics, $name)[0];

        } else {

            return [
                'creator'       => 'nem',
                'description'   => 'Special mosaic for official XEM currency',
                'namespaceId'   => 'nem',
                'name'          => 'xem',
                'divisibility'  => 6,
                'initialSupply' => 8999999999,
                'supplyMutable' => false,
                'transferable'  => true
            ];
        }
    }

    /**
     * Convert mosic structure to plain object
     * @param $mosaics
     * @param null $name
     * @return array
     */
    private function _mosaicsToObject($mosaics, $name = null){

        $return = [];
        foreach ($mosaics as $_mosaic) {

            $_mosaic = $_mosaic['mosaic'];

            if ($_mosaic['id']['name'] == $name || !isset($name)) {

                $mosaic = [
                    'creator'           => $_mosaic['creator'],
                    'description'       => $_mosaic['description'],
                    'namespaceId'       => $_mosaic['id']['namespaceId'],
                    'name'              => $_mosaic['id']['name']
                ];

                foreach ($_mosaic['properties'] as $property) {
                    if ($property['name'] == 'divisibility')    $mosaic['divisibility']     = $property['value'];
                    if ($property['name'] == 'initialSupply')   $mosaic['initialSupply']    = $property['value'];
                    if ($property['name'] == 'supplyMutable')   $mosaic['supplyMutable']    = $property['value'];
                    if ($property['name'] == 'transferable')    $mosaic['transferable']     = $property['value'];
                }

                $return[] = $mosaic;
            }

        }
        return $return;
    }

    /**
     * Check if mosaic with specific namespace and name exists
     * @param $namespace
     * @param $name
     * @return bool
     */
    public function ifMosaicExists($namespace, $name){

        return isset($this->fetchMosaicInfo($namespace, $name)['name']);
    }

    /**
     * Get all mosaics at specified namespace
     * @param $namespace
     * @return array
     */
    public function fetchAllMosaics($namespace) {

        $result = $this->namespaceMosaicDefinitionPage($namespace);
        $mosaics = (isset($result['result']) && isset($result['result']['data']))
            ? $result['result']['data']
            : [];

       return $this->_mosaicsToObject($mosaics);
    }


    /**
     * https://nemproject.github.io/#transaction-fees
     * @return int
     * @throws Exception
     */
    private function estimateTransactionFee(){

        $fee = 0;

        if (empty($this->transaction)) {
            return $fee;
        }


        //Non mosaic transfer
        if (!is_array($this->transaction['mosaics']) || empty($this->transaction['mosaics'])) {

            //Fees for transferring XEM to another account: 0.05 XEM per 10,000 XEM transferred, capped at 1.25 XEM
            $fee +=  max(
                min(
                    0.05 * 1000000 * floor($this->transaction['amount'] / (10000 * 1000000)),
                    1.25 * 1000000
                ),
                0.05 * 1000000
            );
        } else {

            foreach ($this->transaction['mosaics'] as $mosaic) {

                $mosaicInfo = $this->fetchMosaicInfo($mosaic['mosaicId']['namespaceId'], $mosaic['mosaicId']['name']);

                //mosaics with divisibility of 0 and a maximum supply of 10,000 are called small business mosaics. 0.05 XEM fee for any transfer of a small business mosaic.
                if ($mosaicInfo['divisibility'] == 0 && $mosaicInfo['initialSupply'] <= 10000) {

                    $fee += 0.05 * 1000000;
                } else {

                    //For each mosaic that is transferred the fee is calculated the following way: given a mosaic with initial supply s, divisibility d and quantity q, the XEM equivalent is (round to the next smaller integer)
                    // xemEquivalent = (8,999,999,999 * q) / (s * 10^d)

                    $xemEquivalent = (8999999999 * $mosaic['amount']) / ($mosaicInfo['initialSupply'] * pow(10, $mosaicInfo['divisibility']));
                    $fee +=  max(
                        min(
                            0.05 * 1000000 * floor($xemEquivalent / (10000 * 1000000)),
                            1.25 * 1000000
                        ),
                        0.05 * 1000000
                    );

                }
            }
        }

        //Add message fee
        if (isset($this->transaction['message']['payload']) && strlen($this->transaction['message']['payload'])) {
            $fee += 1000000 * 0.05 * (floor(strlen(hex2bin($this->transaction['message']['payload'])) / 32) + 1);
        }

        return $this->transaction['fee'] = $fee;
    }

}
