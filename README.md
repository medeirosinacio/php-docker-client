# PHP Docker Run!!!

## Introdução

Este projeto de teste foi criado para exemplificar como utilizar a API do Docker para executar um comando qualquer
dentro de uma imagem docker e resgatar seu output.

Ele foi construído para fornecer uma representação do comando `docker run` dentro do ambiente da API do Docker. A
classe `DockerApi` fornece métodos para criar, listar, iniciar, parar, remover e obter informações
sobre containers, bem como para baixar imagens e obter informações sobre a versão do Docker. Este projeto é apenas um
exemplo e pode ser usado como base para desenvolvimento de aplicações que utilizam a API do Docker para gerenciamento de
containers.

O projeto foi desenvolvido para testar a funcionalidade e implementação de como poderiamos executar rotinas genericas
especificas em outras linguagens dentro da aplicação PHP, obtendo seu output que poderia ser um processamento de imagem,
calculos ou extração de algum dado.

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

4. Execute o script test-docker-run-inside-php.sh para rodar o arquivo de testes
   dentro da pasta src:

```sh
bin/test-docker-run-inside-php.sh
```

Você deve poder ver no terminal algo como:

```sh
2023-01-18 00:40:09.960001
```

## Como funciona

...