# Docker

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 18/10/2026      | Revisado    | 18/10/2026     |

## Objetivo

Este documento define as pastas e os arquivos que o ambiente em contêineres acrescenta ao projeto, sua escrita e os requisitos de sua definição. Os requisitos gerais de parâmetros e ambientes estão em [Configurações](../03Configuracoes.md).

## Pastas e arquivos no projeto

O uso de contêineres é OPCIONAL. O Docker é um programa do ambiente: não é incluído no projeto e não exige ficha nem justificativa, conforme [Recursos de terceiros](../00Regras.md#recursos-de-terceiros). As pastas e os arquivos abaixo existem enquanto o ambiente em contêineres for utilizado; sua remoção conjunta é PERMITIDA quando ele não for utilizado.

| Caminho | Situação | Finalidade |
| --- | --- | --- |
| `Configuracoes/Docker` | Diretório existente | Destino obrigatório da definição de serviços, redes, volumes e parâmetros do ambiente |
| `Configuracoes/Docker/compose.yaml` | Arquivo existente e vazio | Definição dos serviços pelo Docker Compose |
| `Configuracoes/Docker/Exemplo.env` | Arquivo existente e vazio | Modelo versionado dos valores do ambiente, sem segredos |
| `Configuracoes/Docker/.env` | Destino permitido, não versionado e ausente | Valores privados do ambiente, separados da configuração privada da aplicação |
| `Configuracoes/Docker/Construcao` | Diretório existente, com apenas seu README | Arquivos e recursos de construção da imagem |

Não há definição de serviços ou construção de imagem implementada nesses arquivos.

## Escrita

- `compose.yaml` conserva o nome convencional do Docker Compose, conforme [Grafias externas](../01Padroes.md#grafias-externas). A execução exige seu caminho explícito.
- `.env` conserva o nome convencional; `Exemplo.env` segue o formato de arquivo próprio de [Formato por elemento](../01Padroes.md#formato-por-elemento). As chaves dos dois arquivos usam o formato de variável de ambiente `[FINALIDADE]`.
- O arquivo de construção da imagem, quando criado, DEVE usar a fórmula `[GrafiaOrigem]` com o nome convencional `Dockerfile`, no caminho `Configuracoes/Docker/Construcao/Dockerfile`, conforme [Grafias externas](../01Padroes.md#grafias-externas). Os demais apoios da construção DEVEM seguir a fórmula `Configuracoes/Docker/Construcao/[Finalidade].[extensao]`, conforme o formato de arquivo próprio.
- Os arquivos começam com o comentário de apresentação em linhas iniciadas por `#`, com os campos de [Documentação nos arquivos com comentários](../01Padroes.md#documentação-nos-arquivos-com-comentários).

## Definição do ambiente

O procedimento de execução DEVE partir da raiz do projeto e indicar explicitamente os caminhos de configuração utilizados.

O detalhamento do ambiente, nos comentários dos arquivos de `Configuracoes/Docker` e, quando a subdivisão for necessária, no documento previsto em [Outros documentos](../02Estrutura.md#outros-documentos), identifica:

- Imagem, versão de PHP e extensões necessárias;
- Contexto de construção e caminho do arquivo de construção da imagem;
- Serviços, portas, redes e verificações de disponibilidade;
- Volumes de código e dados persistentes;
- Usuário de execução e permissões;
- Origem das variáveis e segredos;
- Comandos de construção, inicialização, parada e conferência.

Variáveis usadas na definição do ambiente não se tornam automaticamente parâmetros lidos pela aplicação. O ambiente descreve como elas são repassadas e como o carregador as interpreta.
