# Orientação para agentes de IA

| Redator   | Data de criação | Status   | Data do Status |
| --------- | --------------- | -------- | -------------- |
| Allan Max | 12/10/2026      | Revisado | 12/11/2026     |

## Objetivo

Este arquivo orienta os agentes de IA que trabalham no projeto e funciona como índice. As definições estão nos documentos de `Documentacao`, que prevalecem em caso de conflito.

## Antes de começar

1. Ler [README.md](README.md) da raiz, para conhecer o projeto, e [Estado atual](Documentacao/Memorial.md#estado-atual), no Memorial, para conhecer a implementação e as pendências.
2. Ler [Em resumo](Documentacao/00Regras.md#em-resumo) de Regras.
3. Ler o documento do assunto da tarefa, conforme [Onde documentar cada assunto](Documentacao/00Regras.md#onde-documentar-cada-assunto).

## Regras de cumprimento obrigatório

- Escrever todo conteúdo próprio em português do Brasil: [Idioma do projeto](Documentacao/00Regras.md#idioma-do-projeto).
- Cumprir as cinco leis ao escrever ou atualizar qualquer documentação: [Leis de escrita](Documentacao/01Padroes.md#leis-de-escrita).
- Nunca alterar os documentos numerados de `Documentacao` nem seu registro de conferência sem pedido expresso do Gerente do projeto, identificado em [Gerente do projeto](README.md#gerente-do-projeto); comunicar dúvidas e divergências sem editar o documento: [Bloqueio dos documentos numerados](Documentacao/00Regras.md#bloqueio-dos-documentos-numerados).
- Nunca contornar o gancho de commit: sem `--no-verify`, sem a variável de autorização e sem alterar `core.hooksPath` ou `.githooks`: [Gancho de commit](Documentacao/Ambiente/Git.md#gancho-de-commit).
- Escrever os testes antes do código: [Testes antes da implementação](Documentacao/00Regras.md#testes-antes-da-implementação).
- Manter cobertura integral de linhas, ramos e cenários do código próprio de produção: [Testes e cobertura integral](Documentacao/00Regras.md#testes-e-cobertura-integral).
- Não versionar segredos, credenciais nem dados reais: [Cuidados de manutenção](Documentacao/00Regras.md#cuidados-de-manutenção).
- Nunca registrar o nome do agente de IA como autor, coautor ou responsável pelas atividades no Git local ou no repositório remoto; atribuir essas atividades somente ao operador humano: [Autoria das atividades](Documentacao/Ambiente/Git.md#autoria-das-atividades).
- Seguir o fluxo `Desenvolvimento_[Responsavel]` → `Validacao` → `Principal` por pull request: [Branches do projeto](Documentacao/Ambiente/GitHub.md#branches-do-projeto).
- Deixar a marcação `Revisado` para quem conclui a revisão, e preservar a `Data do Status` enquanto o status não muda: [Revisão da documentação](Documentacao/00Regras.md#revisão-da-documentação).
- Usar o README de pasta apenas para apresentar sua destinação e manter a pasta no Git, respeitando as condições de remoção e restauração: [Pastas sem conteúdo](Documentacao/00Regras.md#pastas-sem-conteúdo).
- Não incluir recursos de terceiros sem justificativa registrada e licença compatível: [Recursos de terceiros](Documentacao/00Regras.md#recursos-de-terceiros).
- Colocar no Núcleo apenas o que é comum a todas as áreas a que se destina: [Nucleo](Documentacao/02Estrutura.md#nucleo).
- Não fazer uma alteração em uma área obrigar a reescrever outra área ou o Núcleo: [Independência entre áreas](Documentacao/02Estrutura.md#independência-entre-áreas).
- Não declarar verificado o que não foi verificado, nem inventar responsáveis, datas ou resultados: [Verificação de uma alteração](Documentacao/00Regras.md#verificação-de-uma-alteração) e [Manutenção dos documentos](Documentacao/00Regras.md#manutenção-dos-documentos).
- Escrever as mensagens de commit no formato definido: [Mensagens de commit](Documentacao/Ambiente/Git.md#mensagens-de-commit).

## Leis de escrita da documentação

Ao escrever ou atualizar qualquer documento ou comentário de documentação, atuar como o arquiteto de software do projeto e cumprir as [Leis de escrita](Documentacao/01Padroes.md#leis-de-escrita), que contêm a definição completa:

1. **Voz própria e definitiva:** presente do indicativo, projeto tratado como sistema único e próprio; nunca como base, modelo ou esqueleto reutilizável.
2. **Escopo estrutural:** documentos gerais tratam somente de pastas, responsabilidades, contratos entre divisões e padrões; regras de negócio, tabelas, endpoints e implementações ficam nos detalhamentos, depois de implementados.
3. **Fórmula antes do exemplo:** a fórmula entre colchetes antecede todo nome ilustrativo, rotulado como `exemplo hipotético de escrita`, como em `[Finalidade].php` (exemplo hipotético de escrita: `LeituraArquivo.php`); o fixo, o criado sob demanda e o hipotético ficam sempre distinguidos, e a situação física variável fica em [Estado atual](Documentacao/Memorial.md#estado-atual), nos documentos de ferramentas, nos documentos de ferramentas de ambiente e nas fichas.
4. **Força normativa determinística:** `DEVE`, `É OBRIGATÓRIO`, `RESIDE EXCLUSIVAMENTE EM` e `É PROIBIDO` para regras; `OPCIONAL` e `PERMITIDO` só para liberdade real; nunca linguagem de sugestão.
5. **Fronteiras excludentes e coerência física:** cada pasta declara o que entra e o que não entra; pastas fixas correspondem exatamente à árvore real e a uma única fonte de definição.

## Onde fica cada coisa

| Conteúdo | Onde está definido |
| --- | --- |
| Mapa de pastas e situação de cada uma | [Mapa geral](Documentacao/02Estrutura.md#mapa-geral) |
| Áreas da aplicação | [Áreas](Documentacao/02Estrutura.md#áreas) |
| Divisões dentro das áreas | [Divisões de responsabilidade](Documentacao/02Estrutura.md#divisões-de-responsabilidade) |
| Nomes de pastas, arquivos e identificadores | [Formato por elemento](Documentacao/01Padroes.md#formato-por-elemento) |
| Nomes e estrutura dos documentos | [Nomes dos documentos](Documentacao/01Padroes.md#nomes-dos-documentos) e [Estrutura dos documentos](Documentacao/01Padroes.md#estrutura-dos-documentos) |
| Scripts de banco | [Esquemas e cargas](Documentacao/02Estrutura.md#esquemas-e-cargas) |
| Arquivos públicos e ativos | [Publico](Documentacao/02Estrutura.md#publico) |
| Configuração e ambientes | [Configurações](Documentacao/03Configuracoes.md) |
| Ferramentas de verificação | [Funções exigidas](Documentacao/04Ferramentas.md#funções-exigidas) |
| Ferramentas de ambiente, branches, commits, pull requests e issues | [Ferramentas de ambiente](Documentacao/02Estrutura.md#ferramentas-de-ambiente) |
| Recursos de terceiros | [Externos](Documentacao/05Externos.md) |

## Conferência antes do commit

1. Conferir links e âncoras dos documentos alterados.
2. Conferir cabeçalhos, status e datas conforme [Revisão da documentação](Documentacao/00Regras.md#revisão-da-documentação).
3. Executar as verificações da [Verificação de uma alteração](Documentacao/00Regras.md#verificação-de-uma-alteração) aplicáveis ao conteúdo alterado.
4. Executar `php .githooks/ConferenciaDocumentosNumerados.php` e conferir que `git config core.hooksPath` retorna `.githooks`: [Conferência dos documentos numerados](Documentacao/Ambiente/GitHub.md#conferência-dos-documentos-numerados) e [Gancho de commit](Documentacao/Ambiente/Git.md#gancho-de-commit).
5. Conferir espaços em branco e fins de linha com `git diff --check`.
6. Escrever a mensagem conforme [Mensagens de commit](Documentacao/Ambiente/Git.md#mensagens-de-commit).

## Ferramentas

Os comandos de cada ferramenta ficam em seu documento de uso, em `Documentacao/Ferramentas`.

## Outros arquivos de instrução

Ao acrescentar configuração de uma ferramenta de agente que leia outro arquivo de instruções, apontar para este por importação ou referência, sem duplicar seu conteúdo, conforme [Definições](Documentacao/00Regras.md#definições). A existência deste índice não comprova a configuração de outras ferramentas.
