<h3 align="center">Fake-DokuWiki</h3>

<div align="center">

[![GitHub Issues](https://img.shields.io/github/issues/ConX/fake-dokuwiki.svg)](https://github.com/ConX/fake-dokuwiki/issues) [![GitHub Pull Requests](https://img.shields.io/github/issues-pr/ConX/fake-dokuwiki.svg)](https://github.com/ConX/fake-dokuwiki/pulls) [![GitHub stars](https://img.shields.io/github/stars/ConX/fake-dokuwiki.svg "GitHub stars")](https://github.com/ConX/fake-dokuwiki)

</div>

---

<p align="center"> Deploy a DokuWiki with fake pages, namespaces, and users for testing purposes.
    <br> 
</p>

## 📝 Table of Contents

- [📝 Table of Contents](#-table-of-contents)
- [🧐 About <a name = "about"></a>](#-about-)
- [🏁 How to use this image <a name = "getting_started"></a>](#-how-to-use-this-image-)
  - [Run from the GitHub container registry](#run-from-the-github-container-registry)
  - [Build and run from source](#build-and-run-from-source)
    - [Configuration](#configuration)
    - [Using docker](#using-docker)
    - [Using docker-compose](#using-docker-compose)
- [🎉 Acknowledgements <a name = "acknowledgement"></a>](#-acknowledgements-)

## 🧐 About <a name = "about"></a>

For some [DokuWiki](https://www.dokuwiki.org/) plugin development, it is helpful to test it against a populated wiki installation. This repository provides a DokuWiki container image with automatically generated pages, namespaces, and users.

## 🏁 How to use this image <a name = "getting_started"></a>

By default, the fake-dokuwiki contains:
- 100 generated pages
- Created by a randomly selected user from a pool of 10 users
- Placed in a randomly selecting namespace selected out of 3 namespaces or on the root

### Run from the GitHub container registry

```sh
docker pull ghcr.io/conx/fake-dokuwiki:main
docker run -d --name "fake-dokuwiki" -v "${PWD}/files:/config" -p "8100:80"  ghcr.io/conx/fake-dokuwiki:main
```

To setup the DokuWiki once the container has started you need to go to `http://<IP-ADDRESS>:<PORT>/install.php` to configure your install then restart your container when finished to remove install.php

If you wish to generate more pages/namespaces/users you can use the following command:

```sh
docker exec -it fake-dokuwiki php /app/dokuwiki/bin/gen-fake-wiki.php -u <number of new users> -p <number of new pages> -n <number of new namespaces>
```

### Build and run from source

#### Configuration

To modify the default number of pages, namespaces, and/or users, you will need to edit the `Dockerfile` or the `docker-compose.yml` depending on your selected deployment method. Alternatively, you can use the command line arguments on both tools to achieve the same.

#### Using docker

To build and run the fake-dokuwiki image using `docker` run the following:

```sh
mkdir files
docker build . -t fake-dokuwiki
docker run -d --name "fake-dokuwiki" -v "${PWD}/files:/config" -p "8100:80"  fake-dokuwiki
```
#### Using docker-compose

To build and run the fake-dokuwiki image using `docker-compose` run the following:

```sh
docker-compose up -d
```

## 🎉 Acknowledgements <a name = "acknowledgement"></a>

- [@michitux](https://github.com/michitux): For the idea to use the existing CLI plugins to generate the fake pages/users/namespaces
- [@splitbrain](https://github.com/splitbrain/): For the [dwpage](https://github.com/splitbrain/dokuwiki/blob/master/bin/dwpage.php) CLI plugin script based on which the `gen-fake-wiki.php` was created
- [linuxserver.io](https://www.linuxserver.io/): For providing the [base image](https://hub.docker.com/r/linuxserver/dokuwiki) for this project
- [FakerPHP](https://fakerphp.github.io/): For the excellent PHP library that powers the generation of fake data for this project
