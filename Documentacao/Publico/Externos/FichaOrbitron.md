# Ficha da Orbitron

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 12/10/2026      | Sem revisão | 16/11/2026     |

## Objetivo

A ficha identifica a fonte Orbitron instalada, sua origem, seus arquivos físicos e os registros de conferência da distribuição. As orientações gerais de organização e uso de recursos de terceiros estão em [Externos](../../05Externos.md).

## Identificação

| Campo | Conteúdo |
| --- | --- |
| Nome | Orbitron |
| Versão | 2.001, declarada como `Version 2.001` na tabela de nomes do arquivo TTF; versão v35 do arquivo distribuído pelo Google Fonts |
| Origem | Pacote de download da família no [Google Fonts](https://fonts.google.com/specimen/Orbitron), com o TTF servido em `https://fonts.gstatic.com/s/orbitron/v35/yMJRMIlzdpvBhQQL_Tq8fSx5i814.ttf`. O [OFL.txt distribuído](../../../Publico/Ativos/Fontes/Orbitron/OFL.txt) identifica o projeto de origem, [The League of Moveable Type](https://github.com/theleagueof/orbitron) |
| Licença | SIL Open Font License 1.1, conforme o OFL.txt distribuído, que identifica o nome reservado Orbitron. Termos em [OFL.txt](../../../Publico/Ativos/Fontes/Orbitron/OFL.txt) |
| Justificativa | Apresentar marca e indicadores do jogo com fonte local, sem nova dependência tipográfica |
| Finalidade | Disponibilizar a família tipográfica Orbitron para a apresentação da aplicação |
| Integração | `Principal.css` declara a fonte variável WOFF2 local para títulos e indicadores; a entrega usa `Principal.min.css` |
| Requisitos | Navegador com suporte a WOFF2 e a fontes variáveis e servidor com o tipo de conteúdo `font/woff2` |
| Inclusão | Pendente: registrar a inclusão no projeto |
| Alterações locais | Conversão do TTF variável para WOFF2, preservando o TTF original, conforme [Arquivos instalados](#arquivos-instalados). Nenhuma no README.txt. Fim de linha do OFL.txt normalizado para LF pelo `.gitattributes` |
| Verificação | Comparar o TTF com o pacote de download da origem e conferir os resumos dos arquivos instalados, as tabelas, os nomes, as métricas e os glifos do WOFF2 em relação ao TTF; testar o carregamento quando implementado |

## Arquivos instalados

A pasta `Publico/Ativos/Fontes/Orbitron` contém o TTF variável distribuído, `Orbitron-VariableFont_wght.ttf`; o WOFF2 convertido dele, `Orbitron-VariableFont_wght.woff2`; e o README.txt e o OFL.txt distribuídos. O README descreve o TTF variável e menciona fontes estáticas; esta instalação contém somente a fonte variável, em TTF e em WOFF2, que abrange todos os pesos do eixo `wght`. A licença e o README são metadados da distribuição e seguem as condições de entrega de [Ativos](../../02Estrutura.md#ativos).

O caminho omite a versão para permanecer estável e receber uma única versão da fonte por vez, conforme [Pastas de distribuições externas](../../01Padroes.md#pastas-de-distribuições-externas). A versão instalada permanece identificada na ficha.

A conversão foi feita com fontTools 4.67.0 e compressão Brotli, sem subconjunto de glifos nem alteração de tabelas, nomes ou avisos; o nome reservado Orbitron permanece inalterado. Os SHA-256 registrados são:

```text
5cbf15257420cc3ad886ec7fcd30cf08f49a4c2dd902c9094ff17002b82ffb68  Orbitron-VariableFont_wght.woff2, arquivo instalado
46eefc7084fd9abffd87547c47f729b6071c7f7a8d5af281266bb09e5461c435  Orbitron-VariableFont_wght.ttf, original instalado
```

## Integração e atualização

A integração da fonte DEVE declarar o endereço público do arquivo no CSS do componente responsável e conferir o carregamento e a apresentação nos navegadores adotados. Esta regra não declara um CSS ou componente existente.

Ao substituir a fonte, conferir seus metadados, a licença, o resumo e as referências dos consumidores. O procedimento geral está em [Atualização de ferramentas e dependências](../../00Regras.md#atualização-de-ferramentas-e-dependências).

## Uso no jogo

A conferência HTTP local entrega o recurso pelo tipo correspondente. A verificação visual em navegador e no provedor permanece identificada no [Memorial](../../Memorial.md#verificações-da-implementação-do-jogo). Os arquivos originais de licença permanecem preservados e bloqueados na entrega pública.
