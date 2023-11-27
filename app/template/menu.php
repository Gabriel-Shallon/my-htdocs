<ul class="navbar-nav ml-auto">
    <li class="nav-item">
        <a class="nav-link" href="index.php">Home</a>
    </li>


    <?php 
        require_once "api/Connection.php";
        require_once "api/Transaction.php";
        require_once "api/Logger.php";
        require_once "api/LOggerXML.php";
        require_once "api/Record.php";
        require_once "api/Repository.php";
        require_once "model/Menu.php";

        Transaction::open('arquivo');
        Transaction::setLogger(new LoggerXML('tmp/log_menu.xml'));
        Transaction::log('Criação de Menu');

        $repositorio = new Repository('Menu');
        $menus = $repositorio->load();

        foreach ($menus as $menu){
        
    ?>



    <li class="nav-item">
        <a class="nav-link" href="<?= $menu->link; ?>">
        <?= $menu->descricao;?></a>
    </li>
</ul>

<?php } ?>