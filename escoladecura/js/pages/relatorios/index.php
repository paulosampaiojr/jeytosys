<?php
require '../../conexao.php';

// Busca todas as gerações
$sql_geracoes = "SELECT id, nome FROM geracao";
$result = $conn->query($sql_geracoes);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Gerações</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h1>Relatórios por Geração</h1>
        <ul>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <li>
                    <a href="geracao.php?geracao_id=<?php echo $row['id']; ?>">
                        <?php echo htmlspecialchars($row['nome']); ?>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>
