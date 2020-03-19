# POST-IT

## Description

Post-it is a tool to involve all your teammates in your company communication.
You can submit a content, give a review and even post a validated content directly to your socials networks

## Getting Started

### Prerequisites

What things you need to install the software and how to install them?

- [Docker CE](https://www.docker.com/community-edition)
- [Docker Compose](https://docs.docker.com/compose/install)

### Install

- (optional) Create your `docker-compose.override.yml` file

```bash
cp docker-compose.override.yml.dist docker-compose.override.yml
```
> Notice : Check the file content. If other containers use the same ports, change yours.

#### Init

```bash
cp .env.dist .env
docker-compose up -d
docker-compose exec web composer install
docker-compose exec web php bin/console d:s:u --force
docker-compose exec web php bin/console d:f:l
```
> Notice : the last command generates fixtures. Thanks to it, you can connect yourself with email: admin@admin.fr  password: admin

### Functionalities

#### As ANONYMOUS​ :
- See all published content

#### As ROLE_USER​ :
You are an authenticated user, you also can:
- Submit a content and discuss it thru a comment section

#### As ROLE_REVIEWER​ (​same as ROLE_USER, plus​) :
You are a reviewer, you also can:
- Review and edit all contents before ppublications

#### As ROLE_COMM​ (​same as ROLE_Reviewer, plus​) :
You are a communicator, you also can:
- Publish a content in the company social networks
- Have access to a dashboard

#### As ROLE_REVIEWER​ (​same as ROLE_COMM, plus​) :
You are an administrator, you also can:
- Create, update and delete an user
- Manage company social networks
