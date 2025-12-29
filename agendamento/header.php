<?php $base = "/projeto_ASBI-main"; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style2.css">
    <title>Saúde Bucal Infantil</title>

</head>

<body>
    <nav>
        <div class="nav-logo">
            <a href="<?= $base ?>/index.php"><img src="<?= $base ?>/img/LOGOASBI.png" alt="Logo ASBI"
                    height="120" /></a>
        </div>
        <div class="nav-center">
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="#">Sobre</a></li>
                <li><a href="#">Serviços Sociais</a></li>
                <li><a href="mailto:associacaoasbi@gmail.com">Contato</a></li>
                <div class="user-actions">
                </div>
            </ul>
        </div>
        <div class="nav-login">
            <?php $base = "/projeto_ASBI-main";?>
            <?php if (isset($_SESSION['id'])): ?>
            <?php
                    $dashboard_link = ($_SESSION['tipo'] == 'medico') ? "$base/painel_medico/painel_medico.php" : "$base/painel_cliente/painel_cliente.php";
                ?>
            <a href="<?php echo $dashboard_link; ?>" style="background: #7be141; margin-right: 10px;">Meu Painel</a>
            <a href="<?= $base ?>/cadastro_e_login/logout.php">Logout</a>
            <?php else: ?>
            <a href="<?= $base ?>/cadastro_e_login/login.php">Login</a>

            <?php endif; ?>

        </div>
        <div class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>

    </nav>
    <script>
    const burger = document.getElementById("hamburger");
    const menu = document.querySelector(".nav-links");
    const loginBox = document.querySelector(".nav-login");

    burger.addEventListener("click", () => {
        burger.classList.toggle("active");
        menu.classList.toggle("open");
        loginBox.classList.toggle("open");
    });
    </script>
</body>

</html>