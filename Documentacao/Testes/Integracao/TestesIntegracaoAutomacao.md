# Testes de integração da automação

| Redator | Data de criação | Status | Data do Status |
| --- | --- | --- | --- |
| Allan Max | 11/11/2026 | Revisado | 12/11/2026 |

## Objetivo

Este documento identifica os cenários de `Testes/Integracao/Automacao`, conforme [Testes](../../02Estrutura.md#testes) e [Verificação de uma alteração](../../00Regras.md#verificação-de-uma-alteração).

## Execução

A suíte exige PHP CLI, as extensões do [PHPUnit](../../Ferramentas/PHPUnit.md) e Git em `PATH`. Executar da raiz:

```text
php Ferramentas/PHPUnit/PHPUnit.phar --configuration Ferramentas/PHPUnit/PHPUnit.xml
```

Os cenários verificam classificação, documentação, integridade, preparação, resultado final e proteção dos numerados. Usam arquivos e repositórios temporários isolados; não exigem a aplicação ou servidor. O código e os cenários descrevem seus detalhes. A situação e os limites do fluxo estão em [Situação da automação](../../Ambiente/GitHub.md#situação-da-automação).

`ReprovacaoTeste.php` verifica o registro da issue antes do encerramento, cancelamentos, referências ao PR e ao commit, retomada sem duplicar issue e falhas da API. A implementação PHP é real; as respostas HTTP são substituídas por dados fictícios. Esses cenários não alteram o repositório remoto nem comprovam, por si, as permissões efetivas da plataforma.
