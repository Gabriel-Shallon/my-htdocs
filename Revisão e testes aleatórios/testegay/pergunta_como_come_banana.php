<!DOCTYPE html>
<html>
<head>
    <title>Pergunta: Como você come uma Banana?</title>
</head>
<body>
    <h1>Pergunta: Como você come uma Banana?</h1>
    <form action="processar_resposta.php" method="post">
        <input type="hidden" name="nome" value="<?= $_GET['nome'] ?>">
        <label>
            <input type="radio" name="comoComeBanana" value="Garfo e faca"> Garfo e faca
        </label>
        <label>
            <input type="radio" name="comoComeBanana" value="Da Casca"> Da Casca
        </label>
        <label>
            <input type="radio" name="comoComeBanana" value="Tirando os pedaços"> Tirando os pedaços
        </label>
        <button type="submit">Próxima</button>
    </form>
</body>
</html>
