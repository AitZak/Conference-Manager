## Conference Manager
Vote for conferences and present conferences that will be voted by the community. 

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
docker-compose exec web composer install
docker-compose exec web php bin/console d:s:u --force 
```
### functionalities

Web: http://localhost

- L’administrateur peut gérer les conférences
    > http://localhost/admin/conference/all
  
- L’administrateur peut voir le top 10 des conférences
    > http://localhost/admin/conference/best
  
- L’administrateur peut gérer les utilisateurs
    > http://localhost/admin/user/
  
- Page des conférences notées / non votées
    > http://localhost/conference/voted
  
- Commande pour créer un admin 
    > app:create-admin
    

