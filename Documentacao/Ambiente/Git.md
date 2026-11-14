# Git

| Redator   | Data de criação | Status   | Data do Status |
| --------- | --------------- | -------- | -------------- |
| Allan Max | 18/10/2026      | Revisado | 12/11/2026 |

## Objetivo

Este documento define as pastas e os arquivos que o Git acrescenta ao projeto, sua escrita e as regras das mensagens de commit, do gancho de commit e da autoria das atividades. O repositório remoto, as branches oficiais, os pull requests e as issues estão em [GitHub](GitHub.md).

## Pastas e arquivos no projeto

O Git é o sistema de versionamento adotado. É um programa do ambiente: não é incluído no projeto e não exige ficha nem justificativa, conforme [Recursos de terceiros](../00Regras.md#recursos-de-terceiros). As pastas e os arquivos abaixo existem enquanto o Git for adotado. Os nomes e a localização na raiz de `.git`, `.gitignore` e `.gitattributes` são exigidos por ele; a pasta `.githooks` é a indicada em `core.hooksPath`, conforme [Gancho de commit](#gancho-de-commit).

| Caminho | Situação | Finalidade |
| --- | --- | --- |
| `.git` | Diretório local criado pelo Git, não versionado | Histórico e configuração local do repositório |
| `.gitignore` | Arquivo existente | Exclusões do versionamento |
| `.gitattributes` | Arquivo existente | Normalização de texto e tratamento dos arquivos binários |
| `.githooks` | Diretório existente | Ganchos versionados do Git e os scripts que eles executam, conforme [Gancho de commit](#gancho-de-commit) |
| `.githooks/pre-commit` | Arquivo existente | Gancho executado pelo Git antes de cada commit |
| `.githooks/ConferenciaDocumentosNumerados.php` | Arquivo existente | Conferência dos documentos numerados, executada pelo gancho, pela automação e localmente |

## Escrita

- `.git`, `.gitignore` e `.gitattributes` preservam a grafia exigida pelo Git, conforme [Grafias externas](../01Padroes.md#grafias-externas).
- `.githooks` e `pre-commit` preservam seus nomes de integração com o Git. O gancho usa PHP CLI disponível em `PATH`; o apoio usa a fórmula `[Finalidade].php` e os comentários seguem [Documentação nos arquivos com comentários](../01Padroes.md#documentação-nos-arquivos-com-comentários).
- `.gitignore` e `.gitattributes` começam com o comentário de apresentação em linhas iniciadas por `#`, com os campos de [Documentação nos arquivos com comentários](../01Padroes.md#documentação-nos-arquivos-com-comentários). Cada grupo de regras é precedido por um comentário que identifica seu assunto.
- No `.gitignore`, as exclusões DEVEM acompanhar os caminhos reais dos dados de execução, das credenciais e das configurações privadas, conforme [Dados privados e armazenamentos](../00Regras.md#dados-privados-e-armazenamentos). Em pastas de conteúdo ignorado, uma regra de negação iniciada por `!` DEVE preservar o `README.md`, conforme [Pastas sem conteúdo](../00Regras.md#pastas-sem-conteúdo); a mesma forma preserva os modelos `Exemplo.env`.
- No `.gitattributes`, o texto é normalizado com fim de linha LF, e cada extensão binária versionada no projeto DEVE estar declarada como `binary`.

## Termos

| Termo | Significado |
| --- | --- |
| Commit | Versão do conteúdo guardada no histórico do Git, com identificador único e mensagem que descreve o trabalho realizado |
| Branch | Linha de desenvolvimento do repositório; as branches oficiais estão em [Branches do projeto](GitHub.md#branches-do-projeto) |
| Merge | Incorporação dos commits de uma branch em outra |

## Mensagens de commit

A mensagem de cada commit DEVE começar com um título descritivo do trabalho realizado, com no máximo 72 caracteres, no imperativo afirmativo da segunda pessoa do singular (`tu`), sem incluir o pronome no título.

Quando a mensagem for preparada em arquivo, salvá-la em UTF-8 sem BOM, para que o título não comece com um caractere invisível.

Usar as formas abaixo. A mesma conjugação se aplica aos demais verbos:

| Ação | Forma usada no título | Forma a evitar |
| --- | --- | --- |
| Adicionar | `Adiciona` | `Adicione` |
| Corrigir | `Corrige` | `Corrija` |
| Atualizar | `Atualiza` | `Atualize` |
| Esclarecer | `Esclarece` | `Esclareça` |

Uma linha em branco separa o título do corpo da mensagem. O corpo reúne as informações pertinentes às mudanças, com todos os detalhes exigidos pelas regras aplicáveis ao conteúdo do commit, e apresenta os campos abaixo:

| Campo | Conteúdo |
| --- | --- |
| Alterações | Arquivos, componentes ou documentos afetados e descrição do que foi modificado |
| Estado | O que está concluído e o que permanece em escrita, correção ou revisão |
| Verificações | Verificações executadas e seus resultados; verificações ainda não executadas e falhas ou limitações encontradas |

Quando nenhuma verificação tiver sido executada, o campo `Verificações` informa essa condição.

O formato obrigatório é `[Título no imperativo]`, linha em branco, `Alterações: [descrição]`, `Estado: [situação]` e `Verificações: [evidências]`. Os dois blocos abaixo são exemplos hipotéticos de escrita; não registram commits ou resultados reais.

Exemplo hipotético de escrita de uma correção documental:

```text
Corrige a referência da documentação de arquivos

Alterações: corrigido o destino de uma referência documental.
Estado: texto corrigido; revisão pendente.
Verificações: nenhuma verificação executada.
```

Exemplo hipotético de escrita de uma atualização de ferramenta:

```text
Atualiza o registro da ferramenta de análise

Alterações: atualizados os campos de identificação da ferramenta em seu documento.
Estado: registro preparado; conferência da instalação pendente.
Verificações: versão, licença, integridade e execução ainda não conferidas.
```

As condições de envio estão em [Commits e movimentação entre branches](GitHub.md#commits-e-movimentação-entre-branches).

## Gancho de commit

O gancho `.githooks/pre-commit` aplica localmente o [Bloqueio dos documentos numerados](../00Regras.md#bloqueio-dos-documentos-numerados) a qualquer pessoa ou agente que faça commits na cópia. Toda cópia de trabalho do projeto DEVE ativá-lo uma vez, a partir da raiz:

```text
git config core.hooksPath .githooks
```

O gancho recusa o commit quando as alterações preparadas alteram, acrescentam ou removem um documento numerado ou alteram as linhas do registro de [Conferência dos documentos numerados](GitHub.md#conferência-dos-documentos-numerados), salvo com a autorização do Gerente do projeto, identificado em [Gerente do projeto](../../README.md#gerente-do-projeto). Em seguida, confere o conteúdo preparado com o registro pelo script `.githooks/ConferenciaDocumentosNumerados.php` e recusa o commit em qualquer divergência.

A variável de ambiente `DOCUMENTOS_NUMERADOS_AUTORIZADOS`, com valor `sim`, registra a autorização do Gerente do projeto exclusivamente no processo do commit autorizado. A forma de defini-la pertence ao ambiente utilizado. A autorização não dispensa a correspondência dos documentos com o registro.

É PROIBIDO a agentes de IA definir essa variável, usar `--no-verify`, alterar ou desativar `core.hooksPath` e alterar os arquivos de `.githooks` sem pedido expresso do Gerente do projeto.

## Autoria das atividades

As atividades realizadas com assistência de um agente de IA DEVEM ser atribuídas somente ao operador humano responsável. É PROIBIDO ao agente registrar seu próprio nome, o nome do modelo ou o nome da ferramenta como autor, coautor ou responsável no Git local ou em qualquer registro de atividade destinado ao repositório remoto.

Essa regra abrange os campos de autor e de responsável pelo commit (`author` e `committer`), mensagens de commit, cabeçalhos de documentos, pull requests, issues, comentários e demais registros da atividade. É PROIBIDO incluir assinaturas de agentes, créditos de geração por IA ou atribuições como `Co-authored-by` e `Signed-off-by` em nome de agentes, modelos ou ferramentas.

Usar somente a identidade confirmada do operador humano, incluindo seu nome e, quando exigido pelo Git, seu e-mail. Enquanto essa identidade não estiver confirmada, solicitar os dados necessários antes de registrar a atividade; é PROIBIDO inventar uma identidade ou substituí-la pela do agente. Preservar os autores e as contribuições já conhecidos no histórico, conforme [Manutenção dos documentos](../00Regras.md#manutenção-dos-documentos).
