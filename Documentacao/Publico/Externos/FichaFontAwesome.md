# Ficha do Font Awesome

| Redator   | Data de criação | Status      | Data do Status |
| --------- | --------------- | ----------- | -------------- |
| Allan Max | 12/10/2026      | Revisado | 12/11/2026 |

## Objetivo

A ficha identifica os recursos locais do Font Awesome Free, sua origem, seus caminhos instalados e os registros de conferência da distribuição. As orientações gerais de organização e uso de recursos de terceiros estão em [Externos](../../05Externos.md).

## Identificação

| Campo | Conteúdo |
| --- | --- |
| Nome | Font Awesome, distribuição Free |
| Versão | 7.3.1, conforme os cabeçalhos distribuídos nos arquivos CSS e JavaScript |
| Origem | [Lançamento 7.3.1](https://github.com/FortAwesome/Font-Awesome/releases/tag/7.3.1) do fornecedor, pacote `fontawesome-free-7.3.1-web.zip`, com SHA-256 `35bc95eb1e5f9f04902ba2f5b2d04bf72178fa5bb4b5b5d08deb10115e3eb465` publicado no lançamento |
| Licença | O [LICENSE.txt distribuído](../../../Publico/Ativos/Fontes/FontAwesome-7.3.1/LICENSE.txt) identifica CC BY 4.0 para os ícones SVG e JavaScript, SIL OFL 1.1 para as fontes e MIT para o código; os avisos originais permanecem nos arquivos. Conferência em [Conferência das licenças](#conferência-das-licenças) |
| Justificativa | Pendente: registrar a justificativa de uso no projeto |
| Finalidade | Distribuição de ícones por folhas de estilo, fontes, JavaScript e SVG; identifica recursos disponíveis, sem declarar uma interface implementada |
| Integração | Ainda não implementada: não há componente que carregue estes recursos. Os caminhos disponíveis estão em [Caminhos instalados](#caminhos-instalados) |
| Requisitos | Servidor com os caminhos e tipos de conteúdo previstos em [Entrega web](../../03Configuracoes.md#entrega-web); navegador compatível com os recursos escolhidos |
| Inclusão | Pendente: registrar a inclusão no projeto |
| Alterações locais | Separação dos recursos entre as famílias de ativos e organização das pastas pelo nome e pela versão. Nos 12 arquivos CSS que referenciam fontes, incluindo os minificados, a URL `../webfonts/` foi trocada por `../../Fontes/FontAwesome-7.3.1/webfonts/`, sem outra alteração. As pastas `metadata` e `scss` do pacote não foram instaladas |
| Verificação | Conferir o SHA-256 do pacote de origem e comparar seus arquivos com a cópia local, considerando as alterações identificadas nesta ficha; testar a integração quando implementada |

## Caminhos instalados

Os caminhos são relativos à raiz do projeto.

| Família | Pasta | Recursos |
| --- | --- | --- |
| CSS | `Publico/Ativos/CSS/FontAwesome-7.3.1` | Folhas de estilo originais e minificadas, incluindo `all.min.css` |
| JavaScript | `Publico/Ativos/JavaScript/FontAwesome-7.3.1` | Scripts originais e minificados distribuídos pelo fornecedor |
| Fontes | `Publico/Ativos/Fontes/FontAwesome-7.3.1/webfonts` | Fontes WOFF2 das famílias brands, regular e solid, além da compatibilidade com a versão 4 |
| Imagens | `Publico/Ativos/Imagens/FontAwesome-7.3.1` | Ícones SVG individuais e sprites distribuídos pelo fornecedor |

O nome de identificação da pasta é `FontAwesome-7.3.1`; o nome oficial permanece na ficha. Os nomes internos distribuídos são preservados, conforme [Pastas de distribuições externas](../../01Padroes.md#pastas-de-distribuições-externas).

A versão integra o nome dos caminhos para que os consumidores identifiquem a distribuição específica utilizada.

## Conferência das licenças

- O código sob MIT mantém os avisos de autoria e os termos da licença.
- Os ícones sob [CC BY 4.0](https://creativecommons.org/licenses/by/4.0/legalcode.en) mantêm atribuição, referência à licença e indicação das alterações quando houver. É PROIBIDO aos termos próprios do produto restringir os direitos concedidos sobre esses ícones.
- As fontes sob [SIL OFL 1.1](https://openfontlicense.org/open-font-license-official-text/) mantêm sua licença e avisos, não são vendidas isoladamente e respeitam o nome reservado Font Awesome em versões modificadas.

Preservar os comentários de atribuição incorporados pelo fornecedor, inclusive nos SVG, conforme o LICENSE.txt instalado. A remoção de comentários de SVG próprios não se aplica a esses arquivos de terceiros. Os resultados datados desta ficha pertencem às conferências registradas; não comprovam entrega web ou integração executadas nesta revisão.

## Cuidados na atualização

As folhas de estilo desta instalação apontam para `../../Fontes/FontAwesome-7.3.1/webfonts/` a partir da pasta CSS. Ao substituir a distribuição, preparar novamente esses caminhos para a versão instalada, inclusive nos arquivos minificados, sem alterar os demais estilos ou avisos do fornecedor.

Conferir os recursos efetivamente carregados pelo componente, a apresentação dos ícones e a não entrega dos arquivos não minificados e dos metadados, conforme [Conferência da entrega](../../03Configuracoes.md#conferência-da-entrega).

Os originais preservados nas famílias de ativos não autorizam acesso público ao conteúdo fonte sem processamento. A preparação da entrega DEVE preservar os avisos do fornecedor e atender aos bloqueios de [Ativos](../../02Estrutura.md#ativos). Não há configuração de servidor comprovada por esta ficha.

O procedimento geral está em [Atualização de ferramentas e dependências](../../00Regras.md#atualização-de-ferramentas-e-dependências).
