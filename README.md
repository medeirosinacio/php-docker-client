# PHP Docker Run!!!

## Introdução

Este projeto é uma prova de conceito (PoC) para testar a possibilidade de processar rotinas em diversas linguagens através de um servidor Docker remoto, utilizando a API do Docker. Ele demonstra como podemos utilizar a API do Docker para executar comandos dentro de uma imagem Docker e resgatar sua saída. A ideia é que essa PoC possa ser utilizada como base para o desenvolvimento de aplicações que utilizam a API do Docker para processar rotinas em containers.

Exemplo:

```php
echo Docker::run(
	image: 'python:3.9.16-slim',
	command: ['python', '-c', "from datetime import datetime; print(datetime.now())"]
);
// 2023-01-18 00:28:13.648109
```

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

3. Execute o script build para subir e deixar os containers em execução. Esse script irá instalar as dependências do projeto e subir os containers, construindo-os caso não existam.

    ```sh
    bin/build-up.sh
    ```

4. Em seguida, você pode executar o script `test-docker-run-inside-php.sh` para rodar o arquivo de demonstração na pasta src:

    ```sh
    bin/test-docker-run-inside-php.sh
    ```

   Você deve poder ver no terminal algo como:
   > 2023-01-18 00:40:09.960001


Isso mostra que o comando `docker run` foi executado com sucesso e a saída foi retornada.

É importante notar que os containers são iniciados com o `docker-compose up -d` e, portanto, continuarão em execução em segundo plano. Para parar e remover os containers, você pode usar o comando `docker-compose down`.

Outros scripts de utilitários estão disponíveis na pasta `bin` para facilitar o acesso aos containers, como `enter-in-php-container.sh` e `enter-in-docker-api-container`

## Como funciona

### Infraestrutura

A infraestrutura deste projeto é composta por dois containers Docker. O primeiro container é o container PHP, onde sua aplicação está sendo executada. O segundo container é configurado com o Docker e a API do Docker habilitada. Esses dois containers estão conectados através de uma rede criada pelo docker-compose, chamada "dockered". A aplicação se comunica com a API do Docker através do endereço `http://docker-api:2375`.

Algumas funcionalidades foram abstraídas em scripts bash, que estão disponíveis na pasta `bin`. Estes scripts são:.

- `build-up.sh`: Utilize para subir e configurar o projeto.
- `test-docker-run-inside-php.sh`: Utilize para testar a implementação, ele executa `src/test-docker-run.php`.
- `enter-in-php-container.sh`: Utilize para entrar no container php.
- `enter-in-docker-api-container.sh`: Utilize para entrar no container docker-api.

### Implementação

A implementação deste projeto é baseada em duas classes principais: `Docker` e `DockerClient`. A classe `DockerClient` é responsável por se comunicar com a API do Docker e realizar operações com containers, como criação, inicialização, parada e remoção. Já a classe `Docker` utiliza a classe `DockerClient` para executar o comando "`docker run`" e retornar a saída do comando. Dessa forma, a classe `Docker` é a responsável por gerenciar as operações com os containers, enquanto a classe `DockerClient` é a responsável pela comunicação com a API do Docker.

A chamada do método `Docker::run(...)` é utilizada para executar um comando dentro de uma imagem Docker. Ele recebe dois
parâmetros:

- `$image` : A imagem com tag do Docker na qual o comando será executado.
- `$command`: O comando que será executado dentro da imagem.

Dentro do arquivo `test-docker-run.php` tem um exemplo de como utilizar, voce pode executar este comando de dentro do
container para testar a implementação.

Exemplo de uso:

```php
use medeirosinacio\Sdk\Docker;

// Executando o comando 'echo "Hello World"' dentro da imagem 'alpine'
$output = Docker::run('alpine:latest', ['echo', 'Hello World']);

echo $output;
// Hello World
```

## Observações

- A primeira vez que o comando é executado pode levar mais tempo, pois a imagem ainda precisa ser baixada pelo container da API.
- O código que remove o container após a execução está comentado, pois isso pode causar um atraso na resposta.
- É importante especificar a `tag` da imagem a ser utilizada, pois sem ela, a API do Docker tentará baixar todas as `tags` disponíveis para a imagem em questão, podendo causar problemas de desempenho e consumo de recursos. Além disso, especificar a `tag` garante que a versão correta da imagem será utilizada, evitando possíveis incompatibilidades ou erros.