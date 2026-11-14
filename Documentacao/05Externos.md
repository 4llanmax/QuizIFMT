# Externos

| Redator   | Data de criação | Status   | Data do Status |
| --------- | --------------- | -------- | -------------- |
| Allan Max | 28/09/2026      | Revisado | 11/11/2026     |

## Objetivo

Este documento define a organização dos recursos de terceiros instalados, sua identificação e os limites arquiteturais de sua integração. A presença de uma distribuição não comprova consumo pelo código próprio.

As linguagens do código próprio e a preservação dos arquivos originais em outras linguagens seguem [Arquivos e linguagens permitidos](00Regras.md#arquivos-e-linguagens-permitidos). A distribuição de terceiros não autoriza acrescentar código próprio fora desse limite.

## Em resumo

- Um recurso de terceiros só entra com justificativa registrada e licença compatível. Veja [Organização](#organização).
- Bibliotecas carregadas pelo código PHP próprio ficam em `Externos`; ativos para o navegador seguem os destinos de recursos públicos. Veja [Organização](#organização).
- A ficha identifica a distribuição, tem um conteúdo mínimo e fica em `Documentacao/Externos` ou `Documentacao/Publico/Externos`. Veja [Ficha da dependência](#ficha-da-dependência).
- O código PHP próprio de integração DEVE residir em `Aplicacao`. Veja [Integração com a aplicação](#integração-com-a-aplicação).
- Ativos de terceiros entregues ao navegador ficam nas famílias de `Publico/Ativos`. Veja [Recursos públicos de terceiros](#recursos-públicos-de-terceiros).
- A atualização segue o procedimento comum das regras. Veja [Atualização](#atualização).

## Organização

Antes de incluir uma dependência, conferir se ela atende a [Recursos de terceiros](00Regras.md#recursos-de-terceiros): justificativa registrada e licença que permita a integração, a distribuição e a comercialização previstas para o projeto. Uma dependência que não atenda a esses critérios DEVE ser descartada; é PROIBIDO integrá-la à aplicação.

Cada distribuição de biblioteca carregada pelo código PHP próprio, de produção ou de testes, DEVE ocupar seu diretório exclusivo em `Externos`. As fórmulas de localização são `Externos/[Nome-Versao]` e `Externos/[Nome]`, conforme [Pastas de distribuições externas](01Padroes.md#pastas-de-distribuições-externas). Essas pastas são livres, conforme [Mapa geral](02Estrutura.md#mapa-geral); cada distribuição instalada é identificada por sua ficha, e as fórmulas não identificam diretórios adicionais existentes.

Nomes e caminhos internos fornecidos na distribuição DEVEM preservar a grafia da origem conforme [Idioma do projeto](00Regras.md#idioma-do-projeto). A documentação original do terceiro permanece na pasta da distribuição. A ficha é documentação própria e DEVE residir solta em `Documentacao/Externos`, fora da distribuição, conforme [Fichas e documentos de ferramentas](02Estrutura.md#fichas-e-documentos-de-ferramentas); seu nome segue [Nomes dos documentos](01Padroes.md#nomes-dos-documentos).

É PERMITIDO instalar versões distintas de uma distribuição somente em diretórios separados, com identificação e consumidores inequívocos. A remoção de uma distribuição ou versão DEVE incluir sua ficha e a conferência das referências afetadas.

Programas executados no desenvolvimento ficam em `Ferramentas`, conforme o critério de desempate de [Ferramentas e Externos](02Estrutura.md#ferramentas-e-externos). Recursos entregues ao navegador ficam na família correspondente de `Publico/Ativos`, e sua ficha segue [Recursos públicos de terceiros](#recursos-públicos-de-terceiros).

## Ficha da dependência

A identificação da origem dos arquivos de terceiros segue [Definições](00Regras.md#definições). O conteúdo mínimo da ficha está em [Ficha de recurso de terceiros](01Padroes.md#ficha-de-recurso-de-terceiros).

A ficha descreve a instalação efetiva. Ela não substitui o texto da licença distribuída.

Documentos originais de terceiros DEVEM permanecer identificados como documentação da distribuição e conservar seus textos e avisos. Seus exemplos e instruções não definem a arquitetura própria nem comprovam arquivos ou funcionalidades presentes no projeto. A ficha DEVE registrar diferenças entre o conteúdo descrito pelo fornecedor e o conjunto instalado, inclusive referências locais indisponíveis, sem alterar o original para simular outra distribuição.

## Integração com a aplicação

O código PHP próprio de integração DEVE residir em `Aplicacao`, na área responsável. A divisão `Conectores` é o destino da comunicação com sistemas externos, da tradução de entradas e saídas e do tratamento das falhas dessa comunicação. A persistência em banco, inclusive externo, pertence a `Manipuladores`, conforme [Divisões de responsabilidade](02Estrutura.md#divisões-de-responsabilidade). O uso de uma biblioteca local não determina, por si só, a divisão do código consumidor: sua responsabilidade determina o destino. Ativos de integração destinados ao navegador DEVEM obedecer a [Recursos públicos de terceiros](#recursos-públicos-de-terceiros) e [Ativos](02Estrutura.md#ativos). O Núcleo DEVE receber somente o que é comum a todas as áreas a que se destina, conforme [Nucleo](02Estrutura.md#nucleo).

Alterações em arquivos fornecidos pelo terceiro seguem [Atualização de ferramentas e dependências](00Regras.md#atualização-de-ferramentas-e-dependências) e DEVEM ser identificadas contra a distribuição original. Essa condição não altera o destino obrigatório de arquivos independentes da integração própria nem autoriza misturá-los ao código do fornecedor.

O consumidor recebe a dependência pelo contrato definido para o componente. Credenciais e endereços são parâmetros de [Configurações](03Configuracoes.md), sem valores secretos no código.

Bibliotecas DEVEM manter seus espaços de nomes. A aplicação DEVE carregar suas declarações por um mecanismo compatível com a distribuição, identificado no componente consumidor; a presença da pasta não registra automaticamente esse carregamento. O carregamento próprio segue [Subpastas e espaços de nomes](02Estrutura.md#subpastas-e-espaços-de-nomes).

## Recursos públicos de terceiros

Uma distribuição admite ativos de diferentes tipos. Cada recurso entregue DEVE ocupar a família correspondente: CSS, JavaScript, Imagens ou Fontes. Documentação e metadados originais preservados na distribuição não constituem ativos próprios nem autorizam outra família ou entrega pública.

A ficha própria dos recursos públicos de terceiros fica solta em `Documentacao/Publico/Externos`, conforme [Fichas e documentos de ferramentas](02Estrutura.md#fichas-e-documentos-de-ferramentas). Seu nome usa o mesmo nome de identificação das pastas do recurso nas famílias de `Publico/Ativos`, conforme [Pastas de distribuições externas](01Padroes.md#pastas-de-distribuições-externas). Ela informa a versão ou referência imutável e relaciona os caminhos instalados em `Publico/Ativos` à origem dos recursos. A existência da ficha não exige manter uma segunda cópia completa da distribuição. Metadados distribuídos por terceiros seguem as condições de preservação e bloqueio de entrega de [Ativos](02Estrutura.md#ativos).

A preservação de uma distribuição não torna todos os seus arquivos publicáveis. As condições de entrega e a organização de subpastas estão em [Ativos](02Estrutura.md#ativos).

## Atualização

O procedimento comum de atualização, registro do resultado e conferência dos consumidores está em [Atualização de ferramentas e dependências](00Regras.md#atualização-de-ferramentas-e-dependências). A ficha da dependência acrescenta os comandos, incompatibilidades e verificações de sua integração.
