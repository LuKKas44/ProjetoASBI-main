📘 Projeto — Associação da Saúde Bucal Infantil
Este projeto foi criado com o objetivo de atender crianças em situação de vulnerabilidade, proporcionando acompanhamento de saúde bucal através de uma plataforma simples e eficiente.

O sistema foi desenvolvido em PHP e JavaScript.

Dentistas voluntários podem se cadastrar no site, liberar horários e atender pacientes inscritos. Os associados (pacientes) podem visualizar horários disponíveis, realizar agendamentos e acompanhar seu histórico de atendimentos.

🚀 Funcionalidades do Sistema
👨‍⚕️ Para Dentistas

Cadastro e login como Dentista

Carteirinha com informações pessoais

Liberação de horários disponíveis

Finalização de consultas com:

⁻¹

Observações

Relatórios com Chart.js

Lista de pacientes

Pesquisa por nome e procedimento para continuidade do tratamento

👤 Para Associados (Pacientes)

Cadastro e login como Associado

Carteirinha personalizada

Histórico completo de atendimentos

Visualização dos horários liberados pelos dentistas

Realização de agendamentos

🛠️ Requisitos
Certifique-se de ter instalado:

PHP >= 8.0

Servidor Web (Apache ou Nginx)

MySQL

⚠️IMPORTANTE:
O projeto foi desenvolvido utilizando a porta 3316 do MySQL (XAMPP). Altere a porta do seu MySQL para 3316 para evitar erros de conexão.

🗄️ Banco de Dados
Crie um banco MySQL chamado: clinica_1

Importe o arquivo localizado na raiz do projeto:

database.sql

Esse arquivo contém apenas a estrutura das tabelas.

⚠️Ajustes Necessários
Devido ao contratempo, duas conexões ficaram fora da padronização. Você precisa criar manualmente os seguintes arquivos:

📌conexao.php (na raiz do projeto)

Responsável pela conexão principal com o banco.

📌 db.php (na pasta /agendamento)

Responsável pela conexão usada no sistema de agendamentos.

Exemplos de ambos os arquivos estão incluídos no repositório, para facilitar a criação.

🧭 Como Navegar pelo Sistema
👨‍⚕️ Como usar como Dentista

Cadastre-se como Dentista no menu "Cadastro".

Faça login como Dentista.

Acesse sua Carteirinha.

No menu lateral, vá até Agenda.

Horários livres disponíveis.

Aguarde os associados agendarem.

Após cada consulta, finalize registrando:

⁻¹

Observações

👤 Como usar como Associado

Cadastre-se como Associado.

Faça login.

Acesse sua Carteirinha.

Acesse o menu Agendamentos.

Veja os horários disponíveis.

Escolha um horário e confirme o agendamento.

Consulte seu Histórico de Atendimentos na Carteirinha.
