<h1>Open source NEM PHP library</h1>
<p>The best blockchain is NEM!</p>

<img src="https://nemproject.github.io/logo.png" style="height: 100px"/>
<h1>NIS server installation</h1>

<ul>
   <li>First you should install your own NIS server on Ubuntu/Debian, following instructions: https://forum.nem.io/t/nem-supernode-command-line-tutorial-for-debian-8-4/2211</li>
</ul>

<h1>PHP library installation</h1>

   <h3>Clone this repo to folder nem-php</h3>
   <pre>
      mkdir nem-php
      cd nem-php
      clone https://github.com/Dmatafonov/nem-php.git .
   </pre>
   
   <h3>Set up library</h3>
   <pre>
      
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

   </pre>
   
<h1>Usage</h1>
   
   <h3>Send XEM</h3>
   
   <pre>
   
     //Prepare transaction
      $nemPhp->prepareTransaction(
            1, //How much XEM to send 
            0, //Put higher fee if you want, otherwise leave it zero so minimum fee will be taken off
            'NDNRSW-FEQ256-HQLUYT-L6SOS3-RYHEJK-BLOWL2-C6MJ' //adress where to send
      );
      
      //You can check your future transaction before sending
      print_r($nemPhp->transaction);
      
      //And commit transaction to the network (you shoud almost immidiately hear 'dink' sound from you wallet
      $result = $nemPhp->commitTransaction();
      
      //See how its gone
      print_r($result);
      
   </pre>
   
   <h3>Send Mosaic</h3>
   
   <pre>

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

      //And commit transaction to the network (you shoud almost immidiately hear 'dink' sound from you wallet
      $result = $nemPhp->commitTransaction();

      //See how its gone
      print_r($result);

   </pre>
      
   ```php
   test();
   ```
