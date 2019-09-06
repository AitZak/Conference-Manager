## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

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
docker-compose exec --user=application web bash
```
### Fonctionnalités

Web: http://localhost

- L’administrateur peut gérer les conférences
    > http://localhost/admin/conference/all
  
- L’administrateur peut voir le top 10 des conférences
    > http://localhost/admin/conference/best
  
- L’administrateur peut gérer les utilisateurs
    > http://localhost/admin/user/
  
- Page des conférences notées / non votées
    > http://localhost/conference/voted
  
  
