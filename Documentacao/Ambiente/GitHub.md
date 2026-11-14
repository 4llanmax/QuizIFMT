# GitHub

| Redator   | Data de criação | Status   | Data do Status |
| --------- | --------------- | -------- | -------------- |
| Allan Max | 18/10/2026      | Revisado | 13/11/2026     |

## Objetivo

Este documento define as pastas e os arquivos que o GitHub acrescenta ao projeto, sua escrita e as regras do repositório remoto: permissões, branches oficiais, pull requests, issues e automação. Os commits e a autoria das atividades estão em [Git](Git.md).

## Pastas e arquivos no projeto

O GitHub é a plataforma do repositório remoto adotado. Não é incluído no projeto e não exige ficha nem justificativa, conforme [Recursos de terceiros](../00Regras.md#recursos-de-terceiros). As pastas e os arquivos abaixo pertencem à plataforma, não integram a organização própria do projeto e existem somente enquanto o GitHub for o repositório remoto adotado.

| Caminho | Situação | Finalidade |
| --- | --- | --- |
| `.github` | Diretório existente | Configurações reconhecidas pelo GitHub |
| `.github/workflows` | Diretório existente | Fluxos do GitHub Actions |
| `.github/workflows/Validacao.yml` | Arquivo existente | Verificações automatizadas, conforme [Automação](#automação) |
| `.github/workflows/Reprovacao.yml` | Arquivo existente | Registro das reprovações após o término das verificações |
| `.github/Verificacoes` | Diretório existente | Apoio próprio às verificações da plataforma |
| `.github/Verificacoes/Alteracao.php` | Arquivo existente | Classificação do conteúdo e do destino da entrega e conferência documental |
| `.github/Verificacoes/Validacao.php` | Arquivo existente | Etapas PHP de integridade, preparação, execução das ferramentas e resultado final |
| `.github/Verificacoes/Reprovacao.php` | Arquivo existente | Registro da issue e encerramento do pull request reprovado |
| `Testes/Integracao/Automacao/ValidacaoTeste.php` | Arquivo existente | Cenários PHPUnit da classificação, da preparação e da decisão final do fluxo |
| `Testes/Integracao/Automacao/ReprovacaoTeste.php` | Arquivo existente | Cenários PHPUnit do registro e do encerramento de reprovações com a API substituída |

## Escrita

- `.github` e `workflows` preservam a grafia exigida pelo GitHub Actions, conforme [Grafias externas](../01Padroes.md#grafias-externas).
- Cada fluxo próprio em `.github/workflows` DEVE usar a fórmula `[Finalidade].yml`, conforme o formato de arquivo próprio de [Formato por elemento](../01Padroes.md#formato-por-elemento). `Validacao.yml` é o arquivo existente, não um exemplo hipotético.
- Os apoios de `.github/Verificacoes` usam a fórmula `[Finalidade].php`, conforme [Formato por elemento](../01Padroes.md#formato-por-elemento), com a documentação interna definida em [Documentação nos arquivos com comentários](../01Padroes.md#documentação-nos-arquivos-com-comentários). Os cenários da automação ficam em `Testes/Integracao/Automacao` e seguem os nomes, espaços de nomes e atributos definidos em [PHPUnit](../Ferramentas/PHPUnit.md).
- O fluxo começa com o comentário de apresentação em linhas iniciadas por `#`, com os campos de [Documentação nos arquivos com comentários](../01Padroes.md#documentação-nos-arquivos-com-comentários).
- Chaves exigidas pelo GitHub Actions usam a fórmula `[GrafiaOrigem]`, preservando a grafia da plataforma (exemplos presentes no fluxo existente: `on`, `jobs` e `steps`). Nomes de etapas e mensagens exibidas são textos para leitura em português do Brasil, conforme [Idioma do projeto](../00Regras.md#idioma-do-projeto). Variáveis de ambiente próprias no fluxo usam a fórmula `[FINALIDADE]` de [Formato por elemento](../01Padroes.md#formato-por-elemento); `ESTRUTURA_URL` é a variável existente declarada em `.github/Verificacoes/Validacao.php` para a preparação isolada do fluxo, sem consumidor implementado na aplicação.

## Termos

| Termo | Significado |
| --- | --- |
| Pull request | Pedido feito na plataforma do repositório remoto para fazer o merge de commits em uma branch |
| Issue | Registro na plataforma do repositório remoto para comunicar e acompanhar um problema ou uma proposta |

## Trabalho local e permissões no remoto

A versão reconhecida pelo projeto é a registrada no repositório remoto. As branches `Desenvolvimento_[Responsavel]` e `Validacao` recebem conteúdo nas etapas correspondentes; `Principal` mantém a versão aprovada para produção e as regras vigentes.

É PERMITIDO escrever, alterar e revisar código e documentos na cópia local antes da abertura de um pull request, respeitando as autorizações e os bloqueios aplicáveis aos arquivos. Os documentos numerados e seu registro continuam sujeitos a [Bloqueio dos documentos numerados](../00Regras.md#bloqueio-dos-documentos-numerados), inclusive na cópia local. Os estados de revisão documental e os resultados das verificações descrevem o conteúdo preparado para o merge.

É PERMITIDO a qualquer pessoa com acesso ao ambiente utilizado executar os testes e registrar localmente suas evidências e resultados. A validação de ambiente exige as verificações de [Branches do projeto](#branches-do-projeto) e o acesso descrito em [Ambiente de validação](../03Configuracoes.md#ambiente-de-validação). Ao apresentar o conteúdo, incluir os resultados locais no pull request correspondente. Os resultados descrevem o conteúdo testado e não concedem aprovação nem permissão de merge no remoto.

A incorporação de conteúdo a `Validacao` e `Principal` depende de um pull request aceito e com merge feito por quem tiver as permissões necessárias na plataforma. As branches de desenvolvimento recebem os commits de seu responsável conforme as permissões da plataforma. As permissões de abertura, aprovação e merge são administradas na plataforma; a edição local não concede essas permissões.

| Papel | Atuação |
| --- | --- |
| Autor | Prepara o conteúdo e os commits e apresenta as alterações no pull request |
| Revisor | Confere o conteúdo, registra o resultado e conclui a revisão documental, atualizando o estado de revisão correspondente |
| Responsável pela integração | Avalia o pull request, aceita ou recusa o conteúdo e faz o merge conforme suas permissões e os requisitos da branch de destino |

É PERMITIDO à mesma pessoa preparar, revisar e concluir a revisão do próprio trabalho, abrir o pull request, aprová-lo e fazer o merge, desde que suas permissões e as regras da plataforma permitam essas ações. Não há exigência de pessoas distintas para esses papéis.

A aceitação de ampliações e adaptações cabe ao responsável pela integração em `Principal`, conforme [Ampliações & Adaptações](../00Regras.md#ampliações--adaptações). Os papéis desta seção descrevem as atividades do fluxo; sua atribuição e as permissões de aprovação no remoto são definidas na plataforma, tanto no trabalho individual quanto em equipe. A coordenação de cada pull request inclui os autores e revisores do conteúdo afetado.

## Branches do projeto

O fluxo é `Desenvolvimento_[Responsavel]` → `Validacao` → `Principal`. Existem duas branches compartilhadas, `Validacao` e `Principal`, e uma branch de desenvolvimento por responsável; é PROIBIDO manter `Desenvolvimento` como branch compartilhada de elaboração.

A branch de desenvolvimento DEVE usar a fórmula `Desenvolvimento_[Responsavel]`: prefixo literal `Desenvolvimento`, um único sublinhado e o nome do operador humano responsável, sem espaços ou acentos, com inicial maiúscula em cada palavra. `Responsavel` segue [Composição dos nomes](../01Padroes.md#composição-dos-nomes). Exemplo hipotético de escrita: `Desenvolvimento_AllanMax`.

Cada responsável DEVE usar uma identificação única e estável no sufixo de sua branch, correspondente à pessoa registrada como autora das atividades; é PROIBIDO substituí-la pelo nome de um agente, modelo ou ferramenta. É PERMITIDA a coexistência de branches de desenvolvimento de responsáveis distintos. Os nomes fixos das branches compartilhadas são `Validacao` e `Principal`, sem acentos e com a grafia indicada; `Desenvolvimento` isolado não é um nome permitido de branch de desenvolvimento.

| Branch ou família de branches | Finalidade |
| --- | --- |
| `Desenvolvimento_[Responsavel]` | Elaborar funcionalidades, corrigir problemas e executar os testes locais sob responsabilidade da pessoa identificada no nome |
| `Validacao` | Integrar conjuntos aprovados no pull request e conferir a aplicação em ambiente correspondente à produção |
| `Principal` | Manter as versões aprovadas para produção |

Cada responsável DEVE trabalhar em sua própria branch, preservando os commits, os autores e o trabalho alheio. A existência de branches pessoais permite trabalho simultâneo e não concede autorização para alterar a branch de outra pessoa. Os pull requests partem diretamente de `Desenvolvimento_[Responsavel]` para `Validacao` e de `Validacao` para `Principal`. A remoção e a recriação da branch de desenvolvimento seguem [Commits e movimentação entre branches](#commits-e-movimentação-entre-branches).

### Seleção da entrega

O pull request apresenta toda a diferença entre sua branch de origem e a de destino; a indicação de commits na descrição não exclui as demais alterações dessa diferença. Todo o conjunto apresentado DEVE estar pronto para a etapa de destino. Isso vale de `Desenvolvimento_[Responsavel]` para `Validacao` e de `Validacao` para `Principal`.

- Identificar no pull request os commits apresentados e incluir todas as alterações das quais a entrega depende e que ainda não estão no destino, mesmo que sejam de outras entregas.
- Identificar as dependências que já estão no destino e conferir sua compatibilidade.
- Uma dependência incompleta impede apresentar a entrega como pronta: o conjunto completo DEVE atender aos requisitos da branch.
- Conferir a diferença entre as próprias branches de origem e destino antes de abrir o pull request, incluindo todas as dependências e preservando a aplicação completa e compatível. Conteúdo ainda em elaboração ou reprovado nessa diferença impede apresentar o conjunto como pronto.
- As verificações e a aprovação valem para o conjunto apresentado no pull request.

### Verificações e implantação

Na branch de desenvolvimento do responsável, testar exaustivamente o código conforme [Testes e cobertura integral](../00Regras.md#testes-e-cobertura-integral), incluindo comportamento, segurança e regressões. A validação de ambiente complementa esses testes, conferindo nas condições de produção a execução, as integrações, a entrega e a segurança.

Configurar as proteções de `Validacao` para exigir pull request, revisão e sucesso das verificações obrigatórias antes do merge. Em `Principal`, exigir pull request aceito e conferência do conjunto já validado, sem uma nova execução dos testes no GitHub. As branches de desenvolvimento recebem trabalho em elaboração e não disparam a validação automatizada de integração, conforme [Automação](#automação).

A versão liberada é um commit de `Principal`, e o conteúdo versionável implantado DEVE corresponder a ele; valores privados do ambiente ficam fora do controle de versão. A implantação é feita por quem tem acesso autorizado aos servidores ou por uma automação que use essa versão. O projeto define os requisitos técnicos de compatibilidade, segurança e recuperação, incluindo os cuidados de banco em [Cuidados de manutenção](../00Regras.md#cuidados-de-manutenção). A escolha, a execução e o registro das medidas operacionais de implantação, contenção e recuperação da produção pertencem à administração do ambiente, conforme [Referências da operação](../03Configuracoes.md#referências-da-operação). Erros encontrados após a liberação são acompanhados por issues e corrigidos pelo fluxo `Desenvolvimento_[Responsavel]` → `Validacao` → `Principal`.

### Fluxo no remoto

1. **Preparar o trabalho em `Desenvolvimento_[Responsavel]`.** Elaborar o conteúdo, registrar os commits e executar os testes locais. A incorporação à branch pessoal segue as permissões do responsável, sem exigir um pull request de integração para essa branch.
2. **Abrir o pull request da entrega pronta para `Validacao`.** Identificar o conjunto, os commits, as dependências e a versão de partida do destino. Concluir a revisão e os testes locais e executar no GitHub as verificações aplicáveis ao pull request, conforme [Automação](#automação). Abrir o pull request não equivale ao merge.
3. **Aceitar e fazer o merge em `Validacao`.** O responsável pela integração confere os resultados obrigatórios, o conjunto, os documentos e a versão de partida do destino. Somente o conjunto aprovado nas verificações e aceito é integrado. Uma verificação em andamento permanece pendente; ao concluir com reprovação, seguir [Reprovação no pull request](#reprovação-no-pull-request).
4. **Executar a validação de ambiente**, quando a alteração afetar a execução:
   - Instalar o conjunto incorporado a `Validacao`, identificado pelo commit, em um ambiente que replica a produção em versões, serviços, configurações e estrutura de dados relevantes, com dados de teste e segredos próprios.
   - Executar os fluxos reais afetados e conferir integrações, regressões e controles de segurança.
   - Registrar no pull request para `Validacao` quem testou, o ambiente, os commits testados e os resultados, mesmo após o merge. Falhas seguem [Falha na validação de ambiente](#falha-na-validação-de-ambiente).

   Alterações exclusivamente documentais seguem apenas suas conferências de conteúdo.
5. **Abrir o pull request para `Principal`, aceitar e fazer o merge.**
   - Abrir o pull request diretamente de `Validacao` para `Principal`. Todo o conjunto apresentado, incluindo suas dependências, DEVE estar validado com sucesso. A presença de conteúdo reprovado ou ainda não validado impede a promoção.
   - No pull request, identificar o commit do conjunto e referenciar os pull requests para `Validacao` e suas evidências.
   - Testes e aceitação valem somente para o conjunto exato, com a mesma versão de partida e as mesmas dependências. Conferir que a composição tem conteúdo idêntico ao conjunto validado. Se diferir, devolver o conjunto à branch do responsável e reapresentá-lo por outro pull request para `Validacao`, repetindo as verificações e a validação de ambiente aplicáveis; a promoção não introduz conteúdo não validado.
   - O pull request para `Principal` não exige uma nova execução dos testes no GitHub. O responsável pela integração confere os resultados de `Validacao`, a revisão e a identidade do conjunto, aceita o pull request e faz o merge.
   - Após o merge, registrar no pull request o commit resultante em `Principal`, vinculá-lo ao identificador testado e conferir que o conteúdo versionado é idêntico ao testado, qualquer que seja a forma de merge.
   - O merge em `Principal` aprova o conteúdo para produção e torna vigentes as regras e adaptações incluídas.

### Reprovação no pull request

Quando as verificações terminarem e houver falha, cancelamento ou ausência de execução de uma verificação obrigatória sem dispensa aplicável, o pull request para `Validacao` DEVE ser reprovado e encerrado automaticamente SEM merge. Registrar a reprovação, o commit examinado e os resultados; abrir a issue da correção com o conteúdo de [Registro da reprovação](#registro-da-reprovação). É PROIBIDO incorporar o conjunto reprovado ou reaproveitar esse pull request encerrado para apresentar a correção.

O trabalho permanece na branch do responsável. A correção segue [Correção e nova submissão](#correção-e-nova-submissão), por outro pull request, com referência ao anterior, ao commit reprovado e à issue.

### Falha na validação de ambiente

1. Reprovar o conjunto completo testado e bloquear seu merge em `Principal`.
2. Abrir uma issue conforme [Registro da reprovação](#registro-da-reprovação), com o máximo das informações verificáveis disponíveis sobre o conjunto, o ambiente, os erros e o que corrigir. Vinculá-la ao pull request que aprovou a integração e ao commit efetivamente reprovado no ambiente; registrar a reprovação também naquele pull request.
3. Preparar as correções em `Desenvolvimento_[Responsavel]`, recriando essa branch se tiver sido removida, conforme [Commits e movimentação entre branches](#commits-e-movimentação-entre-branches). Preservar o trabalho existente e concluir novamente os testes de desenvolvimento.
4. Abrir outro pull request para `Validacao`, vinculado ao anterior, à issue e ao commit reprovado, com os commits da correção e todas as dependências do conjunto corrigido. A nova submissão segue [Correção e nova submissão](#correção-e-nova-submissão).
5. Se a execução anterior deixou efeitos que interfiram na nova validação, recuperar ou recriar o ambiente de validação conforme a recuperação preparada para o banco e os demais armazenamentos, conferindo seu estado antes dos novos testes.
6. Validar de novo o conjunto completo antes de encaminhá-lo a `Principal`. Enquanto a falha permanecer, acrescentar entregas independentes não permite promovê-lo.

O pull request já integrado preserva seu resultado de merge. A reprovação posterior do ambiente é registrada nos comentários e na issue, sem apresentar o merge anterior como recusado ou desfeito. A proposta de correção não resolve a reprovação sem a conferência dos resultados.

### Correção e nova submissão

O responsável DEVE corrigir o problema em `Desenvolvimento_[Responsavel]`, acrescentar os testes que reproduzem a falha quando houver alteração de código e concluir novamente os testes locais. Abrir OUTRO pull request para `Validacao`, indicando o pull request anterior, a issue, o commit reprovado, os commits de correção e todas as dependências do conjunto corrigido.

O novo pull request repete as verificações obrigatórias. Se aprovado e integrado, o conjunto completo passa novamente pela validação de ambiente quando afetar a execução. Registrar o novo commit testado e sua correspondência com o conteúdo integrado. A issue somente é encerrada após a conferência da correção na etapa em que a falha ocorreu e das regressões aplicáveis; uma proposta de correção ou um teste ainda não executado não resolve a reprovação.

### Registro e merge

Cada pull request registra:

| Informação | Conteúdo |
| --- | --- |
| Origem e destino | Branches envolvidas e identificadores dos commits de partida |
| Entrega apresentada | Commits de origem, alterações apresentadas e dependências incluídas |
| Verificações | Resultados das verificações exigidas; quando houver testes em ambiente, identificação do ambiente e dos commits testados |
| Decisão | Resultado da conferência e identificação de quem aceitou ou recusou o merge |
| Resultado do merge | Commit incorporado ao destino, conferência do conteúdo e falhas ou pendências da operação |

Antes de cada merge, conferir o commit de partida e coordenar a operação com os autores, os revisores e o responsável pela integração do conteúdo afetado, preservando as alterações alheias e os commits de outras entregas que continuam em `Desenvolvimento_[Responsavel]`. Mudanças no commit de partida, conflitos ou alterações posteriores exigem conferir de novo o conjunto e atualizar a aceitação. Se o merge falhar, registrar no pull request o estado observado, sem declará-lo concluído.

O estado de revisão documental acompanha a versão de cada pull request; a conferência dos documentos segue [Encaminhamento & Integração](../00Regras.md#encaminhamento--integração).

### Casos especiais

**Intervenção na produção.** Quando uma intervenção autorizada pela administração do ambiente deixa a produção provisoriamente diferente de `Principal`, a divergência exige regularização pelo fluxo normal. Identificar a versão de origem e as diferenças nos logs de acesso restrito, conforme [Banco e armazenamento](../03Configuracoes.md#banco-e-armazenamento), e apresentar pelo fluxo normal as alterações versionáveis, como código, modelos e parâmetros não sensíveis, para compor a versão do projeto no remoto; credenciais e valores privados ficam fora. A correspondência volta quando a operação usa a versão regularizada ou retoma uma versão de `Principal`.

**Entrega abandonada.** Preparar a retirada da entrega incorporada a `Validacao` em `Desenvolvimento_[Responsavel]`, por novos commits que ajustem também documentos, testes e componentes dependentes. Apresentá-la em um pull request para `Validacao`, referenciando o pull request original e a issue, se houver, e executar as verificações e a validação do conjunto resultante. O histórico dos pull requests e dos commits é preservado.

**Promoção que exige alterações.** Alterações necessárias para preparar uma promoção, inclusive para resolver conflitos, são feitas também em `Desenvolvimento_[Responsavel]` e reapresentadas pelo fluxo normal, com os commits identificados nos pull requests e o trabalho em elaboração preservado. Se afetarem a execução, concluir de novo os testes de desenvolvimento e a validação de ambiente antes do merge em `Principal`. A promoção parte diretamente de `Validacao` para `Principal` e apresenta o conjunto já validado, conforme o passo 5 do [Fluxo no remoto](#fluxo-no-remoto). Se houver diferença em relação ao conjunto testado, a correção retorna ao responsável e passa novamente pelas verificações e pela validação de ambiente aplicáveis, com a dispensa das alterações exclusivamente documentais.

**Conflitos em documentos.** Alterações documentais decorrentes da resolução de conflitos seguem [Revisão da documentação](../00Regras.md#revisão-da-documentação). Se o texto de um documento `Revisado` for alterado, marcar a versão resultante como `Sem revisão` e concluir novamente sua revisão antes do merge em `Validacao` ou `Principal`.

**Branch `Validacao` ausente.** Se `Validacao` ainda não existir no remoto, criá-la conforme as permissões da plataforma antes do primeiro merge; sua ausência não autoriza omitir essa branch do fluxo.

**Recuperação de arquivos.** Para recuperar arquivos perdidos ou retomar o trabalho a partir de uma versão anterior:

1. Identificar o commit de origem. É PERMITIDO usar a última versão de `Principal` para recuperar parte dos arquivos ou todos.
2. Antes de substituir arquivos, conferir o estado atual do trabalho e decidir o que é preservado ou descartado.
3. Recuperar os arquivos e conferir o conjunto, inclusive a compatibilidade com os arquivos que permaneceram quando a recuperação for parcial.
4. Antes do envio ao remoto, informar na mensagem do commit, conforme [Mensagens de commit](Git.md#mensagens-de-commit), a versão de origem, o estado anterior do trabalho, os arquivos recuperados e substituídos, o trabalho preservado ou descartado e os resultados das verificações.
5. Preparar a recuperação na branch do responsável e apresentá-la em um pull request para `Validacao`. Concluir as verificações, a validação e a aceitação do conjunto antes da promoção para `Principal`.

## Commits e movimentação entre branches

A incorporação de commits às branches oficiais do remoto segue [Trabalho local e permissões no remoto](#trabalho-local-e-permissões-no-remoto) e [Branches do projeto](#branches-do-projeto).

- Os commits de desenvolvimento e de correção são preparados em `Desenvolvimento_[Responsavel]` e submetidos diretamente a `Validacao` por pull request. A promoção é feita por pull request diretamente de `Validacao` para `Principal`, conforme [Fluxo no remoto](#fluxo-no-remoto).
- Depois do merge em `Validacao`, é PERMITIDO remover a branch `Desenvolvimento_[Responsavel]` local e remota, conforme as permissões da plataforma, se não houver outro pull request ou trabalho pendente que dependa dela. Antes da remoção, conferir que os commits da entrega permanecem no destino e que o pull request registra os identificadores necessários para retomar o conjunto. `Validacao` e `Principal` permanecem.
- Para a mesma entrega, a branch removida só é recriada se o conjunto for devolvido ao responsável para correção. Recriá-la com o mesmo nome a partir do commit do conjunto devolvido, identificado no pull request e na issue, preservando os commits e autores. A recriação não altera o resultado do pull request anterior; a correção segue por outro pull request para `Validacao`, conforme [Correção e nova submissão](#correção-e-nova-submissão). Se a branch ainda existir, preparar a correção nela, preservando o trabalho pendente.

Para incorporar conteúdo a cada branch oficial:

| Destino | Requisitos |
| --- | --- |
| `Desenvolvimento_[Responsavel]` | Aceita conteúdo em elaboração, incluindo documentos `Sem revisão` ou `Em revisão` e verificações ainda pendentes. A mensagem de cada commit apresentado registra as alterações, o estado do trabalho e as verificações realizadas ou pendentes, conforme [Mensagens de commit](Git.md#mensagens-de-commit) |
| `Validacao` | Revisão do conjunto apresentado concluída e verificações obrigatórias do pull request executadas com sucesso, conforme [Verificação de uma alteração](../00Regras.md#verificação-de-uma-alteração). Alterações que afetem a execução exigem a suíte completa e a cobertura integral do código próprio de produção do conjunto resultante. Documentos incluídos ou alterados DEVEM estar `Revisado` |
| `Principal` | Os requisitos de `Validacao` e a validação de ambiente descrita em [Fluxo no remoto](#fluxo-no-remoto). Erros conhecidos no conjunto apresentado, verificações exigidas ainda pendentes e documentos não revisados impedem o merge |

As condições para alterações exclusivamente documentais estão em [Verificação de uma alteração](../00Regras.md#verificação-de-uma-alteração).

## Comunicação de pendências

A comunicação de um erro de código exige a abertura de uma issue no repositório remoto. A issue reúne a descrição do erro e as informações necessárias à investigação, correção e conferência. O pull request da correção DEVE referenciar essa issue.

É PERMITIDO registrar também pendências de documentação, dúvidas e propostas em uma issue. Quando a alteração estiver preparada e não tratar de um erro de código, é PERMITIDO registrá-la diretamente no pull request, com os mesmos dados pertinentes ao conteúdo.

O título identifica o erro, a dúvida ou a alteração proposta e o elemento, componente ou documento afetado. O corpo apresenta os dados abaixo, conforme o tipo de pendência:

| Conteúdo afetado | Dados da comunicação |
| --- | --- |
| Qualquer conteúdo | Descrição do erro, da dúvida ou da alteração proposta, fontes envolvidas, alcance de cada fonte e comportamento ou conteúdo observado |
| Código | Arquivos e componentes afetados, ambiente e condições necessárias, passos para reproduzir o erro e resultados esperado e observado |
| Documentação | Caminho do documento, seção e trecho com erro, justificativa da correção e texto proposto para substituição |
| Alteração de documento revisado | Caminho do documento, seção e trecho afetado, conteúdo atual, alteração proposta, justificativa e referências que precisam ser atualizadas |
| Definição | Elemento a definir, regras aplicáveis, motivo da dúvida, definição proposta e justificativa; para grafias externas, incluir a grafia e sua origem ou exigência |

Na comunicação:

- Quando a pendência envolver código e documentação, o corpo inclui os dados dos dois casos.
- As referências permitem localizar as fontes e os trechos afetados; evidências e exemplos não contêm credenciais nem dados sensíveis.
- Distinguir o conteúdo preparado ou testado localmente do conteúdo incorporado às branches do remoto.
- Registrar as verificações realizadas e pendentes; é PROIBIDO apresentar uma proposta ainda sem merge como incorporada.
- É PERMITIDO identificar o conteúdo atual e o proposto pela diferença exibida no pull request, com a versão de partida, a versão proposta e os arquivos abrangidos identificados. Nessa situação, descrever o alcance, a justificativa e as referências afetadas sem reproduzir todos os trechos no corpo do registro. Em issues sem essa comparação disponível, incluir os trechos pertinentes ou referências precisas às versões atual e proposta.
- É PERMITIDO acrescentar atualizações da proposta e informações sobre seu tratamento à descrição ou aos comentários da mesma issue ou do mesmo pull request, conforme as permissões de acesso.

Para alterações de documentos revisados, o registro identifica o documento, os trechos abrangidos e o conteúdo proposto, mesmo quando a edição local já tiver sido feita. Esses dados DEVEM constar do pull request antes da aceitação do merge. O resultado da conferência é registrado como aceito, recusado ou pendente de correção, com os trechos afetados e a justificativa da decisão.

As condições para corrigir ou encaminhar uma divergência seguem [Definições](../00Regras.md#definições).

### Registro da reprovação

A issue DEVE identificar o responsável pela correção e reunir o máximo das informações abaixo que estiverem disponíveis, sem inventar causas, datas, responsáveis ou resultados. Identificar explicitamente informações indisponíveis, hipóteses ainda não confirmadas e verificações não executadas.

| Informação | Conteúdo do registro |
| --- | --- |
| Origem e conjunto | Pull request reprovado ou já integrado, branch de origem, responsável, commits de origem, composição e commit efetivamente testado, versão de partida do destino e dependências |
| Etapa e resultado | Falha no pull request ou no ambiente de `Validacao`, data e hora com fuso quando conhecidas, verificação ou fluxo afetado, resultado esperado e resultado observado |
| Ambiente | Versões de PHP, banco, servidor e serviços, sistema operacional ou imagem de contêiner, configuração relevante, diferenças conhecidas em relação à produção e condições de acesso |
| Reprodução | Pré-condições, preparação, sequência dos passos, entradas fictícias, frequência da falha e alcance observado |
| Evidências | Endereço da execução, tarefa e etapa, comandos aplicados, códigos de saída, mensagens, rastreamentos de erro, trechos de logs e imagens pertinentes |
| Investigação e recuperação | Causas confirmadas, hipóteses, verificações já realizadas, efeitos deixados no ambiente, medidas de recuperação e limitações conhecidas |
| Correção e aceitação | Componente ou configuração a corrigir, critérios verificáveis de aceitação, issue e pull requests relacionados, commits da correção e resultados das novas verificações |

É PROIBIDO incluir credenciais, dados pessoais reais ou outros dados sensíveis na issue. Sanitizar as evidências e usar entradas fictícias; evidências sensíveis permanecem no armazenamento de acesso restrito do ambiente, com referência segura e indicação do acesso necessário, conforme [Referências da operação](../03Configuracoes.md#referências-da-operação). A ausência de um dado impede inventá-lo, não impede registrar os demais.

## Automação

A automação da plataforma do repositório DEVE executar as mesmas verificações reproduzíveis utilizadas no desenvolvimento, com versões e configuração identificadas. Seus caminhos DEVEM corresponder aos diretórios reais de aplicação, configurações e ferramentas. Com o GitHub como repositório remoto, os arquivos de automação residem em `.github/workflows`, local exigido pela plataforma, e DEVEM referenciar os caminhos reais das ferramentas. A situação efetiva do fluxo está em [Situação da automação](#situação-da-automação).

Antes de executar cada ferramenta, a automação compara seu executável com o resumo registrado na linha `Integridade` do documento da ferramenta, conforme [Documento de ferramenta](../01Padroes.md#documento-de-ferramenta). Essa comparação não valida assinaturas nem consulta a fonte.

Se faltarem configurações, preparação dos testes ou cenários exigidos para o conteúdo do pull request, a automação falha informando a preparação incompleta. Uma etapa não executada ou uma suíte sem cenários não equivale a validação bem-sucedida; o relato segue [Verificação de uma alteração](../00Regras.md#verificação-de-uma-alteração).

É PERMITIDO preparar banco, servidor e serviços de integração na automação, desde que os recursos de teste permaneçam isolados. Seus detalhes, como endereços, certificados, usuário de execução e variáveis, DEVEM ser documentados nos comentários do próprio arquivo de automação.

As verificações automatizadas no GitHub DEVEM executar EXCLUSIVAMENTE nos pull requests cujo destino é `Validacao`. É PROIBIDO disparar ou exigir essa validação em pushes, por execução manual ou em pull requests destinados a `Principal`, às branches de desenvolvimento ou a outros destinos. Isso não dispensa os testes locais obrigatórios nem a validação de ambiente.

`Validacao.yml` restringe os testes a esse destino. `Reprovacao.yml` recebe o término dessas execuções, incluindo cancelamentos, e registra a reprovação conforme [Registro da reprovação](#registro-da-reprovação), sem executar testes. O registro usa o código da branch padrão e confere a execução e o pull request pela API; não executa o código da entrega com permissões de escrita. O token da execução tem leitura do conteúdo e das execuções e escrita de issues e pull requests somente nesse fluxo de registro.

O conjunto do pull request DEVE passar pelas verificações aplicáveis de [Verificação de uma alteração](../00Regras.md#verificação-de-uma-alteração), com versões, configuração, escopo e critérios identificados. Quando afetar a execução, executar a suíte completa e medir a cobertura integral do código próprio de produção; documentação isolada segue somente suas conferências aplicáveis. Conferir também os formatos de [Arquivos e linguagens permitidos](../00Regras.md#arquivos-e-linguagens-permitidos) e a integridade dos numerados.

As verificações obrigatórias DEVEM concluir com sucesso antes do merge em `Validacao`. Tarefa pendente, cancelada ou ignorada sem dispensa aplicável não comprova aprovação. A reprovação exige encerramento automático do pull request sem merge e outra submissão para a correção, conforme [Reprovação no pull request](#reprovação-no-pull-request). A falha posterior no ambiente e os dados da issue seguem [Registro da reprovação](#registro-da-reprovação).

A definição documental de um gatilho, proteção ou encerramento automático não comprova sua implementação. A situação efetiva e as pendências ficam no [Memorial](../Memorial.md#estado-atual).

### Seleção das verificações

A seleção aplica [Verificação de uma alteração](../00Regras.md#verificação-de-uma-alteração). Os cenários e o comando local estão em [Testes de integração da automação](../Testes/Integracao/TestesIntegracaoAutomacao.md). A existência dos arquivos não comprova que os gatilhos e proteções exigidos estão implementados; conferir as pendências no [Memorial](../Memorial.md#configuração-do-fluxo).

### Conferência dos documentos numerados

A tarefa `conferirDocumentosNumerados`, o gancho e a conferência local executam `.githooks/ConferenciaDocumentosNumerados.php`. O apoio recusa documentos ausentes, extras ou divergentes e registro ausente ou duplicado. Cada linha abaixo contém o caminho e o SHA-256 do conteúdo com fim de linha LF. A atualização do registro segue [Bloqueio dos documentos numerados](../00Regras.md#bloqueio-dos-documentos-numerados).

| Documento | SHA-256 |
| --- | --- |
| `Documentacao/00Regras.md` | `6f9d2b8cef682d3cbc70f6c6b028df07187946bbebb81c80722b0f22f8452098` |
| `Documentacao/01Padroes.md` | `9d2f7f2f2164d938ad76dcb5caa30be88b02b5e193d5ff0f45456b6fc53d4a0c` |
| `Documentacao/02Estrutura.md` | `0d97de7d8ef85c45662fd15b119ed06e0ad456a1ab06562f435279d3596d6068` |
| `Documentacao/03Configuracoes.md` | `998cf837b00b6612f3047120e4ba1aa05b53812056e57bef5264a1e055481d28` |
| `Documentacao/04Ferramentas.md` | `2c295a2e0f2d0e1802a3a61accab2e5530d8471046ce404f6a73d970b864a1df` |
| `Documentacao/05Externos.md` | `4f507af7b3a3b60f8809ff2bfbd055ec66913e9603ca563b6e2336a637a564d4` |

A conferência local parte da raiz:

```text
php .githooks/ConferenciaDocumentosNumerados.php
```

O responsável pela integração DEVE recusar a promoção quando a tarefa falhar, mesmo sem proteção configurada na plataforma.

### Situação da automação

O estado, as pendências de configuração e os limites estão no [Memorial](../Memorial.md#limites-da-automação).
