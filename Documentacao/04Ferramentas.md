# Ferramentas

| Redator   | Data de criação | Status   | Data do Status |
| --------- | --------------- | -------- | -------------- |
| Allan Max | 28/09/2026      | Revisado | 18/10/2026     |

## Objetivo

Este documento define as funções exigidas das ferramentas de desenvolvimento, sua organização e seu uso. Cada ferramenta adotada DEVE ter configuração identificada e procedimento reproduzível; a presença do executável não comprova essa preparação.

## Em resumo

- Cada ferramenta adotada ocupa sua própria pasta em `Ferramentas`; seu documento de uso fica em `Documentacao/Ferramentas`. Veja [Organização](#organização).
- O projeto exige análise estática, verificação e correção da forma do código, testes e cobertura. Veja [Funções exigidas](#funções-exigidas).
- O documento de cada ferramenta registra versão, origem, licença, justificativa e integridade. Veja [Registro de instalação](#registro-de-instalação).
- A configuração das verificações delimita o código próprio e as métricas medidas. Veja [Configuração das verificações](#configuração-das-verificações).
- As verificações são executadas a partir da raiz, e as falhas seguem o tratamento definido. Veja [Procedimento de uso](#procedimento-de-uso).
- Programas e plataformas do ambiente, não incluídos no projeto, não pertencem a `Ferramentas`. Veja [Organização](#organização).

## Organização

A fórmula obrigatória de organização é `Ferramentas/[NomeFerramenta]`, com o executável e os arquivos da ferramenta na pasta correspondente ao nome real da ferramenta. Essas pastas são livres, conforme [Mapa geral](02Estrutura.md#mapa-geral), e a fórmula não identifica outro diretório físico. A documentação original fornecida com a ferramenta permanece em sua pasta.

O documento de uso escrito pelo projeto fica solto em `Documentacao/Ferramentas`, um por ferramenta, conforme [Fichas e documentos de ferramentas](02Estrutura.md#fichas-e-documentos-de-ferramentas); ele identifica a pasta e a instalação da ferramenta. A configuração DEVE ficar junto da ferramenta responsável. É PERMITIDO compartilhar um padrão entre ferramentas, com o caminho identificado nos respectivos documentos e sem duplicação. Arquivos exigidos em outro local pela ferramenta DEVEM permanecer nesse local.

Os nomes do executável, da configuração e do documento seguem [Arquivos das ferramentas](01Padroes.md#arquivos-das-ferramentas). Ao trocar uma ferramenta, conferir os consumidores de seus arquivos compartilhados antes de remover sua pasta e seu documento.

Ferramentas distribuídas como pacote executável pelo PHP não são classes da aplicação e não entram no carregamento de produção.

Programas e plataformas do ambiente que não são incluídos no projeto, como o sistema de versionamento, o repositório remoto, os contêineres e o gerenciador de banco, não pertencem a `Ferramentas`; seguem [Ferramentas de ambiente](02Estrutura.md#ferramentas-de-ambiente). A automação que executa estas verificações na plataforma do repositório remoto é definida no documento dessa plataforma.

## Funções exigidas

| Função | O que a ferramenta adotada faz |
| --- | --- |
| Análise estática | Analisa tipos, chamadas e contratos do código |
| Forma do código | Verifica a forma de escrita configurada |
| Correção automática | Corrige automaticamente parte dos problemas de forma, com o mesmo padrão da verificação de forma |
| Testes e cobertura | Executa os cenários automatizados e mede a cobertura de linhas e ramos no escopo da linguagem atendida |

O documento de uso de cada ferramenta, em `Documentacao/Ferramentas`, DEVE informar a função que ela cumpre, a linguagem atendida e seus comandos. As ferramentas PHP não comprovam análise, testes ou cobertura de outras linguagens; cada conteúdo executável DEVE ter verificação e medição próprias conforme [Alcance e evidências da cobertura](00Regras.md#alcance-e-evidências-da-cobertura). Uma ferramenta adicional recebe sua própria pasta em `Ferramentas` e seu documento de uso em `Documentacao/Ferramentas`. Cada ferramenta incluída no projeto segue [Recursos de terceiros](00Regras.md#recursos-de-terceiros).

## Registro de instalação

O documento de uso de cada ferramenta contém a seção `Registro de instalação`, com o conteúdo mínimo definido em [Documento de ferramenta](01Padroes.md#documento-de-ferramenta). A atualização segue [Atualização de ferramentas e dependências](00Regras.md#atualização-de-ferramentas-e-dependências).

## Configuração das verificações

| Verificação | Configuração necessária |
| --- | --- |
| Análise estática | Caminhos de código próprio, nível adotado, carregamento de símbolos e exclusões delimitadas |
| Forma do código | Conjunto de verificações, arquivos incluídos e arquivos excluídos |
| Testes e cobertura | Preparação, categorias, sufixo dos arquivos, ambiente isolado, código próprio incluído na medição e relatórios de cobertura de linhas e ramos |
| Correção automática | Mesmo padrão de forma usado pelo verificador e escopo explícito |

É PERMITIDO informar conteúdo de terceiros à análise exclusivamente para reconhecimento de símbolos, sem tratá-lo como código próprio a reformatar.

Os cenários de testes DEVEM ficar em `Testes/Unitario` e `Testes/Integracao`; a preparação compartilhada DEVE ficar no caminho prescrito `Testes/Apoio/Preparacao.php`, conforme [Testes](02Estrutura.md#testes). Recursos exclusivos de um cenário seguem [Categorias e isolamento dos cenários](00Regras.md#categorias-e-isolamento-dos-cenários). Os nomes das classes e dos arquivos de teste seguem [Formato por elemento](01Padroes.md#formato-por-elemento); a configuração da suíte identifica esses arquivos pelo sufixo correspondente.

A sequência de desenvolvimento, a meta de cobertura e o tratamento das falhas estão em [Testes e cobertura integral](00Regras.md#testes-e-cobertura-integral). A configuração e os relatórios identificam também arquivos de produção não exercitados e as métricas que a instalação consegue medir. Os requisitos para medir linhas e ramos ficam no documento da ferramenta de testes.

Cada configuração DEVE declarar os critérios de aprovação e reprovação da verificação correspondente, incluindo o tratamento de avisos e verificações não executadas. Os documentos das ferramentas DEVEM apontar para esses critérios e explicar a interpretação do resultado. Configuração ausente, escopo indefinido ou critério pendente impedem declarar a verificação aprovada. A situação da configuração de cada ferramenta fica em seu documento de uso.

Para arquivos de terceiros modificados, delimitar na configuração e nos relatórios as partes de código próprio de produção acrescentadas ou alteradas, conforme [Alcance e evidências da cobertura](00Regras.md#alcance-e-evidências-da-cobertura). A ferramenta utilizada DEVE medir as linhas e os ramos de decisão desse escopo. Uma limitação da ferramenta não dispensa os testes nem a cobertura exigida.

## Procedimento de uso

1. Conferir instalação, requisitos e integridade pelo registro da ferramenta.
2. Identificar a configuração e o escopo da verificação.
3. Executar o comando documentado a partir da raiz do projeto.
4. Ler o resultado e, quando houver erro de código, abrir uma issue no repositório remoto conforme [Comunicação de pendências](Ambiente/GitHub.md#comunicação-de-pendências). Falhas nos testes seguem [Tratamento de falhas nos testes](00Regras.md#tratamento-de-falhas-nos-testes).
5. Após correção, executar a verificação afetada novamente.

Exemplos e comandos próprios de cada ferramenta ficam nos documentos de uso. Invocar apenas o executável sem configuração não demonstra que o projeto foi verificado.
