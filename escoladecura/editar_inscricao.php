<?php
session_start();
require 'conexao.php'; // Inclua a conexão com o banco de dados

// Verifique a conexão com o banco
if (!$conn) {
    die("Erro na conexão com o banco de dados: " . mysqli_connect_error());
}

// Verifique se a sessão de inscrição foi concluída
if (!isset($_SESSION['inscricao_concluida'])) {
    header("Location: index.php"); // Redireciona caso a inscrição não tenha sido concluída
    exit();
}

// Recupera os dados da sessão
$nome_completo = $_SESSION['nome_completo'];
$email = $_SESSION['email'];  // Caso tenha armazenado no banco ou sessão
$telefone = $_SESSION['telefone'];  // Caso tenha armazenado no banco ou sessão
$sexo = $_SESSION['sexo'];  // Caso tenha armazenado no banco ou sessão
$geracao_id = $_SESSION['geracao_id'];
$nome_lider = $_SESSION['nome_lider'];
$contato_lider = $_SESSION['contato_lider'];
$dia_escola = $_SESSION['dia_escola'];
$codigo_matricula = $_SESSION['codigo_matricula'];
$horario_inscricao = $_SESSION['horario_inscricao'];

// Consulta para recuperar o nome da geração
$sql_geracao = "SELECT nome FROM geracao WHERE id = ?";
$stmt_geracao = $conn->prepare($sql_geracao);
$stmt_geracao->bind_param("i", $geracao_id);
$stmt_geracao->execute();
$stmt_geracao->bind_result($nome_geracao);
$stmt_geracao->fetch();
$stmt_geracao->close();

// Verifica se o formulário foi enviado para atualizar os dados
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $novo_nome_completo = $_POST['nome_completo'];
    $novo_email = $_POST['email'];
    $novo_telefone = $_POST['telefone'];
    $novo_sexo = $_POST['sexo'];
    $novo_nome_lider = $_POST['nome_lider'];
    $novo_contato_lider = $_POST['contato_lider'];
    $novo_dia_escola = $_POST['dia_escola'];

    // Atualizar no banco de dados
    $sql_update = "UPDATE inscricoes SET nome_completo = ?, email = ?, telefone = ?, sexo = ?, nome_lider = ?, contato_lider = ?, dia_escola = ? WHERE codigo_matricula = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssssss", $novo_nome_completo, $novo_email, $novo_telefone, $novo_sexo, $novo_nome_lider, $novo_contato_lider, $novo_dia_escola, $codigo_matricula);

    if ($stmt_update->execute()) {
        $_SESSION['nome_completo'] = $novo_nome_completo;
        $_SESSION['email'] = $novo_email;
        $_SESSION['telefone'] = $novo_telefone;
        $_SESSION['sexo'] = $novo_sexo;
        $_SESSION['nome_lider'] = $novo_nome_lider;
        $_SESSION['contato_lider'] = $novo_contato_lider;
        $_SESSION['dia_escola'] = $novo_dia_escola;

        echo "<script>alert('Dados atualizados com sucesso!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar os dados. Tente novamente.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Inscrição</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <h1>Editar Inscrição</h1>
        <form method="POST">
            <label for="nome_completo">Nome Completo:</label>
            <input type="text" name="nome_completo" id="nome_completo" value="<?php echo htmlspecialchars($nome_completo); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <label for="telefone">Telefone:</label>
            <input type="text" name="telefone" id="telefone" value="<?php echo htmlspecialchars($telefone); ?>" required>

            <label for="sexo">Sexo:</label>
            <select name="sexo" id="sexo" required>
                <option value="Masculino" <?php echo ($sexo == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                <option value="Feminino" <?php echo ($sexo == 'Feminino') ? 'selected' : ''; ?>>Feminino</option>
                <option value="Outro" <?php echo ($sexo == 'Outro') ? 'selected' : ''; ?>>Outro</option>
            </select>

            <label for="nome_lider">Nome do Líder:</label>
            <input type="text" name="nome_lider" id="nome_lider" value="<?php echo htmlspecialchars($nome_lider); ?>" required>

            <label for="contato_lider">Contato do Líder:</label>
            <input type="text" name="contato_lider" id="contato_lider" value="<?php echo htmlspecialchars($contato_lider); ?>" required>

            <label for="dia_escola">Dia da Escola:</label>
            <select name="dia_escola" id="dia_escola" required>
                <option value="Domingo" <?php echo ($dia_escola == 'Domingo') ? 'selected' : ''; ?>>Domingo</option>
                <option value="Quinta" <?php echo ($dia_escola == 'Quinta') ? 'selected' : ''; ?>>Quinta</option>
            </select>

            <button type="submit">Atualizar Dados</button>
        </form>
    </div>
</body>
</html>
