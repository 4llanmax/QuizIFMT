# Ficha da Manrope

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 18/10/2026      | Revisado | 12/11/2026 |

## Objetivo

A ficha identifica a fonte Manrope instalada, sua origem, seus arquivos físicos e os registros de conferência da distribuição. As orientações gerais de organização e uso de recursos de terceiros estão em [Externos](../../05Externos.md).

## Identificação

| Campo | Conteúdo |
| --- | --- |
| Nome | Manrope |
| Versão | 4.504, declarada como `Version 4.504` na tabela de nomes dos arquivos TTF; versão v20 do arquivo variável distribuído pelo Google Fonts |
| Origem | Pacote de download da família no [Google Fonts](https://fonts.google.com/specimen/Manrope), obtido pelo manifesto `https://fonts.google.com/download/list?family=Manrope`. A identificação de origem está preservada no [OFL.txt distribuído](../../../Publico/Ativos/Fontes/Manrope/OFL.txt). O repositório atual é [Manrope](https://github.com/googlefonts/manrope) |
| Licença | SIL Open Font License 1.1, conforme o OFL.txt distribuído e a tabela de nomes dos TTF. Termos em [OFL.txt](../../../Publico/Ativos/Fontes/Manrope/OFL.txt) |
| Justificativa | Pendente: registrar a justificativa de uso no projeto |
| Finalidade | Disponibilizar a família tipográfica Manrope para a apresentação da aplicação |
| Integração | Não implementada: não há declaração CSS ou componente que carregue a fonte. Os requisitos de integração estão em [Integração e atualização](#integração-e-atualização) |
| Requisitos | Navegador com suporte a WOFF2 e a fontes variáveis e servidor com o tipo de conteúdo `font/woff2` |
| Inclusão | Pendente: registrar a inclusão no projeto |
| Alterações locais | O TTF variável foi convertido para WOFF2, alterando somente o formato. O TTF variável original permanece ao lado do WOFF2, e os TTF estáticos não foram instalados, conforme [Arquivos instalados](#arquivos-instalados). Nenhuma no README.txt. O OFL.txt tem o fim de linha normalizado para LF pelo `.gitattributes` do projeto; seu texto é idêntico ao distribuído |
| Verificação | Comparar o TTF com a distribuição de origem e conferir os resumos dos arquivos instalados, as tabelas, os nomes, as métricas e os glifos do WOFF2 em relação ao TTF; testar o carregamento quando implementado |

## Arquivos instalados

A pasta `Publico/Ativos/Fontes/Manrope` contém o TTF variável distribuído, `Manrope-VariableFont_wght.ttf`; o WOFF2 convertido dele, `Manrope-VariableFont_wght.woff2`; e os arquivos README.txt e OFL.txt distribuídos. O README descreve o TTF variável e a subpasta `static` com 7 TTF estáticos; esta instalação contém somente a fonte variável, em TTF e em WOFF2, que abrange todos os pesos do eixo `wght`. A licença e o README são metadados da distribuição e seguem as condições de entrega de [Ativos](../../02Estrutura.md#ativos).

O caminho omite a versão para permanecer estável e receber uma única versão da fonte por vez, conforme [Pastas de distribuições externas](../../01Padroes.md#pastas-de-distribuições-externas). A versão instalada permanece identificada na ficha.

A conversão foi feita com fontTools 4.67.0 e compressão Brotli, sem subconjunto de glifos nem alteração de tabelas, nomes ou avisos. Os SHA-256 registrados são:

```text
2b591b8c90bca10c923a0b40326da9fa9236aa3dee12ab71467907c27cac92fc  Manrope-VariableFont_wght.woff2, arquivo instalado
2b7a1ebc80c79246faa1b6e7093c7b91de2a4ceedb9b9a2b2fa05cf9bf8c77cd  Manrope-VariableFont_wght.ttf, original instalado
```

O resumo do TTF instalado coincide com o do arquivo distribuído pelo Google Fonts. Esses resultados não comprovam o carregamento da fonte pela aplicação.

## Integração e atualização

A integração da fonte DEVE declarar o endereço público dos arquivos utilizados no CSS do componente responsável e conferir o carregamento e a apresentação nos navegadores adotados. Esta regra não declara um CSS ou componente existente.

Ao substituir a fonte, conferir seus metadados, a licença, o resumo e as referências dos consumidores. O procedimento geral está em [Atualização de ferramentas e dependências](../../00Regras.md#atualização-de-ferramentas-e-dependências).
