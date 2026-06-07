# Redirecionamento de URLs — API Laravel

API REST para criar, gerenciar e rastrear links encurtados. Cada link aponta para um ou mais destinos (URLs de destino) e pode contabilizar acessos. O projeto foi pensado como backend de um serviço de redirecionamento, com autenticação, organização por clientes e histórico de alterações.

## O que a aplicação faz

Um **redirect** é um link encurtado com título, vinculado a um **cliente** (quem contratou ou usa o link). Cada redirect possui um ou mais **destinos** — URLs reais para onde o usuário final é enviado. Cada destino recebe um identificador único (UUID) usado na rota pública de redirecionamento.

Fluxo típico:

1. Um usuário autenticado cadastra clientes e cria redirects com seus destinos.
2. O sistema gera links no formato `/go-to/{destination_id}`.
3. Quando alguém acessa o link, a API redireciona para a URL configurada e, se habilitado, incrementa o contador de cliques.
4. Alterações na URL de um destino ficam registradas em logs de auditoria.

Se o destino não existir, o visitante é redirecionado para uma URL de fallback configurável via variável de ambiente.

## Funcionalidades

- **Autenticação JWT** — registro, login, refresh e logout de usuários.
- **Gestão de clientes** — CRUD com soft delete; redirects dependem de um cliente ativo.
- **Gestão de redirects** — criação com múltiplos destinos, listagem paginada e exclusão (somente sem destinos vinculados).
- **Destinos com UUID** — cada URL de destino tem ID único para o link público.
- **Contagem de cliques** — opcional por destino (`needs_count`).
- **Logs de auditoria** — histórico quando a URL de um destino é alterada.
- **Soft deletes** — clientes, redirects e destinos podem ser removidos sem perder o histórico no banco.

## Stack

| Tecnologia | Uso |
|---|---|
| PHP 8.2+ | Linguagem principal |
| Laravel 11 | Framework e estrutura da API |
| JWT Auth (`tymon/jwt-auth`) | Autenticação stateless |
| SQLite / MySQL | Persistência (configurável) |

## Arquitetura

O código segue uma separação simples entre camadas:

```
routes/          → definição dos endpoints
app/Http/Controllers/  → validação de entrada e respostas HTTP
app/Services/    → regras de negócio (redirects, destinos, clientes, logs)
app/Models/      → entidades Eloquent e relacionamentos
```

Principais entidades:

- **User** — usuário autenticado que opera a API.
- **Customer** — cliente dono dos redirects.
- **Redirect** — link encurtado (título + cliente).
- **Destination** — URL de destino com contador e flag de contagem.
- **Log** — registro de alterações de URL.

## Endpoints principais

### Público

| Método | Rota | Descrição |
|---|---|---|
| `GET` | `/go-to/{destination_id}` | Redireciona para a URL do destino |

### Autenticação

| Método | Rota | Descrição |
|---|---|---|
| `POST` | `/api/auth/register` | Cadastro de usuário |
| `POST` | `/api/auth/login` | Login (retorna JWT) |
| `POST` | `/api/auth/refresh` | Renova o token |
| `POST` | `/api/logout` | Encerra a sessão |

### Clientes *(requer JWT)*

| Método | Rota | Descrição |
|---|---|---|
| `POST` | `/api/customer/create` | Cria cliente |
| `GET` | `/api/customer/all` | Lista paginada |
| `GET` | `/api/customer/all-without-pagination` | Lista completa |
| `GET` | `/api/customer/{id}` | Detalhes |
| `DELETE` | `/api/customer/{id}/delete` | Remove cliente |

### Redirects *(requer JWT)*

| Método | Rota | Descrição |
|---|---|---|
| `POST` | `/api/redirect/create` | Cria redirect com destinos |
| `GET` | `/api/redirect/all` | Lista paginada |
| `GET` | `/api/redirect/{id}` | Lista destinos do redirect |
| `POST` | `/api/redirect/{id}/new-destination` | Adiciona destinos |
| `DELETE` | `/api/redirect/{id}/delete` | Remove redirect |

### Destinos

| Método | Rota | Descrição |
|---|---|---|
| `PUT` | `/api/destination/{id}/update` | Atualiza URL do destino |
| `DELETE` | `/api/destination/{id}/delete` | Remove destino |

### Logs

| Método | Rota | Descrição |
|---|---|---|
| `GET` | `/api/logs/{destination_id}` | Histórico de alterações |

## Como rodar localmente

**Requisitos:** PHP 8.2+, Composer, extensões PHP usuais do Laravel.

```bash
# Clonar e instalar dependências
composer install

# Configurar ambiente
cp .env.example .env
php artisan key:generate
php artisan jwt:secret

# Banco (SQLite por padrão)
touch database/database.sqlite
php artisan migrate

# Subir o servidor
php artisan serve
```

A API ficará disponível em `http://localhost:8000`. As rotas autenticadas ficam sob o prefixo `/api`.

### Variáveis de ambiente relevantes

| Variável | Descrição |
|---|---|
| `APP_URL` | URL base da aplicação |
| `FALLBACK_REDIRECT_URL` | URL para onde redirecionar quando um destino não existe |
| `DB_CONNECTION` | Driver do banco (`sqlite`, `mysql`, etc.) |

## Licença

MIT
