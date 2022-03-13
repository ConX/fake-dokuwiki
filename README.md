<h3 align="center">Fake-DokuWiki</h3>

<div align="center">

[![GitHub Issues](https://img.shields.io/github/issues/ConX/fake-dokuwiki.svg)](https://github.com/ConX/fake-dokuwiki/issues) [![GitHub Pull Requests](https://img.shields.io/github/issues-pr/ConX/fake-dokuwiki.svg)](https://github.com/ConX/fake-dokuwiki/pulls) [![GitHub stars](https://img.shields.io/github/stars/ConX/fake-dokuwiki.svg "GitHub stars")](https://github.com/ConX/fake-dokuwiki)

</div>

---

<p align="center"> Deploy a DokuWiki with fake pages, namespaces, and users for testing.
    <br> 
</p>

## 📝 Table of Contents

- [About](#about)
- [How to use this image](#getting_started)
- [Acknowledgments](#acknowledgement)

## 🧐 About <a name = "about"></a>

For some [DokuWiki](https://www.dokuwiki.org/) plugin development, it is helpful to test it against a populated wiki installation. This repository provides a DokuWiki container image with automatically generated pages, namespaces, and users.

## 🏁 How to use this image <a name = "getting_started"></a>

### Configuration

By default, the deployed DokuWiki will:
- Generate 100 pages
- Created by a randomly selected user from a pool of 10 users
- Placed in a randomly selecting namespace selected out of 3 namespaces or on the root

To modify these numbers, you will need to edit the `Dockerfile` or the `docker-compose.yml` depending on your selected deployment method. Alternatively, you can use the command line arguments on both tools to achieve the same.

### docker

To build and run the fake-dokuwiki image using `docker` run the following:

```sh
mkdir files
docker build . -t fake-dokuwiki
docker run -d --name "fake-dokuwiki" -v "${PWD}/files:/config" -p "8100:80"  fake-dokuwiki
```
### docker-compose

To build and run the fake-dokuwiki image using `docker-compose` run the following:

```sh
docker-compose up -d
```

## 🎉 Acknowledgements <a name = "acknowledgement"></a>

- [@splitbrain](https://github.com/splitbrain/): For the [dwpage](https://github.com/splitbrain/dokuwiki/blob/master/bin/dwpage.php) CLI plugin script based on which the `gen-fake-wiki.php` was created
- [linuxserver.io](https://www.linuxserver.io/): For providing the [base image](https://hub.docker.com/r/linuxserver/dokuwiki) for this project
- [FakerPHP](https://fakerphp.github.io/): For the excellent PHP library that powers the generation of fake data for this project
