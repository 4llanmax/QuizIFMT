# SQLite

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 18/10/2026      | Revisado    | 18/10/2026     |

## Objetivo

Este documento define as pastas e os arquivos que o SQLite, gerenciador de banco padrão do projeto, acrescenta ao projeto e sua escrita. As regras gerais dos scripts de banco estão em [Esquemas e cargas](../02Estrutura.md#esquemas-e-cargas) e [Cuidados de manutenção](../00Regras.md#cuidados-de-manutenção).

## Pastas e arquivos no projeto

O SQLite é um programa do ambiente, acessado pelo PHP por meio da extensão `pdo_sqlite`. Não é incluído no projeto e não exige ficha nem justificativa, conforme [Recursos de terceiros](../00Regras.md#recursos-de-terceiros). Não há banco, esquema ou conexão implementados.

| Caminho | Situação | Finalidade |
| --- | --- | --- |
| `Dados/Esquemas/SQLite` | Diretório fixo existente, com apenas seu README | Scripts de criação e alteração da estrutura do banco SQLite |
| `Dados/Cargas/SQLite` | Diretório fixo existente, com apenas seu README | Scripts de carga inicial, manutenção exclusivamente de dados e carga de teste para SQLite |
| `Dados/Arquivos/BancoDados` | Diretório fixo existente, com apenas seu README | Destino comum dos bancos em arquivo; recebe o arquivo do banco SQLite e seus auxiliares de execução, ignorados pelo Git |

As três pastas são fixas e definidas em [Mapa geral](../02Estrutura.md#mapa-geral); a permanência das subpastas `SQLite` com a adoção de outro gerenciador segue [Esquemas e cargas](../02Estrutura.md#esquemas-e-cargas). Sem scripts ou bancos, mantêm seus READMEs, conforme [Pastas sem conteúdo](../00Regras.md#pastas-sem-conteúdo).

## Escrita

- Os scripts seguem [Nomes dos scripts de banco de dados](../01Padroes.md#nomes-dos-scripts-de-banco-de-dados), com a extensão `sql`.
- Os comentários de documentação dos scripts usam linhas iniciadas por `--` ou blocos `/* ... */`, com o comentário de apresentação de [Documentação nos arquivos com comentários](../01Padroes.md#documentação-nos-arquivos-com-comentários).
- Tabelas, colunas e índices seguem o formato de banco de [Formato por elemento](../01Padroes.md#formato-por-elemento).
- O arquivo de banco DEVE usar a fórmula `Dados/Arquivos/BancoDados/[Finalidade].sqlite` (exemplo hipotético de escrita: `Dados/Arquivos/BancoDados/Principal.sqlite`); o caminho é informado pelo parâmetro de configuração do banco, conforme [Banco e armazenamento](../03Configuracoes.md#banco-e-armazenamento). A extensão `sqlite` está excluída do versionamento pelo `.gitignore`, descrito em [Git](Git.md). Os arquivos auxiliares criados pelo SQLite durante a execução conservam os nomes gerados por ele.
