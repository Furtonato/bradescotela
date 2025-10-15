<?php
// login.php
session_name('ADMINSESS');
session_set_cookie_params([
    'lifetime' => 604800,      // 7 dias
    'path' => '/',      // Caminho do cookie
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

// Lógica de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // ATENÇÃO: Usar credenciais fixas não é seguro. O ideal é buscar de um banco de dados.
    if ($email === 'acesso@gmail.com' && $senha === '102030') {
        $_SESSION['admin'] = true;
        header('Location: dashboard.php'); // ou o nome do seu painel principal
        exit;
    } else {
        $erro = 'Credenciais inválidas. Tente novamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Painel Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* CSS para o Layout Moderno */
        :root {
            --background: #111827;
            --surface: #1F2937;
            --primary: #4F46E5;
            --primary-hover: #4338CA;
            --text-primary: #F9FAFB;
            --text-secondary: #9CA3AF;
            --border: #374151;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background);
            color: var(--text-primary);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }
        .login-form {
            background-color: var(--surface);
            padding: 2.5rem;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        .login-form h2 {
            text-align: center;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .login-form .form-subtitle {
            text-align: center;
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }
        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .input-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }
        .input-group input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 3rem;
            background-color: var(--background);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.3);
        }
        .login-form button {
            width: 100%;
            padding: 0.9rem;
            background-color: var(--primary);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .login-form button:hover {
            background-color: var(--primary-hover);
        }
        .error-message {
            background-color: rgba(239, 68, 68, 0.1);
            color: #F87171;
            padding: 0.75rem;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        /* --- CSS DO PUSH ATUALIZADO --- */
        .motivational-push {
            position: fixed;
            /* 1. Posiciona no topo e centro horizontal */
            top: 20px;
            left: 50%;

            /* 2. Estilos visuais mantidos */
            background-color: var(--surface);
            border: 1px solid var(--border);
            border-left: 4px solid var(--primary);
            border-radius: 8px;
            padding: 1rem;
            width: 100%;
            max-width: 380px; /* Um pouco mais largo para o centro */
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            display: flex;
            gap: 1rem;
            align-items: center;
            z-index: 1000;

            /* 3. Animação agora é na vertical (translateY) e começa fora da tela (-150%) */
            transform: translate(-50%, -150%);
            opacity: 0;
            transition: transform 0.6s cubic-bezier(0.25, 1, 0.5, 1), opacity 0.6s ease-out;
        }
        .motivational-push.show {
            /* 4. Estado final da animação: centralizado e visível */
            transform: translate(-50%, 0);
            opacity: 1;
        }
        /* --- FIM DA ATUALIZAÇÃO --- */

        .push-icon {
            font-size: 1.5rem;
        }
        .push-content {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .push-content strong {
            font-weight: 500;
            color: var(--text-primary);
        }
        .push-content span {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }
        .push-close {
            font-size: 1.5rem;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0 0.5rem;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-form">
            <h2>Login do Painel</h2>
            <p class="form-subtitle">Bem-vindo(a) de volta!</p>

            <?php if (isset($erro)): ?>
                <p class="error-message"><?= htmlspecialchars($erro); ?></p>
            <?php endif; ?>

            <form method="post" action="login.php">
                <div class="input-group">
                    <i class="bi bi-envelope"></i>
                    <input name="email" type="email" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <i class="bi bi-lock"></i>
                    <input name="senha" type="password" placeholder="Senha" required>
                </div>
                <button type="submit">Entrar</button>
            </form>
        </div>
    </div>

    <div id="motivational-push" class="motivational-push">
        <div class="push-icon">💡</div>
        <div class="push-content">
            <strong id="push-title">Frase do Dia</strong>
            <span id="push-message">Carregando...</span>
        </div>
        <div class="push-close" id="push-close">&times;</div>
    </div>

    <script>
        // JavaScript permanece o mesmo, ele apenas controla a classe ".show"
        document.addEventListener('DOMContentLoaded', () => {
            const phrases = [
                "O sucesso é a soma de pequenos esforços repetidos dia após dia.",
                "Acredite em você mesmo e tudo será possível.",
                "O único lugar onde o sucesso vem antes do trabalho é no dicionário.",
                "Não espere por oportunidades, crie-as.",
                "Sua maior fraqueza está em desistir. O caminho mais certo de vencer é tentar mais uma vez."
            ];

            const pushElement = document.getElementById('motivational-push');
            const messageElement = document.getElementById('push-message');
            const closeButton = document.getElementById('push-close');

            const randomPhrase = phrases[Math.floor(Math.random() * phrases.length)];
            messageElement.textContent = randomPhrase;

            setTimeout(() => {
                pushElement.classList.add('show');
            }, 500);

            setTimeout(() => {
                pushElement.classList.remove('show');
            }, 7000);

            closeButton.addEventListener('click', () => {
                pushElement.classList.remove('show');
            });
        });
    </script>

</body>
</html>