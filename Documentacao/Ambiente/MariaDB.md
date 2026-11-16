# MariaDB

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 16/11/2026      | Sem revisão | 16/11/2026     |

## Objetivo

Identificar o gerenciador adotado, os destinos de seus scripts e a compatibilidade do ambiente. Os dados e os fluxos atendidos estão em [Jogo](../Aplicacao/Site/Jogo.md).

## Pastas e arquivos no projeto

O MariaDB é serviço do ambiente e não é incluído na aplicação. A versão configurada no Wamp e utilizada nas verificações locais é 10.6.28.

| Caminho | Situação | Finalidade |
| --- | --- | --- |
| `Dados/Esquemas/MariaDB` | Diretório existente | Instalação e evolução estrutural para MariaDB |
| `Dados/Esquemas/MariaDB/I.2026.11.15.0001.sql` | Arquivo existente | Instalação inicial do esquema |
| `Dados/Cargas/MariaDB` | Diretório existente | Carga e manutenção de dados para MariaDB |
| `Dados/Cargas/MariaDB/I.2026.11.15.0001.sql` | Arquivo existente | Carga inicial do acervo e da configuração |
| `Dados/Esquemas/MariaDB/M.2026.11.16.0001.sql` | Arquivo existente | Categorias e arquivamento de perguntas |
| `Dados/Esquemas/MariaDB/M.2026.11.16.0002.sql` | Arquivo existente | Identidades, clientes, códigos e tokens MCP |
| `Configuracoes/Aplicacao/Exemplo.env` | Arquivo existente | Modelo dos parâmetros reconhecidos |
| `Configuracoes/Aplicacao/.env` | Destino privado e não versionado | Valores da instalação |

## Escrita

Scripts seguem as fórmulas de [Nomes dos scripts](../01Padroes.md#nomes-dos-scripts-de-banco-de-dados). Cada arquivo começa com comentários SQL de finalidade, redator, criação, status e data do status. Tabelas, colunas e índices seguem [Formato por elemento](../01Padroes.md#formato-por-elemento).

As pastas fixas de SQLite permanecem preservadas; não participam da execução do jogo.

## Compatibilidade e conexão

A aplicação usa o protocolo MySQL pelo driver PDO `pdo_mysql`, com InnoDB e `utf8mb4_unicode_ci`. A implementação usa recursos disponíveis no MariaDB 10.6.28, incluindo funções de janela e validação JSON. O driver não troca o gerenciador adotado por MySQL.

Não há requisito de criação de usuários, alteração de configuração global do servidor, eventos agendados ou processos permanentes. O banco e o usuário são preparados pelo painel da hospedagem. A conexão está documentada em [Inicialização](../Aplicacao/Nucleo/Inicializacao.md#conexão-e-falhas).

A [imagem oficial do MariaDB](https://hub.docker.com/_/mariadb/tags?name=10.6) fornece a versão fixada para o serviço descartável do GitHub. Esse serviço não é requisito de implantação da aplicação.

## Instalação e recuperação

Importar primeiro o esquema e depois a carga, uma única vez, em banco vazio previamente criado. Os scripts não executam `CREATE DATABASE`, não incluem credenciais e não entram pela interface pública. A carga contém somente o acervo fornecido e os parâmetros padrão, sem jogadores fictícios.

DDL do MariaDB tem confirmações implícitas; uma falha estrutural não garante reversão automática de todos os comandos anteriores. Em falha de instalação, conferir o estado do banco e recuperar a instalação vazia ou seu backup antes de repetir. Em falha da carga transacional, executar `ROLLBACK` e conferir os registros antes de repetir.

Não reaplicar arquivos iniciais em uma instalação em uso. Atualizações estruturais ou de dados recebem novos scripts de manutenção e recuperação, conforme [Cuidados de manutenção](../00Regras.md#cuidados-de-manutenção).

O procedimento de backup, a permissão de restauração e sua retenção DEVEM ser definidos pela administração do ambiente. A aplicação não altera essas configurações do serviço.
