<?php 
        require_once "api/Connection.php";
        require_once "api/Transaction.php";
        require_once "api/Logger.php";
        require_once "api/LOggerXML.php";
        require_once "api/Record.php";
        require_once "model/Address.php";

        Transaction::open('arquivo');
        Transaction::setLogger(new LoggerXML('tmp/log_address.xml'));
        Transaction::log('Gerando o Address');

       
        $address = Address::find(1);
?>



<div class="col-lg-4 col-sm-6">
                  <h3 class="footer_text">Address</h3>
                  <div class="location_text">
                     <ul>
                        <li>
                           <a href="#">
                           <span class="padding_left_10"><i class="fa fa-map-marker" aria-hidden="true"></i></span><?= $address->endereco;?></a>
                        </li>
                        <li>
                           <a href="#">
                           <span class="padding_left_10"><i class="fa fa-phone" aria-hidden="true"></i></span>"<?= $address->telefone;?>"
                           </a>
                        </li>
                        <li>
                           <a href="#">
                           <span class="padding_left_10"><i class="fa fa-envelope" aria-hidden="true"></i></span>"<?= $address->email;?>"
                           </a>
                        </li>
                     </ul>
                  </div>
               </div>
