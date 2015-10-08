# @todo

## Ponta-pé inicial

- sgodce (https://svn.icmbio.gov.br/svn/sgdoc-e/outs/implementacao/trunk)
- sicae (https://svn.icmbio.gov.br/svn/sicae/outs/sicae/implementacao/trunk)
- static_cdn (https://svn.icmbio.gov.br/svn/static_cdn/implementacao/trunk)
- SSPCore (https://svn.icmbio.gov.br/svn/docs/outs/arquitetura/sialSoftwarePublico/branches/config-ini)
- mainapp (https://svn.icmbio.gov.br/svn/mainapp/branches/separacao-config-layout-data-webservice-library)
- libcorp (https://svn.icmbio.gov.br/svn/libcorp/outs/libcorp/implementacao/branches/separacao-config-webservice)

## Hosts

```
sgodce.localhost     127.0.0.1
sicae.localhost      127.0.0.1
static.cdn.localhost 127.0.0.1
ws.localhost         127.0.0.1
```

## VHosts

### sgodce.localhost

```xml
<virtualHost *:80>
    ServerName sgodce.localhost
    DocumentRoot /var/www/sgdoce-codigo/sgdoce/public
    ErrorLog /var/log/apache2/sgdoce.error.log
    CustomLog /var/log/apache2/sgdoce.log combined

    SetEnv APPLICATION_ENV "development"

    <Directory /var/www/sgdoce-codigo/sgdoce/public>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order deny,allow
        allow from all
    </Directory>
</VirtualHost>
```

### sicae.localhost

```xml
<virtualHost *:80>
    ServerName sicae.localhost
    DocumentRoot /var/www/sgdoce-codigo/sicae/public
    ErrorLog /var/log/apache2/sicae.error.log
    CustomLog /var/log/apache2/sicae.log combined

    SetEnv APPLICATION_ENV "development"

    <Directory /var/www/sgdoce-codigo/sicae/public>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order deny,allow
        allow from all
    </Directory>
</VirtualHost>
```

### static.cdn.localhost

```xml
<virtualHost *:80>
    ServerName static.cdn.localhost
    DocumentRoot /var/www/sgdoce-codigo/static_cdn
    ErrorLog /var/log/apache2/static_cdn.error.log
    CustomLog /var/log/apache2/static_cdn.log combined

    <Directory /var/www/sgdoce-codigo/static_cdn>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order deny,allow
        allow from all
    </Directory>
</VirtualHost>

```

### ws.localhost

```xml
<virtualHost *:80>
    ServerName ws.localhost
    DocumentRoot /var/www/sgdoce-codigo/mainapp/br/gov/mainapp/webservice
    ErrorLog /var/log/apache2/ws.error.log
    CustomLog /var/log/apache2/ws.log combined

    SetEnv APPLICATION_ENV "development"

    <Directory /var/www/sgdoce-codigo/mainapp/br/gov/mainapp/webservice>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order deny,allow
        allow from all
    </Directory>
</VirtualHost>
```
