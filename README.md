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
   </pre>
