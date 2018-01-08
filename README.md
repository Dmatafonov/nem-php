<h1>Open source NEM PHP library</h1>
<p> PHP implementation of official NEM API : https://nemproject.github.io/</p> 

<img src="https://nemproject.github.io/logo.png" style="height: 100px"/>
<h1>NIS server installation</h1>

<ul>
   <li>First you should install your own NIS server on Ubuntu/Debian, following instructions: https://forum.nem.io/t/nem-supernode-command-line-tutorial-for-debian-8-4/2211</li>
</ul>

<h1>PHP library installation</h1>

   <h3>Clone this repo to folder nem-php</h3>
   
   ```
      mkdir nem-php
      cd nem-php
      clone https://github.com/Dmatafonov/nem-php.git .
   ```
   
   <h3>Set up library</h3>
   
   ```php
      
      require_once 'nem-php/nem-php.php';
      
      $config = [
        'net'               => 'mainnet', //testnet or mijin
        'nis_address'       => 'http://127.0.0.1',
        'private'           => 'xxx', //put your private key from nem wallet
        'public'            => 'xxx', //put your public key from nem wallet
        'security_check'    => true //leave it true if you are not sure
      ];

      $nemPhp = new NemPhp($config);  //Get library
      print_r($nemPhp->heartbit()); //Check if it's working

   ```
   
<h1>Usage</h1>
   
   <h3>Send XEM</h3>
   
   ```php
   
     //Prepare transaction
      $nemPhp->prepareTransaction(
            1, //How much XEM to send 
            0, //Put higher fee if you want, otherwise leave it zero so minimum fee will be taken off
            'NDNRSW-FEQ256-HQLUYT-L6SOS3-RYHEJK-BLOWL2-C6MJ' //adress where to send
      );
      
      //You can check your future transaction before sending
      print_r($nemPhp->transaction);
      
      //Or get estimated transaction fee in microXEM (actual amount in XEM will be divided vy 1000000)
      echo $nemPhp->transaction['fee'];
      
      //And commit transaction to the network (you shoud almost immidiately hear 'dink' sound from you wallet
      $result = $nemPhp->commitTransaction();
      
      //See how its gone
      print_r($result);
      
   ```
   
   <h3>Send Mosaic</h3>
   
   ```php

     //Prepare transaction
      $nemPhp->prepareTransaction(
            1, //How much mosaics to send 
            0, //Put higher fee if you want, otherwise leave it zero so minimum fee will be taken off
            'NDNRSW-FEQ256-HQLUYT-L6SOS3-RYHEJK-BLOWL2-C6MJ' //adress where to send
      );

      //Add mosaics to your transaction
      $nemPhp->addMosaicToTransaction(
         'zeus-test', //namespace
         'aapl', //name
         1 //quantity
      );


      //You can check your future transaction before sending
      print_r($nemPhp->transaction);
      
      //Or get estimated transaction fee in microXEM (actual amount in XEM will be divided vy 1000000)
      echo $nemPhp->transaction['fee']; 

      //And commit transaction to the network (you shoud almost immidiately hear 'dink' sound from you wallet
      $result = $nemPhp->commitTransaction();

      //See how its gone
      print_r($result);

   ```
      
<h3>Get mosaic info by namespace and name</h3>

   ```php
   
   $mosaic = $nemPhp->fetchMosaicInfo('zeus-test', 'aaple');
   
   ```
   

<h3>Get all mosaics info by namespace </h3>

   ```php
   
   $mosaics = $nemPhp->fetchAllMosaics('zeus-test');
   
   ```
      
      
<h3>Fetch incoming transactions</h3>
   
   ```php

     //Get 25 most recent transactions 
      $transactions = $nemPhp->accountTransfersIncoming(
          null, //address - if ommitted, then address is taken from public key
          null, //Get 25 transactions that appeared directly before the transaction with paricular id
          null  //Get 25 transactions that appeared directly before the transaction with paricular hash
      );

      //Get 25 transactions that appeared directly before the transaction with paricular id
      $transactions = $nemPhp->accountTransfersIncoming(null, $id);
      
      //Get 25 transactions that appeared directly before the transaction with paricular hash
      $transactions = $nemPhp->accountTransfersIncoming(null, null, '674cf29a76c2e86368f8ff6608db731fa6aa54cf4bfdf4efe6c65c946eb3ae01');

   ```
   
   <h3>Fetch outgoing transactions</h3>
   
   
   ```php

     //Get 25 most recent transactions 
      $transactions = $nemPhp->accountTransfersOutgoing(
          null, //address - if ommitted, then address is taken from public key
          null, //Get 25 transactions that appeared directly before the transaction with paricular id
          null  //Get 25 transactions that appeared directly before the transaction with paricular hash
      );


   ```
   
   
<h3>Fetch all transactions</h3>
      
   
   ```php

     //Get 25 most recent transactions 
      $transactions = $nemPhp->accountTransfersAll(
          null, //address - if ommitted, then address is taken from public key
          null, //Get 25 transactions that appeared directly before the transaction with paricular id
          null  //Get 25 transactions that appeared directly before the transaction with paricular hash
      );
      

   ```
   
   
<h3>Fetch uncofirmed transactions</h3>
   
   
   ```php

     //Get 25 most recent transactions 
      $transactions = $nemPhp->accountUnconfirmedTransactions(
          null, //address - if ommitted, then address is taken from public key
      );
      

   ```
   
   
<h3>Other supported API calls</h3>
   
   ```
   
      //Account related calls
      ->status(); //get account status
      ->accountGenerate(); //generate new account
      ->accountGet(); //request account data
      ->accountGetFromPublicKey($public = null); //same as above, ut you can put someone's public key to gind out his address and how rich he is
      ->accountGetForwarded(); //Requesting the original account data for a delegate account
      ->accountStatus(); //short account status
      ->localAccountTransfersIncoming($id = null, $hash = null); //same as accountTransfersIncoming but with messages decrypted
      ->localAccountTransfersOutgoing($id = null, $hash = null); //same as accountTransfersOutgoing but with messages decrypted
      ->localAccountTransfersAll($id = null, $hash = null); //same as accountTransfersAll but with messages decrypted
      ->accountHarvests($address = null, $hash = null); //Requesting harvest info data for an account
      ->accountImportances(); //Retrieving account importances for accounts
      ->accountNamespacePage(); //Retrieving namespaces that an account owns
      ->accountMosaicDefinitionPage($address = null, $parent = null, $id = null); //raw mosaic definition, same as fetchMosaicInfo
      ->accountMosaicOwned($address = null); //Retrieving mosaics that an account owns
      ->accountUnlock(); //Unlocking accounts
      ->accountLock(); //Locking accounts
      ->accountUnlockedInfo(); //Retrieving the unlock info
      ->accountHistoricalGet($address = null, $startHeight, $endHeight, $increment = 1000); //Retrieving historical account data
      
      
      //Blockchain related calls
      ->chainHeight(); //Block chain height
      ->chainScore(); //Block chain score
      ->chainLastBlock(); //Last block of the block chain score
      ->blockAtPublic($blockHeight); //Getting a block with a given height
      ->localChainBlocksAfter($blockHeight); //Getting part of a chain
      
      //Namespaces
      ->namespaceRootPage(); //Retrieving root namespaces
      ->namespaceRequest($id); //Retrieving a specific namespace
      
      
   ```
   
<h3>Donate if you like it or contibute if you don't have XEM yet :)</h3>
<p>XEM: <b>NDNRSW-FEQ256-HQLUYT-L6SOS3-RYHEJK-BLOWL2-C6MJ</b></p>
