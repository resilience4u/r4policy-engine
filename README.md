# R4Policy Engine

[![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/resilience4u/r4policy-engine/tests.yml?branch=main)](https://github.com/resilience4u/r4policy-engine/actions)
[![Coverage](https://img.shields.io/badge/coverage-~82%25-yellowgreen.svg)](./build/coverage/index.html)

> **R4Policy Engine** — motor declarativo de políticas de resiliência (Retry, Circuit Breaker, Rate Limiter, Bulkhead...)  
> inspirado em _Resilience4j_ e integrado ao ecossistema **R4PHP** da [Resilience4u](https://github.com/resilience4u).

---

## Visão Geral

O **R4Policy Engine** permite definir e avaliar políticas de resiliência a partir de arquivos YAML ou JSON —  
criando *chains* compostas de _Retry_, _Circuit Breaker_, _Timeout_ e outros comportamentos.

Ele é o componente de **Policy Evaluation** da iniciativa [Resilience4u](https://github.com/resilience4u),  
e pode ser usado de forma standalone ou acoplado ao [`r4php`](https://github.com/resilience4u/r4php).

---

## Estrutura e Conceitos

```
+ src/
  ├── CLI/                 # Comandos CLI (validate, dump, diff)
  ├── Evaluator/           # Avaliação e encadeamento de políticas
  ├── Loader/              # Carregadores YAML e JSON
  ├── Model/               # PolicyDefinition e Registry
  ├── Telemetry/           # Métricas e instrumentação (R4Telemetry-ready)
  └── R4PolicyEngine.php   # Núcleo de execução
```

Principais entidades:

| Classe | Responsabilidade |
|--------|------------------|
| `R4PolicyEngine` | Gerencia registro, carregamento e avaliação de políticas |
| `PolicyDefinition` | Representação imutável de uma política declarada |
| `PolicyEvaluator` | Cria chains de execução resiliente com base nas definições |
| `YamlPolicyLoader` / `JsonPolicyLoader` | Faz o parsing de arquivos de configuração |
| `TelemetryBridge` | Ponto de integração com `R4Telemetry` |

---

## Instalação

```bash
composer require resilience4u/r4policy-engine
```

---

## Uso via CLI

O `r4policy` inclui um CLI pronto (baseado em **Symfony Console**) para validar, inspecionar e comparar arquivos de políticas.

### Validar um arquivo

```bash
./bin/r4policy validate policies.yaml
```

Saída esperada:
```
OK - policies.yaml is valid.
```

### Listar políticas carregadas

```bash
./bin/r4policy dump policies.yaml
```

Exemplo de saída:
```
Loaded policies:
- user_api [retry]
- send_email [circuit_breaker]
```

### Comparar dois arquivos (diff)

```bash
./bin/r4policy diff old.yaml new.yaml
```

Exemplo:
```
~ changed user_api
+ added   payment_gateway
- removed legacy_service
```

---

## Testes e Cobertura

Executar toda a suíte de testes com cobertura HTML:

```bash
composer test
```

ou diretamente:

```bash
./vendor/bin/phpunit --colors=always --coverage-html build/coverage
```

A cobertura atual atinge **~82% das linhas**, com cenários de integração e CLI totalmente testados.

---

## Design

- Inspirado em _Resilience4j_ e _Hystrix_
- Totalmente modular, extensível e _framework-agnostic_
- Integrável com `Resiliente\R4PHP` (implementações de políticas concretas)
- Telemetria desacoplada via `TelemetryBridge`

---

## Roadmap (v0.2+)

- [ ] Adicionar suporte a TimeLimiter e Bulkhead
- [ ] Exportar métricas para `R4Telemetry`
- [ ] Adicionar schema validation no loader
- [ ] Suporte a políticas compostas e encadeamentos dinâmicos

---

## Licença

Lançado sob a licença **MIT**.  
© 2025 — parte do ecossistema **[Resilience4u](https://github.com/resilience4u)**.
