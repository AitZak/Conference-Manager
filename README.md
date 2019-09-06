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
Write in .env: MAILER_URL=smtp://mailhog:1025
```
### functionalities

Web: http://localhost
phpMyAdmin: http://localhost:8080
MailHog: http://localhost:8025

- Administrator can manage conferences
    > http://localhost/admin/conference/all
  
- Administrator can see the 10 most popular conferences
    > http://localhost/admin/conference/best
  
- Administrator can manage users
    > http://localhost/admin/user/
  
- Page of voted and not voted conferences
    > http://localhost/conference/voted
  
- Create admin with command
    > app:create-admin
    
- 

