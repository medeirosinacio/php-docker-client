# PHP Docker Run!!!

## Introdução

Este projeto de teste é uma classe de exemplo que demonstra como utilizar a API do Docker para executar tarefas
relacionadas a containers.

Ele foi construído para fornecer uma representação do comando `docker run` dentro do ambiente da API do Docker. A
classe `DockerApi` fornece métodos para criar, listar, iniciar, parar, remover e obter informações
sobre containers, bem como para baixar imagens e obter informações sobre a versão do Docker. Este projeto é apenas um
exemplo e pode ser usado como base para desenvolvimento de aplicações que utilizam a API do Docker para gerenciamento de
containers.

## Pré-requisitos

- Docker
- Docker Compose

## Instalação

1. Clone este repositório:

```sh
git clone https://github.com/medeirosinacio/docker-api.git
```

2. Navegue até a pasta do projeto:

```sh
cd php-docker-run
```

3. Execute o script build para subir e deixar os containers em execução:

```sh
bin/build-up.sh
```

4. Execute o script run para rodar o arquivo run.php dentro da pasta src:

```sh
bin/run.sh
```

## Como funciona