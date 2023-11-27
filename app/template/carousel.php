<!-- banner section start --> 



<?php 


require_once "api/Connection.php";
        require_once "api/Transaction.php";
        require_once "api/Logger.php";
        require_once "api/LOggerXML.php";
        require_once "api/Record.php";
        require_once "model/Carousel.php";

        Transaction::open('arquivo');
        Transaction::setLogger(new LoggerXML('tmp/log_carousel.xml'));
        Transaction::log('Gerando o Carousel');

       
        $repositorio = new Repository('Carousel');
        $carousels = $repositorio->load();


        foreach ($carousels as $carousel){

?>



<div class="banner_section layout_padding">
               <div class="container-fluid">
                  <div id="main_slider" class="carousel slide" data-ride="carousel">
                     <div class="carousel-inner">
                        <div class="carousel-item active">
                           <div class="row">
                              <div class="col-sm-6">
                                 <div class="banner_taital_main">
                                    <h1 class="banner_taital"><?= $carousel->titulo; ?></h1>
                                    <p class="banner_text"><?= $carousel->descricao; ?></p>
                                    <div class="btn_main">
                                       <div class="started_text"><a href="#">Compre Agora</a></div>
                                       <div class="started_text active"><a href="#">Contato</a></div>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-sm-6">
                                 <div class="banner_img"><img src="images/banner-img.png"></div>
                              </div>
                           </div>
                        </div>
                        
                     <a class="carousel-control-prev" href="#main_slider" role="button" data-slide="prev">
                     <img src="images/arrow-left.png">
                     </a>
                     <a class="carousel-control-next" href="#main_slider" role="button" data-slide="next">
                     <img src="images/arrow-right.png">
                     </a>
                  </div>
               </div>
            </div>
            <!-- banner section end -->
            
        <?php } ?>