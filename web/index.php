<?php
// Conectar ao MySQL
$servername = getenv("DB_ADDR");
$username = getenv("DB_USER");
$password = getenv("DB_PASSWORD");
$dbname = "animaisdb";

// Criar a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Manipulação de formulários
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        // Adicionar novo animal
        $nome = $_POST['nome'];
        $especie = $_POST['especie'];
        $idade = $_POST['idade'];
        $peso = $_POST['peso'];
        $descricao = $_POST['descricao'];
        
        $sql = "INSERT INTO animais (nome, especie, idade, peso, descricao) VALUES ('$nome', '$especie', $idade, $peso, '$descricao')";
        $conn->query($sql);
        
        // Redirecionar para evitar reenvio do formulário
        header("Location: index.php");
        exit();
    } elseif (isset($_POST['edit'])) {
        // Editar animal existente
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $especie = $_POST['especie'];
        $idade = $_POST['idade'];
        $peso = $_POST['peso'];
        $descricao = $_POST['descricao'];
        
        $sql = "UPDATE animais SET nome='$nome', especie='$especie', idade=$idade, peso=$peso, descricao='$descricao' WHERE id=$id";
        $conn->query($sql);
        
        // Redirecionar para evitar reenvio do formulário
        header("Location: index.php");
        exit();
    } elseif (isset($_POST['delete'])) {
        // Apagar animal
        $id = $_POST['id'];
        
        $sql = "DELETE FROM animais WHERE id=$id";
        $conn->query($sql);
        
        // Redirecionar para evitar reenvio do formulário
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista de Animais</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script>
    $(document).ready(function() {
        $('#animaisTable').DataTable();

        // Editar modal
        $('.editBtn').on('click', function() {
            const id = $(this).data('id');
            const nome = $(this).data('nome');
            const especie = $(this).data('especie');
            const idade = $(this).data('idade');
            const peso = $(this).data('peso');
            const descricao = $(this).data('descricao');
            
            $('#editId').val(id);
            $('#editNome').val(nome);
            $('#editEspecie').val(especie);
            $('#editIdade').val(idade);
            $('#editPeso').val(peso);
            $('#editDescricao').val(descricao);
            
            $('#editModal').show();
        });

        // Apagar modal
        $('.deleteBtn').on('click', function() {
            const id = $(this).data('id');
            $('#deleteId').val(id);
            $('#deleteModal').show();
        });

        // Fechar modais
        $('.close').on('click', function() {
            $('.modal').hide();
        });
    });
    </script>
    <style>
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            padding-top: 100px; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Lista de Animais</h1>
    <button onclick="$('#addModal').show()">Adicionar Animal</button>
    <table id="animaisTable" class="display">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Espécie</th>
                <th>Idade</th>
                <th>Peso</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Obter os dados da tabela
            $sql = "SELECT * FROM animais";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Mostrar os dados de cada linha
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["id"]. "</td>
                            <td>" . $row["nome"]. "</td>
                            <td>" . $row["especie"]. "</td>
                            <td>" . $row["idade"]. "</td>
                            <td>" . $row["peso"]. "</td>
                            <td>" . $row["descricao"]. "</td>
                            <td>
                                <button class='editBtn' data-id='" . $row["id"] . "' data-nome='" . $row["nome"] . "' data-especie='" . $row["especie"] . "' data-idade='" . $row["idade"] . "' data-peso='" . $row["peso"] . "' data-descricao='" . $row["descricao"] . "'>Editar</button>
                                <button class='deleteBtn' data-id='" . $row["id"] . "'>Apagar</button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Nenhum animal encontrado</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Modal Adicionar -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Adicionar Animal</h2>
            <form method="post">
                <input type="hidden" name="add" value="1">
                <label for="nome">Nome:</label><br>
                <input type="text" id="nome" name="nome"><br>
                <label for="especie">Espécie:</label><br>
                <input type="text" id="especie" name="especie"><br>
                <label for="idade">Idade:</label><br>
                <input type="number" id="idade" name="idade"><br>
                <label for="peso">Peso:</label><br>
                <input type="number" step="0.01" id="peso" name="peso"><br>
                <label for="descricao">Descrição:</label><br>
                <textarea id="descricao" name="descricao"></textarea><br>
                <input type="submit" value="Adicionar">
            </form>
        </div>
    </div>

    <!-- Modal Editar -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Editar Animal</h2>
            <form method="post">
                <input type="hidden" name="edit" value="1">
                <input type="hidden" id="editId" name="id">
                <label for="editNome">Nome:</label><br>
                <input type="text" id="editNome" name="nome"><br>
                <label for="editEspecie">Espécie:</label><br>
                <input type="text" id="editEspecie" name="especie"><br>
                <label for="editIdade">Idade:</label><br>
                <input type="number" id="editIdade" name="idade"><br>
                <label for="editPeso">Peso:</label><br>
                <input type="number" step="0.01" id="editPeso" name="peso"><br>
                <label for="editDescricao">Descrição:</label><br>
                <textarea id="editDescricao" name="descricao"></textarea><br>
                <input type="submit" value="Editar">
            </form>
        </div>
    </div>

    <!-- Modal Apagar -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Apagar Animal</h2>
            <form method="post">
                <input type="hidden" name="delete" value="1">
                <input type="hidden" id="deleteId" name="id">
                <p>Tem certeza que deseja apagar este animal?</p>
                <input type="submit" value="Apagar">
            </form>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
