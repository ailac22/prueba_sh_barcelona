# Practica SH_Barcelona Iván Moreno Valdés

## Com s'ha fet

### Decisió de tecnologia y nginx

A continuació s'exposa com he fet la prova. Es suposa que partim d'un host amb Linux.

Per crear una app amb un login en php tenim diversos frameworks disponibles. S'ha elegit yii2 senzillament per que es el que es requereix per a aquesta feina y es el que evaluareu millor suposo

Qualsevol framework de desenvolupament web sol tenir un template amb les funcionalitats bàsiques que pot necessitar la nostra web. En aquest cas, yii2 te dues plantilles oficials que es poden emprar:

- [Plantilla bàsica](https://github.com/yiisoft/yii2-app-basic)
- [Plantilla avançada](https://github.com/yiisoft/yii2-app-advanced)

Encara que les dues tenen funcionalitat de login, ninguna de les dues te exactament el que volia. Per una banda la básica te login bàsic però no conecta amb una base de dades, mentre que l'avançada conecta amb la base de dades, pero te massa coses com confirmació per correu, signup, reset de la contrasenya, etc. Es cerca només una pantalla de login amb una ruta protegida i res més. 

S'ha emprat la plantilla bàsica y s'ha modificat amb coses de l'avançada.

Clonem el repo de la app bàsica

> git clone https://github.com/yiisoft/yii2-app-basic.git codi_prova

per fer l'entorn dockeritzat, emprarem [docker compose](https://docs.docker.com/compose/install/). La plantilla ja du una plantilla de docker-compose.yml, que canviarem a mesura de les nostres necessitats. Aquesta duu per defecte la imatge oficial amb apache. Volem emprar nginx. Podriem canviar a l'imatge oficial amb nginx (tag :8.0-fpm), pero emprarem una altra imatge de dockerhub basada en la imatge oficial:

- [Dockerhub](https://hub.docker.com/r/touch4it/docker-php7)
- [GitHub](https://github.com/touch4it/docker-php7)

modifiquem la imatge oficial al docker-compose.yml per `touch4it/yii2-php-fpm-nginx:7.4-dev`, i definim el mount binding que ens indica la imatge:

- .:/var/www/html

Instal·lem composer al contenidor amb les instruccions de l'aplicació bàsica: 

> # docker-compose run --rm php composer update --prefer-dist
> # docker-compose run --rm php composer install  

una vegada fet aixo, podem arrancar el contenidor: 

> # docker-compose up -d

i mirem si al port 8000 veim la aplicació bàsica

A mi m'ha donat el problema de que no pot escriure a certes rutes perque no té permissos d'escritura, per tant fem:

```bash
chmod -R o+w ./assets
chmod -R o+w ./runtime
chmod -R o+w ./web/assets
chmod -R o+w ./tmp
```

caldrà canviar permissos d'altres fitxers creats durant el procés. 

### MySQL

Hem de afegir un segon servei al docker-compose per allotjar la base de dades MySQL. La plantilla avençada fa aixo de manera similar a aquesta:

```bash
  mysql:
    image: mysql:8.0.27
    environment:
      - MYSQL_ROOT_PASSWORD=verysecret
      - MYSQL_DATABASE=yii2basic
      - MYSQL_USER=yii2basic
      - MYSQL_PASSWORD=secret
```

I canviem la configuració al fitxer config/db.php

```yaml
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=mysql;dbname=yii2basic',
    'username' => 'yii2basic',
    'password' => 'secret',
    'charset' => 'utf8',
```
Ara haurem de crear una primera migració per crear la taula de usuaris i un usuari amb el que poder fer login. Dins el contenidor php:

> php yii message/create create_user_table

A la migració crearem una taula amb els camps id,username,passwordHash i authKey. En concret: 

``` 
      'passwordHash' => '$2y$13$Kggcn1Iq1JWw5gFjbJuv.OoO22yaXiquSjQZy75fr6e/PTwsLSuBG',
      'authKey' => 'QbIzc4KVqQdPZkU4-rFK18Hct32lmVt'
```

el passwordHash l'hem obtingut fent el hash de la contrasenya "123456" mitjançant la funció Yii::$app->security->generatePasswordHash($password);

l'authKey es una string aleatòria obtinguda amb Yii::$app->security->generateRandomString()

> php yii migrate

### Usuari

Aqui hem de canviar la clase User, de forma similar a la que es fa en:

- [La documentació de yii2](https://www.yiiframework.com/doc/guide/2.0/en/security-authentication)
- La template avançada

Fem la clase usuari un ActiveRecord i fem que vagi a cercar a la base de dades configurades les dades d'usuari amb els mètodes que ens ofereix. Una vegada fet aixo, la funcionalitat del template hauria de funcionar amb l'usuari:

nom: sh_barcelona
pass: 123456

### Controlador y vistes

Ara volem afegir una ruta protegida, de forma que només quan estem loguejats sigui visible, i si intentem accedir sense estar loguejats, obtindrem un error HTTP 401 unauthorized.


