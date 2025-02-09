<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscrição</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        function validarEmail(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        }

        function validarTelefone(telefone) {
            return !isNaN(telefone.replace(/\D/g, '')) && telefone.length >= 10;
        }

        function validarFormulario(event) {
            const telefone = document.getElementById("telefone").value;
            const email = document.getElementById("email").value;
            const nome_completo = document.getElementById("nome_completo").value;
            const contato_lider = document.getElementById("contato_lider").value;
            const geracao = document.getElementById("geracao").value;

            if (!validarTelefone(telefone)) {
                alert("O campo 'Telefone' deve conter um número válido.");
                event.preventDefault();
                return false;
            }

            if (contato_lider && !validarTelefone(contato_lider)) {
                alert("O campo 'Contato do Líder' deve conter um número válido.");
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

            if (geracao === "") {
                alert("Por favor, selecione uma geração válida.");
                event.preventDefault();
                return false;
            }

            return true;
        }

        $(document).ready(function() {
            $('#telefone, #contato_lider').mask('(00) 00000-0000');
        });
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
            <input type="text" id="nome_completo" name="nome_completo" required placeholder="Digite seu nome completo">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="exemplo@dominio.com">

            <label for="telefone">Telefone:</label>
            <input type="tel" id="telefone" name="telefone" required placeholder="(99) 99999-9999">

            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo" required>
                <option value="" disabled selected>Selecione o sexo</option>
                <option value="Masculino">Masculino</option>
                <option value="Feminino">Feminino</option>
            </select>

            <label for="geracao">Geração:</label>
            <select id="geracao" name="geracao" required>
                <option value="" disabled selected>Selecione uma geração</option>
                <?php
                require 'conexao.php';
                $sql = "SELECT id, nome FROM geracao";
                $result = $conn->query($sql);
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='".htmlspecialchars($row['id'])."'>".htmlspecialchars($row['nome'])."</option>";
                    }
                } else {
                    echo "<option disabled>Erro ao carregar gerações</option>";
                }
                $conn->close();
                ?>
            </select>

            <label for="nome_lider">Nome do Líder:</label>
            <input type="text" id="nome_lider" name="nome_lider" placeholder="Nome do líder">

            <label for="contato_lider">Contato do Líder:</label>
            <input type="tel" id="contato_lider" name="contato_lider" placeholder="(99) 99999-9999">

            <label for="dia_escola">Dia da Escola:</label>
            <select id="dia_escola" name="dia_escola" required>
                <option value="" disabled selected>Selecione o dia</option>
                <option value="Domingo">Domingo</option>
                <option value="Quinta">Quinta</option>
            </select>

            <button type="submit">Enviar</button>
        </form>
    </div>
</body>
</html>
