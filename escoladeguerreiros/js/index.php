<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscrição</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        // Verifica se o email é válido
        function validarEmail(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        }

        // Valida os campos antes de enviar o formulário
        function validarFormulario(event) {
            const telefone = document.getElementById("telefone").value;
            const email = document.getElementById("email").value;
            const nome_completo = document.getElementById("nome_completo").value;

            if (isNaN(telefone) || telefone.length < 10) {
                alert("O campo Telefone deve conter apenas números e ter no mínimo 10 dígitos.");
                event.preventDefault();
                return false;
            }

            if (!validarEmail(email)) {
                alert("Por favor, insira um email válido.");
                event.preventDefault();
                return false;
            }

            if (nome_completo.trim() === "") {
                alert("O campo 'Nome Completo' não pode estar vazio.");
                event.preventDefault();
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <div class="form-container">
        <div class="container">
            <img src="assets/guerreiros.png" alt="Imagem centralizada" width="100%">
        </div>

        <h1>Formulário de Inscrição</h1>
        <form action="processa_inscricao.php" method="POST" onsubmit="return validarFormulario(event)">
            <label for="nome_completo">Nome Completo:</label>
            <input type="text" id="nome_completo" name="nome_completo" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone" required>

            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo" required>
                <option value="Masculino">Masculino</option>
                <option value="Feminino">Feminino</option>
            </select>

            <label for="geracao">Geração:</label>
            <select id="geracao" name="geracao" required>
                <?php
                // Conexão com o banco de dados
                require 'conexao.php';
                $sql = "SELECT id, nome FROM geracao";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                }
                $conn->close();
                ?>
            </select>

            <label for="nome_lider">Nome do Líder:</label>
            <input type="text" id="nome_lider" name="nome_lider">

            <label for="contato_lider">Contato do Líder:</label>
            <input type="text" id="contato_lider" name="contato_lider">

            <label for="dia_escola">Dia da Escola:</label>
            <select id="dia_escola" name="dia_escola" required>
                <option value="Domingo">Domingo</option>
                <option value="Quinta">Quinta</option>
            </select>

            <button type="submit">Enviar</button>
        </form>
    </div>
</body>
</html>
