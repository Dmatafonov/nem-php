<?php
/**
 * Created Denis Matafonov dmatafonov@gmail.com
 * Date: 07.01.2018
 * Time: 20:08
 */

Class NemApi {


    /**
     * AccountPrivateKeyTransactionsPage
     * https://nemproject.github.io/#accountPrivateKeyTransactionsPage
     * @param null $hash
     * @param null $id
     * @return array
     * @throws Exception
     */
    private function AccountPrivateKeyTransactionsPage($hash= null, $id = null){

        $this->checkIfSecure();

        return [
            'value'     => $this->private,
            'hash'      => $hash,
            'id'        => $id
        ];
    }

    /**
     * https://nemproject.github.io/#heart-beat-request
     * @return array
     */
    public function heartbit(){
        return $this->fetch_nis('/heartbeat');
    }

    /**
     * https://nemproject.github.io/#status-request
     * @return array
     */
    public function status(){
        return $this->fetch_nis('/status');
    }

    /*
     * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     Retrieving account data//
     https://nemproject.github.io/#retrieving-account-data
    * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     */

    /**
     * Generating new account data
     * https://nemproject.github.io/#generating-new-account-data
     * @return array
     */
    public function accountGenerate(){
        return $this->fetch_nis('/account/generate');
    }


    /**
     * Requesting the account data
     * https://nemproject.github.io/#requesting-the-account-data
     * @param $address
     * @return array
     */
    public function accountGet($address = null){

        if (!$address) $address = $this->getAddressFromPublicKey();
        return $this->fetch_nis('/account/get', [
            'address' => str_replace('-', '', $address)
        ]);
    }


    /**
     * Requesting the account data
     * https://nemproject.github.io/#requesting-the-account-data
     * @return array
     */
    public function accountGetFromPublicKey($public = null){
        return $this->fetch_nis('/account/get/from-public-key', [
            'publicKey' => ($public) ? $public : $this->public
        ]);
    }

    /**
     * Requesting the original account data for a delegate account
     * https://nemproject.github.io/#requesting-the-original-account-data-for-a-delegate-account
     * @param $address
     * @return array
     */
    public function accountGetForwarded($address = null){

        if (!$address) $address = $this->getAddressFromPublicKey();
        return $this->fetch_nis('/account/get/forwarded', [
        'address' => str_replace('-', '', $address)
        ]);
    }

    /*
     * Requesting the account status
     * https://nemproject.github.io/#requesting-the-account-status
     * @param $address
     * @return array
     */
    public function accountStatus($address = null){

        if (!$address) $address = $this->getAddressFromPublicKey();
        return $this->fetch_nis('/account/status', [
            'address' => str_replace('-', '', $address)
        ]);
    }


    /**
     * * Requesting incoming transaction data for an account
     * https://nemproject.github.io/#requesting-transaction-data-for-an-account
     * @param $address
     * @param $hash
     * @param $id
     * @return array
     */
    public function accountTransfersIncoming($address = null, $hash = null, $id= null){

        if (!$address) $address = $this->getAddressFromPublicKey();
        return $this->fetch_nis('/account/transfers/incoming', [
            'address'   => str_replace('-', '', $address),
            'hash'      => $hash,
            'id'        => $id
        ]);
    }


    /**
     * * Requesting outgoing transaction data for an account
     * https://nemproject.github.io/#requesting-transaction-data-for-an-account
     * @param $address
     * @param $hash
     * @param $id
     * @return array
     */
    public function accountTransfersOutgoing($address = null, $hash = null, $id= null){

        if (!$address) $address = $this->getAddressFromPublicKey();
        return $this->fetch_nis('/account/transfers/outgoing', [
            'address'   => str_replace('-', '', $address),
            'hash'      => $hash,
            'id'        => $id
        ]);
    }


    /**
     * * Requesting all transaction data for an account
     * https://nemproject.github.io/#requesting-transaction-data-for-an-account
     * @param $address
     * @param $hash
     * @param $id
     * @return array
     */
    public function accountTransfersAll($address = null, $hash = null, $id= null){

        if (!$address) $address = $this->getAddressFromPublicKey();
        return $this->fetch_nis('/account/transfers/all', [
            'address'   => str_replace('-', '', $address),
            'hash'      => $hash,
            'id'        => $id
        ]);
    }


    /**
     * Requesting uncofirmed transaction data for an account
     * https://nemproject.github.io/#requesting-transaction-data-for-an-account
     * @param $address
     * @return array
     */
    public function accountUnconfirmedTransactions($address = null){

        if (!$address) $address = $this->getAddressFromPublicKey();
        return $this->fetch_nis('/account/unconfirmedTransactions', [
            'address'   => str_replace('-', '', $address)
        ]);
    }

    /**
     * Transaction incoming data with decoded messages
     * https://nemproject.github.io/#transaction-data-with-decoded-messages
     * @return array
     */
    public function localAccountTransfersIncoming($id = null, $hash = null){

        $params = $this->AccountPrivateKeyTransactionsPage($id, $hash);

        return $this->fetch_nis('/local/account/transfers/incoming', $params
            , 'POST');
    }

    /**
     * Transaction outgoing data with decoded messages
     * https://nemproject.github.io/#transaction-data-with-decoded-messages
     * @return array
     */
    public function localAccountTransfersOutgoing($id = null, $hash = null){

        $params = $this->AccountPrivateKeyTransactionsPage($id, $hash);

        return $this->fetch_nis('/local/account/transfers/outgoing', $params
            , 'POST');
    }

    /**
     * Transaction all data with decoded messages
     * https://nemproject.github.io/#transaction-data-with-decoded-messages
     * @return array
     */
    public function localAccountTransfersAll($id = null, $hash = null){

        $params = $this->AccountPrivateKeyTransactionsPage($id, $hash);
        return $this->fetch_nis('/local/account/transfers/all', $params, 'POST');
    }

    /**
     * Requesting harvest info data for an account
     * https://nemproject.github.io/#requesting-harvest-info-data-for-an-account
     * @return array
     */
    public function accountHarvests($address = null, $hash = null){

        if (!$address) $address = $this->getAddressFromPublicKey();
        return $this->fetch_nis('/account/harvests', [
            'address' => str_replace('-', '', $address),
            'hash' => $hash
        ]);
    }

    /**
     * Retrieving account importances for accounts
     * https://nemproject.github.io/#requesting-transaction-data-for-an-account
     * @return array
     */
    public function accountImportances(){
        return $this->fetch_nis('/account/importances');
    }

    /**
     * Retrieving namespaces that an account owns
     * https://nemproject.github.io/#requesting-transaction-data-for-an-account
     * @param $address
     * @param null $parent
     * @param null $id
     * @param null $pageSize
     * @return array
     */
    public function accountNamespacePage($address = null, $parent = null, $id = null, $pageSize = null){

        if (!$address) $address = $this->getAddressFromPublicKey();
        return $this->fetch_nis('/account/namespace/page', [
            'address'   => str_replace('-', '', $address),
            'parent'    => $parent,
            'id'        => $id,
            'pageSize'  => $pageSize
        ]);
    }

    /**
     * Retrieving mosaic definitions that an account has created
     * https://nemproject.github.io/#retrieving-mosaic-definitions-that-an-account-has-created
     * @param $address
     * @param $parent
     * @param $id
     * @return array
     */
    public function accountMosaicDefinitionPage($address = null, $parent = null, $id = null){

        if (!$address) $address = $this->getAddressFromPublicKey();
        return $this->fetch_nis('/account/mosaic/definition/page', [
            'address'   => str_replace('-', '', $address),
            'parent'    => $parent,
            'id'        => $id
        ]);
    }

    /**
     * Retrieving mosaics that an account owns
     * https://nemproject.github.io/#retrieving-mosaics-that-an-account-owns
     * @param $address
     * @return array
     */
    public function accountMosaicOwned($address = null){

        if (!$address) $address = $this->getAddressFromPublicKey();
        return $this->fetch_nis('/account/mosaic/owned', [
            'address'   => str_replace('-', '', $address)
        ]);
    }


    /**
     * Unlocking accounts
     * https://nemproject.github.io/#locking-and-unlocking-accounts
     * @return array
     */
    public function accountUnlock(){
        return $this->fetch_nis('/account/unlock', [
            'value'     => $this->private
        ], 'POST');
    }

    /**
     * Locking accounts
     * https://nemproject.github.io/#locking-and-unlocking-accounts
     * @return array
     */
    public function accountLock(){
        return $this->fetch_nis('/account/lock', [
            'value'     => $this->private
        ], 'POST');
    }

    /**
     * Retrieving the unlock info
     * Each node can allow users to harvest with their delegated key on that node. The NIS configuration has entries for configuring the maximum number of allowed harvesters and optionally allow harvesting only for certain account addresses. The unlock info gives information about the maximum number of allowed harvesters and how many harvesters are already using the node.
     * https://nemproject.github.io/#retrieving-the-unlock-info
     * @return array
     */
    public function accountUnlockedInfo(){
        return $this->fetch_nis('/account/unlocked/info', [], 'POST');
    }


    /**
     * Retrieving historical account data
     * The configuration for NIS offers the possibility for a node to expose additional features that other nodes don't want to offer. One of those features is the supply of historical account data like balance and importance information. To turn on this feature for your NIS, you need to add HISTORICAL_ACCOUNT_DATA to the list of optional features in the file config.properties.
     * https://nemproject.github.io/#retrieving-historical-account-data
     * @param $address string The address of the account.
     * @param $startHeight int The block height from which on the data should be supplied.
     * @param $endHeight int The block height up to which the data should be supplied. The end height must be greater than or equal to the start height.
     * @param int $increment The value by which the height is incremented between each data point. The value must be greater than 0. NIS can supply up to 1000 data points with one request. Requesting more than 1000 data points results in an error.
     * @return array
     */
    public function accountHistoricalGet($address = null, $startHeight, $endHeight, $increment = 1000){

        if (!$address) $address = $this->getAddressFromPublicKey();
        return $this->fetch_nis('/account/mosaic/definition/page', [
            'address'       => str_replace('-', '', $address),
            'startHeight'   => $startHeight,
            'endHeight'     => $endHeight,
            'increment'     => $increment
        ]);
    }


/*
    * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    Block chain related requests
    https://nemproject.github.io/#block-chain-related-requests
    * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 */

    /**
     * Block chain height
     * https://nemproject.github.io/#block-chain-height
     * @return array
     */
    public function chainHeight(){
        return $this->fetch_nis('/chain/height');
    }


    /**
     * Block chain score
     * https://nemproject.github.io/#block-chain-score
     * @return array
     */
    public function chainScore(){
        return $this->fetch_nis('/chain/score');
    }


    /**
     * Last block of the block chain score
     * https://nemproject.github.io/#last-block-of-the-block-chain-score
     * @return array
     */
    public function chainLastBlock(){
        return $this->fetch_nis('/chain/last-block');
    }


    /**
     * Getting a block with a given height
     * https://nemproject.github.io/#getting-a-block-with-a-given-height
     * @param $blockHeight int height of requested block
     * @return array
     */
    public function blockAtPublic($blockHeight){
        return $this->fetch_nis('/block/at/public', [
            'height' => $blockHeight
        ], 'POST');
    }


    /**
     * Getting part of a chain
     * Gets up to 10 blocks after given block height from the chain. If the database contains less than 10 block after the given height, then less blocks are returned. The returned data is an array of ExplorerBlockViewModel JSON objects.
     * https://nemproject.github.io/#getting-part-of-a-chain
     * @param $blockHeight int height of requested block
     * @return array
     */
    public function localChainBlocksAfter($blockHeight){
        return $this->fetch_nis('/local/chain/blocks-after', [
            'height' => $blockHeight
        ], 'POST');
    }




/*
    * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    Namespaces
    https://nemproject.github.io/#namespaces
    * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 */

    /**
     * Retrieving root namespaces
     * https://nemproject.github.io/#retrieving-root-namespaces
     * @param $id int|null
     * @param $pageSize int|null
     * @return array
     */
    public function namespaceRootPage($id = null, $pageSize = null){
        return $this->fetch_nis('/namespace/root/page', [
            'id'        => $id,
            'pageSize'  => $pageSize
        ]);
    }



    /**
     * Retrieving a specific namespace
     * https://nemproject.github.io/#retrieving-a-specific-namespace
     * @param $id int|null
     * @return array
     */
    public function namespaceRequest($id){
        return $this->fetch_nis('/namespace', [
            'namespace'        => $id
        ]);
    }

/*
    * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    Mosaics
    https://nemproject.github.io/#mosaics
    * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 */

    /**
     * Retrieving mosaic definitions
     * https://nemproject.github.io/#retrieving-mosaic-definitions
     * @param $namespace
     * @param $id
     * @param $pageSize
     * @return array
     */
    public function namespaceMosaicDefinitionPage($namespace, $id = null, $pageSize = 100){
        return $this->fetch_nis('/namespace/mosaic/definition/page', [
            'namespace'         => $namespace,
            'id'                => $id,
            'pageSize'          => max(100, $pageSize),
        ]);
    }


/*
    * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    Initiating transactions
    https://nemproject.github.io/#initiating-transactions
    * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 */



    /**
     * https://nemproject.github.io/#version-2-transfer-transactions
     * @return array
     * @throws Exception
     */
    public function requestPrepareAnnounce(){

        $this->checkIfSecure();

        return $this->fetch_nis('/transaction/prepare-announce',
            [
                'privateKey'    => $this->private,
                'transaction'   => $this->transaction
            ]
        , 'POST');
    }

}
