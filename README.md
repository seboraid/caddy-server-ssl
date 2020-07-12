## Instalar GO
```
mkdir ~/go
cd ~/go
wget https://dl.google.com/go/go1.14.3.linux-amd64.tar.gz
sudo tar -C /usr/local -xzf go1.14.3.linux-amd64.tar.gz
sudo su -
echo "export PATH=$PATH:/usr/local/go/bin" > /etc/profile.d/go.sh
exit
source ~/.bash_profile
go version
sudo yum install git
```

## Install xCaddy builder
```
sudo su -
go get -u github.com/caddyserver/xcaddy/cmd/xcaddy
cd go/bin/
# ./xcaddy list-modules
#go list -m
#go mod init
./xcaddy build --with github.com/caddy-dns/cloudflare --with github.com/gamalan/caddy-tlsredis
sudo mv caddy /usr/bin/
caddy list-modules | grep redis
```

## Create caddy user/group
```
sudo groupadd --system caddy
sudo useradd --system --gid caddy --create-home --home-dir /var/lib/caddy --shell /usr/sbin/nologin --comment "Caddy web server" caddy
``` 

## Get Caddy files
```
git clone https://github.com/seboraid/caddy-server-ssl.git
sudo mkdir /etc/caddy

git fetch
```

Test Caddy
```
./caddy run --environ --config /etc/caddy/config.json
```

## Install Caddy as Service
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

## Install PHP and FPM-PHP
```
sudo amazon-linux-extras install -y php7.2
sudo yum install -y php-fpm
chkconfig --add php-fpm
chkconfig php-fpm on
systemctl enable caddy
```

## Configure PHP-FPM

`vim /etc/php-fpm.d/www.conf`

```
listen = /var/php.sock
listen.owner = caddy
listen.group = caddy
listen.mode = 0666

user = caddy
group = caddy

php_admin_value[error_log] = /var/log/php-fpm/www-error.log
php_admin_flag[log_errors] = on
```

## Move delegation PHP file
```
sudo mkdir -p /var/www/html
sudo mv delegated.php /var/www/html/delegated.php
sudo chmod 644 /var/www/html/delegated.php
sudo chown caddy.caddy /var/www/html/delegated.php
```
