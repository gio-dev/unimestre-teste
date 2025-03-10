# Projeto Unimestre - Teste

Projeto para cadastro de curriculos, com login, cadastro, edição e visualização das informações.

## Informações do Projeto

Projeto feito por Giovan Dias

Tempo de desenvolvimento: 14 horas

Tecnologias: Laravel 12, VueJs 3, Inertia, Breeze, SQLite/Mysql, Docker, TypeScript, Vite e Tailwind.

## Como executar (a partir da raiz do projeto)

1. `composer install`

2. `docker-compose up -d` (caso tenha o Docker Compose instalado) ou `composer run dev` (caso tenha o PHP e NPM instalado em sua máquina)

3.1 Pelo Docker:

    1. Acesse o bash: `docker-compose exec app bash`
    
4. Execute as migrações: `php artisan migrate`

5. Otimize a aplicação: `php artisan optimize`

## Explicações

- Modelo Solid com Clean Code e arquitetura quase hexagonal.
- Usado UI Kit do Laravel.
