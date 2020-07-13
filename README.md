## Instalar GO
```
sudo su -
wget https://dl.google.com/go/go1.14.3.linux-amd64.tar.gz
tar -C /usr/local -xzf go1.14.3.linux-amd64.tar.gz
echo "export PATH=$PATH:/usr/local/go/bin" > /etc/profile.d/go.sh
source /etc/profile.d/go.sh
go version
yum install git
```

## Install xCaddy builder
```
go get -u github.com/caddyserver/xcaddy/cmd/xcaddy
cd go/bin/
go list -m
go mod init
./xcaddy build --with github.com/caddy-dns/cloudflare --with github.com/gamalan/caddy-tlsredis
mv caddy /usr/bin/
caddy list-modules | grep redis
```

## Create caddy user/group
```
groupadd --system caddy
useradd --system --gid caddy --create-home --home-dir /var/lib/caddy --shell /usr/sbin/nologin --comment "Caddy web server" caddy
``` 

## Get Caddy files
```
git clone https://github.com/seboraid/caddy-server-ssl.git
mkdir /etc/caddy
cp caddy-server-ssl/config.json /etc/caddy/
```

Test Caddy
```
caddy run --environ --config /etc/caddy/config.json
```

## Install Caddy as Service
`vim /etc/systemd/system/caddy.service`

```
[Unit]
Description=Caddy
Documentation=https://caddyserver.com/docs/
After=network.target

[Service]
User=caddy
Group=caddy
ExecStart=/usr/bin/caddy run --environ --config /etc/caddy/config.json
ExecReload=/usr/bin/caddy reload --config /etc/caddy/config.json
TimeoutStopSec=5s
LimitNOFILE=1048576
LimitNPROC=512
PrivateTmp=true
ProtectSystem=full
AmbientCapabilities=CAP_NET_BIND_SERVICE

[Install]
WantedBy=multi-user.target
```

`systemctl enable caddy`


## Install PHP and FPM-PHP
```
amazon-linux-extras install -y php7.2
yum install -y php-fpm
systemctl enable php-fpm
systemctl restart php-fpm
```

## Configure PHP-FPM
`vim /etc/php-fpm.d/www.conf`

```
listen = /var/run/php.sock
listen.owner = caddy
listen.group = caddy
listen.mode = 0660

user = caddy
group = caddy

#comment
; listen.acl_users
; listen.allowed_clients
```
`service php-fpm restart`

`chown caddy /var/log/php-fpm/`

## Move delegation PHP file
```
mkdir -p /var/www/html
mv caddy-server-ssl/delegated.php /var/www/html/delegated.php
chmod 644 /var/www/html/delegated.php
chown caddy.caddy /var/www/html/delegated.php
```
