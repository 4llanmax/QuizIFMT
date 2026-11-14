# Regras

| Redator   | Data de criação | Status   | Data do Status |
| --------- | --------------- | -------- | -------------- |
| Allan Max | 28/09/2026      | Revisado | 12/11/2026     |

## Objetivo

Este documento estabelece as regras de manutenção, ampliação e atualização do projeto e de sua documentação.

## Em resumo

- As regras vigentes são as que estão em `Principal`; o trabalho segue `Desenvolvimento_[Responsavel]` → `Validacao` → `Principal` por pull request. Veja [Branches do projeto](Ambiente/GitHub.md#branches-do-projeto).
- Os testes DEVEM ser escritos antes do código; o código próprio de produção DEVE atingir cobertura integral de linhas, ramos de decisão e cenários. Veja [Testes e cobertura integral](#testes-e-cobertura-integral).
- Todo conteúdo próprio é escrito em português do Brasil. Veja [Idioma do projeto](#idioma-do-projeto).
- A documentação percorre os estados `Sem revisão`, `Em revisão` e `Revisado`. Veja [Revisão da documentação](#revisão-da-documentação).
- Os documentos numerados são bloqueados: mudam somente por pedido expresso do Gerente do projeto, com o registro de conferência atualizado. Veja [Bloqueio dos documentos numerados](#bloqueio-dos-documentos-numerados).
- Recursos de terceiros só entram com justificativa registrada e licença compatível. Veja [Recursos de terceiros](#recursos-de-terceiros).
- Segurança e proteção de dados pessoais acompanham toda funcionalidade. Veja [Segurança do código](#segurança-do-código).
- Uma forma diferente da regra geral exige uma adaptação registrada. Veja [Ampliações & Adaptações](#ampliações--adaptações).

## Onde documentar cada assunto

### Referência técnica do projeto

A documentação do projeto reúne as definições estruturais, os padrões e os requisitos de manutenção. A situação física variável está em [Estado atual](Memorial.md#estado-atual), nos documentos de ferramentas, nos documentos de ferramentas de ambiente e nas fichas, conforme a lei 3 de [Leis de escrita](01Padroes.md#leis-de-escrita). As regras de execução e verificação são obrigações arquiteturais; não comprovam a existência de componentes, operações, ambientes ou testes implementados.

| Documento | Pergunta que responde | Conteúdo |
| --- | --- | --- |
| [Regras](00Regras.md) (`00Regras.md`) | Como manter e atualizar o projeto? | Procedimentos de manutenção, documentação, verificação e revisão. |
| [Padrões](01Padroes.md) (`01Padroes.md`) | Como escrever e nomear? | Formatos, convenções, exemplos e conteúdo mínimo dos documentos. |
| [Estrutura](02Estrutura.md) (`02Estrutura.md`) | Onde colocar? | Pastas, responsabilidades e limites de conteúdo. |
| [Configurações](03Configuracoes.md) (`03Configuracoes.md`) | Como configurar? | Parâmetros, preparação e descrição do ambiente. |
| [Ferramentas](04Ferramentas.md) (`04Ferramentas.md`) | Como verificar o desenvolvimento? | Funções exigidas das ferramentas, sua organização e seu uso. |
| [Externos](05Externos.md) (`05Externos.md`) | Como integrar terceiros? | Organização e uso das dependências. |
| `Documentacao/Aplicacao` | O que os componentes implementados fazem e como utilizá-los? | Detalhamentos da finalidade, funcionalidades, contratos, integrações, configuração, fluxos e uso dos componentes implementados. As definições gerais ficam nos documentos numerados. |
| `Documentacao/Publico` | Quais cuidados específicos se aplicam aos arquivos expostos ao navegador? | Informações de uso, geração, manutenção ou distribuição dos arquivos de `Publico`. |
| `Documentacao/Testes` | Como executar e conferir os testes implementados? | Detalhamentos da execução, preparação dos ambientes, cenários, rastreabilidade dos requisitos e evidências de cobertura dos componentes implementados. As definições gerais ficam nos documentos numerados. |
| `Documentacao/Adaptacoes` | O que funciona de forma diferente do padrão documentado? | Diferenças em relação ao padrão, situações em que se aplicam e orientações de uso. |
| `Documentacao/Ambiente` | O que cada ferramenta de ambiente acrescenta ao projeto e como usá-la? | Um documento por ferramenta de ambiente, com suas pastas, seus arquivos, sua escrita e suas regras de uso, incluindo branches, commits, pull requests, issues e automação, conforme [Ferramentas de ambiente](02Estrutura.md#ferramentas-de-ambiente). |
| `Documentacao/Ferramentas`, `Documentacao/Externos` e `Documentacao/Publico/Externos` | Como cada ferramenta e cada recurso de terceiros está instalado e integrado? | Documentos de uso das ferramentas e fichas dos recursos de terceiros, conforme [Fichas e documentos de ferramentas](02Estrutura.md#fichas-e-documentos-de-ferramentas). |
| Outras pastas de `Documentacao` | Onde ficam assuntos subdivididos? | Conforme [Documentacao](02Estrutura.md#documentacao). |
| `Documentacao/Memorial.md` | Qual é o estado atual e o histórico do projeto? | Implementação presente, pendências, decisões e conferências históricas, com referências às definições e aos registros de revisão. |
| [Orientação para agentes de IA](../AGENTS.md) (`AGENTS.md`) | Por onde um agente de IA começa? | Índice para agentes de IA, sem definições próprias, que aponta para os documentos. |

Antes de criar um documento, conferir se o assunto cabe em uma seção do documento responsável. Um detalhamento separado se justifica por um alcance específico ou pela subdivisão necessária do assunto. Ele acrescenta informações concretas e referencia as definições gerais, sem criar uma segunda fonte para elas. Ao subdividir conteúdo existente, mover os detalhes e deixar sua apresentação e o link na origem, conforme [Manutenção dos documentos](#manutenção-dos-documentos).

Por exemplo, os requisitos gerais de parâmetros e ambientes pertencem a [Configurações](03Configuracoes.md). O contrato de um carregador implementado pertence ao detalhamento de seu componente. Não criar outro documento apenas para reescrever as orientações gerais de configuração.

O documento **01Padroes.md** explica a forma de escrever e nomear. Uma frase como “O nome da pasta DEVE começar com maiúscula” pertence a Padrões porque descreve a escrita do nome.

O documento **00Regras.md** explica os cuidados de manutenção. Uma frase como “Ao renomear uma pasta, o autor DEVE atualizar as referências” pertence a Regras porque descreve o trabalho que acompanha a alteração.

A palavra “DEVE” aparece nas duas frases. O assunto da frase indica em qual documento ela DEVE ficar.

Por exemplo, um arquivo CSS é descrito em três documentos, cada um com uma função:

- **00Regras.md:** define os cuidados ao alterar o arquivo.
- **01Padroes.md:** explica como nomear o arquivo.
- **02Estrutura.md:** indica onde armazenar o arquivo.

### Termos utilizados

| Termo | Significado |
| --- | --- |
| Revisão documental | Ciclo dos estados `Sem revisão`, `Em revisão` e `Revisado` |
| Entrega | Conjunto selecionável de alterações que percorre as branches do projeto, incluindo os componentes e documentos necessários ao resultado proposto; nos textos sobre branches e pull requests, “entrega” designa esse conjunto |
| Entrega web | Envio de arquivos e respostas ao navegador pelo servidor, a partir de `Publico`; nos textos sobre servidor, ativos, respostas e arquivos entregues ao usuário, “entrega” designa a entrega web |
| Liberação | Merge do conjunto validado em `Principal`, disponibilizando a versão aprovada para produção |
| Aprovação | Decisão sobre uma alteração ou sua aplicação |
| Conferência | Verificação de usos, referências ou evidências |
| Validação de ambiente | Etapa que confere o conjunto integrado nas condições correspondentes à produção, conforme [Fluxo no remoto](Ambiente/GitHub.md#fluxo-no-remoto) |
| Log | Registro técnico do ambiente sobre operações realizadas, resultados e falhas, com destino e acesso definidos na configuração |
| Gerente do projeto | Pessoa que autoriza a alteração dos documentos numerados, conforme [Bloqueio dos documentos numerados](#bloqueio-dos-documentos-numerados); sua identificação está em [Gerente do projeto](../README.md#gerente-do-projeto) |

Os termos commit, branch e merge estão definidos em [Termos](Ambiente/Git.md#termos) do Git; pull request e issue, em [Termos](Ambiente/GitHub.md#termos) do GitHub.

## Definições

Todo documento próprio DEVE cumprir as [Leis de escrita](01Padroes.md#leis-de-escrita): voz própria e definitiva, escopo estrutural nos documentos gerais, fórmula antes do exemplo, força normativa determinística e fronteiras excludentes com coerência física.

Cada definição DEVE ter uma única fonte responsável. Outros documentos DEVEM apontar para ela; é PERMITIDO resumir seu efeito local sem reproduzir a definição completa.

Um formato específico vale somente no alcance declarado. Fora dele, permanece o formato geral.

Os arquivos próprios do projeto, incluindo testes e arquivos auxiliares, seguem as regras e os padrões aplicáveis, mesmo quando ficam em pastas de ferramentas ou dependências de terceiros.

É PERMITIDO conservar os nomes de origem dos arquivos fornecidos por terceiros, conforme [Idioma do projeto](#idioma-do-projeto). Sua origem DEVE ser identificada na ficha da dependência externa; não é necessário cadastrar cada arquivo fornecido. O conteúdo mínimo da ficha está em [Ficha de recurso de terceiros](01Padroes.md#ficha-de-recurso-de-terceiros).

A existência de um arquivo, pasta, configuração ou registro não comprova implementação, execução nem aprovação das verificações. A descrição de um comportamento desejado também não comprova sua implementação.

Ao encontrar divergências entre documentos, código e exemplos, registrar as fontes envolvidas, seus alcances e o comportamento observado, distinguindo o comportamento desejado daquele verificado na implementação.

A partir desse registro, preparar a correção ou abrir uma issue conforme [Comunicação de pendências](Ambiente/GitHub.md#comunicação-de-pendências). A preparação das alterações segue [Trabalho local e permissões no remoto](Ambiente/GitHub.md#trabalho-local-e-permissões-no-remoto), e a versão documental segue [Revisão da documentação](#revisão-da-documentação). Até a resolução, manter explícitas a pendência e a divergência, sem escolher silenciosamente a versão mais conveniente.

## Ampliações & Adaptações

Uma **ampliação** cria uma regra geral nova, que passa a integrar a documentação do projeto e DEVE ser seguida em todas as situações às quais se aplica. Uma **adaptação** permite adotar uma forma diferente da regra geral em uma situação específica e justificada, mantendo essa regra válida para os demais casos.

Criar uma pasta ou um componente já previsto na documentação é aplicar as regras existentes; isso, por si só, não constitui ampliação nem adaptação.

| Proposta | Condição de aplicação | Onde é preparada |
| --- | --- | --- |
| Ampliação | O trabalho apresenta uma necessidade que a organização documentada não consegue acomodar e que exige uma nova regra de aplicação geral | No documento responsável pela definição do assunto, conforme [Onde documentar cada assunto](#onde-documentar-cada-assunto) |
| Adaptação | Cumprir uma regra existente impede atender a uma necessidade específica do trabalho, mas essa regra DEVE continuar valendo para os demais casos | Em `Documentacao/Adaptacoes`, conforme o [Registro de adaptação](#registro-de-adaptação) |

**A adoção de ampliações e adaptações é PERMITIDA somente depois de demonstrada a insuficiência das alternativas pertinentes que respeitam a estrutura e as regras documentadas.** Enquanto houver uma solução que atenda à necessidade dentro das regras, essa solução DEVE ser utilizada. Facilidade, rapidez ou preferência pessoal não justificam criar uma regra ou adotar uma adaptação.

Para propor uma ampliação ou adaptação:

1. Consultar [Estrutura](02Estrutura.md) e a documentação dos assuntos envolvidos. Examinar a fundo as alternativas dentro das regras: reutilizar recursos, dividir o problema e ajustar componentes sem desviar suas responsabilidades. É PERMITIDO incluir protótipos e testes na preparação local para avaliá-las.
2. Se as alternativas forem insuficientes, preparar a proposta no destino indicado na tabela e atualizar as referências afetadas. O conteúdo da proposta e sua vigência seguem [Atualização e vigência](#atualização-e-vigência).
3. Apresentar a proposta e suas evidências conforme [Comunicação de ampliações e adaptações](01Padroes.md#comunicação-de-ampliações-e-adaptações). É PERMITIDO registrar a proposta no canal de revisão depois da preparação local, mas esse registro DEVE conter os campos pertinentes antes da aceitação do merge em qualquer das branches oficiais, identificando o conteúdo preparado, as evidências disponíveis e as pendências. Informações necessárias à análise, definição ou alcance ainda pendentes impedem a aceitação da proposta para `Principal`.
4. A preparação e as etapas de integração e validação da proposta seguem [Fluxo no remoto](Ambiente/GitHub.md#fluxo-no-remoto). Falhas no ambiente de validação seguem [Falha na validação de ambiente](Ambiente/GitHub.md#falha-na-validação-de-ambiente).
5. Antes do merge em `Principal`, conferir a documentação, o alcance e os componentes em conjunto e registrar o resultado da avaliação e a aceitação pelo responsável pela integração.

Os limites das adaptações quanto a testes e cobertura estão em [Alcance e evidências da cobertura](#alcance-e-evidências-da-cobertura). As adaptações permanecem vigentes até sua alteração ou remoção pelo mesmo fluxo das regras gerais.

### Registro de adaptação

Preparar a adaptação em um documento de `Documentacao/Adaptacoes` com nome de assunto, sem prefixo numérico, conforme [Nomes dos documentos](01Padroes.md#nomes-dos-documentos). Colocar um link para ele junto da definição afetada e, quando houver, na documentação dos componentes abrangidos, sem repetir a adaptação nesses documentos. Sua aceitação e vigência seguem [Ampliações & Adaptações](#ampliações--adaptações). Documentos de adaptação são acrescentados somente quando houver uma adaptação concreta; sem eles, a pasta mantém seu `README.md`, conforme [Pastas sem conteúdo](#pastas-sem-conteúdo).

Os campos e as orientações de preenchimento estão em [Formato de adaptação](01Padroes.md#formato-de-adaptação).

### Casos especiais

- **Mudança em regra geral referenciada por adaptações.** Ao modificar ou remover uma regra geral, conferir as adaptações que a referenciam e atualizar sua vigência, links e orientação dos componentes.
- **Adaptações sobrepostas incompatíveis.** Incluir no registro da proposta as definições em conflito, os alcances afetados e a solução proposta. A solução DEVE constar nesse registro e estar refletida na documentação antes de ser aplicada aos componentes.
- **Adaptação que compromete a segurança.** Avaliar sua correção ou remoção, atualizar os componentes e as referências abrangidos e executar as verificações exigidas. Um incidente de segurança segue também [Resposta a incidentes](#resposta-a-incidentes).

## Criação de pastas da aplicação

Manter as pastas fixas e as áreas indicadas em [Aplicacao](02Estrutura.md#aplicacao). As demais pastas DEVEM ser criadas somente quando receberem o primeiro arquivo de implementação. Não criar pastas opcionais apenas para colocar um `README.md` ou repetir a organização de outra área.

Antes de criar uma pasta, definir sua finalidade e a área da aplicação à qual ela pertence, conforme [Nucleo](02Estrutura.md#nucleo) e [Independência entre áreas](02Estrutura.md#independência-entre-áreas). Aplicar esse critério também às pastas de apoio.

Se houver conflito sobre a área responsável ou a localização da pasta, abrir uma issue conforme [Comunicação de pendências](Ambiente/GitHub.md#comunicação-de-pendências) antes de criá-la ou movê-la.

Para criar uma pasta da aplicação:

1. Identificar a área à qual a pasta pertence, considerando a finalidade de seu conteúdo.
2. Escolher a divisão adequada em [Divisões de responsabilidade](02Estrutura.md#divisões-de-responsabilidade).
3. Usar a pasta existente quando ela atender à finalidade definida. Se for necessário criar pastas, criar somente as que faltam.
4. Quando a pasta ainda não estiver descrita em [Aplicacao](02Estrutura.md#aplicacao), conferir se sua finalidade e localização são permitidas pelas regras existentes. Se forem, atualizar a documentação do assunto e as referências afetadas, incluindo a estrutura quando necessário. Se a criação exigir uma regra geral nova ou uma exceção à organização permitida, seguir [Ampliações & Adaptações](#ampliações--adaptações).

Ao retirar o último arquivo de implementação de uma pasta opcional, remover a pasta e os auxiliares que perderam a função e atualizar suas referências. Não acrescentar um `README.md` apenas para preservar essa pasta opcional. Se uma pasta fixa ou uma área perder seu último arquivo versionado, restaurar seu `README.md` conforme [Pastas sem conteúdo](#pastas-sem-conteúdo).

## Idioma do projeto

Todo conteúdo próprio do projeto DEVE ser escrito em português do Brasil. Essa regra abrange a documentação, os comentários, os textos apresentados pela aplicação e os nomes definidos pelo projeto. A forma de escrever nomes e textos segue [Composição dos nomes](01Padroes.md#composição-dos-nomes).

É PERMITIDO conservar a grafia de origem de palavras e nomes recebidos de terceiros, exigidos por linguagem, ferramenta ou protocolo ou mantidos por uma convenção externa adotada. Isso inclui nomes exigidos externamente e os nomes convencionais dos arquivos existentes, identificados em [Grafias externas](01Padroes.md#grafias-externas).

É PERMITIDO usar os termos técnicos cache, download, log e upload nos textos e nomes próprios do projeto. Nos textos, usar essas grafias; nos identificadores, aplicar as maiúsculas e minúsculas de [Formato por elemento](01Padroes.md#formato-por-elemento). O plural acrescenta `s` quando o termo designa uma coleção, como nas pastas fixas `Dados/Arquivos/Logs` e `Dados/Arquivos/Uploads`. Nos textos sobre versionamento, usam-se commit, branch, merge, pull request e issue, com os gêneros o commit, a branch, o merge, o pull request e a issue.

Ao aplicar padrões, recomendações ou exemplos de fontes externas, adequar os textos, comentários e nomes próprios ao português do Brasil, inclusive quando a fonte recomendar termos em outro idioma. Traduzir esses termos, salvo quando forem de uso comum no português ou estiverem abrangidos pelas demais exceções de grafia desta seção.

Identificar a origem ou a exigência das grafias externas e seu alcance na documentação responsável pelo assunto. Uma nova convenção externa adotada ou um novo termo técnico DEVE ter uma grafia única documentada nesse assunto. Quando houver dúvida sobre sua inclusão nas exceções, abrir uma issue conforme [Comunicação de pendências](Ambiente/GitHub.md#comunicação-de-pendências) antes de nomear o elemento.

Uma escolha de idioma ou grafia fora dessas exceções exige o [Registro de adaptação](#registro-de-adaptação). Arquivos auxiliares próprios DEVEM seguir o padrão de seu tipo, definido em [Nomes dos documentos](01Padroes.md#nomes-dos-documentos).

## Arquivos e linguagens permitidos

O código próprio de produção, testes, apoios e automação DEVE usar exclusivamente as linguagens abaixo, nos destinos de [Estrutura](02Estrutura.md). A extensão não autoriza armazenar conteúdo de outra linguagem nem criar um arquivo fora da responsabilidade de sua pasta.

| Linguagem | Conteúdo permitido |
| --- | --- |
| PHP | Aplicação, testes, apoios, ganchos e rotinas próprias de automação |
| JavaScript | Código do navegador e testes diretamente relacionados a esse código |
| CSS | Estilos próprios destinados ao navegador |
| HTML | Marcação das interfaces e documentos entregues ao navegador, inclusive quando produzida por PHP |
| SQL | Esquemas e cargas de banco nos destinos correspondentes |

É PROIBIDO acrescentar código próprio em outras linguagens, inclusive scripts de shell, PowerShell ou Python, mesmo como apoio de desenvolvimento ou de testes. Rotinas próprias de automação usam PHP; programas externos necessários são chamados diretamente, sem inserir rotinas de outra linguagem em arquivos PHP ou de configuração.

Documentos Markdown, imagens, fontes, SVG, arquivos de dados e formatos declarativos de configuração não constituem permissão para outra linguagem de programação. Sua inclusão segue a finalidade e os destinos definidos em [Estrutura](02Estrutura.md); conteúdo executável embutido continua sujeito à tabela acima.

### Arquivos exigidos por ferramentas e plataformas

Um formato, nome ou local diferente do padrão próprio é PERMITIDO exclusivamente quando exigido por ferramenta ou plataforma efetivamente adotada e identificado em seu documento, conforme [Grafias externas](01Padroes.md#grafias-externas) e [Arquivos das ferramentas](01Padroes.md#arquivos-das-ferramentas). A exceção abrange somente a exigência concreta; não libera outros scripts ou linguagens por conveniência.

O formato, o nome e o local exigidos DEVEM ser preservados. É PERMITIDO editar os valores e as instruções de configuração aceitos pela ferramenta, mantendo a sintaxe exigida e as linguagens próprias permitidas. Essa permissão não exige conservar imutável o conteúdo de uma configuração nem autoriza inserir nela código próprio de outra linguagem.

Código original de terceiro em linguagem diferente somente é PERMITIDO quando integra a distribuição original e é exigido por ferramenta ou plataforma adotada, com origem e exigência identificadas na ficha ou no documento correspondente. Esse código DEVE permanecer original, sem conversão, reescrita ou formatação própria. Atualizações substituem a distribuição pela versão oficial identificada. Código próprio acrescentado em uma distribuição continua sujeito às linguagens permitidas e aos cuidados de [Recursos de terceiros](#recursos-de-terceiros).

## Documentação do projeto

Os documentos de `Documentacao` apresentam a organização, os padrões e os limites arquiteturais do projeto. Detalhamentos DEVEM descrever somente componentes existentes e seu funcionamento comprovado, sem antecipar funcionalidades.

Esses documentos DEVEM explicar:

- Para que cada conjunto serve;
- Quais funcionalidades oferece;
- Como utilizá-lo.

### Memorial do projeto

O `README.md` da raiz DEVE apresentar exclusivamente o projeto, sua identificação, o responsável, a licença e o caminho de leitura da documentação. É PROIBIDO usar essa apresentação como histórico de implementação, registro de decisões, inventário do estado atual ou roteiro operacional.

O histórico do projeto, as decisões registradas, suas justificativas e o inventário geral do estado atual RESIDEM EXCLUSIVAMENTE EM `Documentacao/Memorial.md`, no destino definido em [Documentacao](02Estrutura.md#documentacao). Cada registro DEVE identificar o fato ou a decisão, seu alcance, a data e o responsável quando conhecidos e as referências disponíveis de commit, pull request, issue ou conferência. É PROIBIDO inventar informações, apresentar uma proposta como adotada ou tratar uma definição documentada como implementação comprovada.

O Memorial DEVE distinguir as definições vigentes, as propostas, o que está implementado e as pendências de implementação ou conferência. Não cria regras nem substitui os numerados, as fichas de instalação, os detalhamentos técnicos ou a documentação operacional. Os metadados atuais e as instruções de uso permanecem no documento responsável; os relatos históricos das conferências são transferidos para o Memorial e referenciados na origem.

As evidências e o acompanhamento de erros permanecem também nas issues e nos pull requests correspondentes; o Memorial referencia esses registros, sem duplicar logs ou dados sensíveis. Uma decisão aceita que altere uma regra DEVE estar refletida no documento responsável pelo assunto e seguir [Atualização e vigência](#atualização-e-vigência).

### Localização do conteúdo e exemplos

Informações de uma área, configuração ou integração ficam no documento do assunto correspondente, com links para as definições relacionadas. `Adaptacoes` recebe as adaptações às regras gerais. Um detalhamento que segue o padrão permanece na documentação do próprio assunto.

Um destino permitido pela organização DEVE ser documentado no assunto correspondente. Uma localização incompatível com os destinos definidos exige o procedimento de [Ampliações & Adaptações](#ampliações--adaptações). É PROIBIDO deduzir a existência ou a obrigatoriedade de um arquivo a partir de um exemplo de escrita.

### Atualização e vigência

Ao preparar uma alteração de regra, adaptação ou funcionalidade, atualizar a documentação do assunto, ajustando as descrições, instruções, exemplos e referências afetados. A preparação local e a aceitação no remoto seguem [Trabalho local e permissões no remoto](Ambiente/GitHub.md#trabalho-local-e-permissões-no-remoto). O conteúdo DEVE explicar a definição proposta, seu alcance e sua forma de aplicação, incluindo condições e restrições quando necessárias.

Toda escrita ou atualização de documento DEVE cumprir as [Leis de escrita](01Padroes.md#leis-de-escrita), inclusive nos trechos anteriores afetados pela alteração. A documentação normativa apresenta o que DEVE ser seguido, sem registrar a discussão das alternativas ou manter a decisão em discussão. Diagnósticos, comparações de trechos e resultados de auditoria DEVEM permanecer no canal de revisão; o histórico e as decisões registrados seguem [Memorial do projeto](#memorial-do-projeto); relatórios locais gerados são evidências de trabalho, não definições arquiteturais. É PERMITIDO incluir explicações que ajudem a compreender e aplicar a definição. Essa orientação vale para regras, adaptações e demais documentos produzidos.

As regras e adaptações vigentes são as incorporadas a `Principal` no repositório remoto. Uma alteração preparada localmente, com commit na branch de desenvolvimento do responsável ou submetida à validação de ambiente após sua integração em `Validacao` permanece uma proposta até sua aceitação e seu merge em `Principal`. Até esse merge, continua vigente a versão de `Principal`. A elaboração e os testes de uma proposta não alteram sua vigência.

### Documentação do código e do projeto

Todo arquivo próprio com conteúdo, em formato que aceite comentários, DEVE conter neles a documentação necessária para entendê-lo, mantê-lo e atualizá-lo. Isso inclui PHP, HTML, CSS, JavaScript, SQL e configurações que permitam comentários. Descrever a finalidade do arquivo e os detalhes aplicáveis ao seu conteúdo, incluindo funções, parâmetros, variáveis, retornos, dependências, falhas, efeitos e cuidados de manutenção. Manter cada explicação próxima do trecho correspondente e atualizá-la quando o conteúdo mudar.

Para formatos que não aceitam comentários, manter no Markdown do assunto correspondente as informações necessárias para seu uso ou manutenção, identificando o caminho do arquivo. Imagens próprias e outros ativos visuais sem instruções adicionais de uso ou manutenção não exigem documentação individual nem um catálogo de seus arquivos. Cuidados específicos dos arquivos públicos ficam em `Documentacao/Publico`, conforme [Documentacao](02Estrutura.md#documentacao). As fichas e as licenças dos recursos de terceiros continuam obrigatórias nos destinos definidos em [Externos](05Externos.md).

A documentação do projeto descreve a finalidade do conjunto, as funcionalidades oferecidas e como os arquivos trabalham juntos. Ela apresenta os fluxos, a configuração e as operações disponíveis, com exemplos de uso quando necessários. Os detalhes internos permanecem nos comentários do próprio arquivo quando o formato permitir.

Nos arquivos PHP, a documentação interna DEVE identificar finalidade, parâmetros, variáveis, retorno, falhas e cuidados de alteração aplicáveis. O Markdown do assunto DEVE explicar apenas as responsabilidades e as relações do conjunto existente.

Usar referências aos arquivos para permitir a consulta dos detalhes, sem reproduzir seus comentários na documentação do projeto. Ao alterar uma implementação, atualizar os comentários e seu estado de revisão conforme [Revisão da documentação](#revisão-da-documentação). Se a mudança afetar o comportamento ou o uso do conjunto, preparar também a atualização documental.

### Arquivos entregues ao usuário

HTML, CSS e JavaScript DEVEM ser entregues ao usuário minificados, e SVG próprios DEVEM ser entregues sem comentários. Os arquivos fonte documentados dos ativos próprios DEVEM residir na família correspondente de `Publico/Ativos`, com o acesso direto ao conteúdo fonte bloqueado; sua entrega sem esse processamento é PROIBIDA. A localização física não autoriza a entrega.

A produção do conteúdo entregue DEVE ser documentada na ferramenta adotada ou no código responsável em `Aplicacao`. O procedimento DEVE identificar os originais, os arquivos gerados, seus caminhos e a conferência do resultado. Ativos DEVEM ser processados antes da entrega estática; o HTML dinâmico DEVE ser minificado durante a composição da resposta. Arquivos gerados em disco DEVEM ser arquivos distintos dos originais, na mesma família, nomeados conforme [Ativos públicos](01Padroes.md#ativos-públicos).

### Revisão da documentação

Toda a documentação própria do projeto segue a mesma regra de revisão, incluindo documentos Markdown e comentários em todos os formatos que os aceitem.

Os campos `Redator`, `Data de criação`, `Status` e `Data do Status` DEVEM constar:

- no cabeçalho de cada documento Markdown, conforme [Estrutura dos documentos](01Padroes.md#estrutura-dos-documentos);
- no comentário de apresentação dos arquivos que aceitam comentários, com a sintaxe do formato, conforme [Documentação nos arquivos com comentários](01Padroes.md#documentação-nos-arquivos-com-comentários), referindo-se à documentação do arquivo fonte;
- no documento Markdown que documenta um formato sem suporte a comentários.

O campo `Status` identifica o estado de revisão da versão atual do documento:

| Estado | Fase da revisão documental |
| --- | --- |
| Sem revisão | Escrita ou correção anterior ao início da revisão; a versão atual ainda precisa ser revisada |
| Em revisão | Revisão da versão atual em andamento, incluindo as correções dos erros encontrados nessa fase |
| Revisado | Revisão da versão atual concluída |

O merge em `Validacao` e `Principal` exige o estado de revisão `Revisado` nos documentos incluídos ou alterados; as branches de desenvolvimento também aceitam documentos `Sem revisão` e `Em revisão`. A conferência dessa exigência segue [Encaminhamento & Integração](#encaminhamento--integração). É PERMITIDO ao próprio autor fazer a revisão, conforme [Trabalho local e permissões no remoto](Ambiente/GitHub.md#trabalho-local-e-permissões-no-remoto).

A cada mudança de estado de revisão, atualizar `Status` e `Data do Status`, registrando a data em que a mudança ocorreu. As transições são:

- **Documento novo:** marcar `Sem revisão`, com a data da criação.
- **Alteração de documento `Revisado`:** marcar a nova versão como `Sem revisão`, com a data do início da alteração. É PERMITIDO preparar o conteúdo antes da abertura do pull request, que registra o conteúdo alterado conforme [Comunicação de pendências](Ambiente/GitHub.md#comunicação-de-pendências).
- **Escrita ou correção:** manter `Sem revisão` e preservar `Data do Status` enquanto esse estado não mudar.
- **Início da revisão:** encerrada a escrita ou a correção, marcar `Em revisão` com a data de início.
- **Correções da revisão:** os erros encontrados na revisão em andamento são corrigidos mantendo `Em revisão` e sua data de início. Isso inclui acrescentar o conteúdo necessário para resolver erros, contradições ou lacunas apontados nessa revisão; os trechos incluídos ou afetados precisam ser conferidos antes da conclusão.
- **Conteúdo novo durante a revisão:** se um documento `Em revisão` receber conteúdo novo ou outra alteração fora dessas correções, marcar `Sem revisão` com a data de início dessa alteração. Encerrada a escrita, revisar novamente a versão resultante, conferindo também os trechos anteriores afetados.
- **Conclusão:** marcar `Revisado` com a data de conclusão.

Se a alteração criar uma regra geral nova ou adaptação, incluir no pull request os critérios e o registro previstos em [Ampliações & Adaptações](#ampliações--adaptações).

Incorporar as correções decorrentes da revisão ao texto do documento, mantendo as observações e os resultados da conferência no canal de revisão. A preservação da identificação do redator e das datas segue [Manutenção dos documentos](#manutenção-dos-documentos).

Em arquivo com comentários `Revisado`, uma alteração de código preserva o estado de revisão quando nenhum comentário se torna incorreto ou incompleto. Se algum comentário precisar mudar, atualizar a documentação, marcar a nova versão como `Sem revisão` e seguir as transições acima. Scripts de banco já incorporados a `Principal` seguem a preservação do arquivo e a correção documental de [Cuidados de manutenção](#cuidados-de-manutenção). Apresentar as mudanças no pull request. A marcação `Revisado` dos comentários não comprova a aprovação das verificações do código.

#### Encaminhamento & Integração

- Durante a escrita ou a revisão, o documento permanece na branch de desenvolvimento do responsável no estado de revisão `Sem revisão` ou `Em revisão`. Sua submissão para integração em `Validacao` exige concluir a revisão.
- Antes de aceitar um pull request para `Validacao` ou `Principal`, conferir o estado de revisão dos documentos incluídos ou alterados. Se algum estiver `Sem revisão` ou `Em revisão`, manter o merge pendente até a conclusão da revisão. Documentos de outras entregas que não façam parte do pull request não impedem seu merge.
- O responsável pela integração aceita ou recusa o conteúdo conforme suas permissões na plataforma. O merge só ocorre após a aceitação, conforme [Branches do projeto](Ambiente/GitHub.md#branches-do-projeto).
- O documento incorporado mantém o estado de revisão correspondente à sua versão. Regras e adaptações só passam a vigorar quando incorporadas a `Principal`.
- Se forem necessárias correções, registrá-las no pull request e repetir as etapas documentais e verificações afetadas antes do merge.

#### Alterações durante a revisão

Ao apresentar um ajuste durante a revisão, informar todos os pontos modificados no canal em que o trabalho estiver sendo revisado, como o pull request, a issue ou a conversa de revisão. A indicação DEVE permitir conferir os trechos afetados sem reler o arquivo inteiro.

Para cada ponto alterado, informar:

- O título da seção e um link ou indicação da localização do trecho;
- O que foi incluído, alterado, removido ou movido;
- Quais trechos já conferidos precisam ser revistos, quando essa informação estiver disponível.

Essa comunicação também abrange ajustes em partes posteriores ao trecho que está sendo discutido e mudanças em partes já conferidas. Se não for possível saber até onde a revisão avançou, listar todos os pontos alterados sem presumir que algum deles ainda não foi conferido.

Limitar a edição ao ajuste solicitado e às referências afetadas. Quando esse ajuste exigir mudar trechos além daquele em discussão, apontá-los separadamente e explicar sua relação com o ajuste.

### Bloqueio dos documentos numerados

Os documentos numerados são os arquivos da raiz de `Documentacao` cujo nome começa por algarismo, conforme [Nomes dos documentos](01Padroes.md#nomes-dos-documentos). O conteúdo de cada um DEVE corresponder ao SHA-256 registrado em [Conferência dos documentos numerados](Ambiente/GitHub.md#conferência-dos-documentos-numerados). A conferência dos documentos numerados reprova o conjunto quando um documento numerado é alterado, acrescentado ou removido sem a atualização correspondente desse registro. Localmente, o [Gancho de commit](Ambiente/Git.md#gancho-de-commit) recusa o commit que altera um documento numerado ou seu registro sem a autorização do Gerente do projeto.

A alteração de um documento numerado depende de pedido expresso do Gerente do projeto, identificado em [Gerente do projeto](../README.md#gerente-do-projeto). A mudança de estado de revisão documental também altera o documento e segue a mesma condição. O documento alterado e o registro atualizado DEVEM constar do mesmo commit, e o pull request DEVE identificar os documentos numerados alterados e quem pediu a alteração.

É PROIBIDO a agentes de IA alterar documentos numerados ou seu registro de conferência sem esse pedido expresso, inclusive para corrigir trecho que pareça incorreto, ambíguo, incompleto ou contraditório. A dúvida ou a divergência DEVE ser comunicada ao Gerente do projeto ou registrada em issue conforme [Comunicação de pendências](Ambiente/GitHub.md#comunicação-de-pendências), sem editar o documento. Essa condição prevalece, para os documentos numerados, sobre a preparação da correção prevista em [Definições](#definições). É PROIBIDO atualizar o registro apenas para obter aprovação das verificações. É PROIBIDO contornar o gancho de commit, conforme [Gancho de commit](Ambiente/Git.md#gancho-de-commit).

### Manutenção dos documentos

As versões documentais seguem os estados definidos em [Revisão da documentação](#revisão-da-documentação); sua incorporação no remoto segue [Trabalho local e permissões no remoto](Ambiente/GitHub.md#trabalho-local-e-permissões-no-remoto).

Preservar nas revisões a identificação da pessoa responsável pelo texto no campo `Redator` e a data de criação conhecidas, conforme [Cabeçalho](01Padroes.md#cabeçalho). Preservar os autores e as contribuições conhecidos no histórico; o uso de assistência de ferramentas não as torna responsáveis pelo documento. Renomear um documento não altera sua data de criação. Quando faltarem esses metadados, consultar o histórico de versões para recuperar as informações. Indicar como `Pendente` o que ainda não estiver confirmado, sem inventar responsáveis ou datas.

Se uma seção não se aplica, omiti-la. Se falta informação necessária, registrar `Pendente` e descrever no próprio documento o que falta. Marcas `Pendente` não impedem o merge de um documento `Revisado`, salvo quando a informação pendente for necessária a uma verificação exigida para o conteúdo da entrega ou à análise de uma ampliação ou adaptação para sua aceitação em `Principal`. Identificar explicitamente recursos definidos e ainda não implementados. Propostas ainda não adotadas não integram as definições vigentes.

Ao resolver uma pendência, atualizar a descrição de uso e retirar do documento a marca `Pendente` correspondente. Ao dividir um documento, manter uma apresentação breve e links para os detalhes no documento geral. Ao mover arquivos, revisar links de entrada e saída. Ao remover um documento, atualizar os links e as referências que apontam para ele; a versão anterior permanece no histórico do Git.

### Pastas sem conteúdo

O `README.md` de uma pasta própria existe somente para manter no Git uma pasta sem outros arquivos versionados, nela ou em suas subpastas. Quando a pasta receber o primeiro arquivo versionado, remover o `README.md` e atualizar suas referências. Se uma pasta fixa, uma área ou uma pasta declarada existente no documento de uma ferramenta de ambiente perder o último arquivo versionado, restaurar seu `README.md`.

O `README.md` apresenta a finalidade da pasta em uma frase e aponta para a seção do documento geral ou do documento de ferramenta de ambiente que define seu uso. Ele não documenta assuntos, regras nem contratos. Seu formato segue [README de pasta sem conteúdo](01Padroes.md#readme-de-pasta-sem-conteúdo).

Ficam fora dessa regra o `README.md` da raiz, que apresenta o projeto, e os READMEs e documentos distribuídos por terceiros, que permanecem junto da distribuição.

Em pastas cujo conteúdo é ignorado pelo Git, como os destinos de `Dados/Arquivos`, o `README.md` permanece como seu único arquivo versionado. As regras de exclusão DEVEM permitir seu versionamento.

As definições gerais das pastas padrão ficam nos documentos numerados. Detalhamentos de componentes e testes só são acrescentados quando houver implementação a descrever. Registros de adaptações seguem [Registro de adaptação](#registro-de-adaptação); os demais assuntos seguem [Onde documentar cada assunto](#onde-documentar-cada-assunto). Pastas e arquivos gerados durante a execução não são versionados, conforme [Dados privados e armazenamentos](#dados-privados-e-armazenamentos).

## Manutenção de código e dados

### Cuidados de manutenção

#### Alterações em geral

- Alterações de comportamento DEVEM identificar os contratos, exemplos e documentos afetados. Preparar as atualizações documentais, apresentar seu alcance no pull request, comunicar pendências conforme [Comunicação de pendências](Ambiente/GitHub.md#comunicação-de-pendências) e conferir a [Revisão da documentação](#revisão-da-documentação).
- Ao mover ou renomear um elemento, revisar usos, espaços de nomes, carregamento, links, configurações e automações.
- Antes de alterar contratos públicos, valores persistidos ou formatos de dados, avaliar o impacto nas partes da aplicação e nos sistemas externos que os utilizam, além da necessidade de migração.

#### Scripts de banco

- A etapa de um script é identificada pelas branches em que seu conteúdo está versionado: ele é preparado na branch de desenvolvimento do responsável, incorporado a `Validacao` para a validação de ambiente e, após aprovação, incorporado a `Principal` com o mesmo conteúdo validado. Um script é de produção a partir de sua incorporação a `Principal`. O merge dos scripts segue a seleção de entregas e as conferências de [Branches do projeto](Ambiente/GitHub.md#branches-do-projeto).
- Enquanto o script não estiver incorporado a `Principal`, sua alteração ou renomeação é PERMITIDA. Essas alterações são feitas na branch de desenvolvimento do responsável e seguem novamente para `Validacao` antes da incorporação a `Principal`.
- O conteúdo e o nome do script ficam fixos a partir de sua primeira incorporação a `Principal` no remoto e DEVEM ser preservados nas branches oficiais, incluindo comentários e metadados. Depois disso, correções de código exigem novos scripts na sequência de aplicação, começando na branch de desenvolvimento do responsável.
- Conferir se o script já foi incorporado a `Principal` antes de apresentar sua alteração ou renomeação para merge.

#### Instalação e atualização de banco

Documentar a instalação ou atualização de um banco e sua recuperação em `Documentacao/Aplicacao`, no detalhamento da área ou do componente responsável pelo banco. Identificar os scripts, a ordem de aplicação, as versões de código compatíveis, os efeitos e os requisitos de cópia de segurança, referenciando os parâmetros do ambiente descritos em [Banco e armazenamento](03Configuracoes.md#banco-e-armazenamento). O pull request aponta para esse detalhamento.

Manter as instruções enquanto forem necessárias às operações de instalação, atualização ou recuperação previstas para o componente, indicando o estado do banco e as versões a que se aplicam. A execução em um ambiente não encerra sua aplicação aos demais; os resultados de execuções específicas ficam nos logs do respectivo ambiente. Quando mudanças no componente tornarem um procedimento inaplicável, retirar suas instruções da documentação atual, atualizar as referências e preservar a versão anterior no histórico do Git.

Para instalar ou atualizar um banco:

1. **Conferir o banco atual.** Identificar a versão de instalação, as manutenções já executadas e as dependências dos scripts a aplicar.
2. **Organizar os scripts.**
   - Definir a ordem de aplicação conforme [Esquemas e cargas](02Estrutura.md#esquemas-e-cargas).
   - Antes de abrir cada pull request de promoção para `Validacao` ou `Principal`, conferir novamente a sequência do destino e resolver arquivos com nomes repetidos e scripts acrescentados fora da ordem.
   - Se a sequência já estiver em produção, acrescentar novos scripts compatíveis ao final dela.
   - Se a promoção seletiva mudar a ordem antes testada, preparar os ajustes na branch de desenvolvimento do responsável, reapresentá-los a `Validacao` e testar a sequência prevista para produção sobre um banco que represente seu estado anterior.
   - A data no nome, definida em [Nomes dos scripts de banco de dados](01Padroes.md#nomes-dos-scripts-de-banco-de-dados), não permite repetir um script nem executá-lo como se tivesse sido aplicado antes dos demais.
3. **Definir a ordem das mudanças de código e banco.** Quando a alteração envolver ambos, indicar qual é atualizado primeiro, quais versões funcionam juntas durante a atualização e em quais situações a execução DEVE ser interrompida.
4. **Preparar a recuperação.**
   - Descrever como desfazer a manutenção ou restaurar o banco se houver perda irreversível, incluindo os casos em que apenas parte do script for executada e os comandos sem reversão por transação.
   - Em produção, exigir uma cópia de segurança do banco antes de cada manutenção, com identificação do estado anterior e conferência da restauração a partir dessa cópia. Alterações destrutivas DEVEM destacar essa obrigatoriedade na documentação da atualização e indicar o ponto de recuperação.
   - Nos ambientes isolados de desenvolvimento e de validação, a cópia de segurança é OPCIONAL; é PERMITIDO recuperar o estado pela recriação do ambiente isolado e de seus dados de teste.
   - Se for necessário voltar a uma versão anterior do código, conferir sua compatibilidade com o banco. Se o banco for restaurado, identificar a versão de código correspondente. Recriar tabelas vazias não recupera dados perdidos.
5. **Testar a alteração.** Usar um banco de teste que represente a versão anterior. Aplicar as mudanças na ordem definida e conferir, após cada etapa, a estrutura, os dados e o funcionamento das operações da aplicação que dependem do banco.
6. **Executar a alteração no banco de destino.** Em produção, confirmar a existência e a identificação da cópia de segurança exigida no passo 4 antes de executar a manutenção. Registrar os scripts e suas versões de conteúdo, o estado anterior do banco e o início da execução. Impedir execuções simultâneas capazes de interferir entre si e usar transação quando os comandos e o banco permitirem.
7. **Conferir e registrar o resultado.** Verificar a estrutura, os dados e as operações afetadas e registrar a conclusão ou a falha da execução. Um script estar em `Principal` não comprova que ele foi executado no banco.

**Se houver falha ou interrupção:** registrar a falha nos logs do ambiente, com destino definido em [Banco e armazenamento](03Configuracoes.md#banco-e-armazenamento), e conferir quais alterações realmente foram aplicadas antes de repetir scripts, desfazer alterações ou restaurar o banco. Seguir a recuperação preparada no passo 4 e, ao terminar, conferir e registrar a estrutura e os dados recuperados. Quando a falha decorrer de um erro de código, abrir também uma issue conforme [Comunicação de pendências](Ambiente/GitHub.md#comunicação-de-pendências).

#### Dados privados e armazenamentos

- É PROIBIDO incluir dados de execução, credenciais e configurações privadas no controle de versão. As exclusões DEVEM acompanhar os caminhos reais; é PERMITIDO versionar modelos e dados fictícios de teste.
- A remoção de cache, logs, uploads e bancos DEVE respeitar a finalidade de cada armazenamento. Estar em `Dados/Arquivos` não torna todo conteúdo descartável.
- Para alterações que produzam efeitos em armazenamentos além do banco, identificar os dados afetados e descrever a recuperação no detalhamento do componente responsável:
  - Nos testes locais e no ambiente de validação, usar recursos isolados e documentar sua preparação e limpeza em `Documentacao/Testes`, admitindo a recriação de arquivos, serviços e dados fictícios, conforme [Categorias e isolamento dos cenários](#categorias-e-isolamento-dos-cenários). A reutilização da preparação entre testes é PERMITIDA, e a cópia de segurança desses recursos descartáveis é OPCIONAL.
  - Quando os dados precisarem ser preservados, descrever como recuperar seu estado anterior, os efeitos irreversíveis e a compatibilidade com o código correspondente.
  - Depois da recuperação ou recriação, conferir os dados e as operações afetadas. Reverter o código não recupera arquivos nem desfaz efeitos em serviços externos.

#### Casos especiais

**Script alterado depois de executado.** Se um script for alterado ou renomeado antes de entrar em `Principal`, conferir se a versão anterior já foi executada e em quais bancos. Antes de testar a nova versão nesses bancos, restaurá-los ao estado anterior à execução da versão antiga, conforme a recuperação preparada no passo 4 da sequência de instalação ou atualização. Registrar as versões executadas e os resultados das verificações na mensagem do commit correspondente. Renomear o arquivo não desfaz as alterações que ele já fez no banco.

**Correção documental em script de produção.** Uma correção apenas documental em um script já incorporado a `Principal` DEVE ser registrada no Markdown do assunto correspondente, identificando o script, sua versão incorporada, o trecho incorreto e a informação corrigida. A revisão e os metadados da correção ficam nesse documento. Preservar o arquivo histórico; não criar um novo script executável apenas para corrigir seus comentários. Essa condição prevalece sobre a atualização dos comentários no próprio arquivo.

**Conteúdo privado versionado.** Ao detectar inclusão anterior de conteúdo privado, retirar esse conteúdo dos arquivos preparados para o merge. Guardar os caminhos e as versões afetados em armazenamento privado com acesso restrito, fora do controle de versão e da entrega pública, sem copiar o conteúdo privado. Quando houver um ambiente envolvido, utilizar seus logs protegidos, conforme [Banco e armazenamento](03Configuracoes.md#banco-e-armazenamento); é PERMITIDO registrar uma detecção apenas local em armazenamento privado da própria máquina.

- Se a inclusão alcançar o remoto, comunicar a quem tenha permissão para administrar seu histórico. Quando houver credencial exposta, acionar também quem tem permissão para revogá-la ou substituí-la no serviço correspondente.
- Usar um canal de acesso restrito disponibilizado pela plataforma ou identificado na documentação operacional do ambiente e conferir quem tem acesso de leitura a ele antes de apresentar os detalhes.
- Issues e pull requests de correção incluem apenas informações adequadas ao público que tem acesso a eles; evidências restritas permanecem em seu armazenamento protegido.
- A administração do histórico remoto e a identificação dos perfis autorizados a atuar nele seguem as permissões e os procedimentos da plataforma.
- Seguir também [Resposta a incidentes](#resposta-a-incidentes) quando houver exposição de credenciais ou outras informações protegidas.
- Exclusões posteriores não comprovam retirada do conteúdo já versionado. A remoção por novos commits segue [Commits e movimentação entre branches](Ambiente/GitHub.md#commits-e-movimentação-entre-branches).

## Recursos de terceiros

A regra geral é não usar recursos de terceiros. A adoção de um recurso de terceiros incluído no projeto, como biblioteca, ferramenta, fonte, ícone ou outro arquivo, exige justificativa forte registrada no Memorial e referenciada no campo `Justificativa` de sua ficha ou do documento da ferramenta. Programas do ambiente que não são incluídos no projeto, como servidor, contêineres, banco ou extensões do PHP, não exigem essa justificativa. Os que acrescentam pastas ou arquivos ao projeto são documentados conforme [Ferramentas de ambiente](02Estrutura.md#ferramentas-de-ambiente).

### Licenças

Recursos de terceiros DEVEM ter licença que permita sua utilização, distribuição e comercialização junto com o projeto, sem exigir a divulgação do código-fonte próprio do projeto. **MIT**, **Apache 2.0** e **BSD** são exemplos de licenças que atendem a esse critério; é PERMITIDO aceitar outras licenças que atendam às mesmas condições.

Antes de incluir ou atualizar recursos de terceiros, conferir a licença da versão a utilizar e das dependências fornecidas junto dela, considerando como os recursos são integrados e distribuídos. Registrar a identificação e a compatibilidade da licença na ficha ou no registro de instalação; o histórico da conferência segue [Memorial do projeto](#memorial-do-projeto). Se a licença não permitir essas condições ou sua identificação for impossível, descartar os recursos; é PROIBIDO incorporá-los ao projeto.

Preservar os avisos de autoria e os textos das licenças, cumprindo as demais condições exigidas para o uso e a distribuição dos recursos de terceiros.

## Atualização de ferramentas e dependências

Para atualizar uma ferramenta ou dependência, seguir esta sequência:

1. **Conferir as versões e a instalação atual.** Identificar a versão em uso e a versão a instalar, a origem, a licença, os requisitos, as integrações afetadas e a configuração atual. Para recursos de terceiros, confirmar que a licença atende às [Licenças](#licenças) admitidas.
2. **Preparar a nova versão.** Reunir o conjunto completo dos arquivos necessários, preservando a identificação e a licença do conteúdo de terceiros.
3. **Substituir a instalação.** Aplicar o conjunto preparado, evitando substituir apenas parte da instalação em uso.
4. **Conferir a atualização.** Verificar a versão efetivamente instalada, a instalação e as integrações afetadas antes de declarar a atualização concluída.
5. **Registrar o resultado.** Atualizar a versão instalada na ficha da dependência ou no registro de instalação da ferramenta e registrar o histórico da substituição e das verificações no Memorial. Se houver modificações locais permitidas, identificar na ficha ou no documento da ferramenta os arquivos afetados, o alcance e como reaplicar ou remover essas modificações na próxima atualização; referenciar os resultados dos testes registrados no Memorial.

**Se houver falha:** registrar no Memorial o ocorrido e o estado observado e restaurar os arquivos da versão anterior. Manter na ficha ou no registro de instalação os dados da versão efetivamente presente após a recuperação. Se a causa for um erro de código, abrir uma issue no repositório remoto. É PROIBIDO declarar concluída ou aprovada uma substituição incompleta.

Alterações locais em arquivos da distribuição, quando permitidas por [Arquivos e linguagens permitidos](#arquivos-e-linguagens-permitidos), DEVEM permanecer identificadas em relação ao original e atender às condições de licença e atualização. Essa possibilidade não autoriza instalar código independente da aplicação junto do fornecedor: a integração própria DEVE residir em `Aplicacao`, conforme [Integração com a aplicação](05Externos.md#integração-com-a-aplicação). Código próprio de produção acrescentado ou alterado em uma distribuição de terceiros segue as verificações e a medição definidas em [Alcance e evidências da cobertura](#alcance-e-evidências-da-cobertura).

Ferramentas que modificam arquivos automaticamente DEVEM atuar somente nos arquivos próprios do projeto. Arquivos gerados a partir de recursos de terceiros para entrega ao usuário DEVEM ser arquivos distintos dos originais, que são preservados; é PERMITIDO mantê-los na mesma pasta da distribuição. Para fontes, é PERMITIDO manter somente o arquivo convertido para um formato da família `Fontes` quando a licença autorizar a conversão e ela alterar apenas o formato, sem mudar nomes, avisos ou dados da fonte; a ficha DEVE identificar os arquivos e os resumos SHA-256 do original e do convertido e referenciar no Memorial o registro da conversão, da ferramenta utilizada e da conferência. Conferir as alterações realizadas e executar as verificações de funcionamento aplicáveis, mesmo quando a ferramenta apenas ajustar a formatação.

## Segurança do código

Todo código DEVE ser planejado, escrito, revisado e mantido considerando a segurança desde o início. Essa exigência abrange a aplicação, os scripts, as configurações, as integrações e as dependências utilizadas.

O projeto DEVE cumprir todas as obrigações de proteção de dados aplicáveis ao seu funcionamento, incluindo a Lei Geral de Proteção de Dados Pessoais — LGPD e as normas vigentes da ANPD. As medidas de segurança DEVEM estar presentes desde a definição de uma funcionalidade até sua execução e manutenção. Uma adaptação do projeto não dispensa o cumprimento da legislação.

### Desenvolvimento seguro

Antes de implementar ou alterar uma funcionalidade, identificar suas entradas, os dados envolvidos, os acessos autorizados e os possíveis usos indevidos. Registrar os requisitos e os controles necessários no canal de revisão durante a preparação dos testes. O detalhamento do componente DEVE descrever os controles efetivamente implementados e suas condições de uso, conforme [Documentação do projeto](#documentação-do-projeto).

Aplicar os controles de segurança abaixo conforme a funcionalidade:

- **Acesso:** conceder apenas as permissões necessárias e conferir a autorização em cada operação e acesso a um recurso. É PROIBIDO a um usuário autenticado acessar dados de outro usuário sem permissão. Ocultar um botão ou separar uma pasta não substitui a autorização no servidor.
- **Entradas e saídas:** validar no servidor os tipos, formatos, tamanhos e valores recebidos. Usar parâmetros nas consultas ao banco e codificar as saídas conforme seu destino, evitando injeção de comandos e execução de conteúdo recebido como código.
- **Autenticação:** armazenar senhas com funções de hash específicas para armazenamento seguro de senhas. Proteger sessões, expirar tokens, invalidar sessões encerradas e limitar tentativas de acesso. Proteger operações autenticadas por cookies contra requisições forjadas.
- **Comunicação e armazenamento:** usar conexões criptografadas com certificados verificados. Definir a proteção dos dados armazenados conforme seu risco e proteger as chaves utilizadas. Não criar algoritmos próprios de criptografia.
- **Arquivos e integrações:** limitar tipos, tamanhos, caminhos e destinos aceitos. Impedir execução de arquivos enviados por usuários e acesso a arquivos ou serviços fora dos destinos permitidos.
- **Credenciais e logs:** manter segredos fora do código versionado. Obter credenciais pelo mecanismo de configuração do ambiente, sem exemplos que exponham segredos na linha de comando. Não expor senhas, tokens ou dados pessoais desnecessários em respostas, mensagens de erro e logs. Restringir o acesso aos logs necessários.
- **Recursos e dependências:** limitar operações capazes de consumir recursos de forma abusiva. Conferir vulnerabilidades conhecidas nas dependências e corrigir as que afetem o uso realizado pelo projeto.

### Proteção de dados pessoais

Dado pessoal é uma informação que identifica uma pessoa ou permite identificá-la, inclusive quando combinada com outras informações. Antes de implementar seu tratamento, seguir estas etapas:

1. **Definir o tratamento.** Identificar os dados, a finalidade, o fundamento jurídico e os responsáveis pelo tratamento. Registrar esses requisitos no canal de revisão antes da implementação e refletir o tratamento implementado na documentação da funcionalidade. Se o fundamento jurídico não estiver definido, não iniciar o tratamento.
2. **Limitar a coleta e o uso.** Tratar somente os dados necessários à finalidade informada, manter sua exatidão e impedir usos incompatíveis ou discriminatórios ilícitos.
3. **Informar o titular.** Apresentar informações claras sobre uso, duração, compartilhamento e responsáveis. Quando o fundamento jurídico for consentimento, registrar sua manifestação e permitir sua revogação.
4. **Atender aos direitos.** Disponibilizar meios seguros para exercer os direitos aplicáveis, incluindo acesso, correção, portabilidade, bloqueio e eliminação. Conferir a identidade do solicitante antes de fornecer ou alterar seus dados.
5. **Definir a retenção.** Estabelecer os prazos e implementar a eliminação ou anonimização dos dados que deixarem de ser necessários, incluindo o tratamento das cópias de segurança. Respeitar as hipóteses legais de conservação.
6. **Conferir o compartilhamento.** Identificar destinatários, finalidade e proteção dos dados enviados a terceiros. Transferências internacionais DEVEM atender às condições legais e à regulamentação aplicável.
7. **Tratar os casos especiais.** Aplicar as exigências específicas para dados sensíveis, crianças e adolescentes e decisões automatizadas. Registrar as operações de tratamento e preparar relatórios de impacto quando exigidos.

As definições de tratamento ficam na documentação da funcionalidade; os parâmetros de proteção ficam na documentação da configuração correspondente. Identificar também os responsáveis pelos procedimentos administrativos necessários ao cumprimento dessas definições. Os requisitos DEVEM ser revistos quando mudarem a finalidade, os dados, as integrações ou as normas aplicáveis.

### Verificação da segurança

Antes de aceitar o merge em `Validacao` de uma alteração que afete a execução:

1. Revisar os controles de segurança e os requisitos de proteção de dados afetados.
2. Testar entradas maliciosas, acessos sem permissão, acesso a dados de outros usuários, falhas de integração e exposição indevida de informações, conforme o alcance da alteração.
3. Conferir os procedimentos aplicáveis de acesso, correção, eliminação e recuperação de dados.
4. Registrar as verificações e seus resultados conforme [Verificação de uma alteração](#verificação-de-uma-alteração), cumprindo também as exigências de [Testes e cobertura integral](#testes-e-cobertura-integral).

Falhas de segurança conhecidas e requisitos obrigatórios de proteção de dados não atendidos no conjunto de código a incorporar impedem seu merge em `Validacao` ou `Principal`. Alterações exclusivamente documentais seguem as conferências de [Verificação de uma alteração](#verificação-de-uma-alteração), incluindo a proteção das informações presentes nos documentos. A conformidade exige conferir também as configurações e os procedimentos de operação; testes do código, isoladamente, não comprovam o cumprimento de todas as obrigações legais.

### Resposta a incidentes

Ao identificar um incidente de segurança:

1. Conter a exposição e preservar as evidências, sem divulgar dados sensíveis.
2. Registrar o ocorrido, os dados e componentes afetados e as medidas adotadas nos logs do ambiente, com acesso restrito, conforme [Banco e armazenamento](03Configuracoes.md#banco-e-armazenamento). Quando não houver ambiente envolvido, registrar no armazenamento privado indicado em [Cuidados de manutenção](#cuidados-de-manutenção), no caso “Conteúdo privado versionado”. Se houver erro de código, abrir uma issue no repositório remoto sem reproduzir dados sensíveis ou evidências de acesso restrito.
3. Acionar o responsável pelo tratamento para avaliar riscos e cumprir as comunicações à ANPD e aos titulares quando exigidas, nos prazos e condições previstos na regulamentação vigente.
4. Corrigir a causa, conferir a recuperação e testar as proteções antes de retomar as operações afetadas.

### Fontes de consulta

A consulta às fontes abaixo é OPCIONAL; as obrigações de segurança desta seção permanecem obrigatórias. Ao aplicar orientações dessas fontes, seguir [Idioma do projeto](#idioma-do-projeto), adequando os textos e nomes recomendados ao português do Brasil e respeitando as exceções ali definidas.

- [LGPD — texto da lei](https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2018/lei/l13709compilado.htm).
- [ANPD — normas vigentes](https://www.gov.br/anpd/pt-br/acesso-a-informacao/institucional/atos-normativos/regulamentacoes_anpd).
- [ANPD — comunicação de incidentes de segurança](https://www.gov.br/anpd/pt-br/canais_atendimento/agente-de-tratamento/comunicado-de-incidente-de-seguranca-cis).
- [OWASP — princípios de segurança](https://devguide.owasp.org/en/02-foundations/03-security-principles/).
- [OWASP — guia de revisão de código seguro](https://cheatsheetseries.owasp.org/cheatsheets/Secure_Code_Review_Cheat_Sheet.html).

## Testes e cobertura integral

O código próprio de produção DEVE atingir 100% de cobertura por testes. Seu cumprimento se comprova exclusivamente pelas evidências de [Alcance e evidências da cobertura](#alcance-e-evidências-da-cobertura). Essa exigência abrange o código próprio de produção e os comportamentos definidos para a aplicação, incluindo segurança e proteção contra regressões: falhas introduzidas por alterações em algo que já funcionava.

### Categorias e isolamento dos cenários

Cada cenário prepara entradas e dependências, executa a operação e confere resultados e efeitos observáveis do contrato, como sucesso, falha ou interação relevante. Os testes unitários DEVEM verificar uma unidade com dependências controladas, sem depender de banco, rede ou dados reais da instalação. É PERMITIDO verificar conversão de valores, formatos e serviços com persistência substituída. Substitutos compartilhados e dados fictícios consumidos diretamente pelo código dos testes DEVEM ficar em `Testes/Apoio`; é PERMITIDO manter junto do cenário os recursos usados exclusivamente por ele. Scripts de carga de dados fictícios aplicados a bancos de teste DEVEM ficar em `Dados/Cargas`, com o prefixo `T.`, conforme [Esquemas e cargas](02Estrutura.md#esquemas-e-cargas). Os cenários preservam a independência de ordem e limpam o estado que criaram.

Os testes de integração identificam os participantes reais e os substituídos. É PERMITIDO exercitar persistência, configuração, HTTP, renderização, terminal e integrações externas em ambiente próprio. Um servidor simplificado não comprova a configuração do servidor de implantação; um conector substituído não comprova a disponibilidade do serviço externo. Dados reais de produção e armazenamentos da aplicação em uso não servem como área de teste. O relato distingue sucesso, falha e cenário não executado, indicando a condição ausente quando aplicável.

Cada execução utiliza destinos próprios para arquivos, cache, logs e banco. Execuções paralelas recebem identificações que evitem colisões. A preparação identifica os recursos que criou; a limpeza remove apenas esses recursos. Falhas de preparação e limpeza permanecem visíveis no resultado. Os caminhos não dependem de um diretório de trabalho implícito. O conteúdo dos procedimentos está em [Ambiente de teste](01Padroes.md#ambiente-de-teste), e a execução dos cenários segue o documento da ferramenta de testes indicado em [Funções exigidas](04Ferramentas.md#funções-exigidas).

### Alcance e evidências da cobertura

Manter em `Documentacao/Testes`, no detalhamento da categoria ou do componente responsável, o inventário dos requisitos e contratos vigentes, dos cenários aplicáveis e dos testes que os verificam, identificando lacunas e a referência à última evidência de execução. Os resultados históricos dessas execuções seguem [Memorial do projeto](#memorial-do-projeto).

- **Comportamentos.** Cobrir sucesso, erro, entradas inválidas, limites, permissões, efeitos persistentes e falhas das dependências. Os testes DEVEM conferir resultados e efeitos observáveis, incluindo a ausência de efeitos proibidos, como gravar dados após uma operação recusada. A cobertura de comportamentos é conferida contra esse inventário e permanece separada das métricas de linhas e ramos de decisão. Não declarar cobertura integral enquanto houver cenário obrigatório ausente, não executado ou falho.
- **Linhas e ramos de decisão.** A medição do código DEVE atingir 100% das linhas executáveis e dos ramos de decisão do código próprio de produção. Ramos de decisão são os diferentes caminhos selecionáveis por uma condição no código. Incluir na medição os arquivos ainda não exercitados; medir apenas os arquivos carregados pelos testes não atende à regra.
- **Escopo medido.** A configuração e a documentação das verificações, conforme [Configuração das verificações](04Ferramentas.md#configuração-das-verificações), identificam o código próprio de produção por linguagem, caminho e forma de execução. Incluir inicialização, entradas, visões executáveis, comandos, integrações, scripts do navegador e scripts de banco quando executáveis. Identificar as métricas aplicáveis e as ferramentas que as medem, incluindo os arquivos não exercitados. Conteúdo declarativo recebe verificação própria; a ausência de uma ferramenta capaz de medir código executável não autoriza excluí-lo.
- **Fora do percentual.** Documentação e arquivos sem código executável são conferidos pelas verificações próprias de seu conteúdo. O código dos testes e das ferramentas de desenvolvimento e o código original de terceiros sem modificações próprias não entram no percentual do código de produção; o comportamento das integrações utilizadas DEVE ser testado. Código próprio de produção, incluindo integração, inicialização e implementação de adaptações, faz parte da medição independentemente da pasta em que estiver.
- **Terceiros modificados.** Em arquivos de terceiros modificados localmente, medir as partes de código próprio de produção acrescentadas ou alteradas, identificando essas linhas executáveis e os ramos de decisão em relação à versão original da distribuição. O código original sem alteração não entra na medição. A configuração e os relatórios DEVEM delimitar esse escopo e comprovar a cobertura integral das partes próprias, junto dos testes de seu comportamento e integração.
- **Registro.** Registrar a ferramenta, a configuração, o escopo medido e os relatórios. Quando uma métrica estiver indisponível na medição, registrá-la como não verificada, sem declarar 100%. Utilizar uma ferramenta e uma configuração capazes de medir as métricas e o escopo exigidos; enquanto essa medição não estiver comprovada, é PROIBIDO fazer o merge da alteração de código em `Validacao` ou `Principal`.

Nenhuma adaptação dispensa os testes ou a cobertura integral do código próprio de produção. Verificações alternativas não substituem a medição exigida nem autorizam excluir partes próprias do escopo.

Executar uma linha não comprova que seu resultado foi conferido. Atingir o percentual não dispensa os cenários de comportamento e segurança, nem comprova ausência de todos os defeitos. É PROIBIDO retirar arquivos da medição, eliminar cenários, enfraquecer verificações ou aceitar um resultado incorreto apenas para obter aprovação.

### Testes antes da implementação

Os testes DEVEM ser escritos antes do código novo ou da alteração de código que verificam.

1. Definir o comportamento esperado e os critérios de segurança a partir dos requisitos e contratos. Resolver ambiguidades antes de usá-las como resultado esperado.
2. Escrever os testes correspondentes. Em uma correção, acrescentar primeiro um teste que reproduza o defeito. Em uma alteração, preservar os cenários válidos existentes e acrescentar os novos antes de modificar a implementação.
3. Executar os testes novos e registrar a falha esperada pela ausência do comportamento ou pela reprodução do defeito. Uma falha de configuração ou de preparação do teste não serve como essa evidência. Se o teste já passar, conferir e registrar se o comportamento já existe ou se a verificação é insuficiente.
4. Escrever ou modificar o código para atender aos contratos verificados, preservando os testes válidos.
5. Executar os testes afetados durante o trabalho. Antes de concluir uma alteração que afete a execução, executar a suíte completa e medir a cobertura. Atualizações de dependências também passam por essa conferência.
6. Registrar resultados, falhas, correções, cobertura e limites. Apresentar as evidências disponíveis para revisão.

Em uma refatoração sem mudança de comportamento, executar os testes existentes antes da alteração e completar previamente os cenários que faltarem. Os resultados esperados permanecem os mesmos.

### Tratamento de falhas nos testes

Ao encontrar uma falha:

1. Comparar o resultado com o requisito para identificar o conteúdo que precisa de correção: implementação, teste, configuração ou apoio. Uma falha no teste não demonstra, por si só, que o teste está errado. O teste que reproduz corretamente um defeito permanece; a implementação DEVE ser corrigida.
2. Ao identificar um erro de código, abrir uma issue no repositório remoto, conforme [Comunicação de pendências](Ambiente/GitHub.md#comunicação-de-pendências), e registrar nela a investigação, a correção e a nova execução. A falha inicial esperada de um teste escrito antes da implementação não exige esse registro quando decorrer apenas do comportamento ainda não implementado.
3. Identificar o teste, o requisito e os caminhos afetados, o ambiente, como reproduzir a falha e os resultados esperado e observado, sem dados sensíveis. Enquanto a origem não estiver confirmada, indicar a investigação pendente e preservar as evidências.
4. Conferir os efeitos da correção sobre os contratos, testes e documentos relacionados, preparar as atualizações documentais conforme [Revisão da documentação](#revisão-da-documentação) e apresentar o conjunto no pull request.
5. Depois da correção, executar novamente o cenário que falhou e a suíte completa, conferindo a cobertura.

Uma mudança legítima de requisito DEVE ser documentada e refletida nos testes antes da implementação correspondente. Não mudar o resultado esperado apenas para acompanhar o comportamento defeituoso.

Para alterações que afetem a execução, falhas abertas na suíte do conjunto a incorporar, cenários obrigatórios não executados e cobertura insuficiente impedem declarar concluídas as verificações exigidas para a alteração e fazer seu merge em `Validacao` ou `Principal`. Falhas introduzidas pela alteração ou cuja correção pertença ao alcance impedem também concluir o trabalho. Informar o estado real, inclusive falhas anteriores à alteração, sem escondê-las no resultado.

Falhas comprovadamente anteriores, que não decorram da alteração e cuja correção esteja fora de seu alcance, DEVEM ser informadas ao responsável pela integração. Elas não impedem concluir o trabalho quando:

- os cenários afetados pela alteração tiverem sido executados com sucesso;
- as falhas não impedirem conferir o resultado da alteração;
- a cobertura real estiver informada;
- as conferências exigidas estiverem encerradas.

Essa conclusão não equivale à aprovação das verificações exigidas para integração e não autoriza o merge de código com falha aberta na suíte do conjunto a validar. Se a falha impedir conferir o resultado, informar o impedimento antes de prosseguir.

O merge de uma alteração exclusivamente documental é PERMITIDO mesmo com falhas anteriores nos testes do código, desde que a entrega seja independente e não modifique conteúdo executável, configurações de execução ou comportamento da aplicação. Seu merge depende das conferências documentais aplicáveis, conforme [Verificação de uma alteração](#verificação-de-uma-alteração).

## Verificação de uma alteração

Executar verificações proporcionais ao conteúdo alterado, conforme a tabela abaixo.

Usar as verificações e os critérios de aprovação publicados na documentação da ferramenta ou do componente, identificando configuração e alcance. A proporcionalidade seleciona as verificações aplicáveis sem dispensar suíte completa ou cobertura nas alterações que afetem a execução. Registrar resultados, diagnósticos e pendências. Se faltar critério, configuração ou execução de uma verificação exigida para o conteúdo a incorporar, registrar a pendência e não declarar aprovada a verificação correspondente.

Alterações exclusivamente documentais, apresentadas separadamente e sem modificar a execução da aplicação, são conferidas pela linha de documentação da tabela. Não exigem executar a suíte, medir cobertura de código nem instalar a aplicação no ambiente de `Validacao`. Falhas anteriores de código sem relação com o conteúdo documental não impedem seu merge. A entrega documental continua seguindo `Desenvolvimento_[Responsavel]` → `Validacao` → `Principal`.

Isso inclui alterações apenas em comentários documentais de PHP, JavaScript, CSS ou outro formato, desde que preservem a sintaxe e não afetem a execução, o conteúdo entregue ou o processamento por ferramentas. Anotações interpretadas por ferramentas, diretivas e comentários que afetem a geração ou o comportamento exigem também as verificações do conteúdo correspondente. Para scripts de banco já incorporados a `Principal`, permanece a preservação do arquivo definida em [Cuidados de manutenção](#cuidados-de-manutenção).

| Alteração | Verificação |
| --- | --- |
| Documentação | Cumprimento das [Leis de escrita](01Padroes.md#leis-de-escrita), links, caminhos, exemplos, cabeçalhos, definições conflitantes, proteção das informações presentes nos documentos, sintaxe dos arquivos quando houver alteração de comentários, conferência do `AGENTS.md` quando uma seção citada por ele mudar e [Bloqueio dos documentos numerados](#bloqueio-dos-documentos-numerados) |
| Código próprio de produção | Sintaxe, análise, [Verificação da segurança](#verificação-da-segurança), suíte completa e cobertura conforme [Testes e cobertura integral](#testes-e-cobertura-integral) |
| Código dos testes | Sintaxe, análise, conferência dos cenários e resultados esperados, execução da suíte completa e medição da cobertura do código próprio de produção |
| Arquivos entregues ao usuário | Minificação de HTML, CSS e JavaScript, SVG próprios sem comentários, arquivos fonte não entregues e [Conferência da entrega](03Configuracoes.md#conferência-da-entrega) |
| Configuração ou implantação | Leitura dos parâmetros, inicialização, ambiente de destino e [Verificação da segurança](#verificação-da-segurança) aplicáveis |
| Estrutura ou nomes | Carregamento, referências e execução com diferenciação de maiúsculas e minúsculas |
| Banco ou dependência | Compatibilidade, migração ou recuperação e integração |

Informar o que foi verificado, o resultado e o que permaneceu sem verificação. Ausência de testes, configuração vazia ou verificação não executada não equivale a aprovação.
